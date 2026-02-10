<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use App\Models\ProgrammeStatutDate;
use App\Models\Reservation;
use App\Models\Personnel;
use App\Models\Vehicule;
use App\Models\Paiement;
use App\Models\WalletTransaction;
use App\Models\User;
use App\Models\Itineraire;
use App\Notifications\ReservationConfirmeeNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\DB;


class ReservationApiController extends Controller
{
    /**
     * Normalise un terme de recherche en enlevant les accents et mettant en minuscule
     */
    private function normalizeSearchTerm(string $term): string
    {
        $term = strtolower($term);
        $accents = ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'ô', 'ö', 'ù', 'û', 'ü', 'î', 'ï', 'ç'];
        $noAccents = ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'o', 'o', 'u', 'u', 'u', 'i', 'i', 'c'];
        return str_replace($accents, $noAccents, $term);
    }

    /**
     * GET /api/user/reservations
     * Liste des réservations de l'utilisateur
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            $query = Reservation::with(['programme', 'programme.compagnie', 'programme.vehicule'])
                ->where('user_id', $user->id)
                ->where('statut', '!=', 'en_attente')
                ->orderBy('created_at', 'desc')
                ->orderBy('payment_transaction_id', 'desc')
                ->orderBy('seat_number', 'asc');

            // Filtres
            if ($request->filled('reference')) {
                $query->where(function($q) use ($request) {
                    $q->where('reference', 'like', '%' . $request->reference . '%')
                      ->orWhere('payment_transaction_id', 'like', '%' . $request->reference . '%')
                      ->orWhere('passager_nom', 'like', '%' . $request->reference . '%')
                      ->orWhere('passager_prenom', 'like', '%' . $request->reference . '%');
                });
            }

            if ($request->filled('statut')) {
                $query->where('statut', $request->statut);
            }

            if ($request->filled('date_voyage')) {
                $query->whereDate('date_voyage', $request->date_voyage);
            }

            if ($request->filled('compagnie')) {
                $query->whereHas('programme.compagnie', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->compagnie . '%');
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $reservations = $query->paginate($perPage);

            // Statistiques
            $stats = [
                'confirmed' => Reservation::where('user_id', $user->id)->where('statut', 'confirmee')->count(),
                'pending' => Reservation::where('user_id', $user->id)->where('statut', 'en_attente')->count(),
                'cancelled' => Reservation::where('user_id', $user->id)->where('statut', 'annulee')->count(),
                'total_amount' => Reservation::where('user_id', $user->id)->where('statut', 'confirmee')->sum('montant'),
            ];

            return response()->json([
                'success' => true,
                'data' => $reservations,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur liste réservations API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des réservations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/user/reservations/{reservation}
     * Détails d'une réservation
     */
    public function show(Reservation $reservation)
    {
        try {
            // Vérifier que la réservation appartient à l'utilisateur
            if ($reservation->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $reservation->load(['programme', 'programme.compagnie', 'programme.vehicule', 'user']);

            return response()->json([
                'success' => true,
                'data' => $reservation
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur détails réservation API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la réservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/user/reservations/{reservation}
     * Annuler une réservation
     */
    public function cancel(Request $request, Reservation $reservation)
    {
        try {
            // Vérifier que la réservation appartient à l'utilisateur
            if ($reservation->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            // Vérifier que la réservation peut être annulée
            if ($reservation->statut !== 'en_attente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Seules les réservations en attente peuvent être annulées.'
                ], 422);
            }

            // Annuler la réservation
            $reservation->update([
                'statut' => 'annulee',
                'annulation_reason' => $request->reason ?? 'Annulé par l\'utilisateur',
                'annulation_date' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Réservation annulée avec succès',
                'data' => $reservation
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur annulation réservation API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/user/reservations/{reservation}/ticket
     * Télécharger le billet PDF
     */
    public function ticket(Request $request, Reservation $reservation)
    {
        try {
            // Vérifier que la réservation appartient à l'utilisateur
            if ($reservation->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $reservation->load(['programme', 'programme.compagnie', 'user']);

            // Récupérer le type de billet (aller par défaut)
            $type = $request->query('type', 'aller');
            $seatNumber = $request->query('seat_number') ?: $reservation->seat_number;

            // Sélection des données selon le type
            if ($type === 'retour' && $reservation->is_aller_retour) {
                $programme = $reservation->programmeRetour ?? Programme::find($reservation->programme_retour_id);
                $dateVoyage = $reservation->date_retour;
                
                if (!$programme) {
                    $programme = $reservation->programme;
                }

                $qrCodeBase64 = $reservation->qr_code;
                $ticketType = 'RETOUR';
                $heureDepart = $programme ? $programme->heure_depart : 'N/A';
            } else {
                $programme = $reservation->programme;
                $dateVoyage = $reservation->date_voyage;
                
                if (empty($reservation->qr_code)) {
                    $dateVoyageStr = $dateVoyage instanceof \Carbon\Carbon ? $dateVoyage->format('Y-m-d') : $dateVoyage;
                    $qrCodeData = $this->generateAndSaveQRCode(
                        $reservation->reference,
                        $reservation->id,
                        $dateVoyageStr,
                        $reservation->user_id
                    );
                    
                    $reservation->update([
                        'qr_code' => $qrCodeData['base64'],
                        'qr_code_path' => $qrCodeData['path'],
                        'qr_code_data' => $qrCodeData['qr_data']
                    ]);
                }

                $qrCodeBase64 = $reservation->qr_code;
                $ticketType = $reservation->is_aller_retour ? 'ALLER' : 'ALLER SIMPLE';
                $heureDepart = $programme->heure_depart;
            }

            // Calculer les montants
            $prixUnitaire = (float) $reservation->programme->montant_billet;
            $isAllerRetour = (bool) $reservation->programme->is_aller_retour;
            $tripType = $isAllerRetour ? 'Aller-Retour' : 'Aller Simple';
            $prixTotalIndividuel = $isAllerRetour ? $prixUnitaire * 2 : $prixUnitaire;

            $nomFichier = 'billet-' . strtolower($ticketType) . '-' . $reservation->reference;
            if ($seatNumber) {
                $nomFichier .= '-Place-' . $seatNumber;
            }

            // Générer le PDF du billet
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ticket', [
                'reservation' => $reservation,
                'programme' => $programme,
                'user' => $reservation->user,
                'compagnie' => $programme->compagnie ?? $reservation->programme->compagnie,
                'qrCodeBase64' => $qrCodeBase64,
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

            return $pdf->stream($nomFichier . '.pdf');
        } catch (\Exception $e) {
            Log::error('Erreur génération ticket API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du billet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/user/reservations/{reservation}/download
     * Télécharger le QR Code
     */
    public function download(Reservation $reservation)
    {
        try {
            // Vérifier que la réservation appartient à l'utilisateur
            if ($reservation->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            if (!$reservation->qr_code_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code non trouvé'
                ], 404);
            }

            $path = storage_path('app/public/' . $reservation->qr_code_path);

            if (!file_exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier non trouvé'
                ], 404);
            }

            return response()->download($path, 'billet-' . $reservation->reference . '.png');
        } catch (\Exception $e) {
            Log::error('Erreur téléchargement QR Code API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/user/reservations/{reservation}/round-trip-tickets
     * Récupérer les billets aller ET retour pour une réservation aller-retour
     */
    public function getRoundTripTickets(Request $request, Reservation $reservation)
    {
        try {
            // Vérifier que la réservation appartient à l'utilisateur
            if ($reservation->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            // Charger les relations nécessaires
            $reservation->load(['programme', 'programme.compagnie', 'programme.vehicule', 'user']);

            // Déterminer si la réservation actuelle est "aller" ou "retour"
            $isRetourTicket = str_contains($reservation->reference, 'RET');
            
            // Trouver la réservation complémentaire
            if ($isRetourTicket) {
                // Si c'est le retour, chercher l'aller
                // La référence aller = référence retour sans '-RET-'
                $allerReference = str_replace('-RET-', '-', $reservation->reference);
                
                $allerReservation = Reservation::where('reference', $allerReference)
                    ->where('user_id', Auth::id())
                    ->first();
                
                $retourReservation = $reservation;
            } else {
                // Si c'est l'aller, chercher le retour
                // La référence retour = référence aller avec '-RET' inséré avant le dernier segment
                // Exemple: TX-WAL-MDILNISYOX-27 → TX-WAL-MDILNISYOX-RET-27
                $lastDashPos = strrpos($reservation->reference, '-');
                $retourReference = substr_replace($reservation->reference, '-RET', $lastDashPos, 0);
                
                $allerReservation = $reservation;
                
                $retourReservation = Reservation::where('reference', $retourReference)
                    ->where('user_id', Auth::id())
                    ->first();
            }

            // Vérifier si on a trouvé une réservation complémentaire (= c'est un aller-retour)
            $isAllerRetour = ($allerReservation && $retourReservation);
            
            if (!$isAllerRetour) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette réservation n\'est pas un aller-retour ou la réservation complémentaire est introuvable'
                ], 400);
            }

            // Préparer la réponse avec les deux billets
            $response = [
                'success' => true,
                'is_aller_retour' => true,
                'aller' => null,
                'retour' => null
            ];

            // Ajouter le billet aller
            if ($allerReservation) {
                $allerReservation->load(['programme', 'programme.compagnie', 'programme.vehicule']);
                
                $response['aller'] = [
                    'id' => $allerReservation->id,
                    'reference' => $allerReservation->reference,
                    'seat_number' => $allerReservation->seat_number,
                    'date_voyage' => $allerReservation->date_voyage,
                    'heure_depart' => $allerReservation->heure_depart ?? $allerReservation->programme->heure_depart,
                    'heure_arrive' => $allerReservation->heure_arrive ?? $allerReservation->programme->heure_arrive,
                    'montant' => $allerReservation->montant,
                    'statut' => $allerReservation->statut,
                    'statut_aller' => $allerReservation->statut_aller,
                    'passager_nom' => $allerReservation->passager_nom,
                    'passager_prenom' => $allerReservation->passager_prenom,
                    'passager_email' => $allerReservation->passager_email,
                    'passager_telephone' => $allerReservation->passager_telephone,
                    'programme' => [
                        'id' => $allerReservation->programme->id,
                        'point_depart' => $allerReservation->programme->point_depart,
                        'point_arrive' => $allerReservation->programme->point_arrive,
                        'compagnie' => $allerReservation->programme->compagnie ? [
                            'id' => $allerReservation->programme->compagnie->id,
                            'name' => $allerReservation->programme->compagnie->name,
                        ] : null,
                        'vehicule' => $allerReservation->programme->vehicule ? [
                            'id' => $allerReservation->programme->vehicule->id,
                            'marque' => $allerReservation->programme->vehicule->marque,
                            'modele' => $allerReservation->programme->vehicule->modele,
                            'immatriculation' => $allerReservation->programme->vehicule->immatriculation,
                        ] : null,
                    ],
                    'qr_code' => $allerReservation->qr_code,
                    'qr_code_path' => $allerReservation->qr_code_path,
                    'ticket_url' => url('/api/user/reservations/' . $allerReservation->id . '/ticket?type=aller'),
                ];
            }

            // Ajouter le billet retour
            if ($retourReservation) {
                $retourReservation->load(['programme', 'programme.compagnie', 'programme.vehicule']);
                
                // Pour le retour, utiliser date_retour si disponible
                $dateVoyageRetour = $retourReservation->date_retour ?? $retourReservation->date_voyage;
                
                $response['retour'] = [
                    'id' => $retourReservation->id,
                    'reference' => $retourReservation->reference,
                    'seat_number' => $retourReservation->seat_number,
                    'date_voyage' => $dateVoyageRetour,
                    'heure_depart' => $retourReservation->heure_depart ?? $retourReservation->programme->heure_depart,
                    'heure_arrive' => $retourReservation->heure_arrive ?? $retourReservation->programme->heure_arrive,
                    'montant' => $retourReservation->montant,
                    'statut' => $retourReservation->statut,
                    'statut_retour' => $retourReservation->statut_retour,
                    'passager_nom' => $retourReservation->passager_nom,
                    'passager_prenom' => $retourReservation->passager_prenom,
                    'passager_email' => $retourReservation->passager_email,
                    'passager_telephone' => $retourReservation->passager_telephone,
                    'programme' => [
                        'id' => $retourReservation->programme->id,
                        'point_depart' => $retourReservation->programme->point_depart,
                        'point_arrive' => $retourReservation->programme->point_arrive,
                        'compagnie' => $retourReservation->programme->compagnie ? [
                            'id' => $retourReservation->programme->compagnie->id,
                            'name' => $retourReservation->programme->compagnie->name,
                        ] : null,
                        'vehicule' => $retourReservation->programme->vehicule ? [
                            'id' => $retourReservation->programme->vehicule->id,
                            'marque' => $retourReservation->programme->vehicule->marque,
                            'modele' => $retourReservation->programme->vehicule->modele,
                            'immatriculation' => $retourReservation->programme->vehicule->immatriculation,
                        ] : null,
                    ],
                    'qr_code' => $retourReservation->qr_code,
                    'qr_code_path' => $retourReservation->qr_code_path,
                    'ticket_url' => url('/api/user/reservations/' . $retourReservation->id . '/ticket?type=retour'),
                ];
            }

            // Ajouter les informations sur le paiement total si disponible
            if ($allerReservation && $allerReservation->payment_transaction_id) {
                $response['payment_transaction_id'] = $allerReservation->payment_transaction_id;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Erreur récupération billets aller-retour API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des billets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/user/programmes
     * Liste de tous les programmes disponibles
     */
     public function getAllProgrammes(Request $request)
    {
        try {
            $today = now()->format('Y-m-d');
            
            // 1. Préparation de la requête
            $query = Programme::with(['compagnie', 'vehicule', 'itineraire', 'chauffeur'])
                ->where('statut', 'actif')
                ->where(function($q) use ($today) {
                    // Logique de date corrigée (inclut les programmes récurrents en cours)
                    $q->whereDate('date_fin', '>=', $today)
                      ->orWhere(function($sub) use ($today) {
                          $sub->whereNull('date_fin')
                              ->whereDate('date_depart', '>=', $today);
                      });
                })
                ->orderBy('point_depart', 'asc') // Tri par départ pour grouper visuellement
                ->orderBy('heure_depart', 'asc');

            // Filtre optionnel compagnie
            if ($request->filled('compagnie_id')) {
                $query->where('compagnie_id', $request->compagnie_id);
            }

            // 2. Pagination brute (on récupère les lignes)
            $perPage = $request->get('per_page', 50);
            $paginator = $query->paginate($perPage);

            // 3. Transformation de la collection pour le regroupement
            $groupedCollection = $paginator->getCollection()->groupBy(function($item) {
                // La clé de regroupement : Compagnie + Départ + Arrivée
                // Si la compagnie change OU le trajet change, c'est un groupe différent
                return $item->compagnie_id . '|' . strtolower($item->point_depart) . '|' . strtolower($item->point_arrive);
            })->map(function ($group) {
                // On prend le premier élément comme façade
                $first = $group->first();

                // On injecte la liste des horaires/véhicules disponibles pour ce groupe
                $first->horaires_disponibles = $group->map(function($p) {
                    return [
                        'programme_id' => $p->id,
                        'heure_depart' => $p->heure_depart,
                        'heure_arrive' => $p->heure_arrive,
                        'prix' => $p->montant_billet,
                        'duree' => $p->durer_parcours,
                        'vehicule' => $p->vehicule ? [
                            'id' => $p->vehicule->id,
                            'modele' => $p->vehicule->modele,
                            'immatriculation' => $p->vehicule->immatriculation,
                            'nombre_place' => $p->vehicule->nombre_place,
                            'type_range' => $p->vehicule->type_range,
                        ] : null,
                        'chauffeur' => $p->chauffeur ? [
                            'id' => $p->chauffeur->id,
                            'nom' => $p->chauffeur->nom,
                            'prenom' => $p->chauffeur->prenom,
                        ] : null,
                    ];
                })->values();

                return $first;
            })->values();

            // 4. On remet la collection groupée dans le paginateur
            $paginator->setCollection($groupedCollection);

            return response()->json([
                'success' => true,
                'data' => $paginator
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur liste programmes API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des programmes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * GET /api/user/programmes/simple
     * Programmes aller simple
     */
    public function getSimpleProgrammes(Request $request)
    {
        try {
            $today = now()->format('Y-m-d');
            
            $query = Programme::with(['compagnie', 'vehicule'])
                ->where('date_depart', '>=', $today)
                ->where('statut', 'actif')
                ->where('is_aller_retour', false)
                ->orderBy('date_depart', 'asc')
                ->orderBy('heure_depart', 'asc');

            $perPage = $request->get('per_page', 30);
            $programmes = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $programmes
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur programmes simples API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/user/programmes/aller-retour
     * Programmes aller-retour
     */
    public function getAllerRetourProgrammes(Request $request)
    {
        try {
            $today = now()->format('Y-m-d');
            
            $query = Programme::with(['compagnie', 'vehicule', 'programmeRetour'])
                ->where('date_depart', '>=', $today)
                ->where('statut', 'actif')
                ->where('is_aller_retour', true)
                ->orderBy('date_depart', 'asc')
                ->orderBy('heure_depart', 'asc');

            $perPage = $request->get('per_page', 30);
            $programmes = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $programmes
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur programmes aller-retour API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/user/programmes/search
     * Recherche de programmes
     */
    public function searchProgrammes(Request $request)
    {
        try {
            $request->validate([
                'point_depart' => 'required|string',
                'point_arrive' => 'required|string',
                'date_depart' => 'required|date',
                'compagnie_id' => 'nullable|exists:compagnies,id'
            ]);

            $point_depart = $request->point_depart;
            $point_arrive = $request->point_arrive;
            $date_depart = $request->date_depart;
            $formattedDate = date('Y-m-d', strtotime($date_depart));

            $point_depart_normalized = $this->normalizeSearchTerm($point_depart);
            $point_arrive_normalized = $this->normalizeSearchTerm($point_arrive);

            $query = Programme::with(['compagnie', 'vehicule', 'itineraire'])
                ->where('statut', 'actif')
                ->where(function($q) use ($point_depart, $point_depart_normalized) {
                    $q->where('point_depart', 'like', "%{$point_depart}%")
                      ->orWhereRaw('LOWER(point_depart) LIKE ?', ['%' . strtolower($point_depart) . '%'])
                      ->orWhereRaw('LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(point_depart, "é", "e"), "è", "e"), "ê", "e"), "ô", "o"), "à", "a")) LIKE ?', ['%' . $point_depart_normalized . '%']);
                })
                ->where(function($q) use ($point_arrive, $point_arrive_normalized) {
                    $q->where('point_arrive', 'like', "%{$point_arrive}%")
                      ->orWhereRaw('LOWER(point_arrive) LIKE ?', ['%' . strtolower($point_arrive) . '%'])
                      ->orWhereRaw('LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(point_arrive, "é", "e"), "è", "e"), "ê", "e"), "ô", "o"), "à", "a")) LIKE ?', ['%' . $point_arrive_normalized . '%']);
                })
                ->whereDate('date_depart', '<=', $formattedDate)
                ->where(function($q) use ($formattedDate) {
                    $q->whereDate('date_fin', '>=', $formattedDate)
                      ->orWhereNull('date_fin');
                });

            if ($request->filled('compagnie_id')) {
                $query->where('compagnie_id', $request->compagnie_id);
            }

            $programmes = $query->orderBy('heure_depart', 'asc')->get();

            // Grouper par route
            $groupedRoutes = $programmes->groupBy(function($p) {
                return $p->compagnie_id . '|' . $p->itineraire_id;
            })->map(function($group) {
                $first = $group->first();
                
                $allerHoraires = $group->sortBy('heure_depart')->map(function($p) {
                    return [
                        'id' => $p->id,
                        'heure_depart' => $p->heure_depart,
                        'heure_arrive' => $p->heure_arrive,
                    ];
                })->values();
                
                $retourProgrammes = Programme::where('compagnie_id', $first->compagnie_id)
                    ->where('itineraire_id', $first->itineraire_id)
                    ->where('point_depart', $first->point_arrive)
                    ->where('point_arrive', $first->point_depart)
                    ->where('statut', 'actif')
                    ->orderBy('heure_depart', 'asc')
                    ->get();
                
                $retourHoraires = $retourProgrammes->map(function($p) {
                    return [
                        'id' => $p->id,
                        'heure_depart' => $p->heure_depart,
                        'heure_arrive' => $p->heure_arrive,
                    ];
                })->values();
                
                return [
                    'id' => $first->id,
                    'compagnie' => $first->compagnie,
                    'compagnie_id' => $first->compagnie_id,
                    'itineraire_id' => $first->itineraire_id,
                    'point_depart' => $first->point_depart,
                    'point_arrive' => $first->point_arrive,
                    'montant_billet' => $first->montant_billet,
                    'durer_parcours' => $first->durer_parcours,
                    'vehicule' => $first->vehicule,
                    'vehicule_id' => $first->vehicule_id,
                    'statut' => 'actif',
                    'aller_horaires' => $allerHoraires,
                    'retour_horaires' => $retourHoraires,
                    'has_retour' => $retourHoraires->count() > 0,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => $groupedRoutes,
                'search_params' => [
                    'point_depart' => $point_depart,
                    'point_arrive' => $point_arrive,
                    'date_depart' => $date_depart,
                    'date_depart_formatted' => $formattedDate
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur recherche programmes API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/user/programmes/{id}
     * Détails d'un programme
     */
    public function getProgram($id)
    {
        try {
            $programme = Programme::with(['compagnie', 'vehicule', 'itineraire'])->find($id);

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
        } catch (\Exception $e) {
            Log::error('Erreur détails programme API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/user/programmes/{programId}/reserved-seats
     * Places réservées pour un programme
     */
  public function getReservedSeats($programId)
    {
        try {
            $request = request();
            $dateVoyage = $request->get('date');

            if (!$dateVoyage) {
                return response()->json([
                    'success' => false,
                    'message' => 'La date est requise'
                ], 400);
            }

            $formattedDate = date('Y-m-d', strtotime($dateVoyage));
            
            // On charge le véhicule pour avoir le nombre total de places
            $programme = Programme::with('vehicule')->find($programId);

            if (!$programme) {
                return response()->json([
                    'success' => false,
                    'message' => 'Programme non trouvé'
                ], 404);
            }

            // Type de programme
            $type = $programme->type_programmation 
                ?? ($programme->date_fin ? 'recurrent' : 'ponctuel');

            // --- LOGIQUE DE RÉCUPÉRATION DES PLACES ---
            
            // On considère une place comme occupée si elle n'est PAS annulée.
            // C'est plus sûr pour voir toutes les places prises.
            $reservedSeats = Reservation::where('programme_id', $programId)
                ->whereDate('date_voyage', $formattedDate)
                ->where(function($q) {
                    // 1. Les réservations confirmées / payées
                    $q->whereIn('statut', ['confirmee', 'paye', 'validee'])
                    
                    // 2. OU les réservations en attente (récentes)
                    // J'ai augmenté à 30 minutes pour vos tests, vous pourrez réduire après
                      ->orWhere(function($sub) {
                          $sub->where('statut', 'en_attente')
                              ->where('created_at', '>=', now()->subMinutes(30)); 
                      });
                })
                ->pluck('seat_number')
                ->toArray();

            // Log pour le débogage (Regardez storage/logs/laravel.log)
            Log::info("Places réservées trouvées pour Programme #{$programId} le {$formattedDate} : " . implode(', ', $reservedSeats));

            // Nettoyage : conversion en entiers (int) et suppression des doublons
            $reservedSeats = array_values(array_unique(array_map('intval', $reservedSeats)));

            return response()->json([
                'success' => true,
                'data' => $reservedSeats,
                'details' => [
                    'programme_type' => $type,
                    'total_places' => $programme->vehicule ? (int)$programme->vehicule->nombre_place : 0,
                    'rangee_type' => $programme->vehicule ? $programme->vehicule->type_range : '2x2',
                    'vehicule_modele' => $programme->vehicule ? $programme->vehicule->modele : null,
                ],
                'date' => $formattedDate
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur places réservées API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * GET /api/user/itineraires
     * Liste des itinéraires/trajets par compagnie
     * 
     * Query params:
     * - compagnie_id: Filter by company ID
     * - point_depart: Filter by departure point
     * - point_arrive: Filter by arrival point
     * - paginate: true/false (default: true)
     * - per_page: Number of items per page (default: 20)
     */
    public function getItineraires(Request $request)
    {
        try {
            $query = Itineraire::with(['compagnie']);

            // Filtre par compagnie si spécifié
            if ($request->filled('compagnie_id')) {
                $query->where('compagnie_id', $request->compagnie_id);
            }

            // Filtre par point de départ si spécifié
            if ($request->filled('point_depart')) {
                $query->where('point_depart', 'like', '%' . $request->point_depart . '%');
            }

            // Filtre par point d'arrivée si spécifié
            if ($request->filled('point_arrive')) {
                $query->where('point_arrive', 'like', '%' . $request->point_arrive . '%');
            }

            // Récupérer avec ou sans pagination
            if ($request->get('paginate', true)) {
                $perPage = $request->get('per_page', 20);
                $itineraires = $query->orderBy('point_depart', 'asc')->paginate($perPage);
            } else {
                $itineraires = $query->orderBy('point_depart', 'asc')->get();
            }

            return response()->json([
                'success' => true,
                'data' => $itineraires
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur liste itinéraires API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des itinéraires',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/user/itineraires/search
     * Recherche par itinéraire
     */
     public function searchProgrammesByItineraire(Request $request)
    {
        try {
            $request->validate([
                'point_depart' => 'required|string',
                'point_arrive' => 'required|string',
                'date' => 'nullable|date'
            ]);

            // CORRECTION ICI : on utilise 'chauffeur' car c'est le nom de la fonction dans le Modèle
            $query = Programme::with(['compagnie', 'vehicule', 'chauffeur'])
                ->where('point_depart', $request->point_depart)
                ->where('point_arrive', $request->point_arrive)
                ->where('statut', 'actif');

            if ($request->filled('date')) {
                $formattedDate = date('Y-m-d', strtotime($request->date));
                $query->whereDate('date_depart', '<=', $formattedDate)
                      ->where(function($q) use ($formattedDate) {
                          $q->whereDate('date_fin', '>=', $formattedDate)
                            ->orWhereNull('date_fin');
                      });
            }

            $programmes = $query->orderBy('heure_depart')->get();

            // Groupement par compagnie
            $programmesUniques = $programmes->groupBy('compagnie_id')->map(function ($group) {
                $firstProgramme = $group->first();

                $firstProgramme->horaires_disponibles = $group->map(function($p) {
                    return [
                        'programme_id' => $p->id,
                        'heure_depart' => $p->heure_depart,
                        'heure_arrive' => $p->heure_arrive,
                        'prix' => $p->montant_billet,
                        'duree' => $p->durer_parcours,
                        
                        'vehicule' => $p->vehicule ? [
                            'id' => $p->vehicule->id,
                            'modele' => $p->vehicule->modele,
                            'immatriculation' => $p->vehicule->immatriculation,
                            'nombre_place' => $p->vehicule->nombre_place,
                            'type_range' => $p->vehicule->type_range,
                        ] : null,

                        // CORRECTION ICI : on utilise $p->chauffeur au lieu de $p->personnel
                        'chauffeur' => $p->chauffeur ? [
                            'id' => $p->chauffeur->id,
                            'nom' => $p->chauffeur->nom,
                            'prenom' => $p->chauffeur->prenom,
                            'telephone' => $p->chauffeur->telephone,
                        ] : null,
                    ];
                })->values();

                return $firstProgramme;
            })->values();

            return response()->json([
                'success' => true,
                'data' => $programmesUniques
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur recherche itinéraire API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/user/reservations
     * Créer une nouvelle réservation
     */
    public function store(Request $request)
    {
        Log::info('=== DEBUT RESERVATION API (MOBILE) ===');
        Log::info('User ID:', ['id' => Auth::id()]);
        Log::info('Données reçues:', $request->all());

        try {
            $request->validate([
                'programme_id' => 'required|exists:programmes,id',
                'seats' => 'required|array',
                'nombre_places' => 'required|integer|min:1',
                'date_voyage' => 'required|date',
                'date_retour' => 'nullable|date|after_or_equal:date_voyage',
                'heure_depart' => 'nullable|string', // AJOUTÉ
                'heure_depart_retour' => 'nullable|string', // AJOUTÉ
                'passagers' => 'required|array',
                'passagers.*.nom' => 'required|string',
                'passagers.*.prenom' => 'required|string',
                'passagers.*.email' => 'required|email',
                'passagers.*.telephone' => 'required|string',
                'passagers.*.urgence' => 'required|string',
                'passagers.*.seat_number' => 'required|integer',
                'passagers.*.return_seat_number' => 'nullable|integer', // AJOUTÉ
                'seats_retour' => 'nullable|array', // AJOUTÉ
                'seats_retour.*' => 'nullable|integer', // AJOUTÉ
                'payment_method' => 'required|in:wallet,cinetpay'
            ]);

            $programme = Programme::find($request->programme_id);
            $dateVoyage = $request->date_voyage;
            $dateRetour = $request->date_retour;
            $isAllerRetour = $request->boolean('is_aller_retour', false);

            if (!$programme) {
                return response()->json([
                    'success' => false,
                    'message' => 'Programme non trouvé'
                ], 404);
            }

            // Vérifications de validité
            $dateVoyageTimestamp = strtotime($dateVoyage);
            $dateDepartTimestamp = strtotime($programme->date_depart);
            $dateFinTimestamp = strtotime($programme->date_fin);

            if ($dateVoyageTimestamp < $dateDepartTimestamp || $dateVoyageTimestamp > $dateFinTimestamp) {
                return response()->json([
                    'success' => false,
                    'message' => 'La date sélectionnée est hors de la période de validité'
                ], 422);
            }

            if ($programme->statut !== 'actif') {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce voyage n\'est plus disponible'
                ], 422);
            }

            // Vérifier délai minimum 4 heures
            $departureDateTime = \Carbon\Carbon::parse($dateVoyage . ' ' . $programme->heure_depart);
            $now = \Carbon\Carbon::now();
            $hoursDiff = $now->diffInHours($departureDateTime, false);

            if ($hoursDiff < 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'Les réservations doivent être effectuées au moins 4 heures avant le départ'
                ], 422);
            }

            // === TRANSACTION DB AVEC VERROUILLAGE ===
            DB::beginTransaction();
            
            // D'abord, annuler les réservations en_attente depuis plus de 3 minutes (timeout)
            $timeoutMinutes = 3;
            Reservation::where('programme_id', $request->programme_id)
                ->where('date_voyage', $dateVoyage)
                ->where('statut', 'en_attente')
                ->where('created_at', '<', now()->subMinutes($timeoutMinutes))
                ->update(['statut' => 'annulee']);
            
            // Récupérer les réservations avec verrou (FOR UPDATE) pour éviter les doublons simultanés
            // Seules les réservations confirmées OU en_attente récentes (< 3 min) bloquent les places
            $reservedSeats = Reservation::where('programme_id', $request->programme_id)
                ->where('date_voyage', $dateVoyage)
                ->where(function($q) use ($timeoutMinutes) {
                    $q->where('statut', 'confirmee')
                      ->orWhere(function($q2) use ($timeoutMinutes) {
                          $q2->where('statut', 'en_attente')
                             ->where('created_at', '>=', now()->subMinutes($timeoutMinutes));
                      });
                })
                ->lockForUpdate()
                ->pluck('seat_number')
                ->toArray();

            Log::info('Places actuellement réservées (après timeout 3min):', [
                'programme_id' => $request->programme_id,
                'date' => $dateVoyage,
                'seats' => $reservedSeats
            ]);

            // Vérifier chaque place demandée
            foreach ($request->seats as $seat) {
                if (in_array($seat, $reservedSeats)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "La place $seat n'est plus disponible pour le " . date('d/m/Y', strtotime($dateVoyage))
                    ], 422);
                }
            }
            
            // --- VÉRIFICATION CÔTÉ RETOUR SI ALLER-RETOUR ---
            $returnProgram = null;
            $reservedSeatsRetour = [];
            if ($isAllerRetour && $dateRetour) {
                $returnProgramQuery = Programme::where('compagnie_id', $programme->compagnie_id)
                    ->where('point_depart', $programme->point_arrive)
                    ->where('point_arrive', $programme->point_depart)
                    ->where('statut', 'actif');

                if ($request->filled('heure_depart_retour')) {
                    $returnProgramQuery->where('heure_depart', $request->heure_depart_retour);
                }

                $returnProgram = $returnProgramQuery->first();

                if ($returnProgram) {
                    $reservedSeatsRetour = Reservation::where('programme_id', $returnProgram->id)
                        ->where('date_voyage', $dateRetour)
                        ->where(function($q) use ($timeoutMinutes) {
                            $q->where('statut', 'confirmee')
                              ->orWhere(function($q2) use ($timeoutMinutes) {
                                  $q2->where('statut', 'en_attente')
                                     ->where('created_at', '>=', now()->subMinutes($timeoutMinutes));
                              });
                        })
                        ->pluck('seat_number')
                        ->toArray();
                        
                    // Vérifier si des places retour sont spécifiées
                    $seatsRetour = $request->seats_retour ?? [];
                    if (empty($seatsRetour)) {
                        // Chercher dans les passagers
                        foreach ($request->passagers as $p) {
                            if (isset($p['return_seat_number'])) {
                                $seatsRetour[] = $p['return_seat_number'];
                            }
                        }
                    }

                    foreach ($seatsRetour as $seat) {
                        if (in_array($seat, $reservedSeatsRetour)) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => "La place de retour $seat n'est plus disponible pour le " . date('d/m/Y', strtotime($dateRetour))
                            ], 422);
                        }
                    }
                }
            }
            
            // Calcul prix
            $paymentMethod = $request->input('payment_method', 'cinetpay');
            $prixUnitaire = $programme->montant_billet;
            $montantTotal = $prixUnitaire * $request->nombre_places;
            
            // Si Aller-Retour, on double le prix (chaque passager paie l'aller et le retour)
            if ($isAllerRetour) {
                $montantTotal = $montantTotal * 2;
            }
            
            $user = Auth::user();
            $passagers = $request->passagers;
            $createdReservations = [];

            // === LOGIQUE DE PAIEMENT SELON LA MÉTHODE ===
            if ($paymentMethod === 'wallet') {
                // =====================================================
                // === PAIEMENT WALLET : DÉBIT IMMÉDIAT, PAS DE LIEN ===
                // =====================================================
                
                // Verrouiller la ligne user pour éviter double dépense simultanée
                $user = User::lockForUpdate()->find(Auth::id());

                if (($user->solde ?? 0) < $montantTotal) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Solde insuffisant. Votre solde: ' . number_format($user->solde ?? 0, 0, ',', ' ') . ' FCFA'
                    ], 400);
                }
                
                // Débiter le wallet
                $user->solde -= $montantTotal;
                $user->save();
                
                $transactionId = 'TX-WAL-' . strtoupper(Str::random(10));
                
                // Historique transaction wallet
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $montantTotal,
                    'type' => 'debit',
                    'description' => 'Réservation ' . $request->nombre_places . ' place(s) - ' . $programme->point_depart . ' → ' . $programme->point_arrive,
                    'reference' => $transactionId,
                    'status' => 'completed',
                    'payment_method' => 'wallet',
                    'metadata' => json_encode([
                        'programme_id' => $programme->id,
                        'passagers' => $passagers
                    ])
                ]);

                // Créer le paiement (déjà confirmé pour wallet)
                $paiement = Paiement::create([
                    'user_id' => Auth::id(),
                    'amount' => $montantTotal,
                    'transaction_id' => $transactionId,
                    'status' => 'success', // Wallet = paiement immédiat
                    'currency' => 'XOF',
                    'payment_method' => 'wallet',
                    'payment_date' => now(),
                ]);

                // Créer les réservations (CONFIRMÉES pour wallet)
                foreach ($passagers as $index => $passager) {
                    $seatNumber = $passager['seat_number'];
                    $reference = $transactionId . '-' . $seatNumber;

                    // Générer QR code
                    $qrCodeData = $this->generateAndSaveQRCode(
                        $reference,
                        0, // Sera mis à jour après création
                        $dateVoyage,
                        Auth::id()
                    );

                    $reservationData = [
                        'paiement_id' => $paiement->id,
                        'payment_transaction_id' => $transactionId,
                        'user_id' => Auth::id(),
                        'programme_id' => $request->programme_id,
                        'seat_number' => $seatNumber,
                        'passager_nom' => $passager['nom'],
                        'passager_prenom' => $passager['prenom'],
                        'passager_email' => $passager['email'],
                        'passager_telephone' => $passager['telephone'],
                        'passager_urgence' => $passager['urgence'],
                        'is_aller_retour' => $isAllerRetour,
                        'montant' => $prixUnitaire,
                        'statut' => 'confirmee', // CONFIRMÉ pour wallet
                        'statut_aller' => 'confirmee',
                        'reference' => $reference,
                        'date_voyage' => $dateVoyage,
                        'heure_depart' => $programme->heure_depart,
                        'heure_arrive' => $programme->heure_arrive,
                        'qr_code' => $qrCodeData['base64'],
                        'qr_code_path' => $qrCodeData['path'],
                        'qr_code_data' => $qrCodeData['qr_data'],
                    ];

                    if ($isAllerRetour && $dateRetour) {
                        $reservationData['date_retour'] = $dateRetour;
                        $reservationData['statut_retour'] = 'confirmee';
                        if ($programme->programme_retour_id) {
                            $reservationData['programme_retour_id'] = $programme->programme_retour_id;
                        }
                    }

                    $res = Reservation::create($reservationData);
                    $createdReservations[] = $res;

                    // Création automatique du retour si aller-retour
                    if ($isAllerRetour && $dateRetour) {
                        if ($returnProgram) {
                            $usedSeats = Reservation::where('programme_id', $returnProgram->id)
                                ->where('date_voyage', $dateRetour)
                                ->whereIn('statut', ['confirmee', 'en_attente'])
                                ->pluck('seat_number')
                                ->toArray();

                            $capacity = $returnProgram->vehicule ? intval($returnProgram->vehicule->nombre_place) : 30;
                            $returnSeat = null;

                            // 1. Essayer d'utiliser le siège spécifié pour ce passager
                            if (isset($passager['return_seat_number'])) {
                                $returnSeat = $passager['return_seat_number'];
                            } elseif (isset($request->seats_retour[$index])) {
                                $returnSeat = $request->seats_retour[$index];
                            }

                            // 2. Si non spécifié ou déjà pris, chercher une place libre
                            if (!$returnSeat || in_array($returnSeat, $usedSeats)) {
                                $returnSeat = null;
                                for ($s = 1; $s <= $capacity; $s++) {
                                    if (!in_array($s, $usedSeats)) {
                                        $returnSeat = $s;
                                        break;
                                    }
                                }
                            }

                            if ($returnSeat) {
                                $reservationDataRetour = $reservationData;
                                $reservationDataRetour['programme_id'] = $returnProgram->id;
                                $reservationDataRetour['date_voyage'] = $dateRetour;
                                $reservationDataRetour['heure_depart'] = $returnProgram->heure_depart;
                                $reservationDataRetour['heure_arrive'] = $returnProgram->heure_arrive;
                                $reservationDataRetour['seat_number'] = $returnSeat;
                                $reservationDataRetour['reference'] = $transactionId . '-RET-' . $seatNumber;
                                
                                // Générer QR code pour le retour
                                $qrCodeRetour = $this->generateAndSaveQRCode(
                                    $reservationDataRetour['reference'],
                                    0,
                                    $dateRetour,
                                    Auth::id()
                                );
                                $reservationDataRetour['qr_code'] = $qrCodeRetour['base64'];
                                $reservationDataRetour['qr_code_path'] = $qrCodeRetour['path'];
                                $reservationDataRetour['qr_code_data'] = $qrCodeRetour['qr_data'];

                                $resRetour = Reservation::create($reservationDataRetour);
                                $createdReservations[] = $resRetour;
                            }
                        }
                    }
                }

                DB::commit();

                // Déduction des tickets de la compagnie
                $ticketsToDeduct = $request->nombre_places * ($isAllerRetour ? 2 : 1);
                $programme->compagnie->deductTickets($ticketsToDeduct, "Réservation Wallet #{$transactionId}");

                // Mise à jour statut programme
                $this->updateProgramStatus($programme, $dateVoyage);

                // Envoyer emails de confirmation
                foreach ($createdReservations as $reservation) {
                    try {
                        $this->sendReservationEmail(
                            $reservation,
                            $reservation->programme ?? $programme,
                            $reservation->qr_code,
                            $reservation->passager_email,
                            $reservation->passager_prenom . ' ' . $reservation->passager_nom,
                            $reservation->seat_number,
                            $isAllerRetour ? 'ALLER-RETOUR' : 'ALLER SIMPLE'
                        );
                    } catch (\Exception $e) {
                        Log::error('Erreur envoi email wallet: ' . $e->getMessage());
                    }
                }

                // Réponse pour paiement Wallet (PAS DE LIEN DE PAIEMENT)
                return response()->json([
                    'success' => true,
                    'message' => 'Paiement effectué avec succès via Mon Compte.',
                    'wallet_payment' => true,
                    'requires_payment' => false, // PAS DE PAIEMENT SUPPLÉMENTAIRE
                    'data' => [
                        'transaction_id' => $transactionId,
                        'total_amount' => $montantTotal,
                        'new_balance' => $user->solde,
                        'reservations' => collect($createdReservations)->map(function($r) {
                            return [
                                'id' => $r->id,
                                'reference' => $r->reference,
                                'seat_number' => $r->seat_number,
                                'statut' => $r->statut,
                                'passager_nom' => $r->passager_nom,
                                'passager_prenom' => $r->passager_prenom,
                                'has_qr_code' => !empty($r->qr_code)
                            ];
                        }),
                        'programme' => [
                            'point_depart' => $programme->point_depart,
                            'point_arrive' => $programme->point_arrive,
                            'date_voyage' => $dateVoyage,
                            'heure_depart' => $programme->heure_depart
                        ]
                    ]
                ], 201);

            } else {
                // =====================================================
                // === PAIEMENT CINETPAY : LIEN DE PAIEMENT GÉNÉRÉ ===
                // =====================================================
                
                $transactionId = 'TRANS-' . date('YmdHis') . '-' . strtoupper(Str::random(5));
                
                // Création paiement (en attente)
                $paiement = Paiement::create([
                    'user_id' => Auth::id(),
                    'amount' => $montantTotal,
                    'transaction_id' => $transactionId,
                    'status' => 'pending',
                    'currency' => 'XOF',
                    'payment_method' => 'cinetpay',
                    'payment_date' => null,
                ]);

                // Création réservations (EN ATTENTE pour CinetPay)
                foreach ($passagers as $index => $passager) {
                    $seatNumber = $passager['seat_number'];
                    $reference = $transactionId . '-' . $seatNumber;

                    $reservationData = [
                        'paiement_id' => $paiement->id,
                        'payment_transaction_id' => $transactionId,
                        'user_id' => Auth::id(),
                        'programme_id' => $request->programme_id,
                        'seat_number' => $seatNumber,
                        'passager_nom' => $passager['nom'],
                        'passager_prenom' => $passager['prenom'],
                        'passager_email' => $passager['email'],
                        'passager_telephone' => $passager['telephone'],
                        'passager_urgence' => $passager['urgence'],
                        'is_aller_retour' => $isAllerRetour,
                        'montant' => $prixUnitaire,
                        'statut' => 'en_attente', // EN ATTENTE pour CinetPay
                        'statut_aller' => 'en_attente',
                        'reference' => $reference,
                        'date_voyage' => $dateVoyage,
                        'heure_depart' => $programme->heure_depart,
                        'heure_arrive' => $programme->heure_arrive,
                        'qr_code' => null // Sera généré par webhook
                    ];

                    if ($isAllerRetour && $dateRetour) {
                        $reservationData['date_retour'] = $dateRetour;
                        $reservationData['statut_retour'] = 'en_attente';
                        if ($programme->programme_retour_id) {
                            $reservationData['programme_retour_id'] = $programme->programme_retour_id;
                        }
                    }

                    $res = Reservation::create($reservationData);
                    $createdReservations[] = $res;

                    // Création automatique du retour si aller-retour
                    if ($isAllerRetour && $dateRetour) {
                        if ($returnProgram) {
                            $usedSeats = Reservation::where('programme_id', $returnProgram->id)
                                ->where('date_voyage', $dateRetour)
                                ->whereIn('statut', ['confirmee', 'en_attente'])
                                ->pluck('seat_number')
                                ->toArray();

                            $capacity = $returnProgram->vehicule ? intval($returnProgram->vehicule->nombre_place) : 30;
                            $returnSeat = null;

                            // 1. Essayer d'utiliser le siège spécifié pour ce passager
                            if (isset($passager['return_seat_number'])) {
                                $returnSeat = $passager['return_seat_number'];
                            } elseif (isset($request->seats_retour[$index])) {
                                $returnSeat = $request->seats_retour[$index];
                            }

                            // 2. Si non spécifié ou déjà pris, chercher une place libre
                            if (!$returnSeat || in_array($returnSeat, $usedSeats)) {
                                $returnSeat = null;
                                for ($s = 1; $s <= $capacity; $s++) {
                                    if (!in_array($s, $usedSeats)) {
                                        $returnSeat = $s;
                                        break;
                                    }
                                }
                            }

                            if ($returnSeat) {
                                $reservationDataRetour = $reservationData;
                                $reservationDataRetour['programme_id'] = $returnProgram->id;
                                $reservationDataRetour['date_voyage'] = $dateRetour;
                                $reservationDataRetour['heure_depart'] = $returnProgram->heure_depart;
                                $reservationDataRetour['heure_arrive'] = $returnProgram->heure_arrive;
                                $reservationDataRetour['seat_number'] = $returnSeat;
                                $reservationDataRetour['reference'] = $transactionId . '-RET-' . $seatNumber;
                                $reservationDataRetour['qr_code'] = null;

                                $resRetour = Reservation::create($reservationDataRetour);
                                $createdReservations[] = $resRetour;
                            }
                        }
                    }
                }

                DB::commit(); // Commit AVANT de générer le lien CinetPay

                // === GÉNÉRATION DU LIEN CINETPAY ===
                try {
                    $baseUrl = config('app.url');
                    $returnUrl = "car225://payment?cinetpay=true&transactionId={$transactionId}";
                    $cancelUrl = "car225://payment?cinetpay=false&transactionId={$transactionId}";
                    $fallbackReturnUrl = $baseUrl . "/reservation/paiement/redirect-to-app?transactionId=" . urlencode($transactionId);
                    $fallbackCancelUrl = $baseUrl . "/reservation/paiement/redirect-to-app?cancel=1&transactionId=" . urlencode($transactionId);
                    $notifyUrl = $baseUrl . "/api/user/payment/notify";

                    $cinetpayApiKey = config('services.cinetpay.api_key');
                    $cinetpaySiteId = config('services.cinetpay.site_id');

                    $paymentData = [
                        'apikey' => $cinetpayApiKey,
                        'site_id' => $cinetpaySiteId,
                        'transaction_id' => $transactionId,
                        'amount' => $montantTotal,
                        'currency' => 'XOF',
                        'description' => "Réservation {$request->nombre_places} place(s) - {$programme->point_depart} → {$programme->point_arrive}",
                        'notify_url' => $notifyUrl,
                        'return_url' => $fallbackReturnUrl,
                        'cancel_url' => $fallbackCancelUrl,
                        'mode' => config('services.cinetpay.mode', 'PRODUCTION'),
                        'channels' => 'ALL',
                        'customer_name' => Auth::user()->name,
                        'customer_surname' => Auth::user()->prenom ?? '',
                        'customer_email' => Auth::user()->email,
                        'customer_phone_number' => Auth::user()->phone ?? '00000000',
                        'customer_address' => 'Abidjan',
                        'customer_city' => 'Abidjan',
                        'customer_country' => 'CI',
                        'customer_zip_code' => '00225'
                    ];

                    $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                        ->post('https://api-checkout.cinetpay.com/v2/payment', $paymentData);

                    if ($response->failed() || $response->json('code') !== '201') {
                        Log::error('Erreur CinetPay génération lien:', [
                            'status' => $response->status(),
                            'body' => $response->body()
                        ]);
                        
                        // Annuler les réservations créées
                        foreach ($createdReservations as $res) {
                            $res->update(['statut' => 'annulee']);
                        }
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'Erreur lors de la génération du lien de paiement',
                            'error_details' => $response->json()
                        ], 500);
                    }

                    $cinetpayData = $response->json('data');

                    // Réponse pour paiement CinetPay (AVEC LIEN DE PAIEMENT)
                    return response()->json([
                        'success' => true,
                        'message' => 'Réservations créées. Veuillez procéder au paiement.',
                        'requires_payment' => true, // PAIEMENT REQUIS
                        'wallet_payment' => false,
                        'payment_details' => [
                            'payment_url' => $cinetpayData['payment_url'],
                            'payment_token' => $cinetpayData['payment_token'],
                            'transaction_id' => $transactionId,
                            'mode' => config('services.cinetpay.mode', 'PRODUCTION'),
                            'return_url_deep_link' => $returnUrl,
                            'cancel_url_deep_link' => $cancelUrl,
                            'return_url_web_fallback' => $fallbackReturnUrl,
                            'cancel_url_web_fallback' => $fallbackCancelUrl,
                        ],
                        'data' => [
                            'reservations' => collect($createdReservations)->map(function($r) {
                                return [
                                    'id' => $r->id,
                                    'reference' => $r->reference,
                                    'seat_number' => $r->seat_number,
                                    'statut' => $r->statut,
                                    'passager_nom' => $r->passager_nom,
                                    'passager_prenom' => $r->passager_prenom
                                ];
                            }),
                            'total_amount' => $montantTotal,
                            'programme' => [
                                'point_depart' => $programme->point_depart,
                                'point_arrive' => $programme->point_arrive,
                                'date_voyage' => $dateVoyage,
                                'heure_depart' => $programme->heure_depart
                            ]
                        ]
                    ], 201);

                } catch (\Exception $e) {
                    Log::error('Exception génération lien CinetPay: ' . $e->getMessage());
                    
                    // Annuler les réservations
                    foreach ($createdReservations as $res) {
                        $res->update(['statut' => 'annulee']);
                    }
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Erreur technique lors de la génération du lien: ' . $e->getMessage()
                    ], 500);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur réservation API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur technique',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/user/payment/notify
     * Webhook de notification de paiement CinetPay
     */
    public function handlePaymentNotification(Request $request)
    {
        Log::info('=== WEBHOOK CINETPAY REÇU (API RESERVATION) ===');
        Log::info('Données webhook:', $request->all());

        try {
            // Récupérer l'ID de transaction
            $transactionId = $request->input('cpm_trans_id')
                ?? $request->input('transaction_id')
                ?? $request->input('data.cpm_trans_id')
                ?? null;

            if (!$transactionId) {
                Log::warning('Transaction ID manquant dans le webhook');
                return response()->json(['success' => false, 'message' => 'Transaction ID manquant'], 200);
            }

            Log::info('Transaction ID reçu:', ['transaction_id' => $transactionId]);

            // Vérifier le paiement auprès de CinetPay
            $cinetpayApiKey = config('services.cinetpay.api_key');
            $cinetpaySiteId = config('services.cinetpay.site_id');
            $cinetpayUrl = 'https://api-checkout.cinetpay.com/v2/payment/check';

            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->post($cinetpayUrl, [
                'apikey' => $cinetpayApiKey,
                'site_id' => $cinetpaySiteId,
                'transaction_id' => $transactionId,
            ]);

            Log::info("CinetPay check response status: {$response->status()} for transaction {$transactionId}");

            if ($response->failed()) {
                Log::error("Webhook CinetPay {$transactionId}: échec check API.", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['success' => false, 'message' => 'Vérification CinetPay échouée'], 500);
            }

            $verificationData = $response->json();
            Log::info("CinetPay check body for {$transactionId}:", $verificationData);

            // Extraire les données
            $data = $verificationData['data'] ?? $verificationData ?? [];
            $status = $data['status'] ?? $data['payment_status'] ?? null;

            // Trouver le paiement
            $paiement = Paiement::where('transaction_id', $transactionId)->first();

            if (!$paiement) {
                Log::error('Paiement non trouvé:', ['transaction_id' => $transactionId]);
                return response()->json(['success' => false, 'message' => 'Paiement non trouvé'], 404);
            }

            // Si paiement accepté
            if (strtoupper($status) === 'ACCEPTED') {
                // Mettre à jour le paiement
                $paiement->update([
                    'status' => 'success',
                    'payment_date' => now()
                ]);

                // Récupérer toutes les réservations liées
                $reservations = Reservation::where('payment_transaction_id', $transactionId)->get();

                Log::info('Nombre de réservations à confirmer:', ['count' => $reservations->count()]);

                foreach ($reservations as $reservation) {
                    $dateVoyageStr = $reservation->date_voyage instanceof \Carbon\Carbon
                        ? $reservation->date_voyage->format('Y-m-d')
                        : $reservation->date_voyage;

                    try {
                        // Générer le QR Code
                        $qrCodeData = $this->generateAndSaveQRCode(
                            $reservation->reference,
                            $reservation->id,
                            $dateVoyageStr,
                            $reservation->user_id
                        );

                        // Mettre à jour la réservation
                        $reservation->update([
                            'statut' => 'confirmee',
                            'statut_aller' => 'confirmee',
                            'qr_code' => $qrCodeData['base64'],
                            'qr_code_path' => $qrCodeData['path'],
                            'qr_code_data' => $qrCodeData['qr_data']
                        ]);

                        Log::info('Réservation confirmée:', [
                            'id' => $reservation->id,
                            'reference' => $reservation->reference
                        ]);

                        // Envoyer l'email de confirmation
                        $this->sendReservationEmail(
                            $reservation,
                            $reservation->programme,
                            $qrCodeData['base64'],
                            $reservation->passager_email,
                            $reservation->passager_prenom . ' ' . $reservation->passager_nom,
                            $reservation->seat_number,
                            $reservation->is_aller_retour ? 'ALLER-RETOUR' : 'ALLER SIMPLE'
                        );

                        Log::info('Email envoyé pour réservation:', ['id' => $reservation->id]);

                    } catch (\Exception $e) {
                        Log::error('Erreur traitement réservation webhook:', [
                            'reservation_id' => $reservation->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // Mettre à jour le statut du programme
                if ($reservations->count() > 0) {
                    $firstReservation = $reservations->first();
                    $this->updateProgramStatus($firstReservation->programme, $dateVoyageStr);
                    
                    // === DÉCRÉMENTATION DES TICKETS DE LA COMPAGNIE ===
                    // Charger la compagnie si nécessaire
                    $firstReservation->load('programme.compagnie');
                    
                    if ($firstReservation->programme && $firstReservation->programme->compagnie) {
                        // Compter le nombre total de réservations (aller-retour = 2 tickets par réservation originale)
                        $ticketsToDeduct = 0;
                        foreach ($reservations as $res) {
                            // Chaque réservation = 1 ticket (les retours sont des réservations séparées)
                            $ticketsToDeduct += 1;
                        }
                        
                        Log::info("API CinetPay: Déduction de {$ticketsToDeduct} tickets pour compagnie {$firstReservation->programme->compagnie->name}");
                        
                        $firstReservation->programme->compagnie->deductTickets(
                            $ticketsToDeduct, 
                            "Réservation CinetPay #{$transactionId}"
                        );
                    } else {
                        Log::warning("API CinetPay: Impossible de déduire les tickets - compagnie introuvable");
                    }
                }

                Log::info('Paiement confirmé avec succès');
                return response()->json(['success' => true, 'message' => 'Paiement confirmé'], 200);

            } elseif (in_array(strtoupper($status), ['PENDING', 'AWAITING'])) {
                Log::info("Paiement en attente pour {$transactionId}");
                return response()->json(['success' => true, 'message' => 'Paiement en attente'], 200);

            } else {
                // Paiement échoué - annuler les réservations
                $paiement->update(['status' => 'failed']);
                Reservation::where('payment_transaction_id', $transactionId)
                    ->update(['statut' => 'annulee']);

                Log::warning("Paiement échoué pour {$transactionId}, statut: {$status}");
                return response()->json(['success' => false, 'message' => 'Paiement échoué'], 200);
            }

        } catch (\Exception $e) {
            Log::error('Erreur webhook:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/user/reservations/payment-status/{transaction_id}
     * Vérifier le statut de paiement (pour polling côté mobile)
     */
    public function getPaymentStatus(Request $request, $transactionId): JsonResponse
    {
        Log::info("API getPaymentStatus appelée pour: " . $transactionId);
        
        try {
            // Trouver le paiement
            $paiement = Paiement::where('transaction_id', $transactionId)->first();

            if (!$paiement) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Paiement non trouvé'
                ], 404);
            }

            // Récupérer les réservations associées
            $reservations = Reservation::where('payment_transaction_id', $transactionId)
                ->with(['programme'])
                ->get();

            if ($reservations->isEmpty()) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Réservations non trouvées'
                ], 404);
            }

            $firstReservation = $reservations->first();

            // Déterminer le statut
            if ($paiement->status === 'success' && $firstReservation->statut === 'confirmee') {
                return response()->json([
                    'status' => 'confirmee',
                    'message' => 'Paiement confirmé',
                    'data' => [
                        'transaction_id' => $transactionId,
                        'montant' => $paiement->amount,
                        'date_paiement' => $paiement->payment_date,
                        'reservations' => $reservations->map(function($r) {
                            return [
                                'id' => $r->id,
                                'reference' => $r->reference,
                                'seat_number' => $r->seat_number,
                                'statut' => $r->statut,
                                'qr_code' => $r->qr_code ? true : false
                            ];
                        }),
                        'programme' => [
                            'point_depart' => $firstReservation->programme->point_depart,
                            'point_arrive' => $firstReservation->programme->point_arrive,
                            'date_voyage' => $firstReservation->date_voyage,
                            'heure_depart' => $firstReservation->programme->heure_depart
                        ]
                    ]
                ]);
            } elseif ($paiement->status === 'failed' || $firstReservation->statut === 'annulee') {
                return response()->json([
                    'status' => 'paiement_echoue',
                    'message' => 'Paiement échoué',
                    'data' => [
                        'transaction_id' => $transactionId
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'en_attente',
                    'message' => 'Paiement en attente',
                    'data' => [
                        'transaction_id' => $transactionId,
                        'montant' => $paiement->amount
                    ]
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Erreur getPaymentStatus pour {$transactionId}: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur interne'
            ], 500);
        }
    }

    /**
     * POST /api/user/payment/verify/{transaction_id}
     * Vérifier le paiement DIRECTEMENT auprès de CinetPay et confirmer les réservations
     * Utilisé quand le webhook ne fonctionne pas (localhost, ngrok, etc.)
     */
    public function verifyAndConfirmPayment(Request $request, $transactionId): JsonResponse
    {
        Log::info("=== API verifyAndConfirmPayment appelée pour: {$transactionId} ===");
        
        try {
            // Trouver le paiement
            $paiement = Paiement::where('transaction_id', $transactionId)->first();

            if (!$paiement) {
                return response()->json([
                    'success' => false,
                    'status' => 'not_found',
                    'message' => 'Paiement non trouvé'
                ], 404);
            }

            // Si déjà confirmé, retourner succès
            if ($paiement->status === 'success') {
                $reservations = Reservation::where('payment_transaction_id', $transactionId)
                    ->with(['programme'])
                    ->get();
                
                return response()->json([
                    'success' => true,
                    'status' => 'confirmee',
                    'message' => 'Paiement déjà confirmé',
                    'data' => [
                        'transaction_id' => $transactionId,
                        'reservations' => $reservations->map(function($r) {
                            return [
                                'id' => $r->id,
                                'reference' => $r->reference,
                                'seat_number' => $r->seat_number,
                                'statut' => $r->statut,
                                'has_qr_code' => !empty($r->qr_code)
                            ];
                        })
                    ]
                ]);
            }

            // === VÉRIFICATION AUPRÈS DE CINETPAY ===
            $cinetpayApiKey = config('services.cinetpay.api_key');
            $cinetpaySiteId = config('services.cinetpay.site_id');
            $cinetpayUrl = 'https://api-checkout.cinetpay.com/v2/payment/check';

            Log::info("Vérification CinetPay pour {$transactionId}...");

            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->post($cinetpayUrl, [
                'apikey' => $cinetpayApiKey,
                'site_id' => $cinetpaySiteId,
                'transaction_id' => $transactionId,
            ]);

            if ($response->failed()) {
                Log::error("CinetPay verification failed for {$transactionId}", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json([
                    'success' => false,
                    'status' => 'verification_failed',
                    'message' => 'Vérification CinetPay échouée'
                ], 500);
            }

            $verificationData = $response->json();
            Log::info("CinetPay response for {$transactionId}:", $verificationData);

            $data = $verificationData['data'] ?? $verificationData ?? [];
            $status = $data['status'] ?? $data['payment_status'] ?? null;

            // === PAIEMENT ACCEPTÉ ===
            if (strtoupper($status) === 'ACCEPTED') {
                Log::info("Paiement ACCEPTED pour {$transactionId}, confirmation en cours...");

                // Mettre à jour le paiement
                $paiement->update([
                    'status' => 'success',
                    'payment_date' => now()
                ]);

                // Récupérer les réservations
                $reservations = Reservation::where('payment_transaction_id', $transactionId)->get();
                $confirmedReservations = [];

                foreach ($reservations as $reservation) {
                    $dateVoyageStr = $reservation->date_voyage instanceof \Carbon\Carbon
                        ? $reservation->date_voyage->format('Y-m-d')
                        : $reservation->date_voyage;

                    try {
                        // Générer le QR Code
                        $qrCodeData = $this->generateAndSaveQRCode(
                            $reservation->reference,
                            $reservation->id,
                            $dateVoyageStr,
                            $reservation->user_id
                        );

                        // Mettre à jour la réservation
                        $reservation->update([
                            'statut' => 'confirmee',
                            'statut_aller' => 'confirmee',
                            'qr_code' => $qrCodeData['base64'],
                            'qr_code_path' => $qrCodeData['path'],
                            'qr_code_data' => $qrCodeData['qr_data']
                        ]);

                        $confirmedReservations[] = [
                            'id' => $reservation->id,
                            'reference' => $reservation->reference,
                            'seat_number' => $reservation->seat_number,
                            'statut' => 'confirmee',
                            'has_qr_code' => true
                        ];

                        Log::info("Réservation confirmée: {$reservation->id}");

                        // Envoyer l'email de confirmation
                        $this->sendReservationEmail(
                            $reservation,
                            $reservation->programme,
                            $qrCodeData['base64'],
                            $reservation->passager_email,
                            $reservation->passager_prenom . ' ' . $reservation->passager_nom,
                            $reservation->seat_number,
                            $reservation->is_aller_retour ? 'ALLER-RETOUR' : 'ALLER SIMPLE'
                        );

                    } catch (\Exception $e) {
                        Log::error("Erreur confirmation réservation {$reservation->id}: " . $e->getMessage());
                    }
                }

                // Mettre à jour le statut du programme
                if ($reservations->count() > 0) {
                    $firstReservation = $reservations->first();
                    $dateVoyageStr = $firstReservation->date_voyage instanceof \Carbon\Carbon
                        ? $firstReservation->date_voyage->format('Y-m-d')
                        : $firstReservation->date_voyage;
                    
                    $this->updateProgramStatus($firstReservation->programme, $dateVoyageStr);

                    // Décrémentation des tickets
                    $firstReservation->load('programme.compagnie');
                    if ($firstReservation->programme && $firstReservation->programme->compagnie) {
                        $ticketsToDeduct = $reservations->count();
                        Log::info("Déduction de {$ticketsToDeduct} tickets pour compagnie {$firstReservation->programme->compagnie->name}");
                        $firstReservation->programme->compagnie->deductTickets($ticketsToDeduct, "Réservation CinetPay #{$transactionId}");
                    }
                }

                return response()->json([
                    'success' => true,
                    'status' => 'confirmee',
                    'message' => 'Paiement confirmé avec succès',
                    'data' => [
                        'transaction_id' => $transactionId,
                        'montant' => $paiement->amount,
                        'date_paiement' => now()->toIso8601String(),
                        'reservations' => $confirmedReservations
                    ]
                ]);

            } elseif (in_array(strtoupper($status), ['PENDING', 'AWAITING'])) {
                return response()->json([
                    'success' => false,
                    'status' => 'en_attente',
                    'message' => 'Paiement en cours de traitement'
                ]);

            } else {
                // Paiement échoué
                $paiement->update(['status' => 'failed']);
                Reservation::where('payment_transaction_id', $transactionId)
                    ->update(['statut' => 'annulee']);

                return response()->json([
                    'success' => false,
                    'status' => 'paiement_echoue',
                    'message' => 'Paiement échoué ou annulé',
                    'cinetpay_status' => $status
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Erreur verifyAndConfirmPayment: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Erreur interne: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Générer et sauvegarder le QR Code
     */
    private function generateAndSaveQRCode(string $reference, int $reservationId, string $dateVoyage, int $userId = null)
    {
        try {
            $qrData = [
                'user_id' => $userId,
                'reference' => $reference,
                'timestamp' => time(),
                'date_voyage' => $dateVoyage,
                'reservation_id' => $reservationId,
            ];

            $qrData['verification_hash'] = hash(
                'sha256',
                $reference . $reservationId . $dateVoyage . config('app.key')
            );

            $qrContent = json_encode($qrData);

            $qrCode = QrCode::create($qrContent);
            $qrCode->setSize(180);
            $qrCode->setMargin(5);

            $writer = new PngWriter();
            $qrCodeResult = $writer->write($qrCode);

            $qrCodeImage = $qrCodeResult->getString();
            $qrCodeBase64 = base64_encode($qrCodeImage);

            $qrCodePath = 'qrcodes/' . $reference . '.png';
            $fullPath = storage_path('app/public/' . $qrCodePath);

            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            file_put_contents($fullPath, $qrCodeImage);

            return [
                'base64' => $qrCodeBase64,
                'path' => $qrCodePath,
                'qr_data' => $qrData,
                'qr_content' => $qrContent
            ];
        } catch (\Exception $e) {
            Log::error('Erreur génération QR Code: ' . $e->getMessage());
            throw new \Exception('Erreur lors de la génération du QR Code: ' . $e->getMessage());
        }
    }

    /**
     * Envoyer l'email de confirmation
     */
    private function sendReservationEmail(Reservation $reservation, Programme $programme, string $qrCodeBase64, string $recipientEmail = null, string $recipientName = null, int $seatNumber = null, string $ticketType = null, string $qrCodeRetourBase64 = null, Programme $programmeRetour = null): void
    {
        try {
            $user = Auth::user();
            $email = $recipientEmail ?: ($user ? $user->email : null);
            $name = $recipientName ?: ($user ? $user->name : 'Client');

            if (!$email) {
                Log::warning('Email non disponible:', ['reservation_id' => $reservation->id]);
                return;
            }

            Notification::route('mail', $email)->notify(
                new ReservationConfirmeeNotification(
                    $reservation, 
                    $programme, 
                    $qrCodeBase64, 
                    $name, 
                    $seatNumber, 
                    $ticketType, 
                    $qrCodeRetourBase64, 
                    $programmeRetour
                )
            );

            Log::info('Email envoyé avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur envoi email: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour le statut du programme
     */
    private function updateProgramStatus($programme, $dateVoyage = null): void
    {
        try {
            $query = Reservation::where('programme_id', $programme->id)
                ->where('statut', 'confirmee');

            if ($programme->type_programmation == 'recurrent' && $dateVoyage) {
                $query->where('date_voyage', $dateVoyage);
            }

            $totalReservedSeats = $query->count();
            $totalPlaces = $programme->vehicule->nombre_place ?? 50;

            if ($totalPlaces == 0) {
                $totalPlaces = 50;
            }

            $percentage = ($totalReservedSeats / $totalPlaces) * 100;

            if ($percentage >= 100) {
                $status = 'rempli';
            } elseif ($percentage >= 80) {
                $status = 'presque_complet';
            } else {
                $status = 'vide';
            }

            if ($programme->type_programmation == 'ponctuel') {
                $programme->update([
                    'nbre_siege_occupe' => $totalReservedSeats,
                    'staut_place' => $status
                ]);
            } else {
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

            Log::info('Statut programme mis à jour');
        } catch (\Exception $e) {
            Log::error('Erreur mise à jour statut: ' . $e->getMessage());
        }
    }
}
