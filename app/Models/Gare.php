<?php

namespace App\Models;

use App\Traits\HasCodeId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Gare extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasCodeId;

    protected $fillable = [
        'code_id',
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

    public function agents()
    {
        return $this->hasMany(Agent::class, 'gare_id');
    }

    public function messages()
    {
        return $this->morphMany(CompanyMessage::class, 'recipient');
    }
}
