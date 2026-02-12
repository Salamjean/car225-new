<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signalement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'programme_id',
        'sapeur_pompier_id',
        'type',
        'description',
        'latitude',
        'longitude',
        'statut',
        'vehicule_id', // J'ajoute aussi celui-ci car il manquait dans le modèle suite à la précédente manip
        'photo_path',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    public function sapeurPompier()
    {
        return $this->belongsTo(SapeurPompier::class);
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }
}
