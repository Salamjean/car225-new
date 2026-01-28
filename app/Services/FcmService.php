<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FcmService
{
    protected $projectId;
    protected $credentialsPath;

    public function __construct()
    {
        // Chemin vers le fichier de credentials Firebase (dans storage/app)
        $this->credentialsPath = storage_path('app/car225-54daf-firebase-adminsdk-fbsvc-53ea1a999f.json');
        
        // Récupérer le project_id depuis le fichier de credentials
        if (file_exists($this->credentialsPath)) {
            $credentials = json_decode(file_get_contents($this->credentialsPath), true);
            $this->projectId = $credentials['project_id'] ?? null;
        }
    }

    /**
     * Obtenir un token d'accès OAuth2 pour FCM v1 API
     */
    protected function getAccessToken()
    {
        if (!file_exists($this->credentialsPath)) {
            Log::error('FCM: Fichier de credentials Firebase non trouvé: ' . $this->credentialsPath);
            return null;
        }

        try {
            // Créer un client HTTP avec SSL désactivé pour Windows (dev uniquement)
            $httpClient = new \GuzzleHttp\Client([
                'verify' => false, // Désactiver la vérification SSL pour dev local
            ]);

            // Créer un handler callable à partir du client Guzzle
            $httpHandler = \Google\Auth\HttpHandler\HttpHandlerFactory::build($httpClient);

            $credentials = new ServiceAccountCredentials(
                'https://www.googleapis.com/auth/firebase.messaging',
                json_decode(file_get_contents($this->credentialsPath), true)
            );

            $token = $credentials->fetchAuthToken($httpHandler);
            return $token['access_token'] ?? null;

        } catch (\Exception $e) {
            Log::error('FCM: Erreur lors de l\'obtention du token OAuth2: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Envoyer une notification push à un appareil via FCM v1 API
     */
    public function sendNotification($fcmToken, $title, $body, $data = [])
    {
        if (!$this->projectId) {
            Log::error('FCM: Project ID non configuré. Vérifiez le fichier firebase-credentials.json');
            return [
                'success' => false,
                'message' => 'FCM non configuré. Fichier firebase-credentials.json manquant.',
            ];
        }

        if (!$fcmToken) {
            return [
                'success' => false,
                'message' => 'Token FCM manquant',
            ];
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return [
                'success' => false,
                'message' => 'Impossible d\'obtenir le token d\'accès OAuth2',
            ];
        }

        // Construire le payload pour FCM v1 API
        $payload = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'default_channel',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ],
                    ],
                ],
            ],
        ];

        // Ajouter les données personnalisées si fournies
        if (!empty($data)) {
            // FCM v1 requiert que toutes les valeurs data soient des strings
            $stringData = [];
            foreach ($data as $key => $value) {
                $stringData[$key] = is_string($value) ? $value : json_encode($value);
            }
            $payload['message']['data'] = $stringData;
        }

        try {
            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            $result = $response->json();

            Log::info('FCM v1 Response:', ['status' => $response->status(), 'body' => $result]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Notification envoyée avec succès',
                    'response' => $result,
                ];
            }

            return [
                'success' => false,
                'message' => 'Échec d\'envoi de la notification',
                'error' => $result['error']['message'] ?? 'Erreur inconnue',
                'response' => $result,
            ];

        } catch (\Exception $e) {
            Log::error('FCM v1 Erreur: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Envoyer une notification à plusieurs appareils
     * Note: FCM v1 ne supporte pas les envois multiples en une seule requête,
     * donc on envoie les notifications une par une
     */
    public function sendToMultiple(array $fcmTokens, $title, $body, $data = [])
    {
        if (!$this->projectId) {
            return [
                'success' => false,
                'message' => 'FCM non configuré',
            ];
        }

        $results = [];
        $successCount = 0;
        $failureCount = 0;

        foreach ($fcmTokens as $token) {
            $result = $this->sendNotification($token, $title, $body, $data);
            $results[] = $result;
            
            if ($result['success']) {
                $successCount++;
            } else {
                $failureCount++;
            }
        }

        return [
            'success' => $successCount > 0,
            'message' => "Envoyé: {$successCount}, Échoué: {$failureCount}",
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'results' => $results,
        ];
    }

    /**
     * Vérifier si FCM est correctement configuré
     */
    public function isConfigured()
    {
        return file_exists($this->credentialsPath) && $this->projectId !== null;
    }
}
