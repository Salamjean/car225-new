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
        $date = \Carbon\Carbon::parse($this->date_voyage)->format('Y-m-d');
        return \App\Models\Reservation::where(function($q) use ($date) {
            $q->where('programme_id', $this->programme_id)
              ->whereDate('date_voyage', $date)
              ->where('statut_aller', 'terminee');
        })->orWhere(function($q) use ($date) {
            $q->whereHas('programme', function($sub) {
                $sub->where('programme_retour_id', $this->programme_id);
            })
            ->whereDate('date_retour', $date)
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
     * Calcule le temps restant pour le voyage en cours basé sur le GPS ou le temps
     */
    public function getTempsRestantAttribute()
    {
        if ($this->statut === 'interrompu') {
            return "🚨 Voyage interrompu";
        }

        if ($this->statut !== 'en_cours' || !$this->programme) {
            return null;
        }

        $latestLocation = $this->latestLocation;
        $arrivalGare = $this->gareArrivee ?: $this->programme->gareArrivee;

        // Si on a les coordonnées du chauffeur et de la destination
        if ($latestLocation && $arrivalGare && $arrivalGare->latitude && $arrivalGare->longitude) {
            $dist = $this->calculateDistance(
                $latestLocation->latitude,
                $latestLocation->longitude,
                (float)$arrivalGare->latitude,
                (float)$arrivalGare->longitude
            );

            // Seuil d'arrivée imminente (500 mètres)
            if ($dist < 0.5) {
                return "Arrivée imminente";
            }

            // Estimation basée sur la distance
            // Vitesse moyenne estimée à 50 km/h pour le calcul (évite les fluctuations si le car s'arrête)
            $speed = 50; 
            
            $totalMinutes = round(($dist / $speed) * 60);
            
            if ($totalMinutes < 1) return "Arrivée imminente";

            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;

            if ($hours > 0) {
                return "{$hours}h {$minutes}min restants";
            }
            return "{$minutes}min restants";
        }

        // --- FALLBACK: Heure prévue (Static) ---
        // On ne retourne plus "X min restants" car cela diminue à l'arrêt (Now approche Destination).
        // On affiche l'heure prévue fixe, qui ne diminuera que si le chauffeur bouge réellement via GPS.
        $arrivalDateTime = $this->estimated_arrival_at ?: \Carbon\Carbon::parse($this->date_voyage->format('Y-m-d') . ' ' . $this->programme->heure_arrive);
        return "Prévu vers " . $arrivalDateTime->format('H:i');
    }

    /**
     * Calcule la distance entre deux points GPS (Haversine formula)
     * Retourne la distance en KM
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Rayon de la terre en km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Helper to update estimation based on delay
     */
    public function updateEstimatedArrival($secondsDelay)
    {
        $this->delay_seconds += $secondsDelay;
        
        $date = \Carbon\Carbon::parse($this->date_voyage)->format('Y-m-d');
        $baseArrival = \Carbon\Carbon::parse($date . ' ' . $this->programme->heure_arrive);
        
        if (\Carbon\Carbon::parse($this->programme->heure_arrive)->lt(\Carbon\Carbon::parse($this->programme->heure_depart))) {
            $baseArrival->addDay();
        }

        $this->estimated_arrival_at = $baseArrival->addSeconds((int)$this->delay_seconds);
        $this->save();
    }
}
