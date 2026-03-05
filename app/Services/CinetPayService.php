<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CinetPayService
{
    protected $apiKey;
    protected $apiPassword;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.cinetpay.api_key');
        $this->apiPassword = config('services.cinetpay.api_password');
        $this->baseUrl = config('services.cinetpay.base_url', 'https://api.cinetpay.net');
    }

    // =============================================
    // AUTHENTIFICATION OAuth (Nouvelle API v1)
    // =============================================

    /**
     * Obtenir un access_token via OAuth
     * POST {baseUrl}/v1/oauth/login
     * Le token est valide 24h (86400s), on le cache pour éviter de se reconnecter à chaque requête.
     */
    public function getAccessToken()
    {
        // Vérifier le cache d'abord
        $cachedToken = Cache::get('cinetpay_access_token');
        if ($cachedToken) {
            return $cachedToken;
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->asJson()
                ->post("{$this->baseUrl}/v1/oauth/login", [
                    'api_key' => $this->apiKey,
                    'api_password' => $this->apiPassword,
                ]);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('CinetPay OAuth Login Response', [
                    'code' => $result['code'] ?? 'N/A',
                    'status' => $result['status'] ?? 'N/A',
                ]);

                if (isset($result['code']) && $result['code'] == 200 && isset($result['access_token'])) {
                    $token = $result['access_token'];
                    $expiresIn = $result['expires_in'] ?? 86400;

                    // Cacher le token (avec une marge de 5 minutes)
                    Cache::put('cinetpay_access_token', $token, $expiresIn - 300);

                    return $token;
                }

                Log::error('CinetPay OAuth Error', ['response' => $result]);
            } else {
                Log::error('CinetPay OAuth HTTP Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('CinetPay OAuth Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // =============================================
    // API DE PAIEMENT WEB (Nouvelle API v1)
    // =============================================

    /**
     * Initialiser une transaction de paiement web
     * POST {baseUrl}/v1/payment
     * Requiert un Bearer token obtenu via getAccessToken()
     *
     * Retourne: payment_url, payment_token, notify_token, transaction_id, details
     */
    public function initiatePayment($data)
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Échec de l\'authentification avec CinetPay. Vérifiez votre API Key et API Password.',
                ];
            }

            Log::info('Initiating CinetPay v1 payment', [
                'merchant_transaction_id' => $data['merchant_transaction_id'],
            ]);

            $payload = [
                'currency' => $data['currency'] ?? 'XOF',
                'merchant_transaction_id' => $data['merchant_transaction_id'],
                'amount' => (int) $data['amount'],
                'lang' => $data['lang'] ?? 'fr',
                'designation' => $data['designation'] ?? 'Paiement Car225',
                'client_email' => $data['client_email'],
                'client_first_name' => $data['client_first_name'],
                'client_last_name' => $data['client_last_name'],
                'success_url' => $data['success_url'],
                'failed_url' => $data['failed_url'],
                'notify_url' => $data['notify_url'],
            ];

            // Champs optionnels
            if (!empty($data['client_phone_number'])) {
                $payload['client_phone_number'] = $data['client_phone_number'];
            }
            if (!empty($data['payment_method'])) {
                $payload['payment_method'] = $data['payment_method'];
            }
            if (isset($data['direct_pay'])) {
                $payload['direct_pay'] = $data['direct_pay'];
            }
            if (!empty($data['otp_code'])) {
                $payload['otp_code'] = $data['otp_code'];
            }

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withToken($token)
                ->asJson()
                ->post("{$this->baseUrl}/v1/payment", $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('CinetPay v1 Payment Result', ['result' => $result]);

                if (isset($result['code']) && $result['code'] == 200) {
                    return [
                        'success' => true,
                        'payment_url' => $result['payment_url'] ?? null,
                        'payment_token' => $result['payment_token'] ?? null,
                        'notify_token' => $result['notify_token'] ?? null,
                        'transaction_id' => $result['transaction_id'] ?? null,
                        'merchant_transaction_id' => $result['merchant_transaction_id'] ?? null,
                        'details' => $result['details'] ?? null,
                    ];
                }

                Log::error('CinetPay v1 Payment Error', ['response' => $result]);
                return [
                    'success' => false,
                    'message' => $result['details']['message'] ?? $result['message'] ?? 'Erreur lors de l\'initialisation du paiement',
                ];
            }

            Log::error('CinetPay v1 HTTP Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [
                'success' => false,
                'message' => 'Erreur de connexion avec le service de paiement (HTTP ' . $response->status() . ')',
            ];
        } catch (\Exception $e) {
            Log::error('CinetPay v1 Payment Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors du traitement du paiement',
            ];
        }
    }

    /**
     * Vérifier le statut d'un paiement
     * Utilisé après la notification ou pour vérification manuelle
     * Note: La nouvelle API utilise notify_token pour valider l'authenticité
     */
    public function checkPaymentStatus($transactionId)
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return null;
            }

            // Nouvelle API: GET ou POST pour vérifier le statut
            // On essaie avec l'endpoint de vérification
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withToken($token)
                ->asJson()
                ->post("{$this->baseUrl}/v1/payment/check", [
                    'transaction_id' => $transactionId,
                ]);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('CinetPay v1 Check Payment Result', ['result' => $result]);
                return $result;
            }

            Log::error('CinetPay v1 Check Status Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('CinetPay v1 Check Status Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // =============================================
    // TRANSFERT D'ARGENT (RETRAIT) - API CinetPay
    // =============================================
    // Note: L'API de transfert peut avoir évolué aussi.
    // Pour l'instant on garde la logique existante avec l'ancien endpoint
    // car la doc transfert n'a pas été fournie dans le nouveau format.

    /**
     * Étape 1 : Authentification pour transfert (peut utiliser le nouveau OAuth)
     */
    public function login()
    {
        try {
            // Essayer d'abord avec le nouveau OAuth
            $token = $this->getAccessToken();
            if ($token) {
                return $token;
            }

            // Fallback: ancien endpoint de login pour transfert
            $formData = [
                'apikey' => $this->apiKey,
                'password' => config('services.cinetpay.transfer_password', config('services.cinetpay.secret_key')),
            ];

            Log::info('CinetPay Transfer Login attempt (fallback)');

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->asForm()
                ->post('https://client.cinetpay.com/v1/auth/login', $formData);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['code']) && ($result['code'] === 0 || $result['code'] === '0')) {
                    return $result['data']['token'];
                }
                Log::error('CinetPay Transfer Login Error', ['response' => $result]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('CinetPay Login Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Étape 2 : Ajouter un numéro comme contact CinetPay
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
                ],
            ];

            $url = 'https://client.cinetpay.com/v1/transfer/contact?token=' . $token;

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->asJson()
                ->post($url, $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('CinetPay AddContact Result', ['result' => $result]);

                if (isset($result['code']) && ($result['code'] === 0 || $result['code'] === '0')) {
                    return ['success' => true, 'data' => $result['data'] ?? []];
                }

                return ['success' => false, 'message' => $result['message'] ?? 'Erreur ajout contact'];
            }

            return ['success' => false, 'message' => 'Erreur HTTP lors de l\'ajout du contact'];
        } catch (\Exception $e) {
            Log::error('CinetPay AddContact Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Exception lors de l\'ajout du contact'];
        }
    }

    /**
     * Étape 3 : Envoyer de l'argent à un contact
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
                ],
            ];

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
                    $transferData = $result['data'][0] ?? null;

                    if ($transferData && isset($transferData['status']) && $transferData['status'] === 'success') {
                        return [
                            'success' => true,
                            'data' => $transferData,
                            'message' => 'Transfert initié avec succès',
                        ];
                    }
                }

                return [
                    'success' => false,
                    'message' => $result['message'] ?? $result['description'] ?? 'Erreur lors du transfert',
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur de communication avec le service de transfert (HTTP ' . $response->status() . ')',
            ];
        } catch (\Exception $e) {
            Log::error('CinetPay SendMoney Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors du transfert',
            ];
        }
    }

    /**
     * Méthode principale : Orchestrer le transfert complet (Login → Contact → Envoi)
     */
    public function transfer($data)
    {
        $token = $this->login();
        if (!$token) {
            return [
                'success' => false,
                'message' => 'Échec de l\'authentification avec le service de paiement',
            ];
        }

        $contactResult = $this->addContact($token, $data);
        if (!$contactResult['success']) {
            return [
                'success' => false,
                'message' => 'Échec de l\'ajout du contact: ' . ($contactResult['message'] ?? 'Erreur inconnue'),
            ];
        }

        return $this->sendMoney($token, $data);
    }

    /**
     * Vérifier le statut d'un transfert
     */
    public function checkTransferStatus($clientTransactionId)
    {
        $token = $this->login();
        if (!$token) {
            return null;
        }

        try {
            $url = 'https://client.cinetpay.com/v1/transfer/check/money?token=' . $token;

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->asJson()
                ->post($url, [
                    'client_transaction_id' => $clientTransactionId,
                ]);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('CinetPay CheckTransfer Result', ['result' => $result]);

                if (isset($result['code']) && ($result['code'] === 0 || $result['code'] === '0')) {
                    return $result['data'][0] ?? $result['data'] ?? null;
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('CinetPay CheckTransfer Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Obtenir le solde du compte de transfert CinetPay
     */
    public function getTransferBalance()
    {
        $token = $this->login();
        if (!$token) {
            return null;
        }

        try {
            $url = 'https://client.cinetpay.com/v1/transfer/check/balance?token=' . $token;

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->get($url);

            if ($response->successful()) {
                $result = $response->json();
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
