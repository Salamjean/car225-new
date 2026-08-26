<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ParticulierRegistrationApprovedNotification extends Notification
{
    use Queueable;

    public $code_id;
    public $password;
    public $email;
    public $logoUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($codeId, $password, $email)
    {
        $this->code_id = $codeId;
        $this->password = $password;
        $this->email = $email;
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
            ->subject('CAR 225: Votre inscription en tant que Particulier est validée !')
            ->from('contact@car225.com', 'CAR 225')
            ->view('emails.particulier_approved', [
                'code_id' => $this->code_id,
                'password' => $this->password,
                'email' => $this->email,
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
