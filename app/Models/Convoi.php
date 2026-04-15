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
        'lieu_depart',
        'lieu_retour',
        'date_depart',
        'heure_depart',
        'date_retour',
        'heure_retour',
        'montant',
        'motif_refus',
        'lieu_rassemblement',
        'is_garant',
        'motif_annulation_chauffeur',
        // Champs client walk-in (client sans compte utilisateur)
        'client_nom',
        'client_prenom',
        'client_contact',
        'client_email',
        'created_by_gare',
    ];

    /** Nom affiché du demandeur (user ou client walk-in) */
    public function getDemandeurNomAttribute(): string
    {
        if ($this->user_id && $this->user) {
            return trim(($this->user->prenom ?? '') . ' ' . ($this->user->name ?? '')) ?: 'Utilisateur';
        }
        return trim(($this->client_prenom ?? '') . ' ' . ($this->client_nom ?? '')) ?: 'Client sur place';
    }

    /** Contact du demandeur */
    public function getDemandeurContactAttribute(): ?string
    {
        return $this->user_id ? ($this->user->contact ?? null) : $this->client_contact;
    }

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

