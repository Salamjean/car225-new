<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgrammeHistorique extends Model
{
    use HasFactory;

    protected $fillable = [
        'programme_id',
        'action',
        'vehicule',
        'itineraire',
        'chauffeur',
        'convoyeur',
        'point_depart',
        'point_arrive',
        'duree_parcours',
        'date_depart',
        'heure_depart',
        'heure_arrivee',
        'sieges_occupes',
        'statut_places',
        'pourcentage_occupation',
        'raison'
    ];

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }
}