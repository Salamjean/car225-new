<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Signalement;

class SendSignalementToSapeurPompierNotification extends Notification
{
    use Queueable;

    public $signalement;

    public function __construct(Signalement $signalement)
    {
        $this->signalement = $signalement;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('URGENCE: Nouvel accident signalé - CAR 225')
            ->from('contact@maelysimo.com', 'CAR 225 Support')
            ->view('emails.signalement_sapeur_pompier', [
                'signalement' => $this->signalement,
                'programme' => $this->signalement->programme,
                'user' => $this->signalement->user,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
