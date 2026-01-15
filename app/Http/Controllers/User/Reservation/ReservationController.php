<?php

namespace App\Http\Controllers\User\Reservation;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use App\Models\ProgrammeStatutDate;
use App\Models\Reservation;
use App\Models\Vehicule;
use App\Notifications\ReservationConfirmeeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeMode;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Récupérer les réservations avec eager loading
        $query = Reservation::with(['programme', 'programme.compagnie'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Appliquer les filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date_voyage')) {
            $query->whereDate('date_voyage', $request->date_voyage);
        }

        if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }

        if ($request->filled('compagnie')) {
            $query->whereHas('programme.compagnie', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->compagnie . '%');
            });
        }

        // Pagination
        $reservations = $query->paginate(10)->withQueryString();

        // Statistiques
        $stats = [
            'confirmed' => Reservation::where('user_id', $user->id)->where('statut', 'confirmee')->count(),
            'pending' => Reservation::where('user_id', $user->id)->where('statut', 'en_attente')->count(),
            'cancelled' => Reservation::where('user_id', $user->id)->where('statut', 'annulee')->count(),
            'total_amount' => Reservation::where('user_id', $user->id)->where('statut', 'confirmee')->sum('montant_total'),
        ];

        return view('user.reservation.index', compact('reservations', 'stats'));
    }

    public function show(Reservation $reservation)
    {
        // Vérifier que la réservation appartient à l'utilisateur
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $reservation->load(['programme', 'programme.compagnie',]);

        return view('user.reservation.show', compact('reservation'));
    }

    public function download(Reservation $reservation)
    {
        // Vérifier que la réservation appartient à l'utilisateur
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$reservation->qr_code_path) {
            abort(404, 'QR Code non trouvé');
        }

        $path = storage_path('app/public/' . $reservation->qr_code_path);

        if (!file_exists($path)) {
            abort(404, 'Fichier non trouvé');
        }

        return response()->download($path, 'billet-' . $reservation->reference . '.png');
    }

    public function ticket(Reservation $reservation)
    {
        // Vérifier que la réservation appartient à l'utilisateur
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $reservation->load(['programme', 'programme.compagnie', 'user']);

        // Calculer les montants
        $prixUnitaire = (float) $reservation->programme->montant_billet;
        $isAllerRetour = (bool) $reservation->programme->is_aller_retour;
        $tripType = $isAllerRetour ? 'Aller-Retour' : 'Aller Simple';
        $prixTotalIndividuel = $isAllerRetour ? $prixUnitaire * 2 : $prixUnitaire;

        // Générer le PDF du billet
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ticket', [
            'reservation' => $reservation,
            'programme' => $reservation->programme,
            'user' => $reservation->user,
            'compagnie' => $reservation->programme->compagnie,
            'qrCodeBase64' => $reservation->qr_code ? base64_encode($reservation->qr_code) : null,
            'tripType' => $tripType,
            'prixUnitaire' => $prixUnitaire,
            'prixTotalIndividuel' => $prixTotalIndividuel,
            'isAllerRetour' => $isAllerRetour,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('billet-' . $reservation->reference . '.pdf');
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        // Vérifier que la réservation appartient à l'utilisateur
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        // Vérifier que la réservation peut être annulée
        if ($reservation->statut !== 'en_attente') {
            return redirect()->back()
                ->with('error', 'Seules les réservations en attente peuvent être annulées.');
        }

        // Annuler la réservation
        $reservation->update([
            'statut' => 'annulee',
            'annulation_reason' => $request->reason ?? 'Annulé par l\'utilisateur',
            'annulation_date' => now(),
        ]);

        // Libérer les places réservées
        // TODO: Implémenter la logique pour libérer les places dans le programme

        return redirect()->route('user.reservations.index')
            ->with('success', 'Réservation annulée avec succès.');
    }
    // Dans votre controller ReservationController
    public function create(Request $request)
    {
        // Si des paramètres de recherche sont passés
        if ($request->has(['point_depart', 'point_arrive', 'date_depart'])) {
            $request->validate([
                'point_depart' => 'required|string|max:255',
                'point_arrive' => 'required|string|max:255',
                'date_depart' => 'required|date',
            ]);

            // Utiliser la même logique de recherche que dans la méthode search
            $point_depart = $request->point_depart;
            $point_arrive = $request->point_arrive;
            $date_depart_recherche = $request->date_depart;

            $formattedDate = date('Y-m-d', strtotime($date_depart_recherche));

            $joursFrancais = [
                'monday' => 'lundi',
                'tuesday' => 'mardi',
                'wednesday' => 'mercredi',
                'thursday' => 'jeudi',
                'friday' => 'vendredi',
                'saturday' => 'samedi',
                'sunday' => 'dimanche'
            ];

            $jourAnglais = strtolower(date('l', strtotime($date_depart_recherche)));
            $jour_semaine = $joursFrancais[$jourAnglais] ?? $jourAnglais;

            $query = Programme::with(['compagnie', 'vehicule', 'itineraire'])
                ->where('point_depart', 'like', "%{$point_depart}%")
                ->where('point_arrive', 'like', "%{$point_arrive}%");

            // Recherche combinée ponctuel + récurrent
            $query->where(function ($q) use ($formattedDate, $jour_semaine) {
                // Programmes ponctuels
                $q->where(function ($sub) use ($formattedDate) {
                    $sub->where('type_programmation', 'ponctuel')
                        ->whereRaw('DATE(date_depart) = ?', [$formattedDate]);
                });

                // Programmes récurrents
                $q->orWhere(function ($sub) use ($formattedDate, $jour_semaine) {
                    $sub->where('type_programmation', 'recurrent')
                        ->whereRaw('DATE(date_depart) <= ?', [$formattedDate])
                        ->where(function ($dateCheck) use ($formattedDate) {
                            $dateCheck->where(function ($subDate) use ($formattedDate) {
                                $subDate->whereNotNull('date_fin_programmation')
                                    ->whereRaw('DATE(date_fin_programmation) >= ?', [$formattedDate]);
                            })
                                ->orWhereNull('date_fin_programmation');
                        })
                        ->where(function ($dayCheck) use ($jour_semaine) {
                            $dayCheck->whereJsonContains('jours_recurrence', $jour_semaine)
                                ->orWhere('jours_recurrence', 'like', "%{$jour_semaine}%");
                        });
                });
            });

            if ($request->has('is_aller_retour') && $request->is_aller_retour !== '') {
                $query->where('is_aller_retour', $request->is_aller_retour);
            }

            $programmes = $query->orderBy('heure_depart', 'asc')->paginate(10);

            $searchParams = [
                'point_depart' => $point_depart,
                'point_arrive' => $point_arrive,
                'date_depart' => $date_depart_recherche,
                'date_depart_formatted' => $formattedDate,
                'is_aller_retour' => $request->is_aller_retour,
            ];

            return view('user.reservation.create', compact('programmes', 'searchParams'));
        }

        // Si pas de recherche, afficher le formulaire vide
        return view('user.reservation.create');
    }


    // Afficher les détails du véhicule (pour le bouton "Détails véhicule")
    public function showVehicle($id)
    {
        $vehicule = Vehicule::find($id);

        if (!$vehicule) {
            return response()->json(['error' => 'Véhicule non trouvé'], 404);
        }

        // Récupérer la date depuis la requête
        $request = request();
        $dateVoyage = $request->get('date');

        // Générer le HTML pour la visualisation
        $typeRangeConfig = [
            '2x2' => ['placesGauche' => 2, 'placesDroite' => 2],
            '2x3' => ['placesGauche' => 2, 'placesDroite' => 3],
            '2x4' => ['placesGauche' => 2, 'placesDroite' => 4]
        ];

        $config = $typeRangeConfig[$vehicule->type_range] ?? null;

        if (!$config) {
            return response()->json(['error' => 'Configuration non reconnue'], 400);
        }

        // Récupérer les places réservées si une date est fournie
        $reservedSeats = [];
        if ($dateVoyage) {
            $formattedDate = date('Y-m-d', strtotime($dateVoyage));

            // Trouver les programmes associés à ce véhicule pour cette date
            $programmes = Programme::where('vehicule_id', $vehicule->id)
                ->where(function ($query) use ($formattedDate) {
                    // Programmes ponctuels
                    $query->where(function ($q) use ($formattedDate) {
                        $q->where('type_programmation', 'ponctuel')
                            ->whereRaw('DATE(date_depart) = ?', [$formattedDate]);
                    })
                        // Programmes récurrents
                        ->orWhere(function ($q) use ($formattedDate) {
                        $joursFrancais = [
                            'monday' => 'lundi',
                            'tuesday' => 'mardi',
                            'wednesday' => 'mercredi',
                            'thursday' => 'jeudi',
                            'friday' => 'vendredi',
                            'saturday' => 'samedi',
                            'sunday' => 'dimanche'
                        ];
                        $jourAnglais = strtolower(date('l', strtotime($formattedDate)));
                        $jour_semaine = $joursFrancais[$jourAnglais] ?? $jourAnglais;

                        $q->where('type_programmation', 'recurrent')
                            ->whereRaw('DATE(date_depart) <= ?', [$formattedDate])
                            ->where(function ($dateCheck) use ($formattedDate) {
                                $dateCheck->where(function ($subDate) use ($formattedDate) {
                                    $subDate->whereNotNull('date_fin_programmation')
                                        ->whereRaw('DATE(date_fin_programmation) >= ?', [$formattedDate]);
                                })
                                    ->orWhereNull('date_fin_programmation');
                            })
                            ->where(function ($dayCheck) use ($jour_semaine) {
                                $dayCheck->whereJsonContains('jours_recurrence', $jour_semaine)
                                    ->orWhere('jours_recurrence', 'like', "%{$jour_semaine}%");
                            });
                    });
                })
                ->get();

            // Pour chaque programme, récupérer les places réservées
            foreach ($programmes as $programme) {
                $programReservations = Reservation::where('programme_id', $programme->id)
                    ->where('statut', '!=', 'annulee')
                    ->where(function ($query) use ($formattedDate) {
                        // Vérifier si la colonne date_voyage existe
                        $table = (new Reservation())->getTable();
                        $columns = Schema::getColumnListing($table);

                        if (in_array('date_voyage', $columns)) {
                            $query->where('date_voyage', $formattedDate);
                        } else {
                            $query->where('date_depart', $formattedDate);
                        }
                    })
                    ->pluck('places')
                    ->flatMap(function ($places) {
                        try {
                            return json_decode($places, true) ?? [];
                        } catch (\Exception $e) {
                            return [];
                        }
                    })
                    ->toArray();

                $reservedSeats = array_merge($reservedSeats, $programReservations);
            }

            $reservedSeats = array_unique($reservedSeats);
        }

        $visualizationHTML = $this->generatePlacesVisualization($vehicule, $config, $reservedSeats);

        return response()->json([
            'success' => true,
            'html' => $visualizationHTML,
            'vehicule' => $vehicule,
            'date' => $dateVoyage,
            'reserved_seats' => $reservedSeats
        ]);
    }

    // Récupérer les détails d'un programme (pour le modal)
    public function getProgram($id)
    {
        try {
            $programme = Programme::with(['compagnie', 'vehicule'])->find($id);

            if (!$programme) {
                return response()->json([
                    'success' => false,
                    'error' => 'Programme non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'programme' => $programme
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getReservedSeats($programId)
    {
        try {
            $request = request();
            $dateVoyage = $request->get('date');

            if (!$dateVoyage) {
                Log::warning('Date non fournie pour getReservedSeats');
                return response()->json([
                    'success' => true,
                    'reservedSeats' => []
                ]);
            }

            $formattedDate = date('Y-m-d', strtotime($dateVoyage));

            // Récupérer le programme pour connaître son type
            $programme = Programme::find($programId);

            if (!$programme) {
                return response()->json([
                    'success' => false,
                    'error' => 'Programme non trouvé'
                ], 404);
            }

            Log::info('Récupération places réservées pour:', [
                'programme_id' => $programId,
                'date_voyage' => $formattedDate,
                'type' => $programme->type_programmation
            ]);

            $query = Reservation::where('programme_id', $programId)
                ->where('statut', '!=', 'annulee');

            // Filtrer par date si c'est un programme récurrent
            if ($programme->type_programmation == 'recurrent') {
                $query->where('date_voyage', $formattedDate);
            } else {
                // Pour programme ponctuel, toutes les réservations du programme
                // (pas de filtre par date)
            }

            $reservedSeats = $query->pluck('places')
                ->flatMap(function ($places) {
                    try {
                        return json_decode($places, true) ?? [];
                    } catch (\Exception $e) {
                        return [];
                    }
                })
                ->toArray();

            Log::info('Places réservées trouvées:', $reservedSeats);

            return response()->json([
                'success' => true,
                'reservedSeats' => $reservedSeats,
                'programme_type' => $programme->type_programmation
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur getReservedSeats:', [
                'error' => $e->getMessage(),
                'programId' => $programId
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    // Stocker la réservation avec PDF et QR Code
    public function store(Request $request)
    {
        Log::info('=== DEBUT RESERVATION ===');
        Log::info('User ID:', ['id' => Auth::id()]);
        Log::info('Données reçues:', $request->all());

        $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'seats' => 'required|array',
            'nombre_places' => 'required|integer|min:1',
            'date_voyage' => 'required|date',
            'passagers' => 'required|array',
            'passagers.*.nom' => 'required|string',
            'passagers.*.prenom' => 'required|string',
            'passagers.*.email' => 'required|email',
            'passagers.*.telephone' => 'required|string',
            'passagers.*.urgence' => 'required|string',
        ]);

        Log::info('Validation passée');

        $programme = Programme::find($request->programme_id);

        if (!$programme) {
            Log::error('Programme non trouvé:', ['id' => $request->programme_id]);
            return response()->json([
                'success' => false,
                'message' => 'Programme non trouvé'
            ], 404);
        }

        // Vérifier si la date est valide pour le programme
        $dateVoyage = date('Y-m-d', strtotime($request->date_voyage));
        $dateDepartProgramme = date('Y-m-d', strtotime($programme->date_depart));

        // Pour les programmes ponctuels : vérifier la date exacte
        if ($programme->type_programmation == 'ponctuel') {
            if ($dateVoyage != $dateDepartProgramme) {
                Log::warning('Date invalide pour programme ponctuel:', [
                    'date_voyage' => $dateVoyage,
                    'date_programme' => $dateDepartProgramme
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'La date sélectionnée ne correspond pas à la date du programme ponctuel.'
                ], 422);
            }
        }

        // Pour les programmes récurrents : vérifier le jour de la semaine
        if ($programme->type_programmation == 'recurrent') {
            $joursFrancais = [
                'monday' => 'lundi',
                'tuesday' => 'mardi',
                'wednesday' => 'mercredi',
                'thursday' => 'jeudi',
                'friday' => 'vendredi',
                'saturday' => 'samedi',
                'sunday' => 'dimanche'
            ];

            $jourAnglais = strtolower(date('l', strtotime($dateVoyage)));
            $jourSemaine = $joursFrancais[$jourAnglais] ?? $jourAnglais;

            // Vérifier si le programme a lieu ce jour-là
            $joursRecurrence = json_decode($programme->jours_recurrence, true) ?? [];

            if (!in_array($jourSemaine, $joursRecurrence)) {
                Log::warning('Jour invalide pour programme récurrent:', [
                    'jour' => $jourSemaine,
                    'jours_recurrence' => $joursRecurrence
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Le programme n\'a pas lieu le ' . $jourSemaine . '.'
                ], 422);
            }

            // Vérifier si la date est dans la plage de validité
            if ($programme->date_fin_programmation) {
                $dateFin = date('Y-m-d', strtotime($programme->date_fin_programmation));
                if ($dateVoyage > $dateFin) {
                    Log::warning('Date après fin de programmation:', [
                        'date_voyage' => $dateVoyage,
                        'date_fin' => $dateFin
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'La date sélectionnée est après la fin de la programmation récurrente.'
                    ], 422);
                }
            }

            // Vérifier si la date est après le début
            if ($dateVoyage < $dateDepartProgramme) {
                Log::warning('Date avant début de programmation:', [
                    'date_voyage' => $dateVoyage,
                    'date_debut' => $dateDepartProgramme
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'La date sélectionnée est avant le début de la programmation récurrente.'
                ], 422);
            }
        }

        // Vérifier si les places sont encore disponibles pour cette date spécifique
        $reservedSeats = Reservation::where('programme_id', $request->programme_id)
            ->where('statut', '!=', 'annulee')
            ->where('date_voyage', $dateVoyage)
            ->pluck('places')
            ->flatMap(function ($places) {
                try {
                    return json_decode($places, true) ?? [];
                } catch (\Exception $e) {
                    return [];
                }
            })
            ->toArray();

        Log::info('Places déjà réservées pour le ' . $dateVoyage . ':', $reservedSeats);
        Log::info('Places demandées:', $request->seats);

        foreach ($request->seats as $seat) {
            if (in_array($seat, $reservedSeats)) {
                Log::warning('Place déjà réservée:', ['place' => $seat, 'date' => $dateVoyage]);
                return response()->json([
                    'success' => false,
                    'message' => "La place $seat n'est plus disponible pour le " . date('d/m/Y', strtotime($dateVoyage))
                ], 422);
            }
        }

        try {
            // Générer une référence unique
            $reference = 'RES-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            // Calculer le montant total (doublé si le programme est un aller-retour)
            $prixUnitaire = $programme->montant_billet;
            if ($programme->is_aller_retour) {
                $prixUnitaire *= 2;
            }
            $montantTotal = $prixUnitaire * $request->nombre_places;

            // Créer la réservation d'abord pour avoir l'ID
            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'programme_id' => $request->programme_id,
                'nombre_places' => $request->nombre_places,
                'places' => json_encode($request->seats),
                'places_reservees' => json_encode($request->seats),
                'passagers' => $request->passagers,
                'is_aller_retour' => $programme->is_aller_retour,
                'montant_total' => $montantTotal,
                'statut' => 'confirmee',
                'date_reservation' => now(),
                'reference' => $reference,
                'date_voyage' => $dateVoyage,
                'date_depart' => $dateVoyage,
            ]);

            // Générer et sauvegarder le QR Code
            $qrCodeData = $this->generateAndSaveQRCode($reference, $reservation->id, $dateVoyage);

            // Mettre à jour la réservation avec les données du QR Code
            $reservation->update([
                'qr_code' => $qrCodeData['base64'],
                'qr_code_path' => $qrCodeData['path'],
                'qr_code_data' => json_encode($qrCodeData['qr_data']),
            ]);

            Log::info('Réservation créée:', [
                'id' => $reservation->id,
                'reference' => $reference,
                'date_voyage' => $dateVoyage,
                'type_programme' => $programme->type_programmation,
                'qr_code_generated' => true
            ]);

            // Mettre à jour le statut du programme si nécessaire
            $this->updateProgramStatus($programme, $dateVoyage);

            // ENVOYER LA NOTIFICATION PAR EMAIL AVEC PDF
            // Envoyer uniquement aux passagers individuels
            $passagers = $request->passagers ?? [];
            foreach ($passagers as $passager) {
                if (!empty($passager['email'])) {
                    $this->sendReservationEmail(
                        $reservation,
                        $programme,
                        $qrCodeData['base64'],
                        $passager['email'],
                        $passager['prenom'] . ' ' . $passager['nom']
                    );
                }
            }

            Log::info('=== FIN RESERVATION ===');

            return response()->json([
                'success' => true,
                'message' => 'Réservation créée avec succès. Les billets ont été envoyés aux passagers.',
                'reservation_id' => $reservation->id,
                'reference' => $reference,
                'qr_code_url' => url('/api/reservations/qrcode/' . $reference)
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur création réservation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la réservation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recalculer le statut des places pour un programme (pour admin)
     */
    public function recalculateProgramStatus($programId)
    {
        try {
            $programme = Programme::with('vehicule')->findOrFail($programId);

            // Si c'est un programme récurrent, on ne peut pas calculer un statut global
            // On calcule plutôt le statut pour chaque jour
            if ($programme->type_programmation == 'recurrent') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pour un programme récurrent, les statuts doivent être calculés par date spécifique.',
                    'suggestion' => 'Utilisez la méthode avec date_voyage spécifique.'
                ]);
            }

            // Pour programme ponctuel
            $totalReservedSeats = Reservation::where('programme_id', $programme->id)
                ->where('statut', '!=', 'annulee')
                ->sum('nombre_places');

            $totalPlaces = $programme->vehicule->nombre_place ?? 50;
            $percentage = ($totalReservedSeats / $totalPlaces) * 100;

            if ($percentage >= 100) {
                $programme->staut_place = 'rempli';
            } elseif ($percentage >= 80) {
                $programme->staut_place = 'presque_complet';
            } else {
                $programme->staut_place = 'vide';
            }

            $programme->nbre_siege_occupe = $totalReservedSeats;
            $programme->save();

            return response()->json([
                'success' => true,
                'message' => 'Statut recalculé avec succès',
                'programme' => [
                    'id' => $programme->id,
                    'type' => $programme->type_programmation,
                    'statut' => $programme->staut_place,
                    'places_reservees' => $totalReservedSeats,
                    'total_places' => $totalPlaces,
                    'pourcentage' => round($percentage, 2)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir le statut d'un programme pour une date spécifique
     */
    public function getProgramStatusForDate($programId, $date)
    {
        try {
            $programme = Programme::with('vehicule')->findOrFail($programId);

            // Vérifier que c'est bien un programme récurrent
            if ($programme->type_programmation != 'recurrent') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette fonction est uniquement pour les programmes récurrents.'
                ]);
            }

            $totalReservedSeats = Reservation::where('programme_id', $programme->id)
                ->where('date_voyage', $date)
                ->where('statut', '!=', 'annulee')
                ->sum('nombre_places');

            $totalPlaces = $programme->vehicule->nombre_place ?? 50;
            $percentage = ($totalReservedSeats / $totalPlaces) * 100;

            $statut = 'vide';
            if ($percentage >= 100) {
                $statut = 'rempli';
            } elseif ($percentage >= 80) {
                $statut = 'presque_complet';
            }

            return response()->json([
                'success' => true,
                'date' => $date,
                'statut' => $statut,
                'places_reservees' => $totalReservedSeats,
                'total_places' => $totalPlaces,
                'pourcentage' => round($percentage, 2)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Générer et sauvegarder le QR Code avec Endroid (version simplifiée)
     */
    private function generateAndSaveQRCode(string $reference, int $reservationId, string $dateVoyage): array
    {
        try {
            // Données à encoder dans le QR Code
            $qrData = [
                'reference' => $reference,
                'reservation_id' => $reservationId,
                'user_id' => Auth::id(),
                'date_voyage' => $dateVoyage,
                'timestamp' => time(),
                'verification_hash' => hash('sha256', $reference . $reservationId . $dateVoyage . config('app.key'))
            ];

            $qrContent = json_encode($qrData);

            // Créer le QR Code (méthode simplifiée)
            $qrCode = QrCode::create($qrContent);

            // Configurer le QR Code
            $qrCode->setSize(300);
            $qrCode->setMargin(10);

            // Écrire le QR Code en PNG
            $writer = new PngWriter();
            $qrCodeResult = $writer->write($qrCode);

            // Obtenir le contenu PNG
            $qrCodeImage = $qrCodeResult->getString();

            // Convertir en base64 pour stockage
            $qrCodeBase64 = base64_encode($qrCodeImage);

            // Chemin de sauvegarde
            $qrCodePath = 'qrcodes/' . $reference . '.png';
            $fullPath = storage_path('app/public/' . $qrCodePath);

            // Créer le dossier si nécessaire
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            // Sauvegarder le fichier
            file_put_contents($fullPath, $qrCodeImage);

            Log::info('QR Code généré et sauvegardé:', [
                'reference' => $reference,
                'path' => $qrCodePath
            ]);

            return [
                'base64' => $qrCodeBase64,
                'path' => $qrCodePath,
                'qr_data' => $qrData,
                'qr_content' => $qrContent
            ];
        } catch (\Exception $e) {
            Log::error('Erreur génération QR Code:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception('Erreur lors de la génération du QR Code: ' . $e->getMessage());
        }
    }

    /**
     * Envoyer l'email de confirmation avec PDF et QR Code
     */
    private function sendReservationEmail(Reservation $reservation, Programme $programme, string $qrCodeBase64, string $recipientEmail = null, string $recipientName = null): void
    {
        try {
            $email = $recipientEmail ?: (Auth::user()->email ?? null);
            $name = $recipientName ?: (Auth::user()->name ?? 'Client');

            if (!$email) {
                Log::warning('Tentative d\'envoi d\'email sans destinataire:', ['reservation_id' => $reservation->id]);
                return;
            }

            Log::info('Envoi de la notification à:', [
                'email' => $email,
                'name' => $name,
                'reservation_id' => $reservation->id
            ]);

            // Envoyer la notification à l'email spécifié
            // Note: On peut utiliser Notification::route('mail', $email) pour envoyer à une adresse arbitraire
            Notification::route('mail', $email)->notify(new ReservationConfirmeeNotification($reservation, $programme, $qrCodeBase64, $name));

            Log::info('Notification envoyée avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email de réservation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'reservation_id' => $reservation->id,
                'email' => $email ?? 'N/A'
            ]);
        }
    }

    /**
     * Mettre à jour le statut du programme
     */
    private function updateProgramStatus($programme, $dateVoyage = null): void
    {
        try {
            // Calculer les places réservées
            $query = Reservation::where('programme_id', $programme->id)
                ->where('statut', '!=', 'annulee');

            if ($programme->type_programmation == 'recurrent' && $dateVoyage) {
                // Pour les programmes récurrents, filtrer par date_voyage
                $query->where('date_voyage', $dateVoyage);
            }

            $totalReservedSeats = $query->sum('nombre_places');
            $totalPlaces = $programme->vehicule->nombre_place ?? 50;

            if ($totalPlaces == 0) {
                $totalPlaces = 50;
            }

            $percentage = ($totalReservedSeats / $totalPlaces) * 100;

            // Déterminer le statut
            if ($percentage >= 100) {
                $status = 'rempli';
            } elseif ($percentage >= 80) {
                $status = 'presque_complet';
            } else {
                $status = 'vide';
            }

            if ($programme->type_programmation == 'ponctuel') {
                // Pour programme ponctuel : mettre à jour directement dans le programme
                $programme->update([
                    'nbre_siege_occupe' => $totalReservedSeats,
                    'staut_place' => $status
                ]);
            } else {
                // Pour programme récurrent : utiliser la nouvelle table
                ProgrammeStatutDate::updateOrCreate(
                    [
                        'programme_id' => $programme->id,
                        'date_voyage' => $dateVoyage
                    ],
                    [
                        'nbre_siege_occupe' => $totalReservedSeats,
                        'staut_place' => $status
                    ]
                );
            }

            Log::info('Statut programme mis à jour:', [
                'programme_id' => $programme->id,
                'type' => $programme->type_programmation,
                'date_voyage' => $dateVoyage,
                'statut' => $status,
                'places_reservees' => $totalReservedSeats,
                'total_places' => $totalPlaces,
                'pourcentage' => round($percentage, 2) . '%'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur mise à jour statut programme:', [
                'error' => $e->getMessage(),
                'programme_id' => $programme->id
            ]);
        }
    }

    /**
     * API pour récupérer le QR Code
     */
    public function getQRCode($reference)
    {
        $reservation = Reservation::where('reference', $reference)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$reservation->qr_code) {
            abort(404, 'QR Code non trouvé');
        }

        $imageData = base64_decode($reservation->qr_code);

        return response($imageData)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'inline; filename="qr_' . $reference . '.png"');
    }

    /**
     * API pour vérifier un QR Code scanné
     */
    public function verifyQRCode(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        try {
            Log::info('=== VERIFICATION QR CODE ===');

            // Décoder les données du QR Code
            $qrData = json_decode($request->qr_data, true);

            if (!$qrData || !is_array($qrData)) {
                Log::warning('QR Code invalide - Format incorrect');
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code invalide',
                    'code' => 'INVALID_FORMAT'
                ], 400);
            }

            // Vérifier les champs requis
            $requiredFields = ['reference', 'reservation_id', 'user_id', 'date_voyage', 'verification_hash'];
            foreach ($requiredFields as $field) {
                if (!isset($qrData[$field])) {
                    Log::warning('QR Code invalide - Champ manquant: ' . $field);
                    return response()->json([
                        'success' => false,
                        'message' => 'Données QR Code incomplètes',
                        'code' => 'MISSING_DATA',
                        'missing_field' => $field
                    ], 400);
                }
            }

            // Vérifier le hash de sécurité
            $expectedHash = hash(
                'sha256',
                $qrData['reference'] .
                $qrData['reservation_id'] .
                $qrData['date_voyage'] .
                config('app.key')
            );

            if ($qrData['verification_hash'] !== $expectedHash) {
                Log::warning('QR Code invalide - Hash incorrect', [
                    'received' => substr($qrData['verification_hash'], 0, 10) . '...',
                    'expected' => substr($expectedHash, 0, 10) . '...'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code non authentique',
                    'code' => 'INVALID_HASH'
                ], 400);
            }

            // Rechercher la réservation
            $reservation = Reservation::with(['user', 'programme', 'programme.compagnie', 'programme.vehicule'])
                ->where('reference', $qrData['reference'])
                ->where('id', $qrData['reservation_id'])
                ->where('user_id', $qrData['user_id'])
                ->first();

            if (!$reservation) {
                Log::warning('Réservation non trouvée', $qrData);
                return response()->json([
                    'success' => false,
                    'message' => 'Réservation non trouvée',
                    'code' => 'NOT_FOUND'
                ], 404);
            }

            // Vérifier le statut
            if ($reservation->statut === 'annulee') {
                Log::info('Réservation annulée vérifiée', ['reference' => $reservation->reference]);
                return response()->json([
                    'success' => false,
                    'message' => 'Réservation annulée',
                    'code' => 'CANCELLED',
                    'reservation' => [
                        'reference' => $reservation->reference,
                        'statut' => $reservation->statut,
                        'date_annulation' => $reservation->updated_at
                    ]
                ], 400);
            }

            // Vérifier la date du voyage
            $today = date('Y-m-d');
            $voyageDate = date('Y-m-d', strtotime($reservation->date_voyage));

            if ($voyageDate < $today) {
                Log::info('Voyage passé vérifié', [
                    'voyage_date' => $voyageDate,
                    'today' => $today
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Voyage déjà effectué',
                    'code' => 'EXPIRED',
                    'reservation' => [
                        'reference' => $reservation->reference,
                        'date_voyage' => $reservation->date_voyage,
                        'statut' => 'expired'
                    ]
                ], 400);
            }

            // Si tout est bon, retourner les informations complètes
            $places = json_decode($reservation->places, true) ?? [];

            Log::info('QR Code vérifié avec succès', [
                'reference' => $reservation->reference,
                'user' => $reservation->user->name,
                'valid' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'QR Code valide',
                'code' => 'VALID',
                'reservation' => [
                    'reference' => $reservation->reference,
                    'statut' => $reservation->statut,
                    'date_voyage' => $reservation->date_voyage,
                    'date_reservation' => $reservation->date_reservation,
                    'user' => [
                        'id' => $reservation->user->id,
                        'name' => $reservation->user->name,
                        'email' => $reservation->user->email,
                    ],
                    'programme' => [
                        'id' => $reservation->programme->id,
                        'point_depart' => $reservation->programme->point_depart,
                        'point_arrive' => $reservation->programme->point_arrive,
                        'heure_depart' => $reservation->programme->heure_depart,
                        'heure_arrive' => $reservation->programme->heure_arrive,
                        'durer_parcours' => $reservation->programme->durer_parcours,
                        'compagnie' => $reservation->programme->compagnie->name ?? 'Inconnue',
                        'vehicule' => [
                            'marque' => $reservation->programme->vehicule->marque ?? 'Inconnue',
                            'modele' => $reservation->programme->vehicule->modele ?? 'Inconnue',
                            'immatriculation' => $reservation->programme->vehicule->immatriculation ?? 'Inconnue',
                        ]
                    ],
                    'details' => [
                        'nombre_places' => $reservation->nombre_places,
                        'places' => $places,
                        'montant_total' => $reservation->montant_total,
                        'montant_formatte' => number_format((float) $reservation->montant_total, 0, ',', ' ') . ' FCFA'
                    ],
                    'verification' => [
                        'timestamp' => now()->toDateTimeString(),
                        'scanned_at' => $request->get('scan_time', now()->toDateTimeString()),
                        'is_valid' => true,
                        'is_used' => false
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur vérification QR Code:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'qr_data_received' => substr($request->qr_data, 0, 100) . '...'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification: ' . $e->getMessage(),
                'code' => 'SERVER_ERROR'
            ], 500);
        }
    }

    /**
     * API pour marquer un QR Code comme utilisé
     */
    public function markQRCodeUsed(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
            'agent_id' => 'required|integer',
            'scan_location' => 'nullable|string',
            'scan_time' => 'nullable|date',
        ]);

        try {
            $reservation = Reservation::where('reference', $request->reference)
                ->firstOrFail();

            // Marquer comme scanné/embarqué
            $reservation->update([
                'embarquement_scanned_at' => $request->get('scan_time', now()),
                'embarquement_agent_id' => $request->agent_id,
                'embarquement_location' => $request->scan_location,
                'embarquement_status' => 'boarded',
            ]);

            Log::info('QR Code marqué comme utilisé:', [
                'reference' => $reservation->reference,
                'agent_id' => $request->agent_id,
                'scan_time' => $request->get('scan_time', now())
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Passager marqué comme embarqué',
                'reference' => $reservation->reference,
                'embarquement_time' => $reservation->embarquement_scanned_at
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur marquage QR Code utilisé:', [
                'error' => $e->getMessage(),
                'reference' => $request->reference
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API pour scanner un QR Code (combine vérification + marquage)
     */
    public function scanQRCode(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
            'agent_id' => 'required|integer',
            'scan_location' => 'nullable|string',
            'mark_as_used' => 'boolean',
        ]);

        // D'abord vérifier le QR Code
        $verificationResponse = $this->verifyQRCode($request);

        if (!$verificationResponse->getData()->success) {
            return $verificationResponse;
        }

        // Si la vérification est réussie et qu'on veut marquer comme utilisé
        if ($request->get('mark_as_used', false)) {
            $reference = $verificationResponse->getData()->reservation->reference;

            $markResponse = $this->markQRCodeUsed(new Request([
                'reference' => $reference,
                'agent_id' => $request->agent_id,
                'scan_location' => $request->scan_location,
                'scan_time' => now(),
            ]));

            if ($markResponse->getData()->success) {
                $verificationData = $verificationResponse->getData();
                $verificationData->embarquement = [
                    'marked' => true,
                    'time' => now()->toDateTimeString(),
                    'agent_id' => $request->agent_id
                ];

                return response()->json($verificationData);
            }
        }

        return $verificationResponse;
    }

    // Méthode pour générer la visualisation des places
    private function generatePlacesVisualization($vehicule, $config, $reservedSeats = [])
    {
        $placesGauche = $config['placesGauche'];
        $placesDroite = $config['placesDroite'];
        $placesParRanger = $placesGauche + $placesDroite;
        $totalPlaces = $vehicule->nombre_place;
        $nombreRanger = ceil($totalPlaces / $placesParRanger);

        // Afficher la date si fournie
        $dateInfo = '';
        if (request()->get('date')) {
            $formattedDate = date('d/m/Y', strtotime(request()->get('date')));
            $dateInfo = '<div style="background: #f0f9ff; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #3b82f6;">
            <strong style="color: #1e40af;">Date du voyage :</strong> 
            <span style="color: #1e40af; font-weight: bold;">' . $formattedDate . '</span>
        </div>';
        }

        $html = '
    <div style="text-align: left; max-width: 800px;">
        ' . $dateInfo . '
        
        <div style="margin-bottom: 20px;">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px;">
                <div style="background: #f8f9fa; padding: 12px; border-radius: 8px;">
                    <strong style="color: #6b7280; font-size: 0.9rem;">Immatriculation</strong>
                    <div style="font-weight: bold; font-size: 1.1rem;">' . $vehicule->immatriculation . '</div>
                </div>
                <div style="background: #f8f9fa; padding: 12px; border-radius: 8px;">
                    <strong style="color: #6b7280; font-size: 0.9rem;">Numéro de série</strong>
                    <div style="font-weight: bold; font-size: 1.1rem;">' . ($vehicule->numero_serie ?? 'N/A') . '</div>
                </div>
            </div>
            
            <div style="background: #f8f9fa; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                    <div style="text-align: center;">
                        <div style="color: #6b7280; font-size: 0.9rem;">Type de rangée</div>
                        <div style="color: #fea219; font-weight: bold; font-size: 1.2rem;">' . $vehicule->type_range . '</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="color: #6b7280; font-size: 0.9rem;">Rangées</div>
                        <div style="color: #10b981; font-weight: bold; font-size: 1.2rem;">' . $nombreRanger . '</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="color: #6b7280; font-size: 0.9rem;">Total places</div>
                        <div style="color: #3b82f6; font-weight: bold; font-size: 1.2rem;">' . $totalPlaces . '</div>
                    </div>
                </div>
            </div>
        </div>
        
        <h3 style="margin-bottom: 15px; color: #374151; font-size: 1.1rem; font-weight: 600;">Configuration des places</h3>
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            <!-- En-tête -->
            <div style="display: grid; grid-template-columns: 100px 1fr 80px 1fr; gap: 10px; padding: 15px; background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                <div style="text-align: center; font-weight: 600; color: #4b5563;">Rangée</div>
                <div style="text-align: center; font-weight: 600; color: #4b5563;">Côté gauche</div>
                <div style="text-align: center; font-weight: 600; color: #4b5563;">Allée</div>
                <div style="text-align: center; font-weight: 600; color: #4b5563;">Côté droit</div>
            </div>
            
            <!-- Rangées -->
            <div style="max-height: 400px; overflow-y: auto;">
    ';

        $numeroPlace = 1;

        for ($ranger = 1; $ranger <= $nombreRanger; $ranger++) {
            $placesRestantes = $totalPlaces - ($numeroPlace - 1);
            $placesCetteRanger = min($placesParRanger, $placesRestantes);
            $placesGaucheCetteRanger = min($placesGauche, $placesCetteRanger);
            $placesDroiteCetteRanger = min($placesDroite, $placesCetteRanger - $placesGaucheCetteRanger);

            $borderStyle = $ranger < $nombreRanger ? 'border-bottom: 1px solid #e5e7eb;' : '';

            $html .= '
        <div style="display: grid; grid-template-columns: 100px 1fr 80px 1fr; gap: 10px; padding: 20px; align-items: center; ' . $borderStyle . '">
            <!-- Numéro de rangée -->
            <div style="text-align: center; font-weight: 600; color: #6b7280;">Rangée ' . $ranger . '</div>
            
            <!-- Places côté gauche -->
            <div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
        ';

            // Places côté gauche
            for ($i = 0; $i < $placesGaucheCetteRanger; $i++) {
                $seatNumber = $numeroPlace + $i;
                $isReserved = in_array($seatNumber, $reservedSeats);

                $bgColor = $isReserved
                    ? 'background: linear-gradient(135deg, #ef4444, #dc2626); opacity: 0.7; cursor: not-allowed;'
                    : 'background: linear-gradient(135deg, #fea219, #e89116); cursor: help;';

                $tooltip = $isReserved ? 'title="Place ' . $seatNumber . ' (Occupée)"' : 'title="Place ' . $seatNumber . '"';

                $html .= '
                <div style="width: 50px; height: 50px; ' . $bgColor . ' border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.1rem; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);" ' . $tooltip . '>
                    ' . $seatNumber . '
                </div>
            ';
            }

            $html .= '
            </div>
            
            <!-- Allée -->
            <div style="text-align: center;">
                <div style="width: 10px; height: 40px; background: #9ca3af; border-radius: 5px; margin: 0 auto;"></div>
            </div>
            
            <!-- Places côté droit -->
            <div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
        ';

            // Places côté droit
            for ($i = 0; $i < $placesDroiteCetteRanger; $i++) {
                $seatNumber = $numeroPlace + $placesGaucheCetteRanger + $i;
                $isReserved = in_array($seatNumber, $reservedSeats);

                $bgColor = $isReserved
                    ? 'background: linear-gradient(135deg, #ef4444, #dc2626); opacity: 0.7; cursor: not-allowed;'
                    : 'background: linear-gradient(135deg, #10b981, #059669); cursor: help;';

                $tooltip = $isReserved ? 'title="Place ' . $seatNumber . ' (Occupée)"' : 'title="Place ' . $seatNumber . '"';

                $html .= '
                <div style="width: 50px; height: 50px; ' . $bgColor . ' border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.1rem; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);" ' . $tooltip . '>
                    ' . $seatNumber . '
                </div>
            ';
            }

            $html .= '
            </div>
        </div>
        ';

            $numeroPlace += $placesCetteRanger;
        }

        $html .= '
            </div>
        </div>
        
        <!-- Légende -->
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-top: 20px; padding: 15px; background: #f9fafb; border-radius: 8px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #fea219, #e89116); border-radius: 4px;"></div>
                <span style="color: #4b5563; font-size: 0.9rem;">Côté gauche (disponible)</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 4px;"></div>
                <span style="color: #4b5563; font-size: 0.9rem;">Côté droit (disponible)</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 4px; opacity: 0.7;"></div>
                <span style="color: #4b5563; font-size: 0.9rem;">Place occupée</span>
            </div>
        </div>
    </div>
    ';

        return $html;
    }
}
