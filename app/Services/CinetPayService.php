<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CinetPayService
{
    protected $apiKey;
    protected $siteId;
    protected $baseUrl = 'https://api-checkout.cinetpay.com/v2/payment';

    public function __construct()
    {
        $this->apiKey = config('services.cinetpay.api_key');
        $this->siteId = config('services.cinetpay.site_id');
    }

    public function initiatePayment($data)
    {
        try {
            Log::info('Initiating CinetPay payment', ['transaction_id' => $data['transaction_id']]);

            $payload = [
                'apikey' => $this->apiKey,
                'site_id' => $this->siteId,
                'transaction_id' => $data['transaction_id'],
                'amount' => (int) $data['amount'],
                'currency' => 'XOF',
                'description' => $data['description'] ?? 'Réservation Car225',
                'customer_id' => (string) $data['customer_id'],
                'customer_name' => $data['customer_name'],
                'customer_surname' => $data['customer_surname'],
                'customer_email' => $data['customer_email'],
                'customer_phone_number' => $data['customer_phone_number'],
                'customer_address' => 'Abidjan',
                'customer_city' => 'Abidjan',
                'customer_country' => 'CI',
                'customer_state' => 'CI',
                'customer_zip_code' => '00225',
                'notify_url' => route('payment.notify'),
                'return_url' => route('payment.return', ['transaction_id' => $data['transaction_id']]),
                'channels' => 'ALL',
                'lang' => 'fr',
            ];

            $request = Http::asJson();

            // Si on est en local, on peut ignorer la vérification SSL si le certificat CA n'est pas configuré
            if (app()->environment('local')) {
                $request->withoutVerifying();
            }

            $response = $request->post($this->baseUrl, $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('CinetPay API Result:', ['result' => $result]);

                if (isset($result['code']) && $result['code'] == '201' && isset($result['data'])) {
                    return [
                        'success' => true,
                        'payment_url' => $result['data']['payment_url'],
                        'payment_token' => $result['data']['payment_token']
                    ];
                }

                Log::error('CinetPay API Error', ['response' => $result]);
                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Erreur lors de l\'initialisation du paiement'
                ];
            }

            Log::error('CinetPay HTTP Error', ['status' => $response->status(), 'body' => $response->body()]);
            return [
                'success' => false,
                'message' => 'Erreur de connexion avec le service de paiement'
            ];

        } catch (\Exception $e) {
            Log::error('CinetPay Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors du traitement du paiement'
            ];
        }
    }

    public function checkPaymentStatus($transactionId)
    {
        try {
            $payload = [
                'apikey' => $this->apiKey,
                'site_id' => $this->siteId,
                'transaction_id' => $transactionId
            ];

            $request = Http::asJson();

            if (app()->environment('local')) {
                $request->withoutVerifying();
            }

            $response = $request->post('https://api-checkout.cinetpay.com/v2/payment/check', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('CinetPay Check Status Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // =============================================
    // TRANSFERT D'ARGENT (RETRAIT) - API CinetPay
    // =============================================
    // Documentation: https://docs.cinetpay.com/api/1.0-fr/transfert/utilisation
    // Étapes: 1) Login → 2) Ajouter contact → 3) Envoyer l'argent

    /**
     * Étape 1 : Authentification pour obtenir un token (valide 5 min)
     * POST https://client.cinetpay.com/v1/auth/login (x-www-form-urlencoded)
     */
    public function login()
    {
        try {
            $formData = [
                'apikey' => $this->apiKey,
                'password' => config('services.cinetpay.transfer_password', config('services.cinetpay.secret_key'))
            ];

            Log::info('CinetPay Transfer Login attempt');

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->asForm()
                ->post('https://client.cinetpay.com/v1/auth/login', $formData);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('CinetPay Login Response', ['code' => $result['code'] ?? 'N/A', 'message' => $result['message'] ?? 'N/A']);
                
                if (isset($result['code']) && ($result['code'] === 0 || $result['code'] === '0')) {
                    return $result['data']['token'];
                }
                Log::error('CinetPay Login Error', ['response' => $result]);
            } else {
                Log::error('CinetPay Login HTTP Error', ['status' => $response->status(), 'body' => $response->body()]);
            }
            return null;
        } catch (\Exception $e) {
            Log::error('CinetPay Login Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Étape 2 : Ajouter un numéro comme contact CinetPay
     * POST https://client.cinetpay.com/v1/transfer/contact?token=TOKEN
     * Le numéro doit être dans vos contacts avant de pouvoir envoyer de l'argent.
     * Si le contact existe déjà, CinetPay retourne quand même un succès (code 726 / ERROR_PHONE_ALREADY_MY_CONTACT)
     */
    public function addContact($token, $data)
    {
        try {
            $payload = [
                [
                    'prefix' => $data['prefix'] ?? '225',
                    'phone' => $data['phone'],
                    'name' => $data['name'] ?? 'Client',
                    'surname' => $data['surname'] ?? 'CAR225',
                    'email' => $data['email'] ?? 'client@car225.com',
                ]
            ];

            $url = 'https://client.cinetpay.com/v1/transfer/contact?token=' . $token;

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->asJson()
                ->post($url, $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('CinetPay AddContact Result', ['result' => $result]);

                // Code 0 = succès, le contact existe déjà (726) est aussi considéré comme OK
                if (isset($result['code']) && ($result['code'] === 0 || $result['code'] === '0')) {
                    return ['success' => true, 'data' => $result['data'] ?? []];
                }

                return ['success' => false, 'message' => $result['message'] ?? 'Erreur ajout contact'];
            }

            Log::error('CinetPay AddContact HTTP Error', ['status' => $response->status(), 'body' => $response->body()]);
            return ['success' => false, 'message' => 'Erreur HTTP lors de l\'ajout du contact'];
        } catch (\Exception $e) {
            Log::error('CinetPay AddContact Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Exception lors de l\'ajout du contact'];
        }
    }

    /**
     * Étape 3 : Envoyer de l'argent à un contact
     * POST https://client.cinetpay.com/v1/transfer/money/send/contact?token=TOKEN
     * Le payment_method est optionnel (ex: WAVECI, OMCI, MOMOCI)
     */
    public function sendMoney($token, $data)
    {
        try {
            $payload = [
                [
                    'prefix' => $data['prefix'] ?? '225',
                    'phone' => $data['phone'],
                    'amount' => (int) $data['amount'],
                    'client_transaction_id' => $data['transaction_id'],
                    'notify_url' => $data['notify_url'],
                ]
            ];

            // Ajouter payment_method si spécifié
            if (!empty($data['payment_method'])) {
                $payload[0]['payment_method'] = $data['payment_method'];
            }

            $url = 'https://client.cinetpay.com/v1/transfer/money/send/contact?token=' . $token;

            Log::info('CinetPay SendMoney Request', ['url' => $url, 'payload' => $payload]);

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->asJson()
                ->post($url, $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('CinetPay SendMoney Result', ['result' => $result]);

                if (isset($result['code']) && ($result['code'] === 0 || $result['code'] === '0')) {
                    // data est un tableau de résultats, on prend le premier
                    $transferData = $result['data'][0] ?? null;

                    if ($transferData && isset($transferData['status']) && $transferData['status'] === 'success') {
                        return [
                            'success' => true,
                            'data' => $transferData,
                            'message' => 'Transfert initié avec succès'
                        ];
                    }
                }

                return [
                    'success' => false,
                    'message' => $result['message'] ?? $result['description'] ?? 'Erreur lors du transfert'
                ];
            }

            Log::error('CinetPay SendMoney HTTP Error', ['status' => $response->status(), 'body' => $response->body()]);
            return [
                'success' => false,
                'message' => 'Erreur de communication avec le service de transfert (HTTP ' . $response->status() . ')'
            ];
        } catch (\Exception $e) {
            Log::error('CinetPay SendMoney Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors du transfert'
            ];
        }
    }

    /**
     * Méthode principale : Orchestrer le transfert complet (Login → Contact → Envoi)
     */
    public function transfer($data)
    {
        // === Étape 1 : Login ===
        $token = $this->login();
        if (!$token) {
            return [
                'success' => false,
                'message' => 'Échec de l\'authentification avec le service de paiement'
            ];
        }

        // === Étape 2 : Ajouter le contact ===
        $contactResult = $this->addContact($token, $data);
        if (!$contactResult['success']) {
            return [
                'success' => false,
                'message' => 'Échec de l\'ajout du contact: ' . ($contactResult['message'] ?? 'Erreur inconnue')
            ];
        }

        // === Étape 3 : Envoyer l'argent ===
        return $this->sendMoney($token, $data);
    }

    /**
     * Vérifier le statut d'un transfert
     * POST https://client.cinetpay.com/v1/transfer/check/money?token=TOKEN
     * Champs importants dans la réponse:
     *   - treatment_status: VAL (validé), NEW (nouveau), PENDING, REJECTED
     *   - sending_status: CONFIRM (confirmé par mail) / PENDING (pas encore confirmé)
     */
    public function checkTransferStatus($clientTransactionId)
    {
        $token = $this->login();
        if (!$token) return null;

        try {
            $url = 'https://client.cinetpay.com/v1/transfer/check/money?token=' . $token;

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->asJson()
                ->post($url, [
                    'client_transaction_id' => $clientTransactionId
                ]);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('CinetPay CheckTransfer Result', ['result' => $result]);

                if (isset($result['code']) && ($result['code'] === 0 || $result['code'] === '0')) {
                    return $result['data'][0] ?? $result['data'] ?? null;
                }
            }

            Log::error('CinetPay CheckTransfer Error', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('CinetPay CheckTransfer Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Obtenir le solde du compte de transfert CinetPay
     * GET https://client.cinetpay.com/v1/transfer/check/balance?token=TOKEN
     */
    public function getTransferBalance()
    {
        $token = $this->login();
        if (!$token) return null;

        try {
            $url = 'https://client.cinetpay.com/v1/transfer/check/balance?token=' . $token;

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->get($url);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('CinetPay Balance Result', ['result' => $result]);
                
                if (isset($result['code']) && ($result['code'] === 0 || $result['code'] === '0')) {
                    return $result['data'] ?? null;
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('CinetPay Balance Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
}

