<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convoi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'compagnie_id',
        'itineraire_id',
        'gare_id',
        'personnel_id',
        'vehicule_id',
        'nombre_personnes',
        'reference',
        'statut',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }

    public function itineraire()
    {
        return $this->belongsTo(Itineraire::class);
    }

    public function gare()
    {
        return $this->belongsTo(Gare::class);
    }

    public function chauffeur()
    {
        return $this->belongsTo(Personnel::class, 'personnel_id');
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function passagers()
    {
        return $this->hasMany(ConvoiPassager::class);
    }

    public function latestLocation()
    {
        return $this->hasOne(DriverLocation::class)->latestOfMany();
    }
}

