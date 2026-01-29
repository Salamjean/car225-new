<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use App\Models\Reservation;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\WalletTransaction;
use App\Services\CinetPayService;
use App\Models\Itineraire;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    protected $cinetPayService;

    public function __construct(CinetPayService $cinetPayService)
    {
        $this->cinetPayService = $cinetPayService;
    }

    /**
     * Normalise un terme de recherche
     */
    private function normalizeSearchTerm(string $term): string
    {
        $term = strtolower($term);
        $accents = ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'ô', 'ö', 'ù', 'û', 'ü', 'î', 'ï', 'ç'];
        $noAccents = ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'o', 'o', 'u', 'u', 'u', 'i', 'i', 'c'];
        return str_replace($accents, $noAccents, $term);
    }

    /**
     * Liste des réservations de l'utilisateur
     */
    public function index(Request $request)
    {
        $reservations = Reservation::with(['programme', 'programme.compagnie'])
            ->where('user_id', $request->user()->id)
            ->where('statut', '!=', 'en_attente') // On ne montre pas les tentatives non abouties
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

    /**
     * Lister les itinéraires disponibles
     */
    public function getItineraires()
    {
        // Récupérer les itinéraires (points de départ et d'arrivée uniques)
        // Utilisation de MIN(id) pour satisfaire le mode SQL strict (only_full_group_by)
        $itineraires = Itineraire::selectRaw('MIN(id) as id, point_depart, point_arrive')
            ->groupBy('point_depart', 'point_arrive')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $itineraires
        ]);
    }

    /**
     * Rechercher les programmes par itinéraire
     */
    public function searchProgrammesByItineraire(Request $request)
    {
        $request->validate([
            'itineraire_id' => 'nullable|exists:itineraires,id',
            'point_depart' => 'required_without:itineraire_id|string',
            'point_arrive' => 'required_without:itineraire_id|string',
            'date_depart' => 'nullable|date',
            'is_aller_retour' => 'nullable|boolean'
        ]);

        $query = Programme::with(['compagnie', 'vehicule']);

        // Filtrage par itinéraire
        if ($request->filled('itineraire_id')) {
            $itineraire = Itineraire::find($request->itineraire_id);
            if ($itineraire) {
                // Utilisation des villes de l'itinéraire
                $point_depart = $itineraire->point_depart;
                $point_arrive = $itineraire->point_arrive;
            }
        } else {
            $point_depart = $request->point_depart;
            $point_arrive = $request->point_arrive;
        }

        if (isset($point_depart)) {
            $point_depart_normalized = $this->normalizeSearchTerm($point_depart);
            // Extraire la ville si format "Ville, Pays"
            $point_depart_city = explode(',', $point_depart)[0];
            $point_depart_city = trim($point_depart_city);
            $point_depart_city_normalized = $this->normalizeSearchTerm($point_depart_city);

            $query->where(function($q) use ($point_depart, $point_depart_normalized, $point_depart_city, $point_depart_city_normalized) {
                $q->where('point_depart', 'like', "%{$point_depart}%")
                  ->orWhere('point_depart', 'like', "%{$point_depart_city}%") // Recherche ville seule
                  ->orWhereRaw('LOWER(point_depart) LIKE ?', ['%' . strtolower($point_depart) . '%'])
                  ->orWhereRaw('LOWER(point_depart) LIKE ?', ['%' . strtolower($point_depart_city) . '%']) // Recherche ville seule (lower)
                  ->orWhereRaw('LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(point_depart, "é", "e"), "è", "e"), "ê", "e"), "ô", "o"), "à", "a")) LIKE ?', ['%' . $point_depart_normalized . '%'])
                  ->orWhereRaw('LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(point_depart, "é", "e"), "è", "e"), "ê", "e"), "ô", "o"), "à", "a")) LIKE ?', ['%' . $point_depart_city_normalized . '%']);
            });
        }

        if (isset($point_arrive)) {
            $point_arrive_normalized = $this->normalizeSearchTerm($point_arrive);
             // Extraire la ville si format "Ville, Pays"
            $point_arrive_city = explode(',', $point_arrive)[0];
            $point_arrive_city = trim($point_arrive_city);
            $point_arrive_city_normalized = $this->normalizeSearchTerm($point_arrive_city);

            $query->where(function($q) use ($point_arrive, $point_arrive_normalized, $point_arrive_city, $point_arrive_city_normalized) {
                $q->where('point_arrive', 'like', "%{$point_arrive}%")
                  ->orWhere('point_arrive', 'like', "%{$point_arrive_city}%")
                  ->orWhereRaw('LOWER(point_arrive) LIKE ?', ['%' . strtolower($point_arrive) . '%'])
                  ->orWhereRaw('LOWER(point_arrive) LIKE ?', ['%' . strtolower($point_arrive_city) . '%'])
                  ->orWhereRaw('LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(point_arrive, "é", "e"), "è", "e"), "ê", "e"), "ô", "o"), "à", "a")) LIKE ?', ['%' . $point_arrive_normalized . '%'])
                   ->orWhereRaw('LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(point_arrive, "é", "e"), "è", "e"), "ê", "e"), "ô", "o"), "à", "a")) LIKE ?', ['%' . $point_arrive_city_normalized . '%']);
            });
        }

        // --- Logique de Date & Récurrence ---
        if ($request->filled('date_depart')) {
            $formattedDate = date('Y-m-d', strtotime($request->date_depart));
            
            $joursFrancais = [
                'monday' => 'lundi', 'tuesday' => 'mardi', 'wednesday' => 'mercredi',
                'thursday' => 'jeudi', 'friday' => 'vendredi', 'saturday' => 'samedi', 'sunday' => 'dimanche'
            ];
            $jourAnglais = strtolower(date('l', strtotime($formattedDate)));
            $jour_semaine = $joursFrancais[$jourAnglais] ?? $jourAnglais;

            Log::info("API Search: Filtre date strict pour $formattedDate ($jour_semaine)");

            $query->where(function ($q) use ($formattedDate, $jour_semaine) {
                // Programmes ponctuels pour cette date exacte
                $q->where(function ($sub) use ($formattedDate) {
                    $sub->where('type_programmation', 'ponctuel')
                        ->whereRaw('DATE(date_depart) = ?', [$formattedDate]);
                });
                
                // Programmes récurrents actifs pour ce jour de la semaine
                $q->orWhere(function ($sub) use ($formattedDate, $jour_semaine) {
                    $sub->where('type_programmation', 'recurrent')
                        ->whereRaw('DATE(date_depart) <= ?', [$formattedDate]) // A commencé avant ou ajd
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
        } else {
            // Pas de date : Afficher tous les programmes futurs ou récurrents actifs
            $today = date('Y-m-d');
            Log::info("API Search: Pas de date, recherche globale futurs à partir de $today");
            
            $query->where(function ($q) use ($today) {
                // Ponctuels futurs (ou aujourd'hui)
                $q->where(function ($sub) use ($today) {
                    $sub->where('type_programmation', 'ponctuel')
                        ->whereRaw('DATE(date_depart) >= ?', [$today]);
                });
                
                // Récurrents en cours ou futurs
                $q->orWhere(function ($sub) use ($today) {
                    $sub->where('type_programmation', 'recurrent')
                         ->where(function ($dateCheck) use ($today) {
                            $dateCheck->where(function ($subDate) use ($today) {
                                $subDate->whereNotNull('date_fin_programmation')
                                    ->whereRaw('DATE(date_fin_programmation) >= ?', [$today]);
                            })
                            ->orWhereNull('date_fin_programmation');
                        });
                });
            });
        }

        if ($request->has('is_aller_retour') && $request->is_aller_retour !== null) {
            $query->where('is_aller_retour', $request->boolean('is_aller_retour'));
        }

        $programmes = $query->orderBy('heure_depart', 'asc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $programmes
        ]);
    }

    /**
     * Détails d'une réservation
     */
    public function show($id)
    {
        $reservation = Reservation::with(['programme', 'programme.compagnie', 'programme.vehicule'])
            ->where('user_id', Auth::id())
            ->find($id);

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Réservation non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $reservation
        ]);
    }

    /**
     * Annuler une réservation
     */
    public function cancel(Request $request, $id)
    {
        $reservation = Reservation::where('user_id', Auth::id())->find($id);

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Réservation non trouvée'
            ], 404);
        }

        if ($reservation->statut !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'Seules les réservations en attente peuvent être annulées.'
            ], 400);
        }

        $reservation->update([
            'statut' => 'annulee',
            'annulation_reason' => $request->reason ?? 'Annulé par l\'utilisateur',
            'annulation_date' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Réservation annulée avec succès'
        ]);
    }

    /**
     * Récupérer tous les programmes disponibles
     */
    public function getAllProgrammes()
    {
        $programmes = Programme::with(['compagnie', 'vehicule'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $programmes
        ]);
    }

    /**
     * Récupérer les programmes aller-simple seulement
     */
    public function getSimpleProgrammes()
    {
        $programmes = Programme::with(['compagnie', 'vehicule'])
            ->where('is_aller_retour', false)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $programmes
        ]);
    }

    /**
     * Récupérer les programmes aller-retour seulement
     */
    public function getAllerRetourProgrammes()
    {
        $programmes = Programme::with(['compagnie', 'vehicule'])
            ->where('is_aller_retour', true)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $programmes
        ]);
    }

    /**
     * Rechercher des programmes
     */
    public function searchProgrammes(Request $request)
    {
        $request->validate([
            'point_depart' => 'nullable|string',
            'point_arrive' => 'nullable|string',
            'date_depart' => 'nullable|date',
            'is_aller_retour' => 'nullable|boolean' // 0, 1 ou null pour tout
        ]);

        $point_depart = $request->point_depart;
        $point_arrive = $request->point_arrive;
        $date_depart_recherche = $request->date_depart ?? date('Y-m-d');
        
        $formattedDate = date('Y-m-d', strtotime($date_depart_recherche));
        
        // Jours en français pour la récurrence
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

        $query = Programme::with(['compagnie', 'vehicule']);

        // Filtre Point de départ
        if ($point_depart) {
            $point_depart_normalized = $this->normalizeSearchTerm($point_depart);
            $query->where(function($q) use ($point_depart, $point_depart_normalized) {
                $q->where('point_depart', 'like', "%{$point_depart}%")
                  ->orWhereRaw('LOWER(point_depart) LIKE ?', ['%' . strtolower($point_depart) . '%'])
                  ->orWhereRaw('LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(point_depart, "é", "e"), "è", "e"), "ê", "e"), "ô", "o"), "à", "a")) LIKE ?', ['%' . $point_depart_normalized . '%']);
            });
        }

        // Filtre Point d'arrivée
        if ($point_arrive) {
            $point_arrive_normalized = $this->normalizeSearchTerm($point_arrive);
            $query->where(function($q) use ($point_arrive, $point_arrive_normalized) {
                $q->where('point_arrive', 'like', "%{$point_arrive}%")
                  ->orWhereRaw('LOWER(point_arrive) LIKE ?', ['%' . strtolower($point_arrive) . '%'])
                  ->orWhereRaw('LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(point_arrive, "é", "e"), "è", "e"), "ê", "e"), "ô", "o"), "à", "a")) LIKE ?', ['%' . $point_arrive_normalized . '%']);
            });
        }

        // Filtre Date & Récurrence
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

        // Filtre Aller-Retour (Si spécifié)
        // Note: Si is_aller_retour n'est pas fourni (null), on retourne TOUT (Aller simple ET Aller-Retour)
        if ($request->has('is_aller_retour') && $request->is_aller_retour !== null) {
            $query->where('is_aller_retour', $request->boolean('is_aller_retour'));
        }

        $programmes = $query->orderBy('heure_depart', 'asc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $programmes
        ]);
    }

    /**
     * Détails d'un programme
     */
    public function getProgram($id)
    {
        $programme = Programme::with(['compagnie', 'vehicule'])->find($id);

        if (!$programme) {
            return response()->json([
                'success' => false,
                'message' => 'Programme non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $programme
        ]);
    }

    /**
     * Récupérer les places réservées pour un programme à une date donnée
     */
    public function getReservedSeats(Request $request, $programId)
    {
        $date = $request->query('date');
        
        if (!$date) {
            return response()->json([
                'success' => false,
                'message' => 'Date requise'
            ], 400);
        }

        $formattedDate = date('Y-m-d', strtotime($date));

        $reservedSeats = Reservation::where('programme_id', $programId)
            ->where('statut', 'confirmee')
            ->where('date_voyage', $formattedDate)
            ->pluck('seat_number')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $reservedSeats
        ]);
    }

    /**
     * Créer une réservation (avec support Wallet et Multi-Réservations)
     */
    public function store(Request $request)
    {
        Log::info('=== API: DEBUT RESERVATION (Multi-Seat) ===');
        Log::info('User ID:', ['id' => $request->user()->id]);

        $validated = $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'seats' => 'required|array|min:1',
            'nombre_places' => 'required|integer|min:1',
            'date_voyage' => 'required|date',
            'is_aller_retour' => 'boolean',
            'date_retour' => 'nullable|date',
            'payment_method' => 'nullable|string|in:cinetpay,wallet',
            'passagers' => 'required|array',
            'passagers.*.nom' => 'required|string',
            'passagers.*.prenom' => 'required|string',
            'passagers.*.email' => 'required|email',
            'passagers.*.telephone' => 'required|string',
            'passagers.*.urgence' => 'required|string',
            'passagers.*.seat_number' => 'required|integer',
        ]);

        $programme = Programme::with('compagnie')->find($validated['programme_id']);
        if (!$programme) {
            return response()->json(['success' => false, 'message' => 'Programme non trouvé'], 404);
        }

        $isAllerRetour = $request->boolean('is_aller_retour');
        $dateVoyage = date('Y-m-d', strtotime($validated['date_voyage']));
        $dateRetour = $validated['date_retour'] ?? null;
        $paymentMethod = $request->input('payment_method', 'cinetpay');

        if ($isAllerRetour && !$dateRetour) {
            return response()->json(['success' => false, 'message' => 'Date de retour requise pour aller-retour'], 422);
        }

        // Vérifier disponibilité
        $reservedSeats = Reservation::where('programme_id', $programme->id)
            ->where('statut', 'confirmee')
            ->where('date_voyage', $dateVoyage)
            ->pluck('seat_number')
            ->toArray();

        foreach ($validated['seats'] as $seat) {
            if (in_array($seat, $reservedSeats)) {
                return response()->json([
                    'success' => false, 
                    'message' => "La place $seat n'est plus disponible."
                ], 422);
            }
        }

        try {
            $user = $request->user();
            $prixUnitaire = $programme->montant_billet;
            if ($programme->is_aller_retour) $prixUnitaire *= 2;
            $montantTotal = $prixUnitaire * $validated['nombre_places'];

            // === PAIEMENT WALLET ===
            if ($paymentMethod === 'wallet') {
                if ($user->solde < $montantTotal) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Solde insuffisant. Votre solde: ' . $user->solde . ' FCFA'
                    ], 400);
                }

                DB::beginTransaction();
                try {
                    // 1. Débit
                    $user->solde -= $montantTotal;
                    $user->save();

                    // 2. Transaction
                    $transactionId = 'TX-WAL-' . strtoupper(Str::random(10));
                    WalletTransaction::create([
                        'user_id' => $user->id,
                        'amount' => $montantTotal,
                        'type' => 'debit',
                        'description' => 'Réservation ' . $validated['nombre_places'] . ' place(s)',
                        'reference' => $transactionId,
                        'status' => 'completed',
                        'payment_method' => 'wallet',
                        'metadata' => json_encode(['programme_id' => $programme->id])
                    ]);

                    // 3. Créer réservations (UNE PAR PLACE)
                    $createdReservations = [];
                    foreach ($validated['passagers'] as $passager) {
                        $seatNumber = $passager['seat_number'];
                        $reference = $transactionId . '-' . $seatNumber;

                        $reservationData = [
                            'user_id' => $user->id,
                            'programme_id' => $programme->id,
                            'seat_number' => $seatNumber,
                            'passager_nom' => $passager['nom'],
                            'passager_prenom' => $passager['prenom'],
                            'passager_email' => $passager['email'],
                            'passager_telephone' => $passager['telephone'],
                            'passager_urgence' => $passager['urgence'],
                            'is_aller_retour' => $isAllerRetour,
                            'montant' => $prixUnitaire, // Montant unitaire par place
                            'statut' => 'confirmee',
                            'reference' => $reference,
                            'date_voyage' => $dateVoyage,
                            'payment_transaction_id' => $transactionId,
                            'qr_code' => Str::random(32) // Simulation QR
                        ];

                        if ($isAllerRetour) {
                            $reservationData['date_retour'] = $dateRetour;
                            $reservationData['statut_aller'] = 'confirmee';
                            $reservationData['statut_retour'] = 'confirmee';
                            if ($programme->programme_retour_id) {
                                $reservationData['programme_retour_id'] = $programme->programme_retour_id;
                            }
                        } else {
                            $reservationData['statut_aller'] = 'confirmee';
                        }

                        $res = Reservation::create($reservationData);
                        $createdReservations[] = $res;
                    }

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'Réservations confirmées (Wallet)',
                        'requires_payment' => false,
                        'data' => [
                            'transaction_id' => $transactionId,
                            'reservations' => $createdReservations
                        ]
                    ], 201);

                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
            
            // === PAIEMENT CINETPAY ===
            else {
                $transactionId = 'TRANS-' . date('YmdHis') . '-' . strtoupper(Str::random(5));

                // 1. Créer le paiement en attente
                $paiement = Paiement::create([
                    'user_id' => $user->id,
                    'amount' => $montantTotal,
                    'transaction_id' => $transactionId,
                    'status' => 'pending',
                    'currency' => 'XOF',
                    'payment_method' => 'cinetpay'
                ]);

                // 2. Créer les réservations en attente (UNE PAR PLACE)
                $createdReservations = [];
                foreach ($validated['passagers'] as $passager) {
                    $seatNumber = $passager['seat_number'];
                    $reference = $transactionId . '-' . $seatNumber;

                    $reservationData = [
                        'paiement_id' => $paiement->id,
                        'payment_transaction_id' => $transactionId,
                        'user_id' => $user->id,
                        'programme_id' => $programme->id,
                        'seat_number' => $seatNumber,
                        'passager_nom' => $passager['nom'],
                        'passager_prenom' => $passager['prenom'],
                        'passager_email' => $passager['email'],
                        'passager_telephone' => $passager['telephone'],
                        'passager_urgence' => $passager['urgence'],
                        'is_aller_retour' => $isAllerRetour,
                        'montant' => $prixUnitaire,
                        'statut' => 'en_attente',
                        'reference' => $reference,
                        'date_voyage' => $dateVoyage,
                    ];

                    if ($isAllerRetour) {
                        $reservationData['date_retour'] = $dateRetour;
                        $reservationData['statut_aller'] = 'en_attente';
                        $reservationData['statut_retour'] = 'en_attente';
                        if ($programme->programme_retour_id) {
                            $reservationData['programme_retour_id'] = $programme->programme_retour_id;
                        }
                    } else {
                        $reservationData['statut_aller'] = 'en_attente';
                    }

                    $res = Reservation::create($reservationData);
                    $createdReservations[] = $res;
                }

                // 3. Générer le lien CinetPay
                $description = 'Réservation ' . count($createdReservations) . ' place(s) - ' . $programme->compagnie->name;
                $paymentLinkResult = $this->generateCinetPayLink($paiement, $description);

                if (!$paymentLinkResult['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erreur lien paiement',
                        'error_details' => $paymentLinkResult['error_details']
                    ], 500);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Réservations initialisées. Paiement requis.',
                    'requires_payment' => true,
                    'payment_details' => $paymentLinkResult['cinetpay_data'],
                    'data' => [
                        'transaction_id' => $transactionId,
                        'amount' => $montantTotal
                    ]
                ], 201);
            }

        } catch (\Exception $e) {
            Log::error('Erreur Réservation API:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false, 
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Génère un lien de paiement CinetPay (Style Plateau-app)
     */
    private function generateCinetPayLink(Paiement $paiement, $description)
    {
        try {
            $user = $paiement->user;
            $baseUrl = config('app.url');
            $reference = $paiement->transaction_id;
            
            // Deep links pour l'app mobile
            $returnUrl = "car225://payment?success=true&transactionId={$reference}";
            $cancelUrl = "car225://payment?success=false&transactionId={$reference}";
            
            // URLs de fallback (web)
            $fallbackReturnUrl = $baseUrl . "/payment/callback?transactionId=" . urlencode($reference);
            $fallbackCancelUrl = $baseUrl . "/payment/callback?cancel=1&transactionId=" . urlencode($reference);
            
            // Webhook URL
            $notifyUrl = $baseUrl . "/api/user/payment/notify";

            $cinetpayApiKey = config('services.cinetpay.api_key');
            $cinetpaySiteId = config('services.cinetpay.site_id');

            // ID Unique pour CinetPay
            $cinetpayTransactionId = $reference . '_' . time();

            $paymentData = [
                'apikey' => $cinetpayApiKey,
                'site_id' => $cinetpaySiteId,
                'transaction_id' => $cinetpayTransactionId,
                'amount' => (int)$paiement->amount,
                'currency' => $paiement->currency,
                'description' => $description,
                'notify_url' => $notifyUrl,
                'return_url' => $fallbackReturnUrl,
                'cancel_url' => $fallbackCancelUrl,
                'mode' => 'PRODUCTION',
                'channels' => 'ALL',

                'customer_name' => $user->name,
                'customer_surname' => $user->prenom ?? $user->name,
                'customer_email' => $user->email,
                'customer_phone_number' => $user->contact ?? '0000000000',
                'customer_address' => $user->adresse ?? 'Abidjan',
                'customer_city' => $user->commune ?? 'Abidjan',
                'customer_country' => 'CI',
            ];

            Log::info('Appel CinetPay API:', ['transaction_id' => $cinetpayTransactionId]);

            $response = Http::withoutVerifying()->post('https://api-checkout.cinetpay.com/v2/payment', $paymentData);

            if ($response->failed() || $response->json('code') !== '201') {
                Log::error('Erreur CinetPay (API): ' . $response->body());
                return [
                    'success' => false,
                    'error_details' => $response->json() ?? $response->body()
                ];
            }

            $responseData = $response->json('data');
            
            // Sauvegarder le token dans le paiement
            $paiement->update([
                'payment_token' => $responseData['payment_token'] ?? null
            ]);

            return [
                'success' => true,
                'cinetpay_data' => $responseData,
                'generated_transaction_id' => $cinetpayTransactionId,
                'return_url_deep_link' => $returnUrl,
                'cancel_url_deep_link' => $cancelUrl,
            ];

        } catch (\Exception $e) {
            Log::error('Exception generateCinetPayLink: ' . $e->getMessage());
            return [
                'success' => false,
                'error_details' => $e->getMessage()
            ];
        }
    }

    /**
     * Webhook CinetPay pour confirmer le paiement
     */
    public function handlePaymentNotification(Request $request)
    {
        Log::info('Webhook CinetPay Reçu (Reservation):', $request->all());

        $cinetpayTransactionId = $request->input('cpm_trans_id')
            ?? $request->input('transaction_id')
            ?? $request->input('data.cpm_trans_id');

        if (!$cinetpayTransactionId) {
            return response()->json(['success' => false, 'message' => 'Transaction ID manquant'], 200);
        }

        // Extraire la référence originale
        $reference = explode('_', $cinetpayTransactionId)[0];

        try {
            $paiement = Paiement::where('transaction_id', $reference)->first();

            if (!$paiement) {
                Log::warning("Paiement non trouvé pour reference {$reference}");
                return response()->json(['success' => false, 'message' => 'Paiement non trouvé'], 200);
            }

            if ($paiement->status === 'success') {
                return response()->json(['success' => true, 'message' => 'Déjà traité'], 200);
            }

            // Vérifier auprès de CinetPay
            $response = Http::withoutVerifying()->post('https://api-checkout.cinetpay.com/v2/payment/check', [
                'apikey' => config('services.cinetpay.api_key'),
                'site_id' => config('services.cinetpay.site_id'),
                'transaction_id' => $cinetpayTransactionId,
            ]);

            if ($response->successful()) {
                $data = $response->json('data') ?? $response->json();
                $status = $data['status'] ?? null;

                if ($status === 'ACCEPTED') {
                    // Paiement réussi !
                    $paiement->update([
                        'status' => 'success',
                        'payment_date' => now(),
                        'payment_details' => $data
                    ]);

                    // Confirmer les réservations
                    $reservations = Reservation::where('paiement_id', $paiement->id)->get();
                    foreach ($reservations as $res) {
                        $res->update([
                            'statut' => 'confirmee',
                            'statut_aller' => 'confirmee'
                        ]);
                        if ($res->is_aller_retour) {
                            $res->update(['statut_retour' => 'confirmee']);
                        }
                    }

                    Log::info("Paiement {$reference} confirmé et réservations validées.");
                    
                    Log::info("Paiement {$reference} confirmé et réservations validées.");
                    
                    // Envoyer les emails de confirmation
                    foreach ($reservations as $res) {
                        try {
                            $programme = $res->programme;
                            $user = $res->user; // Ou le passager si différent
                            
                            // Générer QR Code si absent (optionnel, mais mieux vaut le faire)
                            if (empty($res->qr_code_path) && class_exists(\App\Http\Controllers\User\Reservation\ReservationController::class)) {
                                // On instancie le contrôleur web pour utiliser ses helpers (pas idéal mais rapide)
                                // Ou mieux, on refait la logique QR ici.
                                // Pour l'instant, on suppose que le QR est généré ou on le génère à la volée
                            }

                            // Envoi Email
                            // Note: Idéalement, déplacer sendReservationEmail dans un Service partagé
                            $email = $res->passager_email ?? $user->email;
                            $nom = ($res->passager_prenom . ' ' . $res->passager_nom) ?? $user->name;
                            
                            // On utilise la notification directement
                            // Check imports first!
                            // Assumons que ReservationConfirmeeNotification est importé ou FQCN
                            
                            // Génération QR Code à la volée pour l'email si nécessaire
                            // (Simplifié pour l'API, on enverra le lien ou le code string)
                            
                            \Illuminate\Support\Facades\Notification::route('mail', $email)
                                ->notify(new \App\Notifications\ReservationConfirmeeNotification(
                                    $res,
                                    $programme,
                                    $res->qr_code ?? 'QR_CODE_MANQUANT',
                                    $nom,
                                    $res->seat_number,
                                    ($res->is_aller_retour) ? 'ALLER-RETOUR' : 'ALLER SIMPLE',
                                    null, // QR Retour
                                    $res->programmeRetour // Programme Retour
                                ));
                                
                            Log::info("Email de confirmation envoyé à $email pour la réservation {$res->id}");

                        } catch (\Exception $e) {
                            Log::error("Erreur envoi email réservation {$res->id}: " . $e->getMessage());
                        }
                    }
                } else {
                    Log::warning("Paiement {$reference} rejeté par CinetPay: {$status}");
                    $paiement->update(['status' => 'failed']);
                }
            }

            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            Log::error('Erreur Webhook CinetPay: ' . $e->getMessage());
            return response()->json(['success' => false], 200);
        }
    }
}
