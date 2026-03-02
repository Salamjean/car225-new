<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voyage extends Model
{
    use HasFactory;

    protected $fillable = [
        'programme_id',
        'date_voyage',
        'vehicule_id',
        'personnel_id',
        'gare_depart_id',
        'gare_arrivee_id',
        'statut',
    ];

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function chauffeur()
    {
        return $this->belongsTo(Personnel::class, 'personnel_id');
    }

    public function gareDepart()
    {
        return $this->belongsTo(Gare::class, 'gare_depart_id');
    }

    public function gareArrivee()
    {
        return $this->belongsTo(Gare::class, 'gare_arrivee_id');
    }

    public function latestLocation()
    {
        return $this->hasOne(DriverLocation::class)->latestOfMany();
    }

    /**
     * Nombre de passagers scannés pour ce voyage spécifique
     */
    public function getOccupancyAttribute()
    {
        return \App\Models\Reservation::where(function($q) {
            $q->where('programme_id', $this->programme_id)
              ->whereDate('date_voyage', $this->date_voyage)
              ->where('statut_aller', 'terminee');
        })->orWhere(function($q) {
            $q->whereHas('programme', function($sub) {
                $sub->where('programme_retour_id', $this->programme_id);
            })
            ->whereDate('date_retour', $this->date_voyage)
            ->where('statut_retour', 'terminee');
        })->count();
    }

    /**
     * Liste des passagers scannés pour ce voyage
     */
    public function getScannedPassengersAttribute()
    {
        return \App\Models\Reservation::with('user')->where(function($q) {
            $q->where('programme_id', $this->programme_id)
              ->whereDate('date_voyage', $this->date_voyage)
              ->where('statut_aller', 'terminee');
        })->orWhere(function($q) {
            $q->whereHas('programme', function($sub) {
                $sub->where('programme_retour_id', $this->programme_id);
            })
            ->whereDate('date_retour', $this->date_voyage)
            ->where('statut_retour', 'terminee');
        })->get();
    }

    /**
     * Calcule le temps restant pour le voyage en cours
     */
    public function getTempsRestantAttribute()
    {
        if ($this->statut === 'interrompu') {
            return "🚨 Voyage interrompu";
        }

        if ($this->statut !== 'en_cours' || !$this->programme) {
            return null;
        }

        // On se base sur l'heure d'arrivée prévue du programme
        $heureArrivee = $this->programme->heure_arrive;
        $dateVoyage = $this->date_voyage instanceof \Carbon\Carbon 
            ? $this->date_voyage->toDateString() 
            : $this->date_voyage;

        $arriveeDateTime = \Carbon\Carbon::parse($dateVoyage . ' ' . $heureArrivee);
        $now = now();

        if ($now->greaterThanOrEqualTo($arriveeDateTime)) {
            return "Arrivée imminente";
        }

        $diff = $now->diff($arriveeDateTime);
        
        if ($diff->h > 0) {
            return $diff->format('%h h %i min restants');
        }

        return $diff->format('%i min restants');
    }
}
