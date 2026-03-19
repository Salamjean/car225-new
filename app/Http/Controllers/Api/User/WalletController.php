<?php

namespace App\Http\Controllers\Api\User;

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
    protected $waveService;

    public function __construct(CinetPayService $cinetPayService, \App\Services\WaveService $waveService)
    {
        $this->cinetPayService = $cinetPayService;
        $this->waveService = $waveService;
    }

    /**
     * Obtenir les informations du portefeuille (solde et transactions)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $transactions = $user->walletTransactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'solde' => $user->solde,
                'currency' => 'FCFA',
                'transactions' => $transactions
            ]
        ]);
    }

    /**
     * Initier un rechargement de portefeuille via CinetPay
     */
    public function recharge(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'payment_method' => 'nullable|string|in:cinetpay,wave'
        ]);

        $user = $request->user();
        $amount = $request->amount;
        $paymentMethod = $request->input('payment_method', 'cinetpay');
        
        // --- CALCUL COMMISSION (4% additionnel) ---
        $commissionRate = 4.00;
        $commissionAmount = round($amount * ($commissionRate / 100));
        $totalToPay = $amount + $commissionAmount;

        $transactionId = 'W-RECH-' . time() . '-' . Str::random(5);

        // Créer la transaction locale en attente
        $transaction = WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'commission_amount' => $commissionAmount,
            'commission_rate' => $commissionRate,
            'type' => 'credit',
            'description' => 'Rechargement portefeuille',
            'reference' => $transactionId,
            'status' => 'pending',
            'payment_method' => $paymentMethod,
            'metadata' => [
                'initiated_at' => now()->toDateTimeString(),
                'source' => 'mobile_api',
                'total_paid' => $totalToPay
            ]
        ]);

        if ($paymentMethod === 'wave') {
            return $this->processWaveRecharge($transaction);
        }

        // Par défaut: CinetPay
        $paymentLinkResult = $this->generateCinetPayLink($transaction, 'Rechargement Compte ' . config('app.name'));

        if (!$paymentLinkResult['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur initialisation CinetPay',
                'error_details' => $paymentLinkResult['error_details']
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Initialisation du rechargement réussie',
            'payment_details' => $paymentLinkResult['cinetpay_data'],
            'transaction_id' => $transactionId
        ]);
    }

    /**
     * Génère un lien de paiement CinetPay (Adapté pour Wallet)
     */
    private function generateCinetPayLink(WalletTransaction $transaction, $description)
    {
        try {
            $user = $transaction->user;
            $baseUrl = config('app.url'); // ou une URL spécifique pour les deep links
            $reference = $transaction->reference;
            
            // Deep links et URLs (A ajuster selon vos besoins deep link vs web)
            // Pour le wallet, on utilise car225://payment avec type=wallet pour la cohérence
            $returnUrl = "car225://payment?success=true&transactionId={$reference}&type=wallet&method=cinetpay";
            $cancelUrl = "car225://payment?success=false&transactionId={$reference}&type=wallet&method=cinetpay";
            
            // Fallback Web - Utilise la nouvelle route publique (Forcé en HTTPS pour Wave/CinetPay)
            $fallbackReturnUrl = secure_url(route('wallet.payment.result', ['transactionId' => $reference, 'success' => 'true'], false));
            $fallbackCancelUrl = secure_url(route('wallet.payment.result', ['transactionId' => $reference, 'success' => 'false', 'cancel' => 1], false));
            
            // Webhook URL (API)
            $notifyUrl = $baseUrl . "/api/user/wallet/notify";

            $cinetpayApiKey = config('services.cinetpay.api_key');
            $cinetpaySiteId = config('services.cinetpay.site_id');
            $cinetpayTransactionId = $reference; // On garde la ref transaction comme ID CinetPay (ou suffixé si besoin)

            $paymentData = [
                'apikey' => $cinetpayApiKey,
                'site_id' => $cinetpaySiteId,
                'transaction_id' => $cinetpayTransactionId,
                'amount' => (int)($transaction->amount + $transaction->commission_amount),
                'currency' => 'XOF', // Ou dynamique si stocké
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
                'customer_zip_code' => '00225',
                'metadata' => json_encode([
                    'return_url_deep_link' => $returnUrl,
                    'cancel_url_deep_link' => $cancelUrl
                ])
            ];

            Log::info('Appel CinetPay API (Wallet):', ['transaction_id' => $cinetpayTransactionId]);

            // Appel direct API CinetPay
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->post('https://api-checkout.cinetpay.com/v2/payment', $paymentData);

            if ($response->failed() || $response->json('code') !== '201') {
                Log::error('Erreur CinetPay Wallet (API): ' . $response->body());
                return [
                    'success' => false,
                    'error_details' => $response->json() ?? $response->body()
                ];
            }

            $responseData = $response->json('data');
            
             // Sauvegarder le token si besoin dans metadata
             $transaction->update([
                 'metadata' => array_merge($transaction->metadata ?? [], ['payment_token' => $responseData['payment_token'] ?? null])
             ]);

            return [
                'success' => true,
                'cinetpay_data' => $responseData,
                'generated_transaction_id' => $cinetpayTransactionId,
            ];

        } catch (\Exception $e) {
            Log::error('Exception generateCinetPayLink (Wallet): ' . $e->getMessage());
            return [
                'success' => false,
                'error_details' => $e->getMessage()
            ];
        }
    }

    /**
     * Webhook CinetPay pour la notification de rechargement (API)
     */
    public function notify(Request $request)
    {
        Log::info('API Wallet Notification Received', $request->all());

        if (!$request->has('cpm_trans_id')) {
             return response()->json(['message' => 'Transaction ID missing'], 400);
        }

        $transactionId = $request->cpm_trans_id;
        $transaction = WalletTransaction::where('reference', $transactionId)->first();

        if (!$transaction) {
            Log::error('API Wallet Transaction not found:', ['id' => $transactionId]);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($transaction->status === 'completed') {
            return response()->json(['message' => 'Already processed'], 200);
        }

        // Vérification
        $paymentStatus = $this->cinetPayService->checkPaymentStatus($transactionId);

        if ($paymentStatus && isset($paymentStatus['data']['status']) && $paymentStatus['data']['status'] === 'ACCEPTED') {
            
            DB::beginTransaction();
            try {
                $transaction = WalletTransaction::where('id', $transaction->id)->lockForUpdate()->first();

                if ($transaction->status !== 'completed') {
                    $transaction->update([
                        'status' => 'completed',
                        'external_transaction_id' => $paymentStatus['data']['operator_id'] ?? null,
                        'payment_method' => $paymentStatus['data']['payment_method'] ?? 'cinetpay',
                        'metadata' => array_merge($transaction->metadata ?? [], ['notify_response' => $paymentStatus['data']])
                    ]);

                    $user = $transaction->user;
                    if ($user) {
                        $user->solde += $transaction->amount;
                        $user->save();

                        // --- CRÉDITER LE PORTEFEUILLE ADMIN ---
                        if ($transaction->commission_amount > 0) {
                            $admin = \App\Models\Admin::first();
                            if ($admin) {
                                $admin->increment('portefeuille', $transaction->commission_amount);
                                Log::info("Commission de {$transaction->commission_amount} ajoutée au portefeuille admin via API notify recharge.");
                            }
                        }

                        // Notification Database + Broadcast
                        $user->notify(new \App\Notifications\GeneralNotification(
                            'Portefeuille rechargé ✅',
                            "Votre compte a été crédité de {$transaction->amount} FCFA.",
                            'success'
                        ));
                    }

                    DB::commit();
                } else {
                    DB::commit();
                }
                
                return response()->json(['message' => 'Success'], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('API Wallet Notify Error', ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Error processing'], 500);
            }
        } else {
            $status = 'failed';
            if (isset($paymentStatus['data']['status']) && $paymentStatus['data']['status'] === 'PENDING') {
                $status = 'pending';
            }
            $transaction->update(['status' => $status]);
            return response()->json(['message' => 'Payment failed or pending'], 200);
        }
    }

    /**
     * Vérifier le statut d'un rechargement
     */
    public function verify(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required',
        ]);

        $transactionId = $request->transaction_id;
        $transaction = WalletTransaction::where('reference', $transactionId)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction introuvable'
            ], 404);
        }

        if ($transaction->status === 'completed') {
            return response()->json([
                'success' => true,
                'message' => 'Transaction déjà validée',
                'new_balance' => $request->user()->fresh()->solde
            ]);
        }

        // Vérification via CinetPayService
        $paymentStatus = $this->cinetPayService->checkPaymentStatus($transactionId);

        if ($paymentStatus && isset($paymentStatus['data']['status']) && $paymentStatus['data']['status'] === 'ACCEPTED') {
            
            DB::beginTransaction();
            try {
                $transaction = WalletTransaction::where('id', $transaction->id)->lockForUpdate()->first();

                if ($transaction->status !== 'completed') {
                    $transaction->update([
                        'status' => 'completed',
                        'external_transaction_id' => $paymentStatus['data']['operator_id'] ?? null,
                        'payment_method' => $paymentStatus['data']['payment_method'] ?? 'cinetpay',
                        'metadata' => array_merge($transaction->metadata ?? [], ['payment_response' => $paymentStatus['data']])
                    ]);

                    $user = $transaction->user;
                    $user->solde += $transaction->amount;
                    $user->save();
                    
                    // --- CRÉDITER LE PORTEFEUILLE ADMIN ---
                    if ($transaction->commission_amount > 0) {
                        $admin = \App\Models\Admin::first();
                        if ($admin) {
                            $admin->increment('portefeuille', $transaction->commission_amount);
                            Log::info("Commission de {$transaction->commission_amount} ajoutée au portefeuille admin via API verify recharge.");
                        }
                    }

                    // Notification Database + Broadcast
                    $user->notify(new \App\Notifications\GeneralNotification(
                        'Portefeuille rechargé ✅',
                        "Votre compte a été crédité de {$transaction->amount} FCFA.",
                        'success'
                    ));
                    
                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'Rechargement confirmé avec succès', 
                        'new_balance' => $user->solde
                    ]);
                } else {
                    DB::commit();
                    return response()->json([
                        'success' => true, 
                        'message' => 'Transaction déjà validée', 
                        'new_balance' => $transaction->user->solde
                    ]);
                }

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing wallet credit API', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour du solde'
                ], 500);
            }

        } else {
             $status = 'failed';
             $cinetpayStatus = $paymentStatus['data']['status'] ?? 'UNKNOWN';

             if ($cinetpayStatus === 'PENDING') {
                 $status = 'pending';
             }
             
             $transaction->update(['status' => $status]);
             
             return response()->json([
                 'success' => false,
                 'message' => 'Paiement non validé ou en attente', 
                 'status' => $status,
                 'cinetpay_status' => $cinetpayStatus
             ]);
        }
    }

    /**
     * Traiter le rechargement via Wave
     */
    private function processWaveRecharge(WalletTransaction $transaction)
    {
        try {
            $baseUrl = config('app.url');
            
            // URLs de retour (utilisant la route publique pour mobile/web) - Toujours en HTTPS pour Wave
            $successUrl = secure_url(route('wallet.payment.result', ['transactionId' => $transaction->reference, 'success' => 'true'], false));
            $errorUrl = secure_url(route('wallet.payment.result', ['transactionId' => $transaction->reference, 'success' => 'false'], false));

            $session = $this->waveService->createCheckoutSession(
                $transaction->amount + $transaction->commission_amount,
                'XOF',
                $successUrl,
                $errorUrl,
                $transaction->reference
            );

            if (!$session || !isset($session['wave_launch_url'])) {
                Log::error('Erreur Wave Wallet Initialisation', ['session' => $session]);
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur initialisation Wave'
                ], 500);
            }

            // Mettre à jour avec l'ID de session Wave
            $transaction->update([
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'wave_id' => $session['id'] ?? null,
                    'wave_launch_url' => $session['wave_launch_url']
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Initialisation Wave réussie',
                'payment_url' => $session['wave_launch_url'],
                'transaction_id' => $transaction->reference,
                'payment_details' => [
                    'checkout_url' => $session['wave_launch_url'],
                    'wave_id' => $session['id'] ?? null,
                    'return_url_deep_link' => "car225://payment?success=true&transactionId={$transaction->reference}&type=wallet&method=wave",
                    'cancel_url_deep_link' => "car225://payment?success=false&transactionId={$transaction->reference}&type=wallet&method=wave",
                    'return_url_web_fallback' => $successUrl,
                    'cancel_url_web_fallback' => $errorUrl,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Exception processWaveRecharge: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur technique Wave'
            ], 500);
        }
    }
}
