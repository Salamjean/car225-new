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

            // Convertir le message en ASCII pur (pas d'accents/Unicode)
            // L'API 1SMS Africa ne supporte pas les SMS Unicode
            $message = $this->stripAccents($message);

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

    /**
     * Supprimer les accents et caractères Unicode d'un texte
     * Nécessaire car l'API 1SMS Africa ne supporte pas les SMS Unicode
     */
    protected function stripAccents(string $text): string
    {
        $search  = ['à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ',
                    'À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý',
                    '→','←','✅','❌','📱','🔒','ø','Ø','œ','Œ'];
        $replace = ['a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','u','u','u','u','y','y',
                    'A','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','O','O','U','U','U','U','Y',
                    '->','<-','','','','','o','O','oe','OE'];

        $text = str_replace($search, $replace, $text);

        // Supprimer tout caractère non-ASCII restant (emojis, symboles spéciaux)
        $text = preg_replace('/[^\x20-\x7E\n]/', '', $text);

        return $text;
    }

    /**
     * Envoyer des SMS de confirmation de reservation
     * Envoie 2 SMS separes pour les aller-retour (1 pour aller, 1 pour retour)
     */
    public function sendReservationSms(
        array $reservations,
        $programme,
        $user,
        bool $isAllerRetour = false,
        ?string $dateRetour = null
    ): bool {
        try {
            // Determiner le numero du destinataire
            $phone = $user->contact ?? null;
            if (!$phone && !empty($reservations)) {
                $phone = $reservations[0]->passager_telephone ?? null;
            }

            if (!$phone) {
                Log::warning('SMS Reservation: aucun numero de telephone disponible', [
                    'user_id' => $user->id ?? null,
                ]);
                return false;
            }

            // Nom du client
            $clientName = trim(($user->prenom ?? '') . ' ' . ($user->name ?? ''));
            if (empty($clientName) && !empty($reservations)) {
                $clientName = trim(($reservations[0]->passager_prenom ?? '') . ' ' . ($reservations[0]->passager_nom ?? ''));
            }

            // Charger les infos gare (nom_gare est le champ du modele Gare)
            $gareDepart = '';
            $gareArrivee = '';
            $gareDepartId = $reservations[0]->gare_depart_id ?? ($programme->gare_depart_id ?? null);
            $gareArriveeId = $reservations[0]->gare_arrivee_id ?? ($programme->gare_arrivee_id ?? null);

            if ($gareDepartId) {
                $gare = \App\Models\Gare::find($gareDepartId);
                $gareDepart = $gare ? $gare->nom_gare : '';
            }
            if ($gareArriveeId) {
                $gare = \App\Models\Gare::find($gareArriveeId);
                $gareArrivee = $gare ? $gare->nom_gare : '';
            }

            // Heure de depart
            $heureDepart = $programme->heure_depart ? substr($programme->heure_depart, 0, 5) : 'N/A';

            // Separer les reservations aller et retour par reference
            $allerReservations = [];
            $retourReservations = [];

            foreach ($reservations as $reservation) {
                if (str_contains(strtoupper($reservation->reference ?? ''), '-RET-')) {
                    $retourReservations[] = $reservation;
                } else {
                    $allerReservations[] = $reservation;
                }
            }

            $result = true;

            // ======== SMS ALLER ========
            if (!empty($allerReservations)) {
                $allerSeats = [];
                $allerRefs = [];
                foreach ($allerReservations as $res) {
                    if ($res->seat_number) $allerSeats[] = $res->seat_number;
                    if ($res->reference && !in_array($res->reference, $allerRefs)) {
                        $allerRefs[] = $res->reference;
                    }
                }

                $firstAller = $allerReservations[0];
                $dateAller = $firstAller->date_voyage ?? null;
                $dateAllerFormatted = $dateAller ? date('d/m/Y', strtotime($dateAller)) : 'N/A';
                // Utiliser l'heure de la réservation si dispo, sinon le programme
                $hAller = ($firstAller->heure_depart) ? substr($firstAller->heure_depart, 0, 5) : $heureDepart;

                $msg = "Bonjour {$clientName}, votre reservation sur Car225 est confirmee !\n";
                $msg .= "Ref: " . ($allerRefs[0] ?? 'N/A') . "\n";
                $msg .= "Trajet: {$programme->point_depart} -> {$programme->point_arrive}\n";

                if ($gareDepart || $gareArrivee) {
                    $msg .= "Gare: {$gareDepart} -> {$gareArrivee}\n";
                }

                $msg .= "Date: {$dateAllerFormatted} a {$hAller}\n";
                $msg .= "Siege(s): " . implode(', ', $allerSeats) . "\n";
                $msg .= "Type: ALLER" . ($isAllerRetour ? " (Aller-Retour)" : " Simple") . "\n";
                $msg .= "Bon voyage avec Car225 !";

                $result = $this->sendSms($phone, $msg) && $result;
            }

            // ======== SMS RETOUR (separe) ========
            if (!empty($retourReservations) && $isAllerRetour) {
                $retourSeats = [];
                $retourRefs = [];
                foreach ($retourReservations as $res) {
                    if ($res->seat_number) $retourSeats[] = $res->seat_number;
                    if ($res->reference && !in_array($res->reference, $retourRefs)) {
                        $retourRefs[] = $res->reference;
                    }
                }

                $firstRetour = $retourReservations[0];
                $dateRetourVal = $firstRetour->date_voyage ?? $dateRetour;
                $dateRetourFormatted = $dateRetourVal ? date('d/m/Y', strtotime($dateRetourVal)) : 'N/A';
                // Pour le retour, l'heure peut être différente
                $hRetour = ($firstRetour->heure_depart) ? substr($firstRetour->heure_depart, 0, 5) : $heureDepart;

                $msg = "Bonjour {$clientName}, voici votre billet RETOUR Car225 !\n";
                $msg .= "Ref: " . ($retourRefs[0] ?? 'N/A') . "\n";
                // Le retour est dans le sens inverse
                $msg .= "Trajet: {$programme->point_arrive} -> {$programme->point_depart}\n";

                if ($gareDepart || $gareArrivee) {
                    // Gares inversees pour le retour
                    $msg .= "Gare: {$gareArrivee} -> {$gareDepart}\n";
                }

                $msg .= "Date: {$dateRetourFormatted} a {$hRetour}\n";
                $msg .= "Siege(s): " . implode(', ', $retourSeats) . "\n";
                $msg .= "Type: RETOUR\n";
                $msg .= "Bon voyage avec Car225 !";

                $result = $this->sendSms($phone, $msg) && $result;
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Erreur SMS Reservation: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
            ]);
            return false;
        }
    }
}
