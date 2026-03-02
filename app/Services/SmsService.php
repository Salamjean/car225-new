<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SmsService
{
    protected string $apiUrl;
    protected string $apiKey;
    protected string $senderId;

    public function __construct()
    {
        $this->apiUrl = rtrim(config('services.yellika.api_url', env('YELLIKA_API_URL', 'http://app.1smsafrica.com/api/v3/')), '/');
        $this->apiKey = config('services.yellika.api_key', env('YELLIKA_API_KEY', ''));
        $this->senderId = config('services.yellika.sender_id', env('YELLIKA_SENDER_ID', 'Plateau app'));
    }

    /**
     * Envoyer un SMS via l'API 1SMS Africa
     */
    public function sendSms(string $to, string $message): bool
    {
        try {
            // Formater le numéro au format international ivoirien si nécessaire
            $to = $this->formatPhoneNumber($to);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/sms/send', [
                'recipient' => $to,
                'sender_id' => $this->senderId,
                'message' => $message,
            ]);

            $body = $response->json();
            $responseMessage = $body['message'] ?? '';

            // L'API 1SMS Africa retourne 403 avec "En attente" quand le SMS est mis en file d'attente
            // C'est un comportement normal — le SMS est bien envoyé
            $isQueued = str_contains($responseMessage, 'En attente');

            if ($response->successful() || $isQueued) {
                Log::info('SMS envoyé avec succès', [
                    'to' => $to,
                    'status' => $response->status(),
                    'response' => $body,
                ]);
                return true;
            }

            Log::error('Échec envoi SMS', [
                'to' => $to,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS: ' . $e->getMessage(), [
                'to' => $to,
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Générer un code OTP à 6 chiffres
     */
    public function generateOtpCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Stocker et envoyer un OTP par SMS
     */
    public function sendOtp(string $contact, string $prenom = '', string $nom = ''): array
    {
        $code = $this->generateOtpCode();

        // Supprimer les anciens OTP pour ce contact
        DB::table('user_otp_codes')
            ->where('contact', $contact)
            ->delete();

        // Stocker le nouvel OTP (expire dans 10 minutes)
        DB::table('user_otp_codes')->insert([
            'contact' => $contact,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
            'verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Composer un message SMS professionnel
        $greeting = '';
        if ($prenom || $nom) {
            $fullName = trim("$prenom $nom");
            $greeting = "Bonjour {$fullName}, ";
        }

        $message = "{$greeting}votre code de verification Car225 est : {$code}. Ce code expire dans 10 minutes. Ne partagez ce code avec personne. Si vous n'etes pas a l'origine de cette demande, ignorez ce message.";
        $sent = $this->sendSms($contact, $message);

        return [
            'success' => $sent,
            'code' => $code, // Utile pour le debug en dev, à retirer en prod
        ];
    }

    /**
     * Vérifier un code OTP
     */
    public function verifyOtp(string $contact, string $code): bool
    {
        $otp = DB::table('user_otp_codes')
            ->where('contact', $contact)
            ->where('code', $code)
            ->where('verified', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($otp) {
            DB::table('user_otp_codes')
                ->where('id', $otp->id)
                ->update(['verified' => true, 'updated_at' => now()]);

            return true;
        }

        return false;
    }

    /**
     * Supprimer les OTP pour un contact
     */
    public function deleteOtp(string $contact): void
    {
        DB::table('user_otp_codes')
            ->where('contact', $contact)
            ->delete();
    }

    /**
     * Formater le numéro de téléphone au format international (+225)
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Supprimer les espaces et caractères spéciaux
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Si le numéro commence déjà par +225, c'est bon
        if (str_starts_with($phone, '+225')) {
            return $phone;
        }

        // Si le numéro commence par 225 (sans +), ajouter le +
        if (str_starts_with($phone, '225')) {
            return '+' . $phone;
        }

        // Sinon, ajouter +225 devant le numéro complet (garder le 0)
        return '+225' . $phone;
    }
}
