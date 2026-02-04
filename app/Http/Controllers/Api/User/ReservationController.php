<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use App\Models\Reservation;
use App\Http\Controllers\User\Reservation\ReservationController as WebReservationController;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\WalletTransaction;
use App\Services\CinetPayService;
use App\Services\FcmService;
use App\Models\Itineraire;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReservationController extends Controller
{
    protected $cinetPayService;
    protected $fcmService;

    public function __construct(CinetPayService $cinetPayService, FcmService $fcmService)
    {
        $this->cinetPayService = $cinetPayService;
        $this->fcmService = $fcmService;
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

        // Transformer les données pour ajouter point_depart et point_arrive
        $reservations->getCollection()->transform(function ($reservation) {
            $reservation->point_depart = $reservation->programme->point_depart ?? null;
            $reservation->point_arrive = $reservation->programme->point_arrive ?? null;
            return $reservation;
        });

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
   // 3. --- AJOUT : Création de l'entrée PAIEMENT (Pour comptabilité/Reservation) ---
                    $paiement = Paiement::create([
                        'user_id' => $user->id,
                        'amount' => $montantTotal,
                        'transaction_id' => $transactionId,
                        'status' => 'success', // Directement succès car débit immédiat
                        'currency' => 'XOF',
                        'payment_method' => 'wallet',
                        'payment_date' => now(), // Payé tout de suite
                    ]);
                    // 3. Créer réservations (UNE PAR PLACE)
                    $createdReservations = [];
                    foreach ($validated['passagers'] as $passager) {
                        $seatNumber = $passager['seat_number'];
                        $reference = $transactionId . '-' . $seatNumber;

                        $reservationData = [
                            'user_id' => $user->id,
                            'programme_id' => $programme->id,
                            'paiement_id' => $paiement->id, // ← AJOUT ICI
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
                       // --- AJOUT IMPORTANT : GENERATION VRAI QR + EMAIL ---
                        $this->finalizeReservation($res);
                        // On rafraichit pour avoir les chemins de QR dans la réponse
                        $res->refresh(); 
                        
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
            
            // 1. Récupération dynamique de l'URL (Ngrok ou Prod)
           $baseUrl = config('app.url'); 
            $baseUrl = rtrim($baseUrl, '/'); 
            $reference = $paiement->transaction_id;
            
            // 2. Deep links pour l'application mobile
            $returnUrl = "car225://payment?success=true&transactionId={$reference}";
            $cancelUrl = "car225://payment?success=false&transactionId={$reference}";
            
            // 3. URLs de fallback (Web) -> C'est ces lignes qu'il vous manquait !
            $fallbackReturnUrl = $baseUrl . "/payment/callback?transactionId=" . urlencode($reference);
            $fallbackCancelUrl = $baseUrl . "/payment/callback?cancel=1&transactionId=" . urlencode($reference);
            
            // 4. Webhook URL
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
                'return_url' => $fallbackReturnUrl, // Maintenant cette variable existe
                'cancel_url' => $fallbackCancelUrl, // Celle-ci aussi
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

            Log::info('Appel CinetPay API:', ['transaction_id' => $cinetpayTransactionId, 'notify_url' => $notifyUrl]);

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
                    // 1. Paiement réussi !
                    $paiement->update([
                        'status' => 'success',
                        'payment_date' => now(),
                        'payment_details' => $data
                    ]);

                    Log::info("Paiement {$reference} validé. Lancement finalisation...");

                    // 2. Confirmer les réservations ET générer QR/Email via la fonction partagée
                    $reservations = Reservation::where('paiement_id', $paiement->id)->get();
                    
                    foreach ($reservations as $res) {
                        if ($res->statut !== 'confirmee') {
                            $res->update(['statut' => 'confirmee']);
                            
                            // C'EST ICI QUE LA MAGIE OPÈRE :
                            $this->finalizeReservation($res);
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

     /**
     * AJOUT : Fonction pour finaliser la réservation (QR Code + Email)
     * Copiée et adaptée du PaymentController Web
     */
    protected function finalizeReservation(Reservation $reservation)
    {
        try {
            // On instancie le contrôleur Web pour utiliser ses méthodes existantes
            // (Assure-toi que les méthodes generateAndSaveQRCode et sendReservationEmail sont publiques dans le WebController)
            $webController = app(WebReservationController::class);

            $dateVoyageStr = $reservation->date_voyage instanceof \Carbon\Carbon
                ? $reservation->date_voyage->format('Y-m-d')
                : date('Y-m-d', strtotime($reservation->date_voyage));

            // 1. Génération du QR Code via le Web Controller
            if (method_exists($webController, 'generateAndSaveQRCode')) {
                $qrCodeData = $webController->generateAndSaveQRCode(
                    $reservation->reference,
                    $reservation->id,
                    $dateVoyageStr,
                    $reservation->user_id
                );

                // 2. Mise à jour des infos QR dans la base
                $reservation->update([
                    'qr_code' => $qrCodeData['base64'],
                    'qr_code_path' => $qrCodeData['path'],
                    'qr_code_data' => $qrCodeData['qr_data'],
                    'statut_aller' => 'confirmee',
                ]);
                
                $qrCodeBase64 = $qrCodeData['base64'];
            } else {
                // Fallback si la méthode n'existe pas (juste pour éviter le crash)
                Log::warning("Méthode generateAndSaveQRCode non trouvée sur le WebController");
                $qrCodeBase64 = 'QR_NON_GENERE';
            }

            // 3. Gestion Retour
            $programmeRetour = null;
            $qrCodeRetour = null;

            if ($reservation->is_aller_retour) {
                $reservation->update(['statut_retour' => 'confirmee']);
                $qrCodeRetour = $qrCodeBase64; // Même QR pour l'instant

                if ($reservation->programme_retour_id) {
                    $programmeRetour = Programme::find($reservation->programme_retour_id);
                } elseif ($reservation->programme && $reservation->programme->programmeRetour) {
                    $programmeRetour = $reservation->programme->programmeRetour;
                }
            }

            // 4. Envoi Email via le Web Controller
            if (method_exists($webController, 'sendReservationEmail')) {
                $webController->sendReservationEmail(
                    $reservation,
                    $reservation->programme,
                    $qrCodeBase64,
                    $reservation->passager_email ?? $reservation->user->email,
                    ($reservation->passager_prenom . ' ' . $reservation->passager_nom),
                    $reservation->seat_number,
                    $reservation->is_aller_retour ? 'ALLER-RETOUR' : 'ALLER SIMPLE',
                    $qrCodeRetour,
                    $programmeRetour
                );
            }

            // 5. Mise à jour places occupées
            if (method_exists($webController, 'updateProgramStatus')) {
                 $webController->updateProgramStatus(
                    $reservation->programme,
                    $dateVoyageStr
                );
            }

            Log::info('API: Finalisation réservation terminée (QR + Email)', ['id' => $reservation->id]);

            // 6. ENVOI NOTIFICATION MOBILE (FCM)
            $this->sendReservationNotification($reservation);

        } catch (\Exception $e) {
            Log::error('API: Erreur critique finalisation réservation:', [
                'id' => $reservation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envoie une notification push au mobile de l'utilisateur
     */
    private function sendReservationNotification(Reservation $reservation)
    {
        try {
            $user = $reservation->user;
            if (!$user || !$user->fcm_token) {
                Log::info('FCM: Aucun token trouvé pour l\'utilisateur ' . ($user->id ?? 'inconnu'));
                return;
            }

            $title = "Ticket confirmé ! 🎟️";
            $programme = $reservation->programme;
            $trajet = "{$programme->point_depart} → {$programme->point_arrive}";
            
            $body = "Votre réservation {$reservation->reference} pour le trajet {$trajet} est confirmée. Place: {$reservation->seat_number}.";
            
            if ($reservation->is_aller_retour) {
                $body = "Votre ticket Aller-Retour {$reservation->reference} ({$trajet}) est confirmé. Place: {$reservation->seat_number}.";
            }

            $data = [
                'type' => 'reservation_confirmed',
                'reservation_id' => (string)$reservation->id,
                'reference' => $reservation->reference,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ];

            Log::info("FCM: Tentative d'envoi à l'utilisateur {$user->id}");
            $result = $this->fcmService->sendNotification($user->fcm_token, $title, $body, $data);
            
            if (!$result['success']) {
                Log::warning("FCM: Échec de l'envoi: " . $result['message']);
            } else {
                Log::info("FCM: Notification envoyée avec succès");
            }

        } catch (\Exception $e) {
            Log::error("FCM: Erreur lors de l'envoi: " . $e->getMessage());
        }
    }

    /**
     * Récupérer tous les billets PDF d'une réservation (API Mobile)
     * 
     * Pour les réservations aller-retour, retourne les 2 billets (ALLER + RETOUR)
     * Pour les réservations aller simple, retourne 1 seul billet
     * 
     * @param Request $request
     * @param int $id ID de la réservation
     * @return \Illuminate\Http\JsonResponse
     */
    public function ticket(Request $request, $id)
    {
        $reservation = Reservation::with(['programme', 'programme.compagnie', 'user', 'programmeRetour'])
            ->where('user_id', Auth::id())
            ->find($id);

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Réservation non trouvée'
            ], 404);
        }

        $seatNumber = $request->query('seat_number') ?: $reservation->seat_number;
        $tickets = [];

        // Calculer les montants
        $prixUnitaire = (float) $reservation->programme->montant_billet;
        $isAllerRetour = (bool) $reservation->is_aller_retour;
        $tripType = $isAllerRetour ? 'Aller-Retour' : 'Aller Simple';
        $prixTotalIndividuel = $isAllerRetour ? $prixUnitaire * 2 : $prixUnitaire;

        try {
            // ============ BILLET ALLER ============
            $allerConsomme = $reservation->statut_aller === 'terminee' || $reservation->statut === 'terminee';
            
            $ticketAller = $this->generateTicketData(
                $reservation,
                $reservation->programme,
                $reservation->date_voyage,
                $isAllerRetour ? 'ALLER' : 'ALLER SIMPLE',
                $seatNumber,
                $prixUnitaire,
                $prixTotalIndividuel,
                $isAllerRetour,
                $tripType,
                $allerConsomme
            );
            $tickets[] = $ticketAller;

            // ============ BILLET RETOUR (si aller-retour) ============
            if ($isAllerRetour) {
                $retourConsomme = $reservation->statut_retour === 'terminee';
                
                // Récupérer le programme retour
                $programmeRetour = $reservation->programmeRetour ?? Programme::find($reservation->programme_retour_id);
                if (!$programmeRetour) {
                    // Fallback: utiliser le programme principal avec trajet inversé
                    $programmeRetour = $reservation->programme;
                }
                
                $ticketRetour = $this->generateTicketData(
                    $reservation,
                    $programmeRetour,
                    $reservation->date_retour,
                    'RETOUR',
                    $seatNumber,
                    $prixUnitaire,
                    $prixTotalIndividuel,
                    $isAllerRetour,
                    $tripType,
                    $retourConsomme,
                    true // isRetour flag
                );
                $tickets[] = $ticketRetour;
            }

            return response()->json([
                'success' => true,
                'message' => 'Billets récupérés avec succès',
                'data' => [
                    'reservation_id' => $reservation->id,
                    'reservation_reference' => $reservation->reference,
                    'is_aller_retour' => $isAllerRetour,
                    'seat_number' => $seatNumber,
                    'total_tickets' => count($tickets),
                    'tickets' => $tickets
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API: Erreur génération PDF billet:', [
                'reservation_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du billet: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Génère les données d'un billet (helper pour ticket())
     */
    private function generateTicketData(
        Reservation $reservation,
        Programme $programme,
        $dateVoyage,
        string $ticketType,
        $seatNumber,
        float $prixUnitaire,
        float $prixTotalIndividuel,
        bool $isAllerRetour,
        string $tripType,
        bool $isConsumed,
        bool $isRetour = false
    ): array {
        // Déterminer l'heure de départ
        $heureDepart = $programme->heure_depart;
        if ($isRetour && $programme->retour_heure_depart) {
            $heureDepart = $programme->retour_heure_depart;
        }

        // Déterminer le trajet (inversé pour retour si même programme)
        if ($isRetour && $reservation->programme_retour_id === null) {
            // Même programme, inverser départ/arrivée
            $trajet = $programme->point_arrive . ' → ' . $programme->point_depart;
        } else {
            $trajet = $programme->point_depart . ' → ' . $programme->point_arrive;
        }

        // Générer le PDF
        $pdf = Pdf::loadView('pdf.ticket', [
            'reservation' => $reservation,
            'programme' => $programme,
            'user' => $reservation->user,
            'compagnie' => $programme->compagnie ?? $reservation->programme->compagnie,
            'qrCodeBase64' => $reservation->qr_code,
            'tripType' => $tripType,
            'ticketType' => $ticketType,
            'dateVoyage' => $dateVoyage,
            'heureDepart' => $heureDepart,
            'prixUnitaire' => $prixUnitaire,
            'prixTotalIndividuel' => $prixTotalIndividuel,
            'isAllerRetour' => $isAllerRetour,
            'seatNumber' => $seatNumber,
        ])->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 150,
            ]);

        $pdfContent = $pdf->output();
        $pdfBase64 = base64_encode($pdfContent);

        $nomFichier = 'billet-' . strtolower(str_replace(' ', '-', $ticketType)) . '-' . $reservation->reference;
        if ($seatNumber) {
            $nomFichier .= '-Place-' . $seatNumber;
        }

        return [
            'type' => strtolower(str_replace(' ', '_', $ticketType)), // 'aller', 'retour', 'aller_simple'
            'ticket_type' => $ticketType, // 'ALLER', 'RETOUR', 'ALLER SIMPLE'
            'filename' => $nomFichier . '.pdf',
            'pdf_base64' => $pdfBase64,
            'content_type' => 'application/pdf',
            'date_voyage' => $dateVoyage,
            'heure_depart' => $heureDepart,
            'trajet' => $trajet,
            'compagnie' => $programme->compagnie->name ?? 'N/A',
            'is_consumed' => $isConsumed,
            'status' => $isConsumed ? 'terminee' : 'valide',
            'status_label' => $isConsumed ? 'Voyage terminé' : 'Valide'
        ];
    }

    /**
     * Télécharger le QR Code PNG d'une réservation (API Mobile)
     * 
     * @param int $id ID de la réservation
     * @return \Illuminate\Http\JsonResponse
     */
    public function download($id)
    {
        $reservation = Reservation::where('user_id', Auth::id())->find($id);

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Réservation non trouvée'
            ], 404);
        }

        if (!$reservation->qr_code_path) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code non disponible pour cette réservation'
            ], 404);
        }

        $path = storage_path('app/public/' . $reservation->qr_code_path);

        if (!file_exists($path)) {
            // Si le fichier n'existe pas mais qu'on a le QR en base64, on retourne le base64
            if ($reservation->qr_code) {
                return response()->json([
                    'success' => true,
                    'message' => 'QR Code récupéré',
                    'data' => [
                        'filename' => 'qrcode-' . $reservation->reference . '.png',
                        'qr_code_base64' => $reservation->qr_code,
                        'content_type' => 'image/png',
                        'reservation_reference' => $reservation->reference,
                    ]
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Fichier QR Code non trouvé'
            ], 404);
        }

        // Lire le fichier et l'encoder en base64
        $qrCodeContent = file_get_contents($path);
        $qrCodeBase64 = base64_encode($qrCodeContent);

        return response()->json([
            'success' => true,
            'message' => 'QR Code téléchargé',
            'data' => [
                'filename' => 'qrcode-' . $reservation->reference . '.png',
                'qr_code_base64' => $qrCodeBase64,
                'content_type' => 'image/png',
                'reservation_reference' => $reservation->reference,
            ]
        ]);
    }
}
