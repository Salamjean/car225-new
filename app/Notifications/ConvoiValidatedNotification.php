<?php

namespace App\Notifications;

use App\Models\Convoi;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConvoiValidatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Convoi $convoi) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'     => '✅ Convoi validé — ' . $this->convoi->reference,
            'message'   => 'Votre convoi a été validé. Montant à payer : ' . number_format($this->convoi->montant, 0, ',', ' ') . ' FCFA. Connectez-vous pour procéder au paiement.',
            'convoi_id' => $this->convoi->id,
            'type'      => 'convoi_valide',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $montantFormate = number_format($this->convoi->montant, 0, ',', ' ') . ' FCFA';

        return (new MailMessage)
            ->subject('✅ Votre demande de convoi a été validée — ' . $this->convoi->reference)
            ->greeting('Bonjour ' . ($notifiable->prenom ?? $notifiable->name) . ',')
            ->line('Bonne nouvelle ! Votre demande de convoi **' . $this->convoi->reference . '** a été validée par la compagnie **' . $this->convoi->compagnie->name . '**.')
            ->line('**Itinéraire :** ' . $this->convoi->lieu_depart . ' → ' . $this->convoi->lieu_retour)
            ->line('**Date de départ :** ' . \Carbon\Carbon::parse($this->convoi->date_depart)->format('d/m/Y') . ' à ' . $this->convoi->heure_depart)
            ->line('**Nombre de personnes :** ' . $this->convoi->nombre_personnes)
            ->line('**Montant à régler :** ' . $montantFormate)
            ->line('Connectez-vous à votre espace pour lire le règlement des convois et procéder au paiement.')
            ->action('Accéder à mon convoi', url('/user/convoi/' . $this->convoi->id))
            ->line('Une fois le paiement effectué, vous pourrez renseigner les informations de vos passagers.')
            ->salutation('Cordialement, l\'équipe CAR225');
    }
}
