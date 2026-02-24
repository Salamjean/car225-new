<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportReplyNotification extends Notification
{
    use Queueable;

    public $supportRequest;
    public $reponseTexte;

    /**
     * Create a new notification instance.
     */
    public function __construct($supportRequest, $reponseTexte)
    {
        $this->supportRequest = $supportRequest;
        $this->reponseTexte = $reponseTexte;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        $mail = (new MailMessage)
            ->subject('Réponse à votre signalement : ' . \Illuminate\Support\Str::limit($this->supportRequest->objet, 40))
            ->greeting('Bonjour,')
            ->line('Vous recevez ce message car notre équipe a traité votre signalement concernant : "'. $this->supportRequest->objet .'".')
            ->line('***')
            ->line('Voici la réponse de notre équipe de support :');

        $lignes = explode("\n", $this->reponseTexte);
        foreach($lignes as $ligne) {
            if (trim($ligne) !== '') {
                $mail->line(trim($ligne));
            }
        }

        $mail->line('***')
            ->line('Nous vous remercions de nous avoir contactés pour nous aider à améliorer nos services.')
            ->salutation('Cordialement, L\'équipe Support CAR 225.');
            
        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
