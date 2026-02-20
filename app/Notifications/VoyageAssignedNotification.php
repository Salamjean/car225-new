<?php

namespace App\Notifications;

use App\Models\Voyage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VoyageAssignedNotification extends Notification
{
    use Queueable;

    protected $voyage;

    /**
     * Create a new notification instance.
     */
    public function __construct(Voyage $voyage)
    {
        $this->voyage = $voyage;
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
        $programme = $this->voyage->programme;
        $vehicule = $this->voyage->vehicule;
        $date = \Carbon\Carbon::parse($this->voyage->date_voyage)->format('d/m/Y');
        $heure = \Carbon\Carbon::parse($programme->heure_depart)->format('H:i');

        return (new MailMessage)
                    ->subject('Nouveau Voyage Assigné #' . $this->voyage->id)
                    ->greeting('Bonjour ' . $notifiable->prenom . ' ' . $notifiable->name . ',')
                    ->line('Vous avez été assigné à un nouveau voyage.')
                    ->line('**Détails du voyage :**')
                    ->line('- **Départ :** ' . $programme->point_depart . ' (' . ($programme->gareDepart->nom_gare ?? 'N/A') . ')')
                    ->line('- **Arrivée :** ' . $programme->point_arrive . ' (' . ($programme->gareArrivee->nom_gare ?? 'N/A') . ')')
                    ->line('- **Date :** ' . $date)
                    ->line('- **Heure de départ :** ' . $heure)
                    ->line('')
                    ->line('**Véhicule assigné :**')
                    ->line('- **Immatriculation :** ' . $vehicule->immatriculation)
                    ->line('- **Modèle :** ' . $vehicule->marque . ' ' . $vehicule->modele)
                    ->line('')
                    ->line('Merci de vous connecter à votre application mobile pour confirmer et voir les détails.')
                    // ->action('Ouvrir l\'application', url('/')) // Optional, usually no dashboard link on email for mobile users
                    ->line('Bonne route !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'voyage_id' => $this->voyage->id,
            'message' => 'Nouveau voyage assigné: ' . $this->voyage->programme->point_depart . ' -> ' . $this->voyage->programme->point_arrive,
        ];
    }
}
