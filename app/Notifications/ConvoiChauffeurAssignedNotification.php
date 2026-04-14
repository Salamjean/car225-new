<?php

namespace App\Notifications;

use App\Models\Convoi;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class ConvoiChauffeurAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(public Convoi $convoi) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $depart  = $this->convoi->lieu_depart  ?? ($this->convoi->itineraire->point_depart  ?? '?');
        $arrivee = $this->convoi->lieu_retour  ?? ($this->convoi->itineraire->point_arrive  ?? '?');
        $date    = $this->convoi->date_depart
            ? Carbon::parse($this->convoi->date_depart)->format('d/m/Y')
            : 'N/A';

        return [
            'title'     => '🚌 Chauffeur affecté — ' . $this->convoi->reference,
            'message'   => "{$depart} → {$arrivee} · Départ le {$date} · Lieu : " . ($this->convoi->lieu_rassemblement ?? 'À définir'),
            'convoi_id' => $this->convoi->id,
            'type'      => 'convoi_chauffeur_assigne',
        ];
    }
}
