<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Email envoyé à un agent ONPC fraîchement créé. Contient l'OTP qui lui
 * permet de définir son mot de passe avant la première connexion.
 */
class SendEmailToOnpcAfterRegistrationNotification extends Notification
{
    use Queueable;

    public string $code;
    public string $email;
    public string $logoUrl;

    public function __construct(string $codeToSend, string $sendToEmail)
    {
        $this->code = $codeToSend;
        $this->email = $sendToEmail;
        $this->logoUrl = asset('assetsPoster/assets/images/logo_car225.png');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('CAR 225 : Compte ONPC créé')
            ->from('contact@maelysimo.com', 'CAR 225')
            ->view('emails.onpc_registration', [
                'code' => $this->code,
                'email' => $this->email,
                'logoUrl' => $this->logoUrl,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
