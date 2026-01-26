<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected $serverKey;

    public function __construct()
    {
        // Clé serveur Firebase (à configurer dans .env)
        $this->serverKey = config('services.fcm.server_key');
    }

    /**
     * Envoyer une notification push à un appareil
     */
    public function sendNotification($fcmToken, $title, $body, $data = [])
    {
        if (!$this->serverKey) {
            Log::error('FCM Server Key non configurée');
            return [
                'success' => false,
                'message' => 'FCM non configuré',
            ];
        }

        if (!$fcmToken) {
            return [
                'success' => false,
                'message' => 'Token FCM manquant',
            ];
        }

        $payload = [
            'to' => $fcmToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => $data,
            'priority' => 'high',
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            $result = $response->json();

            Log::info('FCM Response:', $result);

            if ($response->successful() && isset($result['success']) && $result['success'] > 0) {
                return [
                    'success' => true,
                    'message' => 'Notification envoyée avec succès',
                    'response' => $result,
                ];
            }

            return [
                'success' => false,
                'message' => 'Échec d\'envoi de la notification',
                'response' => $result,
            ];

        } catch (\Exception $e) {
            Log::error('Erreur FCM: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Envoyer une notification à plusieurs appareils
     */
    public function sendToMultiple(array $fcmTokens, $title, $body, $data = [])
    {
        if (!$this->serverKey) {
            return [
                'success' => false,
                'message' => 'FCM non configuré',
            ];
        }

        $payload = [
            'registration_ids' => $fcmTokens,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => $data,
            'priority' => 'high',
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            return [
                'success' => $response->successful(),
                'response' => $response->json(),
            ];

        } catch (\Exception $e) {
            Log::error('Erreur FCM Multiple: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
