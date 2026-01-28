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
        $transactions = $user->walletTransactions()->orderBy('created_at', 'desc')->paginate(10);
        
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
        $transaction = WalletTransaction::where('reference', $transactionId)->first();

        if (!$transaction) {
            Log::error('Wallet Transaction not found for notify:', ['id' => $transactionId]);
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
}
