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
    protected $waveService;

    public function __construct(CinetPayService $cinetPayService, \App\Services\WaveService $waveService)
    {
        $this->cinetPayService = $cinetPayService;
        $this->waveService = $waveService;
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

        return view('user.wallet.index', compact('user', 'transactions'));
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
        
        // --- CALCUL COMMISSION (2% additionnel) ---
        $commissionRate = 2.00;
        $commissionAmount = round($amount * ($commissionRate / 100));
        $totalToPay = $amount + $commissionAmount;

        // La référence Wave doit être unique. Format: W-RECH-[TIMESTAMP]-[RANDOM]
        $transactionId = 'W-RECH-' . time() . '-' . Str::random(4);

        // Créer la transaction locale en attente
        $transaction = WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $amount, // Le montant que l'utilisateur recevra réellement
            'commission_amount' => $commissionAmount,
            'commission_rate' => $commissionRate,
            'type' => 'credit',
            'description' => 'Rechargement portefeuille via Wave',
            'reference' => $transactionId,
            'status' => 'pending',
            'payment_method' => 'wave',
            'metadata' => [
                'initiated_at' => now()->toDateTimeString(),
                'platform' => 'web',
                'total_paid' => $totalToPay
            ]
        ]);

        // Appel Wave Service avec URLs sécurisées (HTTPS obligatoire)
        $appUrl = rtrim(config('app.url'), '/');
        
        $successUrl = secure_url(route('wallet.wave.return', ['transaction_id' => $transactionId], false));
        $errorUrl = secure_url(route('wallet.wave.cancel', ['transaction_id' => $transactionId], false));

        $waveSession = $this->waveService->createCheckoutSession(
            $totalToPay,
            'XOF',
            $successUrl,
            $errorUrl,
            $transactionId
        );

        if ($waveSession && isset($waveSession['wave_launch_url'])) {
            $transaction->update([
                'external_transaction_id' => $waveSession['id'] ?? null,
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'wave_id' => $waveSession['id'] ?? null,
                    'wave_launch_url' => $waveSession['wave_launch_url'],
                ])
            ]);

            return response()->json([
                'message' => 'Initialisation réussie',
                'payment_url' => $waveSession['wave_launch_url'],
                'must_be_redirected' => true
            ]);
        } else {
            $transaction->update(['status' => 'failed']);
            return response()->json([
                'message' => 'Erreur lors de l\'initialisation du paiement Wave',
            ], 500);
        }
    }

    /**
     * Webhook Wave pour le rechargement du Wallet
     */
    public function waveNotify(Request $request)
    {
        $payload = $request->getContent();
        Log::info('Wave Wallet Webhook Received', ['payload' => $payload]);

        // Validation Signature (On réutilise la logique de PaymentController si possible ou on la duplique ici)
        // Pour simplifier, on va injecter ou appeler la validation
        if (!$this->validateWaveSignature($request)) {
             Log::warning('Wave Wallet Webhook: Signature invalide');
             return response()->json(['message' => 'Signature invalide'], 401);
        }

        $data = json_decode($payload, true);
        $eventType = $data['type'] ?? null;

        if ($eventType !== 'checkout.session.completed') {
            return response()->json(['message' => 'Ignored event'], 200);
        }

        $sessionData = $data['data'] ?? [];
        $clientReference = $sessionData['client_reference'] ?? null;

        if (!$clientReference) {
             return response()->json(['message' => 'Missing reference'], 200);
        }

        $transaction = WalletTransaction::where('reference', $clientReference)->first();

        if (!$transaction) {
             Log::error('Wave Wallet: Transaction non trouvée', ['ref' => $clientReference]);
             return response()->json(['message' => 'Not found'], 200);
        }

        if ($transaction->status === 'completed') {
             return response()->json(['message' => 'Already done'], 200);
        }

        // Succès ! On crédite le wallet
        DB::beginTransaction();
        try {
            $transaction = WalletTransaction::where('id', $transaction->id)->lockForUpdate()->first();
            
            if ($transaction->status !== 'completed') {
                $transaction->update([
                    'status' => 'completed',
                    'payment_date' => now(),
                    'metadata' => array_merge($transaction->metadata ?? [], ['wave_confirm' => $sessionData])
                ]);

                $user = $transaction->user;
                $user->solde += $transaction->amount;
                $user->save();

                // --- CRÉDITER LE PORTEFEUILLE ADMIN ---
                if ($transaction->commission_amount > 0) {
                    $admin = \App\Models\Admin::first(); // On prend le premier admin par défaut
                    if ($admin) {
                        $admin->increment('portefeuille', $transaction->commission_amount);
                        Log::info("Commission de {$transaction->commission_amount} ajoutée au portefeuille admin via recharge wallet.");
                    }
                }

                Log::info("Wave Wallet: Compte rechargé pour l'utilisateur {$user->id}. Montant: {$transaction->amount}");
            }
            DB::commit();
            return response()->json(['message' => 'Success'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Wave Wallet: Erreur crédit', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
     * Confirmation de signature Wave
     */
    protected function validateWaveSignature(Request $request)
    {
        $signature = $request->header('wave-signature');
        $payload = $request->getContent();
        $secret = config('services.wave.webhook_secret');

        if (!$signature || !$secret) return false;

        $parts = explode(',', $signature);
        $timestamp = null;
        $signatures = [];

        foreach ($parts as $part) {
            if (strpos($part, 't=') === 0) $timestamp = substr($part, 2);
            if (strpos($part, 'v1=') === 0) $signatures[] = substr($part, 3);
        }

        if (!$timestamp || empty($signatures)) return false;

        $signedPayload = $timestamp . $payload;
        $expectedSignature = hash_hmac('sha256', $signedPayload, $secret);

        return in_array($expectedSignature, $signatures);
    }

    /**
     * Retour de l'utilisateur après paiement Wave
     */
    public function waveReturn(Request $request)
    {
        return redirect()->route('user.wallet.index')
            ->with('success', 'Votre rechargement Wave est en cours de traitement. Votre solde sera mis à jour dans quelques instants.');
    }

    /**
     * Annulation de l'utilisateur Wave
     */
    public function waveCancel(Request $request)
    {
        return redirect()->route('user.wallet.index')
            ->with('error', 'Le rechargement Wave a été annulé.');
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
             return response()->json([
                 'message' => 'Transaction déjà validée',
                 'status' => 'success',
                 'new_balance' => Auth::user()->solde,
             ]);
        }

        // Vérification via CinetPayService (nouvelle API v1)
        $cinetpayTransactionId = $transaction->external_transaction_id ?? $transactionId;
        $paymentStatus = $this->cinetPayService->checkPaymentStatus($cinetpayTransactionId);
        
        Log::info('Verify Recharge Result:', ['id' => $transactionId, 'result' => $paymentStatus]);

        // Nouvelle API: le statut peut être SUCCESS, FAILED, INITIATED, PENDING
        $detailStatus = $paymentStatus['details']['status'] ?? $paymentStatus['data']['status'] ?? null;

        if ($detailStatus === 'SUCCESS' || $detailStatus === 'ACCEPTED') {
            return $this->creditWalletTransaction($transaction, $paymentStatus);
        } else {
            $status = 'failed';
            if (in_array($detailStatus, ['PENDING', 'INITIATED'])) {
                $status = 'pending';
            }
            
            $transaction->update(['status' => $status]);
            
            return response()->json([
                'message' => 'Paiement non validé ou en attente', 
                'status' => $status,
                'cinetpay_status' => $detailStatus ?? 'UNKNOWN',
            ]);
        }
    }

    /**
     * Pages de retour après paiement CinetPay (success/failed)
     */
    public function paymentSuccess(Request $request)
    {
        $transactionId = $request->transaction_id;
        return redirect()->route('user.wallet.index')
            ->with('success', 'Votre paiement est en cours de validation. Votre solde sera mis à jour automatiquement.');
    }

    public function paymentFailed(Request $request)
    {
        $transactionId = $request->transaction_id;
        
        // Marquer la transaction comme échouée
        $transaction = WalletTransaction::where('reference', $transactionId)->first();
        if ($transaction && $transaction->status === 'pending') {
            $transaction->update(['status' => 'failed']);
        }

        return redirect()->route('user.wallet.index')
            ->with('error', 'Le paiement a échoué ou a été annulé.');
    }

    /**
     * Créditer le wallet après vérification du paiement
     */
    protected function creditWalletTransaction($transaction, $paymentData = [])
    {
        DB::beginTransaction();
        try {
            $transaction = WalletTransaction::where('id', $transaction->id)->lockForUpdate()->first();

            if ($transaction->status !== 'completed') {
                $transaction->update([
                    'status' => 'completed',
                    'payment_method' => $paymentData['details']['payment_method'] ?? $paymentData['data']['payment_method'] ?? 'cinetpay',
                    'metadata' => array_merge($transaction->metadata ?? [], ['payment_response' => $paymentData]),
                ]);

                $user = $transaction->user;
                $user->solde += $transaction->amount;
                $user->save();

                // --- CRÉDITER LE PORTEFEUILLE ADMIN ---
                if ($transaction->commission_amount > 0) {
                    $admin = \App\Models\Admin::first();
                    if ($admin) {
                        $admin->increment('portefeuille', $transaction->commission_amount);
                        Log::info("Commission de {$transaction->commission_amount} ajoutée au portefeuille admin via rechargement wallet (verify).");
                    }
                }
                
                DB::commit();

                Log::info("Wallet user {$user->id} credited. Amount: {$transaction->amount}. New balance: {$user->solde}");

                return response()->json([
                    'message' => 'Rechargement confirmé avec succès',
                    'status' => 'success',
                    'new_balance' => $user->solde,
                ]);
            } else {
                DB::commit();
                return response()->json([
                    'message' => 'Transaction déjà validée',
                    'status' => 'success',
                    'new_balance' => $transaction->user->solde,
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing wallet credit', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Erreur lors de la mise à jour du solde'], 500);
        }
    }
    /**
     * Webhook CinetPay pour la notification de paiement (Nouvelle API v1)
     * CinetPay envoie: notify_token, merchant_transaction_id, transaction_id, user{}
     */
    public function notify(Request $request)
    {
        Log::info('CinetPay v1 Notification Received', $request->all());

        // Nouvelle API v1: utilise merchant_transaction_id
        // Fallback sur cpm_trans_id pour compatibilité ancien format
        $merchantTransactionId = $request->merchant_transaction_id ?? $request->cpm_trans_id;
        $notifyToken = $request->notify_token;
        $cinetpayTransactionId = $request->transaction_id;

        if (!$merchantTransactionId) {
            Log::warning('CinetPay Notification: Missing transaction ID');
            return response()->json(['message' => 'Transaction ID missing'], 400);
        }

        // 1. Essayer de trouver une transaction Wallet
        $transaction = WalletTransaction::where('reference', $merchantTransactionId)->first();

        // 2. Si pas de transaction Wallet, essayer de trouver un Paiement (Réservation)
        if (!$transaction) {
            $paiement = \App\Models\Paiement::where('transaction_id', $merchantTransactionId)->first();

            if ($paiement) {
                return $this->handleReservationPaymentNotification($paiement, $cinetpayTransactionId ?? $merchantTransactionId);
            }

            Log::error('Transaction not found for notify:', ['id' => $merchantTransactionId]);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Si déjà traité
        if ($transaction->status === 'completed') {
            Log::info('Transaction already completed:', ['id' => $merchantTransactionId]);
            return response()->json(['message' => 'Already processed'], 200);
        }

        // Valider le notify_token (sécurité)
        $storedNotifyToken = $transaction->metadata['notify_token'] ?? null;
        if ($storedNotifyToken && $notifyToken && $storedNotifyToken !== $notifyToken) {
            Log::warning('CinetPay Notification: notify_token mismatch', [
                'expected' => $storedNotifyToken,
                'received' => $notifyToken,
            ]);
            // On continue quand même pour ne pas bloquer un paiement légitime
        }

        // Vérifier le statut via l'API CinetPay
        $checkId = $cinetpayTransactionId ?? $transaction->external_transaction_id ?? $merchantTransactionId;
        $paymentStatus = $this->cinetPayService->checkPaymentStatus($checkId);

        $detailStatus = $paymentStatus['details']['status'] ?? $paymentStatus['data']['status'] ?? null;

        if ($detailStatus === 'SUCCESS' || $detailStatus === 'ACCEPTED') {
            DB::beginTransaction();
            try {
                $transaction = WalletTransaction::where('id', $transaction->id)->lockForUpdate()->first();

                if ($transaction->status !== 'completed') {
                    $transaction->update([
                        'status' => 'completed',
                        'external_transaction_id' => $cinetpayTransactionId,
                        'payment_method' => $paymentStatus['details']['payment_method'] ?? $paymentStatus['data']['payment_method'] ?? 'cinetpay',
                        'metadata' => array_merge($transaction->metadata ?? [], ['notify_response' => $paymentStatus]),
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
                                Log::info("Commission de {$transaction->commission_amount} ajoutée au portefeuille admin via rechargement wallet (notify).");
                            }
                        }

                        Log::info("Wallet user {$user->id} credited via notify. Amount: {$transaction->amount}. New balance: {$user->solde}");
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
            $status = 'failed';
            if (in_array($detailStatus, ['PENDING', 'INITIATED'])) {
                $status = 'pending';
            }
            $transaction->update(['status' => $status]);
            return response()->json(['message' => 'Payment ' . $status], 200);
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

                // --- AJOUT COMMISSION DANS LE PORTEFEUILLE ADMIN ---
                if ($paiement->commission_amount > 0) {
                    $admin = \App\Models\Admin::first();
                    if ($admin) {
                        $admin->increment('portefeuille', $paiement->commission_amount);
                        Log::info("Commission de {$paiement->commission_amount} FCFA ajoutée au portefeuille admin via notify reservation.");
                    }
                }

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

                    // DÉDUCTION TICKETS COMPAGNIE (Nouvelle logique ajoutée)
                    if ($res->programme && $res->programme->compagnie) {
                        // On déduit SEULEMENT le montant de la réservation (sans frais additionnels si présents au niveau paiement)
                        $res->programme->compagnie->deductTickets($res->montant, "Réservation #{$res->reference} (CinetPay)");
                    }

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

    /**
     * Page de résultat de paiement pour le wallet (Public)
     */
    public function paymentResult(Request $request)
    {
        $transactionId = $request->transactionId ?? $request->transaction_id;
        
        // On détermine le succès en fonction de plusieurs paramètres possibles selon la passerelle
        $success = true;
        
        if ($request->has('success') && $request->success === 'false') {
            $success = false;
        }
        
        if ($request->has('cancel') && $request->cancel == 1) {
            $success = false;
        }
        
        if ($request->has('status') && in_array($request->status, ['failed', 'cancel'])) {
            $success = false;
        }

        return view('user.wallet.payment_result', compact('transactionId', 'success'));
    }
}
