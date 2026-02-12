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
}
