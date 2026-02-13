<?php

namespace App\Http\Controllers\User\Reservation;

use App\Http\Controllers\Controller;
use App\Services\ReservationService;
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
use Illuminate\Support\Facades\DB;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeMode;

class ReservationController extends Controller
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

  
   public function index(Request $request)
    {
        $user = Auth::user();

        // Récupérer les réservations avec les relations nécessaires
        $query = Reservation::with(['programme', 'programme.compagnie', 'programme.gareDepart', 'programme.gareArrivee'])
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
                  ->orWhere('passager_nom', 'like', '%' . $request->reference . '%') // Recherche par nom aussi
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
        $reservations = $query->paginate(10)->withQueryString();

        // Statistiques
        $stats = [
            'confirmed' => Reservation::where('user_id', $user->id)->where('statut', 'confirmee')->count(),
            'pending' => Reservation::where('user_id', $user->id)->where('statut', 'en_attente')->count(),
            'cancelled' => Reservation::where('user_id', $user->id)->where('statut', 'annulee')->count(),
            'total_amount' => Reservation::where('user_id', $user->id)->where('statut', 'confirmee')->sum('montant'),
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

        // On appelle la même logique que ticket mais avec download au lieu de stream
        return $this->ticket(request(), $reservation, true);
    }

    public function ticket(Request $request, Reservation $reservation, $forceDownload = false)
    {
        // Vérifier que la réservation appartient à l'utilisateur
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $reservation->load(['programme', 'programme.compagnie', 'user']);

        // Récupérer le type de billet (aller par défaut)
        $type = $request->query('type', 'aller');
        $seatNumber = $request->query('seat_number') ?: $reservation->seat_number;

        // Sélection des données selon le type
        if ($type === 'retour' && $reservation->is_aller_retour) {
            // Logique RETOUR
            $programme = $reservation->programmeRetour ?? Programme::find($reservation->programme_retour_id);
            $dateVoyage = $reservation->date_retour;
            
            // Si pas de programme retour trouvé, fallback
            if (!$programme) {
                 $programme = $reservation->programme;
            }

            // ON UTILISE LE MEME QR CODE QUE POUR L'ALLER
            $qrCodeBase64 = $reservation->qr_code;
            $ticketType = 'RETOUR';
            $heureDepart = $programme ? $programme->heure_depart : 'N/A';

        } else {
            // Logique ALLER
            $programme = $reservation->programme;
            $dateVoyage = $reservation->date_voyage;
            
            // Vérifier et générer QR aller si manquant
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

        if ($forceDownload) {
            return $pdf->download($nomFichier . '.pdf');
        }

        return $pdf->stream($nomFichier . '.pdf');
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

        // Envoyer les notifications
        try {
            $user = Auth::user();
            $user->notify(new \App\Notifications\ReservationCancelledNotification($reservation, 0, 0));
            
            if ($user->fcm_token) {
                $fcmService = app(\App\Services\FcmService::class);
                $fcmService->sendNotification(
                    $user->fcm_token, 
                    'Annulation effectuée ❌', 
                    "Votre réservation {$reservation->reference} (" . ($reservation->programme->point_depart ?? 'N/A') . " → " . ($reservation->programme->point_arrive ?? 'N/A') . ") a été annulée.",
                    ['type' => 'cancellation', 'reservation_id' => $reservation->id]
                );
            }
        } catch (\Exception $e) {
            Log::error("Notification error (cancel Web): " . $e->getMessage());
        }

        return redirect()->route('reservation.index')
            ->with('success', 'Réservation annulée avec succès.');
    }

    /**
     * Get refund preview for a reservation (AJAX).
     */
    public function getRefundPreview(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        if ($reservation->statut !== 'confirmee') {
            return response()->json([
                'success' => false,
                'message' => 'Cette réservation est déjà ' . ($reservation->statut === 'annulee' ? 'annulée' : $reservation->statut) . '.'
            ], 400);
        }

        $service = new ReservationService();
        $preview = $service->getRefundPreview($reservation);

        return response()->json($preview);
    }

    /**
     * Cancel a confirmed reservation with wallet refund (AJAX).
     */
    public function cancelReservation(Request $request, Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $service = new ReservationService();
        $result = $service->cancelReservation($reservation, $request->reason);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Modify a reservation (cancel & rebook).
     */
   public function modifyReservation(Reservation $oldReservation, array $data): array
    {
        if ($oldReservation->statut !== 'confirmee') {
            return ['success' => false, 'message' => 'Seules les réservations confirmées peuvent être modifiées.'];
        }

        $refundData = $this->calculateRefundPercentage($oldReservation);
        if (!$refundData['can_cancel']) {
            return ['success' => false, 'message' => 'Modification impossible moins de 15 minutes avant le départ.'];
        }

        $oldTotal = (float) $oldReservation->montant;
        $reservationsToCancel = [$oldReservation];

        // Gestion Aller-Retour pour l'annulation
        if ($oldReservation->is_aller_retour) {
            $pairedReservation = $this->findPairedReservation($oldReservation);
            if ($pairedReservation) {
                $oldTotal += (float) $pairedReservation->montant;
                $reservationsToCancel[] = $pairedReservation;
            }
        }

        // Calcul valeur résiduelle
        if ($refundData['percentage'] !== null) {
            $residualValue = round($oldTotal * $refundData['percentage'] / 100, 0);
        } else {
            $residualValue = max(0, $oldTotal - $refundData['penalty']);
        }

        // Chargement Nouveau Programme
        $newProgramme = \App\Models\Programme::findOrFail($data['programme_id']);
        $newPrice = (float)str_replace(' ', '', $newProgramme->montant_billet ?? $newProgramme->prix ?? 0);
        $newTotal = $newPrice;

        $returnProgramme = null;
        if ($oldReservation->is_aller_retour && isset($data['return_programme_id'])) {
            $returnProgramme = \App\Models\Programme::findOrFail($data['return_programme_id']);
            $newTotal += (float)str_replace(' ', '', $returnProgramme->montant_billet ?? $returnProgramme->prix ?? 0);
        }

        $difference = $newTotal - $residualValue;

        try {
            DB::beginTransaction();
            $user = User::findOrFail($oldReservation->user_id);

            // Vérification Solde
            if ($difference > 0 && (float) $user->solde < $difference) {
                DB::rollBack();
                return ['success' => false, 'message' => "Solde insuffisant."];
            }

            // 1. Annuler les anciennes réservations
            foreach ($reservationsToCancel as $res) {
                $res->update([
                    'statut' => 'annulee',
                    'annulation_reason' => 'Modification',
                    'annulation_date' => now(),
                    'refund_amount' => ($refundData['percentage'] !== null) 
                        ? round($res->montant * $refundData['percentage'] / 100, 0)
                        : max(0, $res->montant - ($refundData['penalty'] / count($reservationsToCancel))),
                    'refund_percentage' => $refundData['percentage'],
                ]);
                $this->freeSeat($res);
            }

            // Génération de la nouvelle référence
            $newReference = $oldReservation->reference;
            if (!str_contains($newReference, 'MOD-')) {
                $newReference = 'MOD-' . $newReference . '-' . strtoupper(Str::random(4));
            }

            // --- CORRECTION HEURE ARRIVEE ---
            // On prend celle du programme en priorité, sinon on calcule
            $heureArriveAller = $newProgramme->heure_arrive;
            if (empty($heureArriveAller)) {
                $heureArriveAller = $this->calculateArrivalTime($data['heure_depart'], $newProgramme->durer_parcours);
            }

            // 2. Créer la nouvelle réservation (ALLER)
            $newReservation = Reservation::create([
                'user_id' => $oldReservation->user_id,
                'paiement_id' => $oldReservation->paiement_id, // <-- CORRECTION: Garder le lien paiement
                'payment_transaction_id' => $newReference, // <-- CORRECTION: ID Transaction rempli
                'programme_id' => $data['programme_id'],
                'compagnie_id' => $newProgramme->compagnie_id,
                'seat_number' => $data['seat_number'],
                'passager_nom' => $oldReservation->passager_nom,
                'passager_prenom' => $oldReservation->passager_prenom,
                'passager_email' => $oldReservation->passager_email,
                'passager_telephone' => $oldReservation->passager_telephone,
                'passager_urgence' => $oldReservation->passager_urgence,
                'date_voyage' => $data['date_voyage'],
                'heure_depart' => $data['heure_depart'],
                'heure_arrive' => $heureArriveAller, // <-- CORRECTION: Heure d'arrivée remplie
                'montant' => $newPrice,
                'reference' => $newReference,
                'statut' => 'confirmee',
                'is_aller_retour' => $oldReservation->is_aller_retour,
                'payment_method' => 'wallet',
                'payment_status' => 'payé'
            ]);

            // Générer QR Code Aller
            $this->generateAndSaveQR($newReservation);

            // 3. Créer la nouvelle réservation (RETOUR) si nécessaire
            if ($oldReservation->is_aller_retour && $returnProgramme) {
                
                $heureArriveRetour = $returnProgramme->heure_arrive;
                if (empty($heureArriveRetour)) {
                    $heureArriveRetour = $this->calculateArrivalTime($data['return_heure_depart'], $returnProgramme->durer_parcours);
                }

                $resRetour = Reservation::create([
                    'user_id' => $oldReservation->user_id,
                    'paiement_id' => $oldReservation->paiement_id, // <-- CORRECTION
                    'payment_transaction_id' => $newReference, // <-- CORRECTION
                    'programme_id' => $data['return_programme_id'],
                    'compagnie_id' => $returnProgramme->compagnie_id,
                    'seat_number' => $data['return_seat_number'],
                    'passager_nom' => $oldReservation->passager_nom,
                    'passager_prenom' => $oldReservation->passager_prenom,
                    'passager_email' => $oldReservation->passager_email,
                    'passager_telephone' => $oldReservation->passager_telephone,
                    'passager_urgence' => $oldReservation->passager_urgence,
                    'date_voyage' => $data['return_date_voyage'],
                    'heure_depart' => $data['return_heure_depart'],
                    'heure_arrive' => $heureArriveRetour, // <-- CORRECTION
                    'montant' => $newTotal - $newPrice,
                    'reference' => $newReference . '-RET',
                    'statut' => 'confirmee',
                    'is_aller_retour' => true,
                    'payment_method' => 'wallet',
                    'payment_status' => 'payé'
                ]);

                // Générer QR Code Retour
                $this->generateAndSaveQR($resRetour);
            }

            // Gestion Wallet (Paiement ou Remboursement de la différence)
            if ($difference != 0) {
                $user->solde -= $difference;
                $user->save();

                WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => abs($difference),
                    'type' => $difference > 0 ? 'debit' : 'credit',
                    'description' => "Modification réservation {$oldReservation->reference}",
                    'status' => 'completed',
                    'reference' => 'MOD-' . strtoupper(Str::random(10)),
                    'payment_method' => 'wallet'
                ]);
            }
            
            // Mise à jour des places
            $this->occupySeat($newReservation);
            if(isset($resRetour)) $this->occupySeat($resRetour);

            DB::commit();
            return ['success' => true, 'message' => 'Modification réussie.', 'new_reservation' => $newReservation];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Modification error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur technique: ' . $e->getMessage()];
        }
    }
    /**
     * Recherche et affichage des lignes disponibles - Groupé par route unique
     * L'utilisateur choisit la route, le type (aller/retour), et l'heure de départ
     */
    public function create(Request $request)
    {
        // Paramètres de recherche
        $point_depart = $request->point_depart;
        $point_arrive = $request->point_arrive;
        $date_depart_recherche = $request->date_depart ?? date('Y-m-d', strtotime('+1 day'));
        $heure_depart = $request->heure_depart ?? null;
        $formattedDate = date('Y-m-d', strtotime($date_depart_recherche));

        // Initialiser la requête
        $query = Programme::with(['compagnie', 'itineraire', 'gareDepart', 'gareArrivee'])
            ->where('statut', 'actif');

        // Appliquer les filtres de recherche si présents
        if ($request->filled('point_depart')) {
            $point_depart_normalized = $this->normalizeSearchTerm($point_depart);
            $query->where(function($q) use ($point_depart, $point_depart_normalized) {
                $q->where('point_depart', 'like', "%{$point_depart}%")
                  ->orWhereRaw('LOWER(point_depart) LIKE ?', ['%' . strtolower($point_depart) . '%'])
                  ->orWhereRaw('LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(point_depart, "é", "e"), "è", "e"), "ê", "e"), "ô", "o"), "à", "a")) LIKE ?', ['%' . $point_depart_normalized . '%']);
            });
        }

        if ($request->filled('point_arrive')) {
            $point_arrive_normalized = $this->normalizeSearchTerm($point_arrive);
            $query->where(function($q) use ($point_arrive, $point_arrive_normalized) {
                $q->where('point_arrive', 'like', "%{$point_arrive}%")
                  ->orWhereRaw('LOWER(point_arrive) LIKE ?', ['%' . strtolower($point_arrive) . '%'])
                  ->orWhereRaw('LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(point_arrive, "é", "e"), "è", "e"), "ê", "e"), "ô", "o"), "à", "a")) LIKE ?', ['%' . $point_arrive_normalized . '%']);
            });
        }

        // Filtre de date (service continu ou date fixe)
        $query->whereDate('date_depart', '<=', $formattedDate)
            ->where(function($q) use ($formattedDate) {
                $q->whereDate('date_fin', '>=', $formattedDate)
                  ->orWhereNull('date_fin');
            });

        // Récupérer tous les programmes correspondants
        $allProgrammes = $query->orderBy('heure_depart', 'asc')->get();

        // Récupérer les occupations de sièges en une seule requête (Optimisation N+1)
        $programIds = $allProgrammes->pluck('id');
        $reservationCounts = Reservation::whereIn('programme_id', $programIds)
            ->where('date_voyage', $formattedDate)
            ->whereIn('statut', ['confirmee', 'en_attente', 'terminee'])
            ->select('programme_id', DB::raw('count(*) as count'))
            ->groupBy('programme_id')
            ->pluck('count', 'programme_id');

        // Grouper par route unique (compagnie + itinéraire)
        $groupedRoutes = $allProgrammes->groupBy(function($p) {
    return $p->compagnie_id . '|' . $p->itineraire_id;
})->map(function($group) use ($reservationCounts, $formattedDate) {
    $first = $group->first();
            
            // Tous les horaires aller
            $allerHoraires = $group->sortBy('heure_depart')->map(function($p) use ($reservationCounts, $formattedDate) {
                $vehicule = $p->getVehiculeForDate($formattedDate);
                return [
                    'id' => $p->id,
                    'heure_depart' => $p->heure_depart,
                    'heure_arrive' => $p->heure_arrive,
                    'reserved_count' => $reservationCounts[$p->id] ?? 0,
                    'total_seats' => $vehicule ? $vehicule->nombre_place : 70,
                    'vehicule_id' => $vehicule ? $vehicule->id : null,
                ];
            })->values();
            
            // Retours
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
                    'vehicule_id' => $p->vehicule_id,
                ];
            })->values();
            
            return (object)[
                'id' => $first->id,
                'compagnie' => $first->compagnie,
                'compagnie_id' => $first->compagnie_id,
                'itineraire_id' => $first->itineraire_id,
                'point_depart' => $first->point_depart,
                'point_arrive' => $first->point_arrive,
                'gare_depart' => $first->gareDepart,
                'gare_arrivee' => $first->gareArrivee,
               'montant_billet' => (int) str_replace(' ', '', $first->montant_billet), 
                'durer_parcours' => $first->durer_parcours,
                'statut' => 'actif',
                'aller_horaires' => $allerHoraires,
                'retour_horaires' => $retourHoraires,
                'has_retour' => $retourHoraires->count() > 0,
            ];
        })->values();

        $searchParams = [
            'point_depart' => $point_depart,
            'point_arrive' => $point_arrive,
            'date_depart' => $date_depart_recherche,
            'date_depart_formatted' => $formattedDate,
            'heure_depart' => $heure_depart,
        ];

        // Mismatch logic
        $timeMismatch = false;
        $availableTimesMessage = null;
        if ($heure_depart && $groupedRoutes->count() > 0) {
            $searchedTimeNormalized = substr($heure_depart, 0, 5);
            $matchFound = false;
            $allAvailableTimes = [];
            foreach ($groupedRoutes as $route) {
                foreach ($route->aller_horaires as $horaire) {
                    $heureNormalized = substr($horaire['heure_depart'], 0, 5);
                    $allAvailableTimes[] = $heureNormalized;
                    if ($heureNormalized === $searchedTimeNormalized) $matchFound = true;
                }
            }
            if (!$matchFound && count($allAvailableTimes) > 0) {
                $timeMismatch = true;
                $uniqueTimes = array_unique($allAvailableTimes);
                sort($uniqueTimes);
                $availableTimesMessage = "L'heure " . $searchedTimeNormalized . " n'est pas disponible pour cette route. Horaires disponibles : " . implode(', ', $uniqueTimes);
            }
        }

        $cinetpay_site_id = config('services.cinetpay.site_id');
        $cinetpay_api_key = config('services.cinetpay.api_key');
        $cinetpay_mode = app()->environment('local') ? 'TEST' : 'PRODUCTION';
        
        return view('user.reservation.create', compact('groupedRoutes', 'searchParams', 'cinetpay_site_id', 'cinetpay_api_key', 'cinetpay_mode', 'timeMismatch', 'availableTimesMessage'));
    }


    // Afficher les détails du véhicule (pour le bouton "Détails véhicule")
  public function showVehicle($id)
{
    $request = request();
    $dateVoyage = $request->get('date') ?? date('Y-m-d');
    $programId = $request->get('program_id');
    $heureDepart = $request->get('heure_depart'); // RÉCUPÉRATION HEURE

    $programme = Programme::find($programId);
    
    if (!$programme) {
        return response()->json(['error' => 'Programme non trouvé'], 404);
    }

    // TENTATIVE 1 : Véhicule via getVehiculeForDate (si relation Voyage existe)
    $vehicule = $programme->getVehiculeForDate($dateVoyage);

    if (!$vehicule && $programme->compagnie_id) {
        $vehicule = \App\Models\Vehicule::where('compagnie_id', $programme->compagnie_id)
            ->where('is_active', true)
            ->orderBy('id', 'asc')
            ->first();
    }

    // TENTATIVE 3 : Si toujours rien, créer un véhicule virtuel par défaut
    if (!$vehicule) {
        $vehicule = (object)[
            'id' => 0,
            'immatriculation' => 'N/A',
            'numero_serie' => 'N/A',
            'type_range' => '2x3',
            'nombre_place' => 70,
            'marque' => 'Bus',
            'modele' => 'Standard'
        ];
    }

    // Générer le HTML pour la visualisation
    $typeRangeConfig = [
        '2x2' => ['placesGauche' => 2, 'placesDroite' => 2],
        '2x3' => ['placesGauche' => 2, 'placesDroite' => 3],
        '2x4' => ['placesGauche' => 2, 'placesDroite' => 4],
        'Gamme Prestige' => ['placesGauche' => 2, 'placesDroite' => 2],
        'Gamme Standard' => ['placesGauche' => 2, 'placesDroite' => 3]
    ];

    $config = $typeRangeConfig[$vehicule->type_range] ?? $typeRangeConfig['2x3'];

    // ✅ CORRECTION MAJEURE : Filtrer par heure_depart
    $formattedDate = date('Y-m-d', strtotime($dateVoyage));
    
    $query = Reservation::where('programme_id', $programId)
        ->whereIn('statut', ['confirmee', 'en_attente', 'terminee'])
        ->where('date_voyage', $formattedDate);

    if ($heureDepart) {
        $query->where('heure_depart', $heureDepart);
    }

    $reservedSeats = $query->pluck('seat_number')->toArray();

    \Log::info('Détails véhicule - Places réservées:', [
        'programme_id' => $programId,
        'date' => $formattedDate,
        'heure_depart' => $heureDepart,
        'reserved_seats' => $reservedSeats
    ]);

    $visualizationHTML = $this->generatePlacesVisualization($vehicule, $config, $reservedSeats);

    return response()->json([
        'success' => true,
        'html' => $visualizationHTML,
        'vehicule' => $vehicule,
        'date' => $dateVoyage,
        'heure_depart' => $heureDepart,
        'reserved_seats' => $reservedSeats
    ]);
}


    // Récupérer les détails d'un programme (pour le modal)
    public function getProgram($id)
    {
        try {
            $programme = Programme::with(['compagnie'])->find($id);

            if (!$programme) {
                return response()->json([
                    'success' => false,
                    'error' => 'Programme non trouvé'
                ], 404);
            }

            // Ajouter le véhicule pour la date si fournie
            $date = request()->get('date');
            if ($date) {
                $programme->vehicule_details = $programme->getVehiculeForDate($date);
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

    // Récupérer le véhicule par défaut de la compagnie associée au programme
    public function getDefaultVehicle($id)
    {
        try {
            $programme = Programme::with(['compagnie'])->find($id);

            if (!$programme) {
                return response()->json([
                    'success' => false,
                    'error' => 'Programme non trouvé'
                ], 404);
            }

            // 1. Chercher si un véhicule est assigné pour cette date via un Voyage
            $date = request()->get('date');
            $vehicule = null;
            if ($date) {
                $vehicule = $programme->getVehiculeForDate($date);
            }

            // 2. Fallback: Récupérer le premier véhicule actif de la compagnie
            if (!$vehicule) {
                $vehicule = Vehicule::where('compagnie_id', $programme->compagnie_id)
                    ->where('is_active', true)
                    ->first();
            }

            if (!$vehicule) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun véhicule disponible'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'vehicule_id' => $vehicule->id,
                'vehicule' => $vehicule
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
        $heureDepart = $request->get('heure_depart'); // ✅ AJOUT

        if (!$dateVoyage) {
            \Log::warning('Date non fournie pour getReservedSeats');
            return response()->json([
                'success' => true,
                'reservedSeats' => []
            ]);
        }

        $formattedDate = date('Y-m-d', strtotime($dateVoyage));
        $programme = Programme::find($programId);

        if (!$programme) {
            return response()->json([
                'success' => false,
                'error' => 'Programme non trouvé'
            ], 404);
        }

        \Log::info('Récupération places réservées pour:', [
            'programme_id' => $programId,
            'date_voyage' => $formattedDate,
            'heure_depart' => $heureDepart
        ]);

        // ✅ FILTRAGE PAR DATE ET HEURE
        $query = Reservation::where('programme_id', $programId)
            ->whereIn('statut', ['confirmee', 'en_attente', 'terminee'])
            ->where('date_voyage', $formattedDate);
        
        if ($heureDepart) {
            $query->where('heure_depart', $heureDepart);
        }

        $reservedSeats = $query->pluck('seat_number')->toArray();

        \Log::info('Places réservées trouvées:', $reservedSeats);

        return response()->json([
            'success' => true,
            'reservedSeats' => $reservedSeats,
            'heure_depart' => $heureDepart
        ]);
    } catch (\Exception $e) {
        \Log::error('Erreur getReservedSeats:', [
            'error' => $e->getMessage(),
            'programId' => $programId
        ]);

        return response()->json([
            'success' => false,
            'error' => 'Erreur serveur: ' . $e->getMessage()
        ], 500);
    }
}

    // Stocker la réservation avec PDF et QR Code - UNE RESERVATION PAR PLACE
    public function store(Request $request)
    {
        Log::info('=== DEBUT RESERVATION (NOUVELLE LOGIQUE: 1 LIGNE PAR PLACE) ===');
        Log::info('User ID:', ['id' => Auth::id()]);
        Log::info('Données reçues:', $request->all());

        $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'seats' => 'required|array',
            'nombre_places' => 'required|integer|min:1',
            'date_voyage' => 'required|date',
            'date_retour' => 'nullable|date|after_or_equal:date_voyage', // Validation ajoutée
            'passagers' => 'required|array',
            'passagers.*.nom' => 'required|string',
            'passagers.*.prenom' => 'required|string',
            'passagers.*.email' => 'required|email',
            'passagers.*.telephone' => 'required|string',
            'passagers.*.urgence' => 'required|string',
            'passagers.*.seat_number' => 'required|integer',
            'heure_depart' => 'nullable|string', // AJOUTÉ
            'heure_depart_retour' => 'nullable|string', // AJOUTÉ
            'seats_retour' => 'nullable|array', // AJOUTÉ
            'seats_retour.*' => 'nullable|integer',
            'gare_depart_id' => 'nullable|integer|exists:gares,id',
            'gare_arrivee_id' => 'nullable|integer|exists:gares,id',
        ]);

        Log::info('Données reçues (après validation):', [
            'programme_id' => $request->programme_id,
            'date_voyage' => $request->date_voyage,
            'is_aller_retour' => $request->boolean('is_aller_retour'),
            'date_retour' => $request->date_retour,
        ]);

        Log::info('Validation passée');

        $programme = Programme::find($request->programme_id);
$dateAller = $request->date_voyage;
  $dateRetour = $request->date_retour;$gareDepartId = $request->gare_depart_id ?? $programme->gare_depart_id;
  $gareArriveeId = $request->gare_arrivee_id ?? $programme->gare_arrivee_id;
    $isAllerRetour = $request->boolean('is_aller_retour', false);
        if (!$programme) {
            Log::error('Programme non trouvé:', ['id' => $request->programme_id]);
            return response()->json([
                'success' => false,
                'message' => 'Programme non trouvé'
            ], 404);
        }

        // Vérifier si la date correspond au programme (Support date range)
        $dateVoyage = $request->date_voyage;
        $dateVoyageTimestamp = strtotime($dateVoyage);
        
        $isAllerRetour = $request->boolean('is_aller_retour', false);
        $dateRetour = $request->date_retour;

        $dateDepartTimestamp = strtotime($programme->date_depart);
        $dateFinTimestamp = strtotime($programme->date_fin);

        if ($dateVoyageTimestamp < $dateDepartTimestamp || $dateVoyageTimestamp > $dateFinTimestamp) {
            return response()->json([
                'success' => false,
                'message' => 'La date sélectionnée est hors de la période de validité de ce voyage (' . 
                             date('d/m/Y', $dateDepartTimestamp) . ' au ' . date('d/m/Y', $dateFinTimestamp) . ')'
            ], 422);
        }

        // Vérifier que le programme est actif
        if ($programme->statut !== 'actif') {
            return response()->json([
                'success' => false,
                'message' => 'Ce voyage n\'est plus disponible.'
            ], 422);
        }

        // Vérifier si les places sont encore disponibles
        $reservedSeats = Reservation::where('programme_id', $request->programme_id)
            ->where('statut', 'confirmee')
            ->where('date_voyage', $dateVoyage)
            ->pluck('seat_number')
            ->toArray();

        Log::info('Places déjà réservées pour le ' . $dateVoyage . ':', $reservedSeats);
        Log::info('Places demandées:', $request->seats);

        foreach ($request->seats as $seat) {
            if (in_array($seat, $reservedSeats)) {
                return response()->json([
                    'success' => false,
                    'message' => "La place $seat n'est plus disponible pour le " . date('d/m/Y', strtotime($dateVoyage))
                ], 422);
            }
        }

        try {
            $paymentMethod = $request->input('payment_method', 'cinetpay');

            // Calculer le prix (FlixBus model: prix simple par place)
            $prixUnitaire = $programme->montant_billet;
            $montantTotal = $prixUnitaire * $request->nombre_places;
            
            // Si Aller-Retour, on double le prix
            if ($request->boolean('is_aller_retour', false)) {
                $montantTotal = $montantTotal * 2;
            }

            // VÉRIFICATION DU SOLDE DE LA COMPAGNIE
            if ($programme->compagnie->tickets < $montantTotal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Désolé, cette compagnie n\'a plus assez de crédit pour accepter de nouvelles réservations.'
                ], 422);
            }
        
        // 1. DÉMARRER LA TRANSACTION (Sécurité absolue)
        DB::beginTransaction();

        $user = Auth::user(); // On recharge l'user pour avoir le solde à jour
        
        // Variables d'état
        $paiementStatus = 'pending';
        $reservationStatus = 'en_attente';
        $isWallet = false;
        $transactionId = '';
        
        // --- LOGIQUE DE PAIEMENT ---
        if ($paymentMethod === 'wallet') {
            // Verrouiller la ligne user pour éviter double dépense simultanée
            $user = \App\Models\User::lockForUpdate()->find(Auth::id());

            if (($user->solde ?? 0) < $montantTotal) {
                 DB::rollBack(); // Annuler tout
                 return response()->json([
                    'success' => false,
                    'message' => 'Solde insuffisant pour effectuer cette réservation.',
                ], 400);
            }
            
            // Débit du wallet
            $user->solde -= $montantTotal;
            $user->save();
            
            $transactionId = 'TX-WAL-' . strtoupper(Str::random(10));
            
            // Historique transaction
            \App\Models\WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $montantTotal,
                'type' => 'debit',
                'description' => 'Réservation ' . $request->nombre_places . ' place(s)',
                'reference' => $transactionId,
                'status' => 'completed',
                'payment_method' => 'wallet',
                'metadata' => json_encode([
                    'programme_id' => $programme->id,
                    'passagers' => $request->passagers // Sauvegarde de tous les passagers en JSON
                ])
            ]);

            $paiementStatus = 'success';
            $reservationStatus = 'confirmee';
            $isWallet = true;

            // Déduction du solde de la compagnie (Wallet = immédiat)
            $programme->compagnie->deductTickets($montantTotal, "Réservation Wallet #{$transactionId}");

        } else {
            // CinetPay
            $transactionId = 'TRANS-' . date('YmdHis') . '-' . strtoupper(Str::random(5));
        }

        // 2. CRÉATION DU PAIEMENT (Correction : ajout de payment_method)
        $paiement = \App\Models\Paiement::create([
            'user_id' => Auth::id(),
            'amount' => $montantTotal,
            'transaction_id' => $transactionId,
            'status' => $paiementStatus,
            'currency' => 'XOF',
            'payment_method' => $paymentMethod, // <--- AJOUTÉ POUR CORRESPONDRE AU MODÈLE
            'payment_date' => now(),
        ]);

        $createdReservations = [];
        $passagers = $request->passagers;

        // === LOGIQUE DIFFÉRENTE POUR WALLET: UNE SEULE RÉSERVATION ===
        if ($isWallet) {
            $firstPassenger = $passagers[0];
            $reference = $transactionId . '-GROUP';

            // Préparer les données des passagers avec leurs QR codes individuels
            $passagersData = [];
            foreach ($passagers as $passager) {
                $seatNumber = $passager['seat_number'];
                $passengerRef = $transactionId . '-' . $seatNumber;
                
                // Données QR pour ce passager
                $qrData = [
                    'ref' => $passengerRef,
                    'nom' => $passager['nom'],
                    'prenom' => $passager['prenom'],
                    'date' => $dateVoyage,
                    'depart' => $programme->point_depart,
                    'arrive' => $programme->point_arrive,
                    'seat' => $seatNumber
                ];
                
                // Générer le QR code pour ce passager
                try {
                    $qrDataResult = $this->generateAndSaveQRCode(
                        $passengerRef,
                        0, // Temporary, will be updated after reservation creation
                        $dateVoyage instanceof \Carbon\Carbon ? $dateVoyage->format('Y-m-d') : $dateVoyage,
                        Auth::id()
                    );
                    
                    $passagersData[] = [
                        'nom' => $passager['nom'],
                        'prenom' => $passager['prenom'],
                        'email' => $passager['email'],
                        'telephone' => $passager['telephone'],
                        'urgence' => $passager['urgence'],
                        'seat' => $seatNumber,
                        'reference' => $passengerRef,
                        'qr_code_path' => $qrDataResult['path'] ?? null,
                        'qr_code_base64' => $qrDataResult['base64'] ?? null,
                        'qr_data' => $qrData
                    ];
                } catch (\Exception $e) {
                    Log::error('Erreur génération QR pour passager: ' . $e->getMessage());
                    // Continuer quand même
                    $passagersData[] = [
                        'nom' => $passager['nom'],
                        'prenom' => $passager['prenom'],
                        'email' => $passager['email'],
                        'telephone' => $passager['telephone'],
                        'urgence' => $passager['urgence'],
                        'seat' => $seatNumber,
                        'reference' => $passengerRef,
                        'qr_data' => $qrData
                    ];
                }
            }

          // 3. Créer réservations (UNE PAR PLACE)
            $createdReservations = [];
            foreach ($request->passagers as $index => $passager) {
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
                    'statut' => $reservationStatus,
                    'reference' => $reference,
                    'date_voyage' => $dateVoyage,
                    'qr_code' => Str::random(32),
                    'heure_depart' => $programme->heure_depart,
                    'heure_arrive' => $programme->heure_arrive,
                    'gare_depart_id' => $gareDepartId,
                    'gare_arrivee_id' => $gareArriveeId,
                ];

                if ($isAllerRetour) {
                    $reservationData['date_retour'] = $dateRetour;
                    $reservationData['statut_aller'] = $reservationStatus;
                    $reservationData['statut_retour'] = $reservationStatus;
                    if ($programme->programme_retour_id) {
                        $reservationData['programme_retour_id'] = $programme->programme_retour_id;
                    } elseif ($programme->programmeRetour) {
                         $reservationData['programme_retour_id'] = $programme->programmeRetour->id;
                    }
                } else {
                    $reservationData['statut_aller'] = $reservationStatus;
                }
                
                // Assigner le QR code (Code existant conservé)
                foreach ($passagersData as $pData) {
                    if ($pData['seat'] == $seatNumber) {
                         if (isset($pData['qr_code_base64'])) {
                             $reservationData['qr_code'] = $pData['qr_code_base64'];
                             $reservationData['qr_code_path'] = $pData['qr_code_path'];
                             $reservationData['qr_code_data'] = $pData['qr_data'];
                         }
                         break;
                    }
                }

                $res = Reservation::create($reservationData);
                $createdReservations[] = $res;

                // --- CREATION AUTOMATIQUE DU RETOUR ---
                if ($isAllerRetour && isset($dateRetour)) {
                    $seatsRetour = $request->seats_retour ?? [];
                    
                    $returnProgram = Programme::where('compagnie_id', $programme->compagnie_id)
                        ->where('point_depart', $programme->point_arrive)
                        ->where('point_arrive', $programme->point_depart)
                        ->where('statut', 'actif')
                        ->first();

                    if ($returnProgram) {
                        // 1. Cas sélection manuelle (Si l'utilisateur a choisi sa place)
                        if (!empty($seatsRetour) && isset($seatsRetour[$index])) {
                            $seatNumberAller = $passager['seat_number'];
                            $returnSeat = $seatsRetour[$index];
                            
                            $reservationDataRetour = [
                                'paiement_id' => $paiement->id,
                                'payment_transaction_id' => $transactionId,
                                'user_id' => Auth::id(),
                                'programme_id' => $returnProgram->id,
                                'seat_number' => $returnSeat,
                                'passager_nom' => $passager['nom'],
                                'passager_prenom' => $passager['prenom'],
                                'passager_email' => $passager['email'],
                                'passager_telephone' => $passager['telephone'],
                                'passager_urgence' => $passager['urgence'],
                                'is_aller_retour' => $isAllerRetour,
                                'montant' => $prixUnitaire,
                                'statut' => $reservationStatus,
                                'date_voyage' => $dateRetour,
                                'date_retour' => $dateRetour,
                                'statut_aller' => $reservationStatus,
                                'statut_retour' => $reservationStatus,
                                'heure_depart' => $returnProgram->heure_depart,
                                'heure_arrive' => $returnProgram->heure_arrive,
                                'reference' => $transactionId . '-RET-' . $seatNumberAller,
                                'qr_code' => Str::random(32),
                                'qr_code_path' => 'qrcodes/' . $transactionId . '-RET-' . $seatNumberAller . '.png',
                                'gare_depart_id' => $gareArriveeId, // INVERSÉ
                                'gare_arrivee_id' => $gareDepartId, // INVERSÉ
                            ];
                            
                            $resRetour = Reservation::create($reservationDataRetour);
                            $createdReservations[] = $resRetour;
                        } 
                        // 2. Cas Fallback (Assignation Auto) - LE ELSE EST ICI, CORRECTEMENT PLACÉ
                        else {
                            $seatNumberAller = $passager['seat_number'];
                            
                            $usedSeats = Reservation::where('programme_id', $returnProgram->id)
                                ->where('date_voyage', $dateRetour)
                                ->where('statut', 'confirmee')
                                ->pluck('seat_number')
                                ->toArray();
                            
                            $vehiculeRetour = $returnProgram->getVehiculeForDate($dateRetour);
                            $capacity = $vehiculeRetour ? intval($vehiculeRetour->nombre_place) : 70;
                            $returnSeat = null;
                            for ($s = 1; $s <= $capacity; $s++) {
                                if (!in_array($s, $usedSeats)) {
                                    $returnSeat = $s;
                                    break;
                                }
                            }

                            if ($returnSeat) {
                                $reservationDataRetour = [
                                    'paiement_id' => $paiement->id,
                                    'payment_transaction_id' => $transactionId,
                                    'user_id' => Auth::id(),
                                    'programme_id' => $returnProgram->id,
                                    'seat_number' => $returnSeat,
                                    'passager_nom' => $passager['nom'],
                                    'passager_prenom' => $passager['prenom'],
                                    'passager_email' => $passager['email'],
                                    'passager_telephone' => $passager['telephone'],
                                    'passager_urgence' => $passager['urgence'],
                                    'is_aller_retour' => $isAllerRetour,
                                    'montant' => $prixUnitaire,
                                    'statut' => $reservationStatus,
                                    'date_voyage' => $dateRetour,
                                    'date_retour' => $dateRetour,
                                    'statut_aller' => $reservationStatus,
                                    'statut_retour' => $reservationStatus,
                                    'heure_depart' => $returnProgram->heure_depart,
                                    'heure_arrive' => $returnProgram->heure_arrive,
                                    'reference' => $transactionId . '-RET-' . $seatNumberAller,
                                    'qr_code' => Str::random(32),
                                    'qr_code_path' => 'qrcodes/' . $transactionId . '-RET-' . $seatNumberAller . '.png',
                                    'gare_depart_id' => $request->gare_arrivee_id,
                                    'gare_arrivee_id' => $request->gare_depart_id,
                                ];
                                
                                $resRetour = Reservation::create($reservationDataRetour);
                                $createdReservations[] = $resRetour;
                            }
                        }
                    }
                }
            }  
         
          } else { 
            // === LOGIQUE CINETPAY: PLUSIEURS RÉSERVATIONS (UNE PAR PASSAGER) ===
            foreach ($passagers as $passager) {
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
                    // 'is_aller_retour' => $isAllerRetour, // Probablement ignoré par la DB si n'existe pas
                    'montant' => $prixUnitaire,
                    'statut' => $reservationStatus,
                    'reference' => $reference,
                    'date_voyage' => $dateVoyage,
                    'heure_arrive' => $programme->heure_arrive,
                    'gare_depart_id' => $request->gare_depart_id,
                    'gare_arrivee_id' => $request->gare_arrivee_id,
                ];

                $reservation = Reservation::create($reservationData);
                $createdReservations[] = $reservation;

                // --- CREATION AUTOMATIQUE DU RETOUR (CinetPay) ---
                if ($isAllerRetour && isset($dateRetour)) {
                    $seatsRetour = $request->seats_retour ?? [];
                    
                    // Trouver un programme retour
                    $returnProgramQuery = Programme::where('compagnie_id', $programme->compagnie_id)
                        ->where('point_depart', $programme->point_arrive)
                        ->where('point_arrive', $programme->point_depart)
                        ->where('statut', 'actif');

                    if ($request->filled('heure_depart_retour')) {
                        $returnProgramQuery->where('heure_depart', $request->heure_depart_retour);
                    }

                    $returnProgram = $returnProgramQuery->first();

                    if ($returnProgram) {
                        // Trouver l'index du passager actuel
                        $currentPassagerIndex = array_search($passager, $passagers);
                        
                        // Si l'utilisateur a sélectionné des places retour ET qu'il y a une place pour ce passager
                        if (!empty($seatsRetour) && isset($seatsRetour[$currentPassagerIndex])) {
                            // Utiliser la place sélectionnée
                            $returnSeat = $seatsRetour[$currentPassagerIndex];
                            
                            $reservationDataRetour = $reservationData;
                            $reservationDataRetour['programme_id'] = $returnProgram->id;
                            $reservationDataRetour['date_voyage'] = $dateRetour;
                            $reservationDataRetour['heure_depart'] = $returnProgram->heure_depart;
                            $reservationDataRetour['heure_arrive'] = $returnProgram->heure_arrive;
                            $reservationDataRetour['seat_number'] = $returnSeat;
                            $reservationDataRetour['reference'] = $transactionId . '-RET-' . $seatNumber;
                            $reservationDataRetour['qr_code'] = Str::random(32);
                            $reservationDataRetour['gare_depart_id'] = $request->gare_arrivee_id;
                            $reservationDataRetour['gare_arrivee_id'] = $request->gare_depart_id;
                            
                            $resRetour = Reservation::create($reservationDataRetour);
                            $createdReservations[] = $resRetour;
                        } else {
                            // Fallback : Auto-assign seat
                            $usedSeats = Reservation::where('programme_id', $returnProgram->id)
                                ->where('date_voyage', $dateRetour)
                                ->where('statut', 'confirmee')
                                ->pluck('seat_number')
                                ->toArray();
                            
                            $vehiculeRetour = $returnProgram->getVehiculeForDate($dateRetour);
                            $capacity = $vehiculeRetour ? intval($vehiculeRetour->nombre_place) : 70;
                            $returnSeat = null;
                            for ($s = 1; $s <= $capacity; $s++) {
                                if (!in_array($s, $usedSeats)) {
                                    $returnSeat = $s;
                                    break;
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
                                $reservationDataRetour['qr_code'] = Str::random(32);
                                $reservationDataRetour['gare_depart_id'] = $request->gare_arrivee_id;
                                $reservationDataRetour['gare_arrivee_id'] = $request->gare_depart_id;
                                
                                $resRetour = Reservation::create($reservationDataRetour);
                                $createdReservations[] = $resRetour;
                            }
                        }
                    }
                }
            }
        }

        // Si tout est bon, on valide la transaction DB
        DB::commit();

        // Mise à jour places (hors transaction pour éviter deadlocks complexes, ou dedans selon préférence)
        // Mise à jour places et Envoi Emails (hors transaction)
        if ($isWallet) {
             $this->updateProgramStatus($programme, $dateVoyage);

             // Envoyer un email à chaque passager (chaque réservation = 1 passager)
             if (!empty($createdReservations)) {
                 foreach ($createdReservations as $reservation) {
                     try {
                         // Récupérer le QR code de cette réservation
                         $qrCodeBase64 = $reservation->qr_code;
                         
                         // Envoyer l'email au passager de cette réservation
                         $this->sendReservationEmail(
                             $reservation,
                             $programme,
                             $qrCodeBase64,
                             $reservation->passager_email,
                             $reservation->passager_prenom . ' ' . $reservation->passager_nom,
                             $reservation->seat_number,
                             ($reservation->is_aller_retour) ? 'ALLER-RETOUR' : 'ALLER SIMPLE'
                         );
                         
                         Log::info('Email envoyé avec succès pour réservation wallet:', [
                             'reservation_id' => $reservation->id,
                             'email' => $reservation->passager_email,
                             'seat' => $reservation->seat_number
                         ]);
                     } catch (\Exception $e) {
                         Log::error('Erreur envoi email wallet pour passager: ' . $e->getMessage(), [
                             'reservation_id' => $reservation->id,
                             'email' => $reservation->passager_email
                         ]);
                     }
                 }
             }

             // Real-time update for Seat Map (Wallet)
             try {
                $dateVoyageStr = $dateVoyage instanceof \Carbon\Carbon ? $dateVoyage->format('Y-m-d') : $dateVoyage;
                $allReserved = Reservation::where('programme_id', $programme->id)
                    ->whereDate('date_voyage', $dateVoyageStr)
                    ->where('statut', 'confirmee')
                    ->pluck('seat_number')
                    ->toArray();
                
                broadcast(new \App\Events\SeatUpdated($programme->id, $dateVoyageStr, $allReserved))->toOthers();
            } catch (\Exception $e) {
                Log::error('Real-time broadcast error: ' . $e->getMessage());
            }
        }


        if ($isWallet) {
             return response()->json([
                'success' => true,
                'message' => 'Paiement effectué avec succès via Mon Compte.',
                'wallet_payment' => true,
                'redirect_url' => route('reservation.index') 
            ]);
        }

        // Retour CinetPay
        return response()->json([
            'success' => true,
            'message' => 'Réservations initialisées.',
            'payment_url' => true,
            'transaction_id' => $transactionId,
            'amount' => (int) $montantTotal,
            'currency' => 'XOF',
            'description' => 'Réservation ' . $request->nombre_places . ' place(s)',
            'customer_name' => Auth::user()->name,
            'customer_surname' => Auth::user()->prenom ?? '',
            'customer_email' => Auth::user()->email,
            'customer_phone_number' => Auth::user()->phone ?? '00000000',
            'customer_address' => 'Abidjan',
            'customer_city' => 'Abidjan',
            'customer_country' => 'CI',
            'customer_state' => 'CI',
            'customer_zip_code' => '00225',
        ]);

    } catch (\Exception $e) {
        DB::rollBack(); // ANNULATION TOTALE EN CAS D'ERREUR
        Log::error('Erreur Réservation: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Erreur technique: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Recalculer le statut des places pour un programme (pour admin)
     */
    public function recalculateProgramStatus($programId)
    {
        try {
            $programme = Programme::findOrFail($programId);

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
                ->where('statut', 'confirmee')
                ->count();

            $vehicule = $programme->getVehiculeForDate($programme->date_depart);
            $totalPlaces = $vehicule ? $vehicule->nombre_place : 70;
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
            $programme = Programme::findOrFail($programId);

            // Vérifier que c'est bien un programme récurrent
            if ($programme->type_programmation != 'recurrent') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette fonction est uniquement pour les programmes récurrents.'
                ]);
            }

            $totalReservedSeats = Reservation::where('programme_id', $programme->id)
                ->where('date_voyage', $date)
                ->where('statut', 'confirmee')
                ->count();

            $vehicule = $programme->getVehiculeForDate($date);
            $totalPlaces = $vehicule ? $vehicule->nombre_place : 70;
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
    public function generateAndSaveQRCode(string $reference, int $reservationId, string $dateVoyage, int $userId = null)
    {
        try {
            // Créer les données du QR Code (format JSON sécurisé)
            $qrData = [
                'user_id' => $userId,
                'reference' => $reference,
                'timestamp' => time(),
                'date_voyage' => $dateVoyage,
                'reservation_id' => $reservationId,
            ];

            // Ajouter un hash de vérification pour éviter la falsification
            $qrData['verification_hash'] = hash(
                'sha256',
                $reference . $reservationId . $dateVoyage . config('app.key')
            );
            
            $qrContent = json_encode($qrData);

            // Créer le QR Code (méthode simplifiée)
            $qrCode = QrCode::create($qrContent);

            // Configurer le QR Code
            $qrCode->setSize(180);
            $qrCode->setMargin(5);

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
    public function sendReservationEmail(Reservation $reservation, Programme $programme, string $qrCodeBase64, string $recipientEmail = null, string $recipientName = null, int $seatNumber = null, string $ticketType = null, string $qrCodeRetourBase64 = null, Programme $programmeRetour = null): void
    {
        try {
            // Correction pour le contexte Webhook (Auth::user() peut être null)
            $user = Auth::user();
            $email = $recipientEmail ?: ($user ? $user->email : null);
            $name = $recipientName ?: ($user ? $user->name : 'Client');

            if (!$email) {
                Log::warning('Tentative d\'envoi d\'email sans destinataire:', ['reservation_id' => $reservation->id]);
                return;
            }

            Log::info('Envoi de la notification à:', [
                'email' => $email,
                'name' => $name,
                'reservation_id' => $reservation->id,
                'seat_number' => $seatNumber
            ]);

            // Envoyer la notification à l'email spécifié
            // Note: On peut utiliser Notification::route('mail', $email) pour envoyer à une adresse arbitraire
            if ($reservation->user) {
                $reservation->user->notify(new ReservationConfirmeeNotification($reservation, $programme, $qrCodeBase64, $name, $seatNumber, $ticketType, $qrCodeRetourBase64, $programmeRetour));
                
                // Notification Push FCM
                if ($reservation->user->fcm_token) {
                    try {
                        $fcmService = app(\App\Services\FcmService::class);
                        $dateVoyage = date('d/m/Y', strtotime($reservation->date_voyage));
                        $heureDepart = date('H:i', strtotime($programme->heure_depart));
                        $fcmService->sendNotification(
                            $reservation->user->fcm_token, 
                            'Réservation confirmée ✅', 
                            "Billet {$reservation->reference}: {$programme->point_depart} → {$programme->point_arrive} le {$dateVoyage} à {$heureDepart}. Bon voyage!",
                            ['type' => 'confirmation', 'reservation_id' => $reservation->id]
                        );
                    } catch (\Exception $e) {
                        Log::error('FCM Error (Confirmation Web): ' . $e->getMessage());
                    }
                }
            } else {
            Notification::route('mail', $email)->notify(new ReservationConfirmeeNotification($reservation, $programme, $qrCodeBase64, $name, $seatNumber, $ticketType, $qrCodeRetourBase64, $programmeRetour));
        }

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
    public function updateProgramStatus($programme, $dateVoyage = null): void
    {
        try {
            // Calculer les places réservées
            $query = Reservation::where('programme_id', $programme->id)
                ->where('statut', 'confirmee');

            if ($programme->type_programmation == 'recurrent' && $dateVoyage) {
                // Pour les programmes récurrents, filtrer par date_voyage
                $query->where('date_voyage', $dateVoyage);
            }

            $vehicule = $programme->getVehiculeForDate($dateVoyage ?: $programme->date_depart);
            $totalPlaces = $vehicule ? $vehicule->nombre_place : 70;

            if ($totalPlaces == 0) {
                $totalPlaces = 70;
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
            $reservation = Reservation::with(['user', 'programme', 'programme.compagnie'])
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

            // --- NOUVELLE LOGIQUE DE VALIDATION STRICTE (Aller vs Retour) ---
            $today = date('Y-m-d');
            $voyageDate = date('Y-m-d', strtotime($reservation->date_voyage));
            $retourDate = $reservation->date_retour ? date('Y-m-d', strtotime($reservation->date_retour)) : null;

            // Déterminer quel trajet on vérifie
            $trajetVerifie = 'ALLER';
            $messageValidation = 'Voyage Aller Valide';
            $statutConcerne = $reservation->statut_aller;
            
            // Si c'est un aller-retour et qu'on est à la date du retour
            if ($reservation->is_aller_retour && $retourDate && $today == $retourDate) {
                $trajetVerifie = 'RETOUR';
                $messageValidation = 'Voyage Retour Valide';
                $statutConcerne = $reservation->statut_retour;
            }

            // VALIDATION STRICTE DE LA DATE
            if ($trajetVerifie === 'ALLER' && $today != $voyageDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le scan n\'est possible que le jour du voyage aller (' . date('d/m/Y', strtotime($voyageDate)) . ')',
                    'code' => 'INVALID_DATE',
                    'reservation' => ['date_voyage' => $voyageDate]
                ], 400);
            }

            if ($trajetVerifie === 'RETOUR' && $today != $retourDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le scan n\'est possible que le jour du voyage retour (' . date('d/m/Y', strtotime($retourDate)) . ')',
                    'code' => 'INVALID_DATE',
                    'reservation' => ['date_retour' => $retourDate]
                ], 400);
            }

            // VÉRIFICATION DU STATUT (doit être confirmée)
            if ($statutConcerne != 'confirmee') {
                return response()->json([
                    'success' => false,
                    'message' => "Le billet $trajetVerifie n'est plus à l'état 'confirmée' (Statut: $statutConcerne)",
                    'code' => 'INVALID_STATUS',
                    'trajet' => $trajetVerifie
                ], 400);
            }

            // Vérifier si déjà utilisé (scanné) pour ce trajet
            // Note: Il faudrait idéalement une colonne `scanned_at_aller` et `scanned_at_retour`.
            // Pour l'instant on utilise statut_aller / statut_retour = 'terminee'
            if ($statutConcerne === 'terminee') {
                 return response()->json([
                    'success' => false,
                    'message' => "Billet $trajetVerifie déjà utilisé",
                    'code' => 'ALREADY_USED',
                    'trajet' => $trajetVerifie
                ], 400);
            }

            // Si tout est bon, retourner les informations complètes


            Log::info("QR Code ($trajetVerifie) vérifié avec succès", [
                'reference' => $reservation->reference,
                'user' => $reservation->user->name,
                'trajet' => $trajetVerifie
            ]);

            return response()->json([
                'success' => true,
                'message' => $messageValidation,
                'code' => 'VALID',
                'trajet_verifie' => $trajetVerifie, // Indiquer au scanner quel trajet est validé
                'reservation' => [
                    'reference' => $reservation->reference,
                    // Retourner le statut global mais aussi les détails
                    'statut_global' => $reservation->statut,
                    'statut_aller' => $reservation->statut_aller,
                    'statut_retour' => $reservation->statut_retour,
                    
                    'date_voyage' => $reservation->date_voyage,
                    'date_retour' => $reservation->date_retour,
                    'date_reservation' => $reservation->date_reservation,
                    'user' => [
                        'id' => $reservation->user->id,
                        'name' => $reservation->user->name,
                        'email' => $reservation->user->email,
                    ],
                    // On retourne le programme pertinent selon le trajet ?
                    // Pour simplifier, on retourne le programme ALLER ici, le front peut afficher
                    'programme' => [
                        'id' => $reservation->programme->id,
                        'point_depart' => $reservation->programme->point_depart,
                        'point_arrive' => $reservation->programme->point_arrive,
                        'heure_depart' => $reservation->programme->heure_depart,
                        'heure_arrive' => $reservation->programme->heure_arrive,
                        'compagnie' => $reservation->programme->compagnie->name ?? 'Inconnue',
                        'vehicule' => [
                            'immatriculation' => $reservation->programme->getVehiculeForDate($reservation->date_voyage)->immatriculation ?? 'Inconnue',
                        ]
                    ],
                    'details' => [
                        'places' => $reservation->seat_number, // Ou tableau si groupe
                        'montant_formatte' => number_format((float) $reservation->montant, 0, ',', ' ') . ' FCFA'
                    ],
                    'verification' => [
                        'timestamp' => now()->toDateTimeString(),
                        'scanned_at' => $request->get('scan_time', now()->toDateTimeString()),
                        'is_valid' => true,
                        'trajet_detecte' => $trajetVerifie
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
            'trajet' => 'nullable|string' // ALLER ou RETOUR
        ]);

        try {
            $reservation = Reservation::where('reference', $request->reference)
                ->firstOrFail();

            $trajet = $request->get('trajet', 'ALLER');
            
            // Préparer les données de mise à jour
            $updateData = [
                'embarquement_scanned_at' => $request->get('scan_time', now()),
                'embarquement_agent_id' => $request->agent_id,
                'embarquement_location' => $request->scan_location,
                'embarquement_status' => 'boarded',
            ];

            // Mettre à jour le statut spécifique au trajet
            if ($trajet === 'RETOUR' && $reservation->is_aller_retour) {
                $updateData['statut_retour'] = 'terminee';
                
                // Si l'aller est DÉJÀ terminé, alors tout est terminé
                if ($reservation->statut_aller === 'terminee') {
                    $updateData['statut'] = 'terminee';
                }
            } else {
                // C'est un aller (ou aller simple)
                $updateData['statut_aller'] = 'terminee';
                
                // Si c'est un aller simple, c'est fini
                if (!$reservation->is_aller_retour) {
                    $updateData['statut'] = 'terminee';
                } 
                // Si c'est un aller-retour, on vérifie si le retour est déjà fait (cas rare mais possible si scan désordre ou correction)
                elseif ($reservation->statut_retour === 'terminee') {
                    $updateData['statut'] = 'terminee';
                }
            }

            // Marquer comme scanné/embarqué
            $reservation->update($updateData);

            Log::info("QR Code ($trajet) marqué comme utilisé:", [
                'reference' => $reservation->reference,
                'agent_id' => $request->agent_id
            ]);

            return response()->json([
                'success' => true,
                'message' => "Passager marqué comme embarqué ($trajet)",
                'reference' => $reservation->reference,
                'trajet_valide' => $trajet,
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

        // 1. D'abord vérifier le QR Code
        $verificationResponse = $this->verifyQRCode($request);

        if (!$verificationResponse->getData()->success) {
            return $verificationResponse;
        }

        $verificationData = $verificationResponse->getData();
        $trajetDetecte = $verificationData->trajet_verifie ?? 'ALLER';

        // 2. Si la vérification est réussie et qu'on veut marquer comme utilisé
        if ($request->get('mark_as_used', false)) {
            $reference = $verificationData->reservation->reference;

            $markResponse = $this->markQRCodeUsed(new Request([
                'reference' => $reference,
                'agent_id' => $request->agent_id,
                'scan_location' => $request->scan_location,
                'scan_time' => now(),
                'trajet' => $trajetDetecte
            ]));

            if ($markResponse->getData()->success) {
                $verificationData->embarquement = [
                    'marked' => true,
                    'time' => now()->toDateTimeString(),
                    'agent_id' => $request->agent_id,
                    'trajet' => $trajetDetecte
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

    public function apiProgrammes()
{
    $today = now()->format('Y-m-d');
    
    // FlixBus Model: Simple query - just get future active programs
    $programmes = Programme::with(['compagnie', 'itineraire'])
        ->where('date_depart', '>=', $today)
        ->where('statut', 'actif')
        ->orderBy('date_depart', 'asc')
        ->orderBy('heure_depart', 'asc')
        ->limit(50) // Limite pour éviter surcharge
        ->get();

    return response()->json([
        'success' => true,
        'programmes' => $programmes,
        'count' => $programmes->count()
    ]);
}

/**
 * API pour obtenir les lignes groupées (route uniquement)
 * Retourne: Abidjan -> Alépé (5 horaires disponibles)
 */
public function apiGroupedRoutes()
{
    $today = now()->format('Y-m-d');
    
    // Grouper par trajet (point_depart + point_arrive)
    $routes = Programme::select(
            'point_depart', 
            'point_arrive', 
            'compagnie_id',
            DB::raw('MIN(montant_billet) as prix_min'),
            DB::raw('MAX(montant_billet) as prix_max'),
            DB::raw('COUNT(*) as total_voyages'),
            DB::raw('MIN(date_depart) as prochaine_date'),
            DB::raw('GROUP_CONCAT(DISTINCT heure_depart ORDER BY heure_depart SEPARATOR ", ") as horaires')
        )
        ->with('compagnie:id,name')
        ->where('statut', 'actif')
        ->where(function($query) use ($today) {
             $query->where('date_depart', '>=', $today)
                   ->orWhere('date_fin', '>=', $today);
        })
        ->groupBy('point_depart', 'point_arrive', 'compagnie_id')
        ->orderBy('point_depart')
        ->get();

    return response()->json([
        'success' => true,
        'routes' => $routes,
        'count' => $routes->count()
    ]);
}

/**
 * API pour obtenir les dates disponibles pour une ligne spécifique
 */
public function apiRouteDates(Request $request)
{
    $today = now()->format('Y-m-d');
    
    $dates = Programme::select('date_depart', DB::raw('COUNT(*) as horaires_count'))
        ->where('point_depart', $request->point_depart)
        ->where('point_arrive', $request->point_arrive)
        ->where('date_depart', '>=', $today)
        ->where('statut', 'actif')
        ->groupBy('date_depart')
        ->orderBy('date_depart')
        ->limit(30) // Prochains 30 jours
        ->get();

    return response()->json([
        'success' => true,
        'dates' => $dates
    ]);
}

/**
 * API pour obtenir les horaires pour une ligne et une date spécifique
 */
  public function apiRouteSchedules(Request $request)
    {
        $requestedDate = $request->date ?? now()->format('Y-m-d');
        
        $programmes = Programme::with(['compagnie'])
            ->where('point_depart', $request->point_depart)
            ->where('point_arrive', $request->point_arrive)
            ->where('statut', 'actif')
            ->when($request->compagnie_id && $request->compagnie_id !== 'undefined', function($q) use ($request) {
                return $q->where('compagnie_id', $request->compagnie_id);
            })
            // LOGIQUE DATE : Le voyage doit être commencé (date_depart <= requestedDate)
            // ET NE PAS être terminé (date_fin >= requestedDate OU date_fin est NULL)
            ->where('date_depart', '<=', $requestedDate . ' 23:59:59')
            ->where(function($query) use ($requestedDate) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', $requestedDate);
            })
            ->orderBy('heure_depart')
            ->get();

        $schedules = $programmes->map(function ($programme) use ($requestedDate) {
            $vehicule = $programme->getVehiculeForDate($requestedDate);
            $capacite = $vehicule ? intval($vehicule->nombre_place) : 70;
            
            $reservedCount = Reservation::where('programme_id', $programme->id)
                ->where('date_voyage', $requestedDate)
                ->where('statut', 'confirmee')
                ->count();
                
            return [
                'id' => $programme->id,
                'heure_depart' => $programme->heure_depart,
                'heure_arrive' => $programme->heure_arrive,
                'montant_billet' => (int) str_replace(' ', '', $programme->montant_billet), // Nettoyage prix
                'vehicule' => $programme->vehicule ? $programme->vehicule->immatriculation : 'N/A',
                'vehicule_id' => $programme->vehicule_id,
                'places_totales' => $capacite,
                'places_disponibles' => max(0, $capacite - $reservedCount),
                'date_fin' => $programme->date_fin
            ];
        });

        return response()->json([
            'success' => true,
            'schedules' => $schedules
        ]);
    }


/**
 * API pour obtenir les voyages retour disponibles (sens inversé)
 * Recherche les programmes avec point_depart et point_arrive inversés
 */
    public function apiReturnTrips(Request $request)
    {
        $requestedDate = $request->min_date ?? now()->format('Y-m-d');
        
        $returnTrips = Programme::with(['compagnie'])
            ->where('point_depart', $request->original_arrive)
            ->where('point_arrive', $request->original_depart)
            ->where('statut', 'actif')
            ->when($request->compagnie_id && $request->compagnie_id !== 'undefined', function($q) use ($request) {
                return $q->where('compagnie_id', $request->compagnie_id);
            })
            // Même correction de date que ci-dessus
            ->where('date_depart', '<=', $requestedDate . ' 23:59:59')
            ->where(function($query) use ($requestedDate) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', $requestedDate);
            })
            ->orderBy('heure_depart')
            ->get();

        $returnTrips = $returnTrips->map(function($trip) use ($requestedDate) {
            $trip->display_date = $requestedDate;
            // Nettoyage prix
            $trip->montant_billet = (int) str_replace(' ', '', $trip->montant_billet); 
            return $trip;
        });

        $groupedByTime = $returnTrips->groupBy(function($trip) {
            return $trip->heure_depart;
        });

        return response()->json([
            'success' => true,
            'return_trips' => $returnTrips,
            'grouped' => $groupedByTime,
            'count' => $returnTrips->count(),
            'requested_date' => $requestedDate
        ]);
    }

    /**
     * Get modification data for the modal
     */
     public function getModificationData(Reservation $reservation)
{
    if ($reservation->user_id !== Auth::id()) {
        return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
    }

    $service = new ReservationService();
    $refundData = $service->calculateRefundPercentage($reservation);
    
    if (!$refundData['can_cancel']) {
        return response()->json([
            'success' => false, 
            'can_modify' => false, 
            'message' => 'Modification impossible moins de 15 minutes avant le départ.'
        ]);
    }

    // 1. Gestion de l'Aller-Retour et Paired Reservation
    $pairedReservation = $service->findPairedReservation($reservation);
    $totalOldPrice = (float) $reservation->montant;
    
    // Si c'est un aller-retour, on doit avoir les infos du retour
    $returnDetails = null;
    $isRoundTrip = (bool) $reservation->is_aller_retour;

    if ($pairedReservation) {
        $totalOldPrice += (float) $pairedReservation->montant;
        
        // Si la réservation actuelle est l'ALLER, le paired est le RETOUR
        // Si la réservation actuelle est le RETOUR, le paired est l'ALLER (mais on modifie généralement depuis l'aller)
        // On assume ici qu'on clique sur l'une des deux et on veut modifier l'ensemble.
        
        // Logique pour déterminer qui est le retour
        $retourRes = str_contains($reservation->reference, '-RET') ? $reservation : $pairedReservation;
        
        if ($isRoundTrip) {
            $returnDetails = [
                'prog_id' => $retourRes->programme_id,
                // Formatage STRICT pour input type="date" (YYYY-MM-DD)
                'date' => \Carbon\Carbon::parse($retourRes->date_voyage)->format('Y-m-d'),
                'time' => $retourRes->heure_depart,
                'seat' => $retourRes->seat_number
            ];
        }
    } elseif ($isRoundTrip && $reservation->date_retour) {
        // Cas fallback (si une seule ligne en base pour l'AR - rare mais possible selon ton schema)
        $returnDetails = [
            'prog_id' => $reservation->programme_retour_id,
            'date' => \Carbon\Carbon::parse($reservation->date_retour)->format('Y-m-d'),
            // On essaie de trouver l'heure via le programme si pas stockée
            'time' => $reservation->programmeRetour ? $reservation->programmeRetour->heure_depart : null,
            'seat' => null // Impossible de deviner le siège retour si pas de ligne dédiée
        ];
    }

    // 2. Calcul Valeur Résiduelle
    if ($refundData['percentage'] !== null) {
        $residualValue = round($totalOldPrice * $refundData['percentage'] / 100);
    } else {
        $residualValue = max(0, $totalOldPrice - $refundData['penalty']);
    }

    // 3. Routes disponibles
    $routes = Programme::where('statut', 'actif')
        ->where('date_depart', '>=', now()->format('Y-m-d'))
        ->get()
        ->map(function ($prog) {
            return [
                'id' => $prog->id, 
                'unique_key' => $prog->point_depart . '|' . $prog->point_arrive . '|' . $prog->compagnie_id,
                'name' => $prog->point_depart . ' ➝ ' . $prog->point_arrive,
                'compagnie' => $prog->compagnie->name ?? 'Compagnie',
                'prix' => (int) str_replace(' ', '', $prog->montant_billet ?? $prog->prix ?? 0),
                'depart' => $prog->point_depart,
                'arrive' => $prog->point_arrive,
                'compagnie_id' => $prog->compagnie_id
            ];
        })
        ->unique('unique_key')
        ->values();

    $currentProgramme = $reservation->programme;
    $currentRouteKey = $currentProgramme->point_depart . '|' . $currentProgramme->point_arrive . '|' . $currentProgramme->compagnie_id;

    return response()->json([
        'success' => true,
        'can_modify' => true,
        'reservation' => $reservation,
        // Formatage STRICT pour l'aller aussi
        'formatted_date_aller' => \Carbon\Carbon::parse($reservation->date_voyage)->format('Y-m-d'),
        'current_route_key' => $currentRouteKey,
        'is_aller_retour' => $isRoundTrip,
        'return_details' => $returnDetails,
        'residual_value' => $residualValue,
        'penalty_info' => $refundData['time_remaining'],
        'available_routes' => $routes,
        'user_solde' => (float) Auth::user()->solde
    ]);
}

    /**
     * Get available dates for a programme
     */
    public function getAvailableDates(Programme $programme)
    {
        try {
            // Get dates from ProgrammeStatutDate
            $dates = ProgrammeStatutDate::where('programme_id', $programme->id)
                ->where('date_voyage', '>=', now()->format('Y-m-d'))
                ->where('statut', '!=', 'complet')
                ->orderBy('date_voyage')
                ->get()
                ->map(function($sd) {
                    return [
                        'date' => $sd->date_voyage,
                        'available' => $sd->statut === 'disponible',
                        'places_disponibles' => ($sd->total_places ?? 70) - ($sd->places_reservees ?? 0),
                    ];
                });

            // If no specific dates, return next 30 days
            if ($dates->isEmpty()) {
                $dates = collect();
                for ($i = 0; $i < 30; $i++) {
                    $date = now()->addDays($i)->format('Y-m-d');
                    $dates->push([
                        'date' => $date,
                        'available' => true,
                        'places_disponibles' => 70,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'dates' => $dates
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get available dates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des dates'
            ], 500);
        }
    }

    /**
     * Get available times for a programme on a specific date
     */
    public function getAvailableTimes(Programme $programme, Request $request)
    {
        $date = $request->query('date');

        if (!$date) {
            return response()->json([
                'success' => false,
                'message' => 'Date requise'
            ], 400);
        }

        try {
            // Get all programmes with same route
            $programmes = Programme::where('point_depart', $programme->point_depart)
                ->where('point_arrive', $programme->point_arrive)
                ->where('statut', 'actif')
                ->orderBy('heure_depart')
                ->get()
                ->map(function($p) {
                    return [
                        'programme_id' => $p->id,
                        'heure_depart' => $p->heure_depart,
                        'heure_arrive' => $p->heure_arrive,
                        'available' => true,
                    ];
                });

            return response()->json([
                'success' => true,
                'heures' => $programmes
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get available times: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des horaires'
            ], 500);
        }
    }

    /**
     * Get seat availability for a programme
     */
    public function getSeats(Programme $programme, Request $request)
    {
        $date = $request->query('date');
        $heure = $request->query('heure');

        if (!$date) {
            return response()->json([
                'success' => false,
                'message' => 'Date requise'
            ], 400);
        }

        try {
            // Get specific programme if heure is provided
            $targetProgramme = $programme;
            if ($heure) {
                $targetProgramme = Programme::where('point_depart', $programme->point_depart)
                    ->where('point_arrive', $programme->point_arrive)
                    ->where('heure_depart', $heure)
                    ->where('statut', 'actif')
                    ->first() ?? $programme;
            }

            // Get reserved seats for this date
            $reservedSeats = Reservation::where('programme_id', $targetProgramme->id)
                ->where('date_voyage', $date)
                ->where('statut', 'confirmee')
                ->pluck('seat_number')
                ->toArray();

            // Generate seat grid
            $vehicule = $targetProgramme->getVehiculeForDate($date);
            $totalSeats = $vehicule ? $vehicule->nombre_place : 70;
            $seats = [];
            
            for ($i = 1; $i <= $totalSeats; $i++) {
                $seats[] = [
                    'number' => $i,
                    'available' => !in_array($i, $reservedSeats),
                ];
            }

            return response()->json([
                'success' => true,
                'seats' => $seats,
                'total_seats' => $totalSeats,
                'reserved_count' => count($reservedSeats),
                'vehicule' => $vehicule
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get seats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des places'
            ], 500);
        }
    }

    /**
     * Calculate modification delta in real-time
     */
     public function calculateModificationDelta(Request $request, Reservation $reservation)
    {
        $service = new ReservationService();
        $user = Auth::user();

        // Recalculer valeur résiduelle (le temps a pu passer)
        $refundData = $service->calculateRefundPercentage($reservation);
        
        $pairedReservation = $service->findPairedReservation($reservation);
        $totalOldPrice = (float) $reservation->montant;
        if ($pairedReservation) $totalOldPrice += (float) $pairedReservation->montant;

        if ($refundData['percentage'] !== null) {
            $residualValue = round($totalOldPrice * $refundData['percentage'] / 100);
        } else {
            $residualValue = max(0, $totalOldPrice - $refundData['penalty']);
        }

        // Calculer nouveau prix
        // Note: Le front envoie 'new_programme_id' correspondant à l'horaire choisi
        $newProgramme = Programme::findOrFail($request->new_programme_id);
        $newTotal = (float) str_replace(' ', '', $newProgramme->montant_billet ?? $newProgramme->prix ?? 0);

        // Si l'utilisateur ajoute un retour (non géré dans cet exemple simple mais prévu)
        if ($request->filled('new_return_programme_id')) {
            $newReturnProgramme = Programme::findOrFail($request->new_return_programme_id);
            $newTotal += (float) str_replace(' ', '', $newReturnProgramme->montant_billet ?? $newReturnProgramme->prix ?? 0);
        }

        $delta = $newTotal - $residualValue;
        
        $action = 'neutral';
        if ($delta > 0) $action = 'pay'; // Payer diff
        if ($delta < 0) $action = 'refund'; // Remboursement diff

        return response()->json([
            'success' => true,
            'old_value' => $totalOldPrice,
            'residual_value' => $residualValue,
            'new_total' => $newTotal,
            'delta' => abs($delta),
            'action' => $action,
            'can_afford' => ($action === 'pay' ? ((float)$user->solde >= $delta) : true)
        ]);
    }
     public function processModification(Request $request, Reservation $reservation)
    {
        // Validation simple
        $request->validate([
            'programme_id' => 'required|exists:programmes,id', // C'est le nouveau programme
            'date_voyage' => 'required|date',
            'seat_number' => 'required',
            'heure_depart' => 'required'
        ]);

        $service = new ReservationService();
        
        // On mappe les champs du JS vers ceux attendus par le Service
        $data = $request->all();
        
        // Appel au service
        $result = $service->modifyReservation($reservation, $data);
        
        return response()->json($result);
    }
   public function getAvailableSeatsForSchedule(Request $request)
{
    $programId = $request->get('program_id');
    $date = $request->get('date');
    $heureDepart = $request->get('heure_depart');

    if (!$programId || !$date || !$heureDepart) {
        return response()->json([
            'success' => false,
            'error' => 'Paramètres manquants'
        ], 400);
    }

    $programme = Programme::find($programId);
    
    if (!$programme) {
        return response()->json([
            'success' => false,
            'error' => 'Programme non trouvé'
        ], 404);
    }

    // Récupérer le véhicule
    $vehicule = $programme->getVehiculeForDate($date);
    $totalSeats = $vehicule ? $vehicule->nombre_place : 70;

    // Récupérer les places réservées pour ce créneau précis
    $reservedSeats = Reservation::where('programme_id', $programId)
        ->where('date_voyage', $date)
        ->where('heure_depart', $heureDepart)
        ->where('statut', 'confirmee')
        ->pluck('seat_number')
        ->toArray();

    $availableSeats = [];
    for ($i = 1; $i <= $totalSeats; $i++) {
        if (!in_array($i, $reservedSeats)) {
            $availableSeats[] = $i;
        }
    }

    return response()->json([
        'success' => true,
        'total_seats' => $totalSeats,
        'reserved_seats' => $reservedSeats,
        'available_seats' => $availableSeats,
        'available_count' => count($availableSeats)
    ]);
}


}
