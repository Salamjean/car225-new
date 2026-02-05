<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\CinetPayService;
use Illuminate\Http\Request;
use App\Models\Programme; // <--- AJOUTER CECI
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $cinetPayService;

    public function __construct(CinetPayService $cinetPayService)
    {
        $this->cinetPayService = $cinetPayService;
    }

    /**
     * Webhook CinetPay pour la notification de paiement
     */
    public function notify(Request $request)
    {
        Log::info('CinetPay Notification Received', $request->all());

        if (!$request->has('cpm_trans_id')) {
            Log::warning('CinetPay Notification: Missing transaction ID');
            return response()->json(['message' => 'Missing transaction ID'], 400);
        }

        $transactionId = $request->cpm_trans_id;

        // Récupérer le paiement
        $paiement = \App\Models\Paiement::where('transaction_id', $transactionId)->first();

        if (!$paiement) {
            Log::error('Paiement non trouvé pour la transaction:', ['id' => $transactionId]);
            return response()->json(['message' => 'Paiement non trouvé'], 404);
        }

        // Vérifier le statut du paiement auprès de CinetPay
        $statusInfo = $this->cinetPayService->checkPaymentStatus($transactionId);

        if ($statusInfo && isset($statusInfo['code']) && $statusInfo['code'] == '00') {
            Log::info('CinetPay Payment Confirmed', ['transaction_id' => $transactionId]);

            // Mettre à jour le paiement
            $paiement->update([
                'status' => 'success',
                'payment_method' => $statusInfo['data']['payment_method'] ?? null,
                'payment_date' => now(),
                'payment_details' => array_merge($paiement->payment_details ?? [], $statusInfo)
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
        } else {
            $paiement->update([
                'status' => 'failed',
                'payment_details' => array_merge($paiement->payment_details ?? [], $statusInfo ?? ['error' => 'No info from CinetPay'])
            ]);
        }

        Log::warning('CinetPay Payment Not Confirmed or Failed', ['status' => $statusInfo]);
        return response()->json(['message' => 'Payment failed or pending'], 200);
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
            // Assurez-vous que cette méthode est PUBLIQUE dans ReservationController
            $resController->sendReservationEmail(
                $reservation,
                $reservation->programme, // Programme Aller
                $qrCodeData['base64'],   // QR Code Aller
                $reservation->passager_email,
                $reservation->passager_prenom . ' ' . $reservation->passager_nom, // Concaténation sûre
                $reservation->seat_number,
                $reservation->is_aller_retour ? 'ALLER-RETOUR' : 'ALLER SIMPLE',
                $qrCodeRetour,          // QR Code Retour (si applicable)
                $programmeRetour        // Programme Retour (si applicable)
            );

            // 5. Mise à jour places occupées
            // Assurez-vous que cette méthode est PUBLIQUE dans ReservationController
            $resController->updateProgramStatus(
                $reservation->programme,
                $dateVoyageStr
            );

            // --- DEDUCTION TICKETS ---
            // Charger la compagnie si nécessaire
            if (!$reservation->relationLoaded('programme')) {
                $reservation->load('programme.compagnie');
            } elseif (!$reservation->programme->relationLoaded('compagnie')) {
                $reservation->programme->load('compagnie');
            }

            if ($reservation->programme && $reservation->programme->compagnie) {
                 $deductionQty = $reservation->is_aller_retour ? 2 : 1;
                 
                 // DEBUG: Verify company before deduction
                 \Illuminate\Support\Facades\Log::info("PaymentController: Finalize - Prêt à déduire", [
                    'reservation_id' => $reservation->id,
                    'is_aller_retour' => $reservation->is_aller_retour,
                    'qty' => $deductionQty,
                    'program_id' => $reservation->programme->id,
                    'RESOLVED_COMPANY_ID' => $reservation->programme->compagnie->id, // C'est ici qu'on verra si c'est 2 ou autre
                    'RESOLVED_COMPANY_NAME' => $reservation->programme->compagnie->name
                 ]);

                 $reservation->programme->compagnie->deductTickets($deductionQty, "Réservation #{$reservation->reference} (CinetPay - {$reservation->payment_transaction_id})");
            } else {
                 \Illuminate\Support\Facades\Log::error("PaymentController: IMPOSSIBLE DE DÉDUIRE - Compagnie introuvable", [
                    'reservation_id' => $reservation->id,
                    'has_programme' => (bool)$reservation->programme,
                    'has_compagnie' => $reservation->programme ? (bool)$reservation->programme->compagnie : false
                 ]);
            }

            Log::info('Finalisation réservation terminée avec succès (Email envoyé)', ['id' => $reservation->id]);

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
    public function return(Request $request)
    {
        $transactionId = $request->transaction_id;
        Log::info('User returned from CinetPay', ['transaction_id' => $transactionId]);

        // Vérifier le statut une dernière fois
        $statusInfo = $this->cinetPayService->checkPaymentStatus($transactionId);

        if ($statusInfo && isset($statusInfo['code']) && $statusInfo['code'] == '00') {
            // S'assurer que les réservations sont confirmées au cas où notify n'est pas encore passé
            $paiement = \App\Models\Paiement::where('transaction_id', $transactionId)->first();
            if ($paiement && $paiement->status !== 'success') {
                $paiement->update(['status' => 'success']);
                foreach ($paiement->reservations as $reservation) {
                    if ($reservation->statut !== 'confirmee') {
                        $reservation->update(['statut' => 'confirmee']);
                        $this->finalizeReservation($reservation);
                    }
                }
            }

            return redirect()->route('reservation.index')
                ->with('success', 'Votre paiement a été validé avec succès ! Vos billets sont disponibles.');
        }

        return redirect()->route('reservation.index')
            ->with('info', 'Votre paiement est en cours de traitement ou a été annulé par vos soins.');
    }

}
