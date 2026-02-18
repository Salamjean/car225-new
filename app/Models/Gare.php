<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Gare extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'nom_gare',
        'ville',
        'adresse',
        'email',
        'password',
        'contact',
        'contact_urgence',
        'commune',
        'profile_image',
        'responsable_nom',
        'responsable_prenom',
        'compagnie_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }

    public function personnels()
    {
        return $this->hasMany(Personnel::class, 'gare_id');
    }

    public function vehicules()
    {
        return $this->hasMany(Vehicule::class, 'gare_id');
    }

    public function caisses()
    {
        return $this->hasMany(Caisse::class, 'gare_id');
    }

    public function itineraires()
    {
        return $this->hasMany(Itineraire::class, 'gare_id');
    }
}
