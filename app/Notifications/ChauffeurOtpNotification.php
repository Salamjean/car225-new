<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChauffeurOtpNotification extends Notification
{
    use Queueable;

    public $otp;
    public $chauffeurName;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $otp, string $chauffeurName)
    {
        $this->otp = $otp;
        $this->chauffeurName = $chauffeurName;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Code de vérification - CAR225')
            ->greeting('Bonjour ' . $this->chauffeurName . ',')
            ->line('Bienvenue chez CAR225 ! Votre compte chauffeur a été créé avec succès.')
            ->line('Voici votre code de vérification pour activer votre compte :')
            ->line('**Code OTP : ' . $this->otp . '**')
            ->line('Ce code est valide pendant 10 minutes.')
            ->line('Veuillez utiliser ce code pour vérifier votre adresse email et accéder à votre espace chauffeur.')
            ->action('Vérifier mon compte', url('/chauffeur/verify-otp?email=' . urlencode($notifiable->email)))
            ->line('Si vous n\'avez pas demandé ce code, veuillez ignorer cet email.')
            ->salutation('Cordialement, L\'équipe CAR225');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'otp' => $this->otp,
            'chauffeur_name' => $this->chauffeurName,
        ];
    }
}
