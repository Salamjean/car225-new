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

class ReservationController extends Controller
{
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
        $user = $request->user();

        $query = Reservation::with(['programme.compagnie'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Filtres optionnels
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date_voyage')) {
            $query->whereDate('date_voyage', $request->date_voyage);
        }

        $reservations = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'reservations' => $reservations->items(),
            'pagination' => [
                'current_page' => $reservations->currentPage(),
                'last_page' => $reservations->lastPage(),
                'per_page' => $reservations->perPage(),
                'total' => $reservations->total(),
            ],
            'stats' => [
                'confirmed' => Reservation::where('user_id', $user->id)->where('statut', 'confirmee')->count(),
                'pending' => Reservation::where('user_id', $user->id)->where('statut', 'en_attente')->count(),
                'cancelled' => Reservation::where('user_id', $user->id)->where('statut', 'annulee')->count(),
                'completed' => Reservation::where('user_id', $user->id)->where('statut', 'terminee')->count(),
            ],
        ]);
    }

    /**
     * Détails d'une réservation
     */
    public function show(Request $request, Reservation $reservation)
    {
        // Vérifier que la réservation appartient à l'utilisateur
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.',
            ], 403);
        }

        $reservation->load(['programme.compagnie', 'programme.vehicule']);

        return response()->json([
            'success' => true,
            'reservation' => [
                'id' => $reservation->id,
                'reference' => $reservation->reference,
                'date_voyage' => $reservation->date_voyage,
                'date_retour' => $reservation->date_retour,
                'is_aller_retour' => $reservation->is_aller_retour,
                'seat_number' => $reservation->seat_number,
                'montant' => $reservation->montant,
                'statut' => $reservation->statut,
                'statut_aller' => $reservation->statut_aller,
                'statut_retour' => $reservation->statut_retour,
                'passager_nom' => $reservation->passager_nom,
                'passager_prenom' => $reservation->passager_prenom,
                'passager_email' => $reservation->passager_email,
                'passager_telephone' => $reservation->passager_telephone,
                'qr_code' => $reservation->qr_code,
                'created_at' => $reservation->created_at,
                'programme' => $reservation->programme ? [
                    'id' => $reservation->programme->id,
                    'point_depart' => $reservation->programme->point_depart,
                    'point_arrive' => $reservation->programme->point_arrive,
                    'heure_depart' => $reservation->programme->heure_depart,
                    'montant_billet' => $reservation->programme->montant_billet,
                    'compagnie' => $reservation->programme->compagnie ? [
                        'id' => $reservation->programme->compagnie->id,
                        'name' => $reservation->programme->compagnie->name,
                        'logo' => $reservation->programme->compagnie->logo,
                    ] : null,
                    'vehicule' => $reservation->programme->vehicule ? [
                        'marque' => $reservation->programme->vehicule->marque,
                        'modele' => $reservation->programme->vehicule->modele,
                        'immatriculation' => $reservation->programme->vehicule->immatriculation,
                    ] : null,
                ] : null,
            ],
        ]);
    }

    /**
     * Annuler une réservation
     */
    public function cancel(Request $request, Reservation $reservation)
    {
        // Vérifier que la réservation appartient à l'utilisateur
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.',
            ], 403);
        }

        // Vérifier que la réservation peut être annulée
        if ($reservation->statut !== 'en_attente' && $reservation->statut !== 'confirmee') {
            return response()->json([
                'success' => false,
                'message' => 'Cette réservation ne peut pas être annulée.',
            ], 422);
        }

        $reservation->update([
            'statut' => 'annulee',
            'annulation_reason' => $request->reason ?? 'Annulé par l\'utilisateur via l\'application mobile',
            'annulation_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Réservation annulée avec succès.',
        ]);
    }

    /**
     * Voir tous les programmes disponibles
     */
    public function getAllProgrammes(Request $request)
    {
        $query = Programme::with(['compagnie', 'vehicule', 'itineraire'])
            ->where(function($q) {
                // Programmes ponctuels futurs
                $q->where(function($sub) {
                    $sub->where('type_programmation', 'ponctuel')
                        ->where('date_depart', '>=', now()->format('Y-m-d'));
                })
                // Ou programmes récurrents
                ->orWhere('type_programmation', 'recurrent');
            });

        // Filtrer par type si demandé
        if ($request->has('type')) {
            if ($request->type === 'simple') {
                $query->where('is_aller_retour', false);
            } elseif ($request->type === 'aller_retour') {
                $query->where('is_aller_retour', true);
            }
        }

        $programmes = $query->orderBy('date_depart', 'asc')
            ->orderBy('heure_depart', 'asc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'programmes' => $programmes
        ]);
    }

    /**
     * Voir uniquement les programmes simples
     */
    public function getSimpleProgrammes(Request $request)
    {
        $request->merge(['type' => 'simple']);
        return $this->getAllProgrammes($request);
    }

    /**
     * Voir uniquement les programmes aller-retour
     */
    public function getAllerRetourProgrammes(Request $request)
    {
        $request->merge(['type' => 'aller_retour']);
        return $this->getAllProgrammes($request);
    }

    /**
     * Rechercher des programmes disponibles
     */
    public function searchProgrammes(Request $request)
    {
        $request->validate([
            'point_depart' => 'required|string|max:255',
            'point_arrive' => 'required|string|max:255',
            'date_depart' => 'required|date',
        ]);

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

        $point_depart_normalized = $this->normalizeSearchTerm($point_depart);
        $point_arrive_normalized = $this->normalizeSearchTerm($point_arrive);

        $query = Programme::with(['compagnie', 'vehicule', 'itineraire'])
            ->where(function($q) use ($point_depart, $point_depart_normalized) {
                $q->where('point_depart', 'like', "%{$point_depart}%")
                  ->orWhereRaw('LOWER(point_depart) LIKE ?', ['%' . strtolower($point_depart) . '%']);
            })
            ->where(function($q) use ($point_arrive, $point_arrive_normalized) {
                $q->where('point_arrive', 'like', "%{$point_arrive}%")
                  ->orWhereRaw('LOWER(point_arrive) LIKE ?', ['%' . strtolower($point_arrive) . '%']);
            });

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

        if ($request->filled('is_aller_retour')) {
            $query->where('is_aller_retour', $request->is_aller_retour);
        }

        $programmes = $query->orderBy('heure_depart', 'asc')->paginate(10);

        return response()->json([
            'success' => true,
            'programmes' => $programmes->map(function ($programme) {
                return [
                    'id' => $programme->id,
                    'point_depart' => $programme->point_depart,
                    'point_arrive' => $programme->point_arrive,
                    'heure_depart' => $programme->heure_depart,
                    'montant_billet' => $programme->montant_billet,
                    'is_aller_retour' => $programme->is_aller_retour,
                    'type_programmation' => $programme->type_programmation,
                    'compagnie' => $programme->compagnie ? [
                        'id' => $programme->compagnie->id,
                        'name' => $programme->compagnie->name,
                        'logo' => $programme->compagnie->logo,
                    ] : null,
                    'vehicule' => $programme->vehicule ? [
                        'id' => $programme->vehicule->id,
                        'marque' => $programme->vehicule->marque,
                        'modele' => $programme->vehicule->modele,
                        'nombre_place' => $programme->vehicule->nombre_place,
                    ] : null,
                ];
            }),
            'pagination' => [
                'current_page' => $programmes->currentPage(),
                'last_page' => $programmes->lastPage(),
                'per_page' => $programmes->perPage(),
                'total' => $programmes->total(),
            ],
            'search_params' => [
                'point_depart' => $point_depart,
                'point_arrive' => $point_arrive,
                'date_depart' => $formattedDate,
            ],
        ]);
    }

    /**
     * Récupérer les places réservées pour un programme
     */
    public function getReservedSeats(Request $request, $programId)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $formattedDate = date('Y-m-d', strtotime($request->date));

        $reservedSeats = Reservation::where('programme_id', $programId)
            ->where('statut', 'confirmee')
            ->where('date_voyage', $formattedDate)
            ->pluck('seat_number')
            ->toArray();

        return response()->json([
            'success' => true,
            'reserved_seats' => $reservedSeats,
            'date' => $formattedDate,
        ]);
    }

    /**
     * Récupérer les détails d'un programme
     */
    public function getProgram($id)
    {
        $programme = Programme::with(['compagnie', 'vehicule'])->find($id);

        if (!$programme) {
            return response()->json([
                'success' => false,
                'message' => 'Programme non trouvé',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'programme' => [
                'id' => $programme->id,
                'point_depart' => $programme->point_depart,
                'point_arrive' => $programme->point_arrive,
                'heure_depart' => $programme->heure_depart,
                'montant_billet' => $programme->montant_billet,
                'is_aller_retour' => $programme->is_aller_retour,
                'type_programmation' => $programme->type_programmation,
                'jours_recurrence' => $programme->jours_recurrence,
                'compagnie' => $programme->compagnie ? [
                    'id' => $programme->compagnie->id,
                    'name' => $programme->compagnie->name,
                    'logo' => $programme->compagnie->logo,
                ] : null,
                'vehicule' => $programme->vehicule ? [
                    'id' => $programme->vehicule->id,
                    'marque' => $programme->vehicule->marque,
                    'modele' => $programme->vehicule->modele,
                    'nombre_place' => $programme->vehicule->nombre_place,
                    'type_range' => $programme->vehicule->type_range,
                ] : null,
            ],
        ]);
    }

    /**
     * Créer une réservation et initier le paiement
     */
    public function store(Request $request)
    {
        Log::info('=== API: DEBUT RESERVATION ===');
        Log::info('User ID:', ['id' => $request->user()->id]);
        Log::info('Données reçues:', $request->all());

        $validated = $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'seats' => 'required|array|min:1',
            'nombre_places' => 'required|integer|min:1',
            'date_voyage' => 'required|date',
            'is_aller_retour' => 'boolean',
            'date_retour' => 'nullable|date',
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
            return response()->json([
                'success' => false,
                'message' => 'Programme non trouvé',
            ], 404);
        }

        $isAllerRetour = $request->boolean('is_aller_retour');
        $dateVoyage = date('Y-m-d', strtotime($validated['date_voyage']));
        $dateRetour = $validated['date_retour'] ?? null;

        if ($isAllerRetour && !$dateRetour) {
            return response()->json([
                'success' => false,
                'message' => 'La date de retour est requise pour un aller-retour.',
            ], 422);
        }

        // Vérifier disponibilité des places
        $reservedSeats = Reservation::where('programme_id', $programme->id)
            ->where('statut', 'confirmee')
            ->where('date_voyage', $dateVoyage)
            ->pluck('seat_number')
            ->toArray();

        foreach ($validated['seats'] as $seat) {
            if (in_array($seat, $reservedSeats)) {
                return response()->json([
                    'success' => false,
                    'message' => "La place $seat n'est plus disponible pour le " . date('d/m/Y', strtotime($dateVoyage)),
                ], 422);
            }
        }

        try {
            $user = $request->user();
            
            // Calculer le montant
            $prixUnitaire = $programme->montant_billet;
            if ($programme->is_aller_retour) {
                $prixUnitaire *= 2;
            }
            $montantTotal = $prixUnitaire * $validated['nombre_places'];

            // Générer ID transaction
            $transactionId = 'TRANS-' . date('YmdHis') . '-' . strtoupper(Str::random(5));

            // Créer le paiement
            $paiement = \App\Models\Paiement::create([
                'user_id' => $user->id,
                'amount' => $montantTotal,
                'transaction_id' => $transactionId,
                'status' => 'pending',
                'currency' => 'XOF',
            ]);

            // Créer les réservations
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

                $reservation = Reservation::create($reservationData);

                Log::info('Réservation créée (en attente):', [
                    'id' => $reservation->id,
                    'reference' => $reference,
                    'seat_number' => $seatNumber,
                ]);

                $createdReservations[] = [
                    'id' => $reservation->id,
                    'reference' => $reference,
                    'seat_number' => $seatNumber,
                ];
            }

            Log::info('=== FIN INITIALISATION: ' . count($createdReservations) . ' réservations ===');

            // --- DEBUT GENERATION LIEN CINETPAY (Style Plateau-app) ---
            $description = 'Réservation de ' . $validated['nombre_places'] . ' place(s) - ' . $programme->compagnie->name;
            $paymentLinkResult = $this->generateCinetPayLink($paiement, $description);

            if (!$paymentLinkResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Échec de la génération du lien de paiement. Veuillez réessayer.',
                    'error_details' => $paymentLinkResult['error_details']
                ], 500);
            }

            $cinetpayData = $paymentLinkResult['cinetpay_data'];

            return response()->json([
                'success' => true,
                'message' => 'Réservations initialisées. Utilisez le payment_url pour payer.',
                'requires_payment' => true,
                'payment_details' => [
                    'payment_url' => $cinetpayData['payment_url'],
                    'payment_token' => $cinetpayData['payment_token'],
                    'transaction_id' => $paymentLinkResult['generated_transaction_id'],
                    'mode' => 'PRODUCTION',
                    'return_url_deep_link' => $paymentLinkResult['return_url_deep_link'],
                    'cancel_url_deep_link' => $paymentLinkResult['cancel_url_deep_link'],
                ],
                'data' => [
                    'reservations' => $createdReservations,
                    'paiement' => $paiement
                ]
            ], 201);
            // --- FIN GENERATION LIEN CINETPAY ---

        } catch (\Exception $e) {

        } catch (\Exception $e) {
            Log::error('Erreur création réservation API:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la réservation: ' . $e->getMessage(),
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
                    
                    // TODO: Envoyer SMS/Email/Notification Push ici
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
