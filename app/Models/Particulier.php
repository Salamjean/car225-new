<?php

namespace App\Models;

use App\Traits\HasCodeId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Particulier extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasCodeId;

    protected $table = 'particuliers';

    protected $hidden = [
        'password',
    ];

    protected $fillable = [
        'name',
        'prenom',
        'email',
        'contact',
        'nombre_place_car',
        'date_mise_service',
        'photo_proprietaire',
        'photo_complete_car',
        'photo_avant_car',
        'photo_arriere_car',
        'immatriculation',
        'carte_grise',
        'visite_technique',
        'statut',
        'code_id',
        'password',
        'fcm_token',
        'must_change_password',
        'solde_convoie',
        'motif_rejet',
    ];

    protected $casts = [
        'password'            => 'hashed',
        'date_mise_service'   => 'date',
        'solde_convoie'       => 'decimal:2',
        'must_change_password'=> 'boolean',
    ];

    /** Getter pour le nom complet */
    public function getFullNameAttribute(): string
    {
        return trim($this->prenom . ' ' . $this->name);
    }

    /** URLs des images stockées sur le disque public */
    public function getPhotoProprietaireUrlAttribute(): ?string
    {
        return $this->photo_proprietaire ? asset('storage/' . $this->photo_proprietaire) : null;
    }

    public function getPhotoCompleteCarUrlAttribute(): ?string
    {
        return $this->photo_complete_car ? asset('storage/' . $this->photo_complete_car) : null;
    }

    public function getPhotoAvantCarUrlAttribute(): ?string
    {
        return $this->photo_avant_car ? asset('storage/' . $this->photo_avant_car) : null;
    }

    public function getPhotoArriereCarUrlAttribute(): ?string
    {
        return $this->photo_arriere_car ? asset('storage/' . $this->photo_arriere_car) : null;
    }

    public function getCarteGriseUrlAttribute(): ?string
    {
        return $this->carte_grise ? asset('storage/' . $this->carte_grise) : null;
    }

    public function getVisiteTechniqueUrlAttribute(): ?string
    {
        return $this->visite_technique ? asset('storage/' . $this->visite_technique) : null;
    }

    /** Convois assignés à ce particulier */
    public function convois()
    {
        return $this->hasMany(Convoi::class, 'particulier_id');
    }
}
