<?php

namespace App\Notifications;

use App\Models\Convoi;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConvoiAssignedNotification extends Notification
{
    use Queueable;

    protected Convoi $convoi;

    public function __construct(Convoi $convoi)
    {
        $this->convoi = $convoi;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $itineraire = $this->convoi->itineraire;
        $vehicule = $this->convoi->vehicule;
        $gare = $this->convoi->gare;

        $depart = $itineraire->point_depart ?? 'N/A';
        $arrivee = $itineraire->point_arrive ?? 'N/A';
        $chauffeurName = trim(($notifiable->prenom ?? '') . ' ' . ($notifiable->name ?? ''));
        $today = Carbon::now()->format('d/m/Y');
        $vehiculeModele = trim(($vehicule->marque ?? '') . ' ' . ($vehicule->modele ?? ''));
        $vehiculeModele = $vehiculeModele !== '' ? $vehiculeModele : 'N/A';

        return (new MailMessage)
            ->subject('Nouveau Convoi Assigné ' . ($this->convoi->reference ? ('- ' . $this->convoi->reference) : ''))
            ->greeting('Bonjour ' . ($chauffeurName !== '' ? $chauffeurName : 'Chauffeur') . ',')
            ->line('Vous avez été assigné à un nouveau convoi.')
            ->line('**Détails du convoi :**')
            ->line('- **Référence :** ' . ($this->convoi->reference ?? 'N/A'))
            ->line('- **Trajet :** ' . $depart . ' -> ' . $arrivee)
            ->line('- **Gare :** ' . ($gare->nom_gare ?? 'N/A'))
            ->line('- **Nombre de passagers :** ' . ($this->convoi->nombre_personnes ?? 0))
            ->line('- **Date d\'affectation :** ' . $today)
            ->line('')
            ->line('**Véhicule assigné :**')
            ->line('- **Immatriculation :** ' . ($vehicule->immatriculation ?? 'N/A'))
            ->line('- **Modèle :** ' . $vehiculeModele)
            ->line('')
            ->line('Merci de consulter votre espace chauffeur pour les instructions opérationnelles.');
    }

    public function toArray(object $notifiable): array
    {
        $itineraire = $this->convoi->itineraire;

        return [
            'convoi_id' => $this->convoi->id,
            'reference' => $this->convoi->reference,
            'message' => 'Nouveau convoi assigné: ' . ($itineraire->point_depart ?? 'N/A') . ' -> ' . ($itineraire->point_arrive ?? 'N/A'),
        ];
    }
}

