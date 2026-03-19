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
        'estimated_arrival_at',
        'delay_seconds',
        'motif_annulation',
    ];

    protected $casts = [
        'date_voyage' => 'date',
        'estimated_arrival_at' => 'datetime',
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

        // On utilise l'arrivée estimée si elle existe, sinon celle du programme
        $arrivalDateTime = $this->estimated_arrival_at ?: \Carbon\Carbon::parse($this->date_voyage->format('Y-m-d') . ' ' . $this->programme->heure_arrive);
        
        // Si l'arrivée programmée était le lendemain (ex: départ 23h, durée 4h)
        if (!$this->estimated_arrival_at && \Carbon\Carbon::parse($this->programme->heure_arrive)->lt(\Carbon\Carbon::parse($this->programme->heure_depart))) {
            $arrivalDateTime->addDay();
        }

        $now = now();

        if ($now->greaterThanOrEqualTo($arrivalDateTime)) {
            return "Arrivée imminente";
        }

        $diff = $now->diff($arrivalDateTime);
        
        if ($diff->h > 0) {
            return $diff->format('%h h %i min restants');
        }

        return $diff->format('%i min restants');
    }

    /**
     * Helper to update estimation based on delay
     */
    public function updateEstimatedArrival($secondsDelay)
    {
        $this->delay_seconds += $secondsDelay;
        
        $baseArrival = \Carbon\Carbon::parse($this->date_voyage->format('Y-m-d') . ' ' . $this->programme->heure_arrive);
        if (\Carbon\Carbon::parse($this->programme->heure_arrive)->lt(\Carbon\Carbon::parse($this->programme->heure_depart))) {
            $baseArrival->addDay();
        }

        $this->estimated_arrival_at = $baseArrival->addSeconds($this->delay_seconds);
        $this->save();
    }
}
