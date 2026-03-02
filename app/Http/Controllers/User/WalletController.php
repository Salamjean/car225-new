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

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'phone' => 'required|string',
            'network' => 'required|string', // Orange, MTN, Wave
        ]);

        $user = Auth::user();
        $amount = $request->amount;
        $phone = $request->phone;
        $network = $request->network;

        // 1. Vérifier le solde
        if ($user->solde < $amount) {
            return response()->json(['success' => false, 'message' => 'Solde insuffisant.'], 400);
        }

        DB::beginTransaction();
        try {
            // 2. Débiter le solde immédiatement (pour éviter double dépense)
            // On utilise lockForUpdate pour être sûr
            $user = \App\Models\User::where('id', $user->id)->lockForUpdate()->first();
            
            if ($user->solde < $amount) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Solde insuffisant.'], 400);
            }

            $user->solde -= $amount;
            $user->save();

            // 3. Créer la transaction locale
            $transactionId = 'W-OUT-' . time() . '-' . Str::random(5);
            
            $transaction = WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'debit',
                'description' => 'Retrait vers ' . $network . ' (' . $phone . ')',
                'reference' => $transactionId,
                'status' => 'pending', // En attente de conf CinetPay
                'payment_method' => 'cinetpay_transfer',
                'metadata' => [
                    'phone' => $phone,
                    'network' => $network,
                    'initiated_at' => now()->toDateTimeString(),
                ]
            ]);

            DB::commit();

            // 4. Appeler l'API de transfert CinetPay
            // Mappe le network vers les codes CinetPay si nécessaire
            // Selon l'exemple: WAVECI... pour l'instant on passe tel quel ou on mappe
            $paymentMethod = null;
            switch(strtoupper($network)) {
                case 'ORANGE': $paymentMethod = 'OMCI'; break;
                case 'MTN': $paymentMethod = 'MOMOCI'; break;
                case 'WAVE': $paymentMethod = 'WAVECI'; break;
                // Ajouter d'autres si besoin
                default: $paymentMethod = null; // CinetPay détectera peut-être par préfixe si null?
            }

            $transferData = [
                'prefix' => '225', // Par défaut
                'phone' => $phone,
                'amount' => $amount,
                'transaction_id' => $transactionId,
                'notify_url' => route('wallet.notify.transfer'), // Route spécifique pour le transfert
                'payment_method' => $paymentMethod
            ];

            // On fait l'appel APRES le commit pour débloquer la DB, 
            // mais si l'API échoue, il faudra rembourser.
            $result = $this->cinetPayService->transfer($transferData);

            if ($result['success']) {
                // Succès de l'initiation
                $transaction->update([
                    'status' => 'pending', // Reste pending jusqu'à confirmation webhook ou check
                    'external_transaction_id' => $result['data']['transaction_id'] ?? null,
                    'metadata' => array_merge($transaction->metadata ?? [], ['api_response' => $result['data']])
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Retrait initié avec succès. Vous recevrez une notification une fois validé.',
                    'new_balance' => $user->solde
                ]);
            } else {
                // Echec de l'initiation -> Remboursement
                Log::error("Echec init transfert pour {$transactionId}, remboursement...");
                
                $user->solde += $amount;
                $user->save();

                $transaction->update([
                    'status' => 'failed',
                    'metadata' => array_merge($transaction->metadata ?? [], ['error' => $result['message']])
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Echec du transfert: ' . $result['message']
                ], 500);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur retrait wallet: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
        }
    }

    /**
     * Webhook spécifique pour les transferts (Retraits)
     * Note: CinetPay envoie parfois sur la même URL de notif que le paiement, 
     * mais l'API Transfert demande une notify_url spécifique dans le payload.
     */
    public function notifyTransfer(Request $request)
    {
        Log::info('CinetPay Transfer Notification Received', $request->all());

        // Les champs retournés par CinetPay Transfert peuvent différer du checkout
        // Exemple doc: client_transaction_id, transaction_id, lot, amount, etc.
        
        $clientTransactionId = $request->client_transaction_id ?? $request->cpm_trans_id; // adapter selon retour réel

        if (!$clientTransactionId) {
             return response()->json(['message' => 'Transaction ID missing'], 400);
        }

        $transaction = WalletTransaction::where('reference', $clientTransactionId)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($transaction->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 200);
        }

        // Vérifier le statut envoyé
        // Doc example response success: "treatment_status": "NEW" ? Non, ça c'est la réponse synchrone.
        // La notif asynchrone devrait contenir le statut final (VALIDATED, FAILED ?)
        // On va devoir logger pour voir le format exact en prod, car la doc est légère là dessus.
        // Mais supposons un champ 'status' ou 'treatment_status'

        // Pour l'instant, on log et on return 200.
        // Idéalement on met à jour le statut si on a les infos.
        
        // Si CinetPay envoie treatment_status
        $status = $request->treatment_status ?? $request->status;

        if ($status == 'VALIDATED' || $status == 'SUCCESS') {
            $transaction->update(['status' => 'completed']);
        } elseif ($status == 'FAILED' || $status == 'REJECTED') {
            // Rembourser l'utilisateur
            $user = $transaction->user;
             $user->solde += $transaction->amount;
             $user->save();
             
             $transaction->update(['status' => 'failed']);
        }

        return response()->json(['message' => 'Received'], 200);
    }

    public function index()
    {
        $user = Auth::user();
        $transactions = $user->walletTransactions()->orderBy('created_at', 'desc')->take(5)->get();
        
        // Configuration CinetPay pour le frontend (si nécessaire à l'initialisation)
        $cinetpay_site_id = config('services.cinetpay.site_id');
        $cinetpay_api_key = config('services.cinetpay.api_key'); // Attention à ne pas exposer si sensible, mais site_id est public
        // En mode seamless, la clé publique/site_id est souvent requise.

        return view('user.wallet.index', compact('user', 'transactions', 'cinetpay_site_id'));
    }

    /**
     * Historique des rechargements de l'utilisateur (type credit uniquement)
     * avec recherche et filtre, pages de 10.
     */
    public function rechargeHistory(Request $request)
    {
        $user = Auth::user();

        $query = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->latest();

        // Recherche texte (référence ou méthode de paiement)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('payment_method', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtre par statut
        if ($request->filled('statut') && in_array($request->statut, ['pending', 'completed', 'failed', 'cancelled'])) {
            $query->where('status', $request->statut);
        }

        // Filtre par période
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $transactions = $query->paginate(10)->withQueryString();

        // Statistiques rapides
        $totalRecharge = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('status', 'completed')
            ->sum('amount');

        $totalCount = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->count();

        $pendingCount = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('status', 'pending')
            ->count();

        return view('user.wallet.recharges', compact(
            'user', 'transactions', 'totalRecharge', 'totalCount', 'pendingCount'
        ));
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
