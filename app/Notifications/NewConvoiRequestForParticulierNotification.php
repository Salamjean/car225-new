<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewConvoiRequestForParticulierNotification extends Notification
{
    use Queueable;

    public $convoi;
    public $logoUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($convoi)
    {
        $this->convoi = $convoi;
        $this->logoUrl = asset('assetsPoster/assets/images/logo_car225.png');
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
        return (new MailMessage)
            ->subject('CAR 225: Nouvelle demande de convoi disponible !')
            ->from('contact@car225.com', 'CAR 225')
            ->view('emails.new_convoi_particulier', [
                'convoi' => $this->convoi,
                'logoUrl' => $this->logoUrl,
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
