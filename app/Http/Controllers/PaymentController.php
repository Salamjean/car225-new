<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\WaveService;
use Illuminate\Http\Request;
use App\Models\Programme; // <--- AJOUTER CECI
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $waveService;

    public function __construct(WaveService $waveService)
    {
        $this->waveService = $waveService;
    }

    /**
     * Valide la signature transmise par Wave.
     */
    private function validateWaveSignature(Request $request): bool
    {
        $waveSignature = $request->header('wave-signature');
        $waveWebhookSecret = config('services.wave.webhook_secret');

        if (!$waveWebhookSecret || !$waveSignature) {
            return true; // Si pas de secret ou signature, on passe (environnement de test)
        }

        $timestamp = null;
        $signatures = [];

        $parts = explode(',', (string) $waveSignature);
        foreach ($parts as $part) {
            list($prefix, $value) = array_pad(explode('=', trim($part), 2), 2, null);
            if ($prefix === 't') {
                $timestamp = $value;
            } elseif ($prefix === 'v1') {
                $signatures[] = $value;
            }
        }

        if (!$timestamp || empty($signatures)) {
            return false;
        }

        $payload = $timestamp . $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $waveWebhookSecret);

        return in_array($expectedSignature, $signatures);
    }

    /**
     * Webhook Wave pour la notification de paiement
     */
    public function waveNotify(Request $request)
    {
        $payload = $request->getContent();
        Log::info('Wave Webhook Raw Payload', ['payload' => $payload]);

        if (!$this->validateWaveSignature($request)) {
            Log::warning('Webhook Wave: Signature invalide', [
                'received' => $request->header('wave-signature'),
                'content' => $payload
            ]);
            return response()->json(['message' => 'Signature invalide'], 401);
        }

        $eventType = $request->input('type');
        Log::info("Webhook Wave: Type reçu ({$eventType})");

        if ($eventType !== 'checkout.session.completed') {
            Log::info("Webhook Wave: Événement ignoré ({$eventType})");
            return response()->json(['message' => 'Événement ignoré'], 200);
        }

        $checkoutData = $request->input('data');
        if (!$checkoutData) {
            return response()->json(['message' => 'Données de session manquantes'], 400);
        }

        $clientReference = $checkoutData['client_reference'] ?? null;
        Log::info('Webhook Wave: Client Reference', ['ref' => $clientReference]);
        
        if (!$clientReference) {
            Log::warning('Webhook Wave: client_reference manquant');
            return response()->json(['message' => 'Client reference manquant'], 200);
        }

        // Récupérer le paiement
        $paiement = \App\Models\Paiement::where('transaction_id', $clientReference)->first();
        Log::info('Webhook Wave: Recherche Paiement', ['trouvé' => !!$paiement, 'id' => $clientReference]);

        if (!$paiement) {
            Log::error('Paiement non trouvé pour la référence:', ['id' => $clientReference]);
            return response()->json(['message' => 'Paiement non trouvé'], 404);
        }

        Log::info('Wave Payment Confirmed', ['transaction_id' => $clientReference]);

        // Mettre à jour le paiement
        $paiement->update([
            'status' => 'success',
            'payment_method' => 'wave',
            'payment_date' => isset($checkoutData['when_completed']) ? \Carbon\Carbon::parse($checkoutData['when_completed']) : now(),
            'payment_details' => array_merge($paiement->payment_details ?? [], $checkoutData)
        ]);

        // Mettre à jour les réservations et générer les billets
        $reservations = $paiement->reservations;

        foreach ($reservations as $reservation) {
            if ($reservation->statut !== 'confirmee') {
                $reservation->update(['statut' => 'confirmee']);

                // Appeler la logique de génération de billet
                $this->finalizeReservation($reservation);

                Log::info('Reservation confirmed and ticket generated', ['id' => $reservation->id]);
            }
        }

        return response()->json(['message' => 'Success'], 200);
    }

    /**
     * Finaliser la réservation : QR Code + Email
     */
    protected function finalizeReservation(Reservation $reservation)
    {
        // On utilise l'injection de dépendance via app() pour instancier proprement
        $resController = app(\App\Http\Controllers\User\Reservation\ReservationController::class);

        try {
            $dateVoyageStr = $reservation->date_voyage instanceof \Carbon\Carbon
                ? $reservation->date_voyage->format('Y-m-d')
                : date('Y-m-d', strtotime($reservation->date_voyage));

            // 1. Génération du QR Code
            // Assurez-vous que cette méthode est PUBLIQUE dans ReservationController
            $qrCodeData = $resController->generateAndSaveQRCode(
                $reservation->reference,
                $reservation->id,
                $dateVoyageStr,
                $reservation->user_id
            );

            // 2. Mise à jour de la réservation
            $reservation->update([
                'qr_code' => $qrCodeData['base64'],
                'qr_code_path' => $qrCodeData['path'],
                'qr_code_data' => $qrCodeData['qr_data'],
                'statut_aller' => 'confirmee',
            ]);

            $programmeRetour = null;
            $qrCodeRetour = null; // Par défaut, on n'envoie pas de QR retour séparé si c'est le même

            // 3. Gestion Retour
            if ($reservation->is_aller_retour) {
                $reservation->update(['statut_retour' => 'confirmee']);
                
                // Récupération programme retour
                if ($reservation->programme_retour_id) {
                    $programmeRetour = Programme::find($reservation->programme_retour_id);
                } 
                // Fallback: Si pas d'ID mais relation chargée (rare ici car on vient du modèle Paiement)
                elseif ($reservation->programme && $reservation->programme->programmeRetour) {
                    $programmeRetour = $reservation->programme->programmeRetour;
                }

                // IMPORTANT: Pour un A/R, on utilise généralement le MÊME QR Code
                // Mais votre méthode sendReservationEmail attend un 8ème argument pour le QR Retour
                $qrCodeRetour = $qrCodeData['base64']; 
            }

            // 4. Envoi Email
            $resController->sendReservationEmail(
                $reservation,
                $reservation->programme, // Programme Aller
                $qrCodeData['base64'],   // QR Code Aller
                $reservation->passager_email,
                $reservation->passager_prenom . ' ' . $reservation->passager_nom,
                $reservation->seat_number,
                $reservation->is_aller_retour ? 'ALLER-RETOUR' : 'ALLER SIMPLE',
                $qrCodeRetour,
                $programmeRetour
            );

            // 5. Envoi SMS
            try {
                $smsService = app(\App\Services\SmsService::class);
                $smsService->sendReservationSms(
                    [$reservation], // On envoie pour cette réservation spécifique
                    $reservation->programme,
                    $reservation->user, // Auth::user() est nul en webhook
                    $reservation->is_aller_retour,
                    $reservation->date_retour
                );
            } catch (\Exception $e) {
                Log::error('Erreur envoi SMS via Webhook: ' . $e->getMessage());
            }

            // 6. Mise à jour places occupées
            $resController->updateProgramStatus(
                $reservation->programme,
                $dateVoyageStr
            );

            // --- DEDUCTION TICKETS ---
            if (!$reservation->relationLoaded('programme')) {
                $reservation->load('programme.compagnie');
            } elseif (!$reservation->programme->relationLoaded('compagnie')) {
                $reservation->programme->load('compagnie');
            }

            if ($reservation->programme && $reservation->programme->compagnie) {
                  $reservation->programme->compagnie->deductTickets($reservation->montant, "Réservation #{$reservation->reference} (Wave)");
            } else {
                 \Illuminate\Support\Facades\Log::error("PaymentController: IMPOSSIBLE DE DÉDUIRE - Compagnie introuvable", [
                    'reservation_id' => $reservation->id,
                    'has_programme' => (bool)$reservation->programme,
                    'has_compagnie' => $reservation->programme ? (bool)$reservation->programme->compagnie : false
                 ]);
            }

            Log::info('Finalisation réservation terminée avec succès (Email envoyé)', ['id' => $reservation->id]);

            // Real-time update for Seat Map
            try {
                $allReserved = Reservation::where('programme_id', $reservation->programme_id)
                    ->whereDate('date_voyage', $dateVoyageStr)
                    ->where('statut', 'confirmee')
                    ->pluck('seat_number')
                    ->toArray();
                
                broadcast(new \App\Events\SeatUpdated($reservation->programme_id, $dateVoyageStr, $allReserved))->toOthers();
            } catch (\Exception $e) {
                Log::error('Real-time broadcast error: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            Log::error('Erreur critique lors de la finalisation de la réservation:', [
                'id' => $reservation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    /**
     * Page de retour après le paiement
     */
    public function waveReturn(Request $request)
    {
        $sessionId = $request->input('session_id'); // Wave passes session_id in the url query
        Log::info('User returned from Wave', ['session_id' => $sessionId]);

        return view('payment.success', ['sessionId' => $sessionId]);
    }
    
    public function waveCancel(Request $request) {
        return view('payment.cancel');
    }

}
