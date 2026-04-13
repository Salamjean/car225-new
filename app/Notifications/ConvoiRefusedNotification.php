<?php

namespace App\Notifications;

use App\Models\Convoi;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConvoiRefusedNotification extends Notification
{
    use Queueable;

    public function __construct(public Convoi $convoi) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('❌ Votre demande de convoi a été refusée — ' . $this->convoi->reference)
            ->greeting('Bonjour ' . ($notifiable->prenom ?? $notifiable->name) . ',')
            ->line('Nous vous informons que votre demande de convoi **' . $this->convoi->reference . '** auprès de la compagnie **' . $this->convoi->compagnie->name . '** a été refusée.')
            ->line('**Motif :** ' . $this->convoi->motif_refus)
            ->line('Vous pouvez effectuer une nouvelle demande auprès d\'une autre compagnie.')
            ->action('Nouvelle demande de convoi', url('/user/convoi'))
            ->salutation('Cordialement, l\'équipe CAR225');
    }
}
