<?php

namespace App\Notifications;

use App\Models\Signalement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSignalementNotification extends Notification
{
    use Queueable;

    public $signalement;

    /**
     * Create a new notification instance.
     */
    public function __construct(Signalement $signalement)
    {
        $this->signalement = $signalement;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/sapeur-pompier/signalements/' . $this->signalement->id);

        return (new MailMessage)
            ->subject('URGENT: Signalement d\'accident')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Un accident a été signalé près de votre secteur.')
            ->line('Description: ' . $this->signalement->description)
            ->line('Localisation: ' . $this->signalement->latitude . ', ' . $this->signalement->longitude)
            ->action('Voir le détail', $url)
            ->line('Merci d\'intervenir rapidement.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'signalement_id' => $this->signalement->id,
            'type' => $this->signalement->type,
            'description' => $this->signalement->description,
            'latitude' => $this->signalement->latitude,
            'longitude' => $this->signalement->longitude,
            'user_id' => $this->signalement->user_id,
        ];
    }
}
