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
        'personnel_id',
        'voyage_id',
        'compagnie_id',
        'sapeur_pompier_id',
        'type',
        'description',
        'latitude',
        'longitude',
        'statut',
        'vehicule_id',
        'reservation_id',
        'photo_path',
        'nombre_morts',
        'nombre_blesses',
        'details_intervention',
        'bilan_passagers',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'bilan_passagers' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    public function sapeurPompier()
    {
        return $this->belongsTo(SapeurPompier::class);
    }

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function voyage()
    {
        return $this->belongsTo(Voyage::class);
    }

    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }
}
