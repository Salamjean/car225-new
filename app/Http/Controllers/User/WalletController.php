<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Services\CinetPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    protected $cinetPayService;

    public function __construct(CinetPayService $cinetPayService)
    {
        $this->cinetPayService = $cinetPayService;
    }

    public function index()
    {
        $user = Auth::user();
        $transactions = $user->walletTransactions()->orderBy('created_at', 'desc')->paginate(5);
        
        // Configuration CinetPay pour le frontend (si nécessaire à l'initialisation)
        $cinetpay_site_id = config('services.cinetpay.site_id');
        $cinetpay_api_key = config('services.cinetpay.api_key'); // Attention à ne pas exposer si sensible, mais site_id est public
        // En mode seamless, la clé publique/site_id est souvent requise.

        return view('user.wallet.index', compact('user', 'transactions', 'cinetpay_site_id'));
    }

    public function recharge(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        $user = Auth::user();
        $amount = $request->amount;
        $transactionId = 'W-RECH-' . time() . '-' . Str::random(5);

        // Créer la transaction locale en attente
        $transaction = WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'credit',
            'description' => 'Rechargement portefeuille',
            'reference' => $transactionId,
            'status' => 'pending',
            'payment_method' => 'cinetpay',
            'metadata' => [
                'initiated_at' => now()->toDateTimeString(),
            ]
        ]);

        // Préparer les données pour le SDK CinetPay (Frontend)
        $checkoutData = [
            'transaction_id' => $transactionId,
            'amount' => (int) $amount,
            'currency' => 'XOF',
            'description' => 'Rechargement Compte ' . config('app.name'),
            'customer_name' => $user->name ?? 'Client',
            'customer_surname' => $user->prenom ?? 'Client', // Adapter selon modèle User
            'customer_email' => $user->email,
            'customer_phone_number' => $user->contact ?? '0000000000', // Adapter selon modèle User (contact ou phone)
            'customer_address' => $user->adresse ?? 'Abidjan',
            'customer_city' => $user->adresse ?? 'Abidjan',
            'customer_country' => 'CI',
            'customer_state' => 'CI',
            'customer_zip_code' => '00225',
        ];

        return response()->json([
            'message' => 'Initialisation réussie',
            'checkout_data' => $checkoutData,
            'cinetpay_config' => [
                'apikey' => config('services.cinetpay.api_key'),
                'site_id' => config('services.cinetpay.site_id'),
                'notify_url' => route('cinetpay.notify'), // Route existante ou à créer pour webhook global
                'mode' => app()->environment('local') ? 'TEST' : 'PRODUCTION'
            ]
        ]);
    }

    public function verifyRecharge(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required',
        ]);

        $transactionId = $request->transaction_id;
        $transaction = WalletTransaction::where('reference', $transactionId)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction introuvable'], 404);
        }

        if ($transaction->status === 'completed') {
             return response()->json(['message' => 'Transaction déjà validée', 'new_balance' => Auth::user()->solde]);
        }

        // Vérification via CinetPayService
        $paymentStatus = $this->cinetPayService->checkPaymentStatus($transactionId);
        
        // Log pour debug
        Log::info('Verify Recharge Result:', ['id' => $transactionId, 'result' => $paymentStatus]);

        if ($paymentStatus && isset($paymentStatus['data']['status']) && $paymentStatus['data']['status'] === 'ACCEPTED') {
            
            DB::beginTransaction();
            try {
                // Verrouiller la transaction pour éviter double traitement
                $transaction = WalletTransaction::where('id', $transaction->id)->lockForUpdate()->first();

                if ($transaction->status !== 'completed') {
                    // Update Transaction
                    $transaction->update([
                        'status' => 'completed',
                        'external_transaction_id' => $paymentStatus['data']['operator_id'] ?? null,
                        'payment_method' => $paymentStatus['data']['payment_method'] ?? 'cinetpay',
                        'metadata' => array_merge($transaction->metadata ?? [], ['payment_response' => $paymentStatus['data']])
                    ]);

                    // Credit User Wallet
                    $user = $transaction->user;
                    $user->solde += $transaction->amount;
                    $user->save();
                    
                    DB::commit();

                    return response()->json([
                        'message' => 'Rechargement confirmé avec succès', 
                        'status' => 'success',
                        'new_balance' => $user->solde
                    ]);
                } else {
                    DB::commit();
                    return response()->json(['message' => 'Transaction déjà validée', 'status' => 'success', 'new_balance' => $transaction->user->solde]);
                }

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing wallet credit', ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Erreur lors de la mise à jour du solde'], 500);
            }

        } else {
             // Si échoué ou annulé
             $status = 'failed';
             if (isset($paymentStatus['data']['status']) && $paymentStatus['data']['status'] === 'PENDING') {
                 $status = 'pending';
             }
             
             $transaction->update(['status' => $status]);
             
             return response()->json([
                 'message' => 'Paiement non validé ou en attente', 
                 'status' => $status,
                 'cinetpay_status' => $paymentStatus['data']['status'] ?? 'UNKNOWN'
             ]);
        }
    }
    /**
     * Webhook CinetPay pour la notification de rechargement
     */
    public function notify(Request $request)
    {
        Log::info('CinetPay Wallet Notification Received', $request->all());

        // Validation basique
        if (!$request->has('cpm_trans_id')) {
             return response()->json(['message' => 'Transaction ID missing'], 400);
        }

        $transactionId = $request->cpm_trans_id;
        
        // 1. Essayer de trouver une transaction Wallet
        $transaction = WalletTransaction::where('reference', $transactionId)->first();

        // 2. Si pas de transaction Wallet, essayer de trouver un Paiement (Réservation CinetPay)
        if (!$transaction) {
            // Check Paiement with prefix fallback logic if needed, but usually exact match
            // Sometimes CinetPay appends timestamp, check if splitting needed
            $reference = explode('_', $transactionId)[0];
            $paiement = \App\Models\Paiement::where('transaction_id', $reference)->first();

            if ($paiement) {
                return $this->handleReservationPaymentNotification($paiement, $transactionId);
            }

            Log::error('Transaction nor Paiement not found for notify:', ['id' => $transactionId]);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Si déjà traité
        if ($transaction->status === 'completed') {
            Log::info('Transaction already completed:', ['id' => $transactionId]);
            return response()->json(['message' => 'Already processed'], 200);
        }

        // Vérification API CinetPay
        $paymentStatus = $this->cinetPayService->checkPaymentStatus($transactionId);

        if ($paymentStatus && isset($paymentStatus['data']['status']) && $paymentStatus['data']['status'] === 'ACCEPTED') {
            
            DB::beginTransaction();
            try {
                // Lock
                $transaction = WalletTransaction::where('id', $transaction->id)->lockForUpdate()->first();

                if ($transaction->status !== 'completed') {
                    $transaction->update([
                        'status' => 'completed',
                        'external_transaction_id' => $paymentStatus['data']['operator_id'] ?? null,
                        'payment_method' => $paymentStatus['data']['payment_method'] ?? 'cinetpay',
                        'metadata' => array_merge($transaction->metadata ?? [], ['notify_response' => $paymentStatus['data']])
                    ]);

                    // Créditer le wallet de l'utilisateur (ATTENTION: ne pas utiliser Auth::user())
                    $user = $transaction->user;
                    if ($user) {
                        $user->solde += $transaction->amount;
                        $user->save();
                        Log::info("Wallet user {$user->id} credited via notify. Amount: {$transaction->amount}");
                    } else {
                        Log::error("User not found for transaction {$transaction->id}");
                    }

                    DB::commit();
                } else {
                    DB::commit();
                }
                
                return response()->json(['message' => 'Success'], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing notify wallet credit', ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Error processing'], 500);
            }
        } else {
            // Mise à jour statut échec/attente
            $status = 'failed';
            if (isset($paymentStatus['data']['status']) && $paymentStatus['data']['status'] === 'PENDING') {
                $status = 'pending';
            }
            $transaction->update(['status' => $status]);
            return response()->json(['message' => 'Payment failed or pending'], 200);
        }
    }

    /**
     * Gérer la notification pour un Paiement de Réservation (non-Wallet)
     */
    protected function handleReservationPaymentNotification($paiement, $cinetpayTransactionId)
    {
        if ($paiement->status === 'success') {
             return response()->json(['message' => 'Already processed'], 200);
        }

        $paymentStatus = $this->cinetPayService->checkPaymentStatus($cinetpayTransactionId);

        if ($paymentStatus && isset($paymentStatus['data']['status']) && $paymentStatus['data']['status'] === 'ACCEPTED') {
            DB::beginTransaction();
            try {
                $paiement->update([
                    'status' => 'success',
                    'payment_date' => now(),
                    'payment_details' => $paymentStatus['data']
                ]);

                $reservations = \App\Models\Reservation::where('paiement_id', $paiement->id)->get();
                
                // Instancier ReservationController pour utiliser ses méthodes helpers (QR, Email)
                // C'est un hack pour éviter de dupliquer la logique complexe du QR Code
                $resController = app(\App\Http\Controllers\User\Reservation\ReservationController::class);

                foreach ($reservations as $res) {
                    $res->update([
                        'statut' => 'confirmee',
                        'statut_aller' => 'confirmee',
                        'statut_retour' => ($res->is_aller_retour) ? 'confirmee' : $res->statut_retour
                    ]);

                    // Générer QR Code et sauvegarder
                    try {
                        $qrResult = $resController->generateAndSaveQRCode(
                            $res->reference, 
                            $res->id, 
                            $res->date_voyage, 
                            $res->user_id
                        );
                        
                        $res->update([
                            'qr_code' => $qrResult['base64'], // Pour compatibilité
                            'qr_code_base64' => $qrResult['base64'],
                            'qr_code_path' => $qrResult['path'],
                            'qr_code_data' => $qrResult['qr_content']
                        ]);

                        $programme = $res->programme;
                        
                        // Envoi Email
                        // Check if sendReservationEmail exists/public
                        $resController->sendReservationEmail(
                            $res,
                            $programme,
                            $qrResult['base64'],
                            $res->passager_email,
                            $res->passager_prenom . ' ' . $res->passager_nom,
                            $res->seat_number,
                            ($res->is_aller_retour) ? 'ALLER-RETOUR' : 'ALLER SIMPLE',
                            null, 
                            $res->programmeRetour
                        );

                    } catch (\Exception $e) {
                        Log::error("Erreur post-traitement réservation {$res->id}: " . $e->getMessage());
                    }
                }

                DB::commit();
                Log::info("Paiement Reservation {$paiement->id} confirmé via WalletController::notify");
                return response()->json(['message' => 'Success'], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing notify reservation', ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Error processing'], 500);
            }
        } else {
             $paiement->update(['status' => 'failed']);
             return response()->json(['message' => 'Payment failed'], 200);
        }
    }
}
