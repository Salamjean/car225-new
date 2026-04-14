<?php

namespace App\Notifications;

use App\Models\Convoi;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GareConvoiAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(public Convoi $convoi) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $trajet = ($this->convoi->lieu_depart ?? ($this->convoi->itineraire->point_depart ?? '?'))
                . ' → '
                . ($this->convoi->lieu_retour ?? ($this->convoi->itineraire->point_arrive ?? '?'));

        $dateDepart = $this->convoi->date_depart
            ? \Carbon\Carbon::parse($this->convoi->date_depart)->format('d/m/Y')
            : 'N/A';

        return [
            'title'     => '🚌 Nouveau convoi assigné — ' . $this->convoi->reference,
            'message'   => $trajet . ' · Départ le ' . $dateDepart . ' · ' . $this->convoi->nombre_personnes . ' personne(s)',
            'convoi_id' => $this->convoi->id,
            'type'      => 'convoi_assigne_gare',
        ];
    }
}
