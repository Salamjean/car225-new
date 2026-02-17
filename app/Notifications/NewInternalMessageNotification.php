<?php

namespace App\Notifications;

use App\Models\CompanyMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewInternalMessageNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    protected $companyMessage;

    public function __construct(CompanyMessage $companyMessage)
    {
        $this->companyMessage = $companyMessage;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('CAR 225 : Nouveau message de la direction - ' . $this->companyMessage->subject)
            ->greeting('Bonjour ' . $notifiable->prenom . ',')
            ->line('Vous avez reçu une nouvelle communication importante de la direction de ' . $this->companyMessage->compagnie->name . '.')
            ->line('Sujet : ' . $this->companyMessage->subject)
            ->action('Consulter le message', $this->getLink($notifiable))
            ->line('Merci de rester attentif aux communications internes.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->companyMessage->id,
            'title' => 'Nouveau message : ' . $this->companyMessage->subject,
            'message' => 'Une nouvelle directive a été publiée par la direction.',
            'from' => $this->companyMessage->compagnie->name,
            'type' => 'internal_message',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Nouveau message 📩',
            'message' => $this->companyMessage->subject,
            'type' => 'internal_message',
            'count' => $this->getUnreadCount($notifiable),
        ]);
    }

    protected function getLink($notifiable)
    {
        if ($notifiable instanceof \App\Models\Caisse) {
            return route('caisse.messages.show', $this->companyMessage->id);
        } elseif ($notifiable instanceof \App\Models\Personnel) {
            return route('chauffeur.messages.show', $this->companyMessage->id);
        } elseif ($notifiable instanceof \App\Models\Agent) {
            return route('agent.messages.index'); // To be adjusted if agent has detail view
        }
        return url('/');
    }

    protected function getUnreadCount($notifiable)
    {
        return CompanyMessage::where('recipient_type', get_class($notifiable))
            ->where('recipient_id', $notifiable->id)
            ->where('is_read', false)
            ->count();
    }
}
