<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\CinetPayService;
use Illuminate\Http\Request;
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
        // On va réutiliser la logique de ReservationController
        // Idéalement cela devrait être dans un Service
        $resController = new \App\Http\Controllers\User\Reservation\ReservationController();

        // Puisque ces méthodes sont privées/protégées, je vais devoir les rendre publiques 
        // ou déplacer la logique ici. Pour aller plus vite, je vais appeler les méthodes 
        // si je les rends publiques dans ReservationController.

        // Alternative : Dupliquer temporairement ou mieux, utiliser une méthode statique si possible.
        // Je vais rendre les méthodes publiques dans ReservationController.

        try {
            $dateVoyageStr = $reservation->date_voyage instanceof \Carbon\Carbon
                ? $reservation->date_voyage->format('Y-m-d')
                : date('Y-m-d', strtotime($reservation->date_voyage));

            // Utiliser reflection ou simplement appeler si public
            $qrCodeData = $resController->generateAndSaveQRCode(
                $reservation->reference,
                $reservation->id,
                $dateVoyageStr,
                $reservation->user_id
            );

            $reservation->update([
                'qr_code' => $qrCodeData['base64'],
                'qr_code_path' => $qrCodeData['path'],
                'qr_code_data' => $qrCodeData['qr_data'],
                'statut_aller' => 'confirmee', // Initialiser le statut aller
            ]);

            $programmeRetour = null;

            // Mettre à jour le statut retour si c'est un aller-retour
            if ($reservation->is_aller_retour) {
                $reservation->update([
                    'statut_retour' => 'confirmee', 
                ]);  

                // Essayer de trouver le programme retour
                $programmeRetour = $reservation->programmeRetour ?? 
                                  ($reservation->programme_retour_id ? Programme::find($reservation->programme_retour_id) : null);
                
                // Si toujours pas de programme retour (cas rare), on peut imaginer un fallback ou laisser null
                // La notification gérera le cas null pour le PDF
            }

            // ENVOYER UN SEUL EMAIL AVEC LES DEUX PIECES JOINTES SI BESOIN
            // On passe le MEME QR code ($qrCodeData['base64']) pour l'aller et le retour
            $resController->sendReservationEmail(
                $reservation,
                $reservation->programme,
                $qrCodeData['base64'], // QR Code Aller
                $reservation->passager_email,
                $reservation->getPassagerNomCompletAttribute(),
                $reservation->seat_number,
                $reservation->is_aller_retour ? 'ALLER' : 'ALLER SIMPLE',
                $reservation->is_aller_retour ? $qrCodeData['base64'] : null, // Même QR Code pour le retour
                $programmeRetour
            );

            // Mettre à jour le statut du programme
            $resController->updateProgramStatus(
                $reservation->programme,
                $dateVoyageStr
            );
        } catch (\Exception $e) {
            Log::error('Erreur lors de la finalisation de la réservation:', ['error' => $e->getMessage()]);
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
