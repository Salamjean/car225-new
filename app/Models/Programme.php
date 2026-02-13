<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Programme Model - FlixBus Style
 * 
 * Chaque programme représente UN voyage à une date précise.
 * Plus de récurrence, plus de logique aller-retour complexe.
 */
class Programme extends Model
{
    use HasFactory;

    protected $fillable = [
        'compagnie_id',
        'itineraire_id',
        'point_depart',
        'point_arrive',
        'gare_depart_id',
        'gare_arrivee_id',
        'durer_parcours',
        'date_depart',
        'date_fin',
        'heure_depart',
        'heure_arrive',
        'montant_billet',
        'statut',
    ];

    protected $casts = [
        'date_depart' => 'date',
        'date_fin' => 'date',
        'montant_billet' => 'decimal:2',
    ];

    // ========================================
    // RELATIONS
    // ========================================

    /**
     * Relation avec la compagnie
     */
    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }

    public function gareDepart()
    {
        return $this->belongsTo(Gare::class, 'gare_depart_id');
    }

    public function gareArrivee()
    {
        return $this->belongsTo(Gare::class, 'gare_arrivee_id');
    }

    public function voyages()
    {
        return $this->hasMany(Voyage::class);
    }

    /**
     * Relation avec l'itinéraire
     */
    public function itineraire()
    {
        return $this->belongsTo(Itineraire::class);
    }

    /**
     * Relation avec les réservations
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Relation avec l'historique
     */
    public function historiques()
    {
        return $this->hasMany(ProgrammeHistorique::class);
    }

    public function getVehiculeForDate($date)
    {
        $voyage = $this->voyages()->whereDate('date_voyage', $date)->first();
        return $voyage ? $voyage->vehicule : null;
    }

    // ========================================
    // ACCESSORS - Places disponibles (calculés dynamiquement)
    // ========================================

    /**
     * Nombre de places réservées (confirmées) pour une date donnée
     */
    public function getPlacesReserveesForDate($date)
    {
        return $this->reservations()
            ->whereIn('statut', ['confirmee', 'en_attente', 'terminee'])
            ->whereDate('date_voyage', $date)
            ->count();
    }

    /**
     * Nombre de places réservées (confirmées) - Aujourd'hui (compatibilité)
     */
    public function getPlacesReserveesAttribute()
    {
        return $this->getPlacesReserveesForDate(date('Y-m-d'));
    }

    /**
     * Nombre de places disponibles pour une date donnée
     */
    public function getPlacesDisponiblesForDate($date)
    {
        $vehicule = $this->getVehiculeForDate($date);
        $totalPlaces = $vehicule ? $vehicule->nombre_place : 70; // Fallback to 70
        return max(0, $totalPlaces - $this->getPlacesReserveesForDate($date));
    }

    /**
     * Nombre de places disponibles - Aujourd'hui (compatibilité)
     */
    public function getPlacesDisponiblesAttribute()
    {
        return $this->getPlacesDisponiblesForDate(date('Y-m-d'));
    }

    /**
     * Véhicule - Aujourd'hui (compatibilité)
     */
    public function getVehiculeAttribute()
    {
        return $this->getVehiculeForDate(date('Y-m-d'));
    }

    /**
     * Pourcentage d'occupation
     */
    public function getPourcentageOccupationAttribute()
    {
        if (!$this->vehicule || $this->vehicule->nombre_place == 0) {
            return 0;
        }
        return round(($this->places_reservees / $this->vehicule->nombre_place) * 100);
    }

    /**
     * Statut des places (calculé dynamiquement)
     */
    public function getStatutPlacesAttribute()
    {
        $pourcentage = $this->pourcentage_occupation;
        
        if ($pourcentage >= 100) {
            return 'complet';
        } elseif ($pourcentage >= 80) {
            return 'presque_complet';
        } else {
            return 'disponible';
        }
    }

    /**
     * Le programme a des places disponibles
     */
    public function getAPlacesDisponiblesAttribute()
    {
        return $this->places_disponibles > 0 && $this->statut === 'actif';
    }

    // ========================================
    // ACCESSORS - Formatage
    // ========================================

    /**
     * Date formatée
     */
    public function getDateDepartFormateeAttribute()
    {
        return $this->date_depart ? $this->date_depart->format('d/m/Y') : null;
    }

    /**
     * Heure de départ formatée
     */
    public function getHeureDepartFormateeAttribute()
    {
        return date('H:i', strtotime($this->heure_depart));
    }

    /**
     * Heure d'arrivée formatée
     */
    public function getHeureArriveeFormateeAttribute()
    {
        return date('H:i', strtotime($this->heure_arrive));
    }

    /**
     * Trajet complet
     */
    public function getTrajetCompletAttribute()
    {
        return $this->point_depart . ' → ' . $this->point_arrive;
    }

    /**
     * Équipage complet pour une date donnée
     */
    public function getEquipageCompletForDate($date)
    {
        $voyage = $this->voyages()->whereDate('date_voyage', $date)->first();
        return $voyage ? ($voyage->personnel ? $voyage->personnel->prenom . ' ' . $voyage->personnel->name : 'Chauffeur non assigné') : 'Voyage non programmé';
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Programmes actifs (non annulés)
     */
    public function scopeActif($query)
    {
        return $query->where('statut', 'actif');
    }

    /**
     * Programmes à venir
     */
    public function scopeAVenir($query)
    {
        return $query->where('date_fin', '>=', now()->format('Y-m-d'));
    }

    /**
     * Programmes passés
     */
    public function scopePasses($query)
    {
        return $query->where('date_depart', '<', now()->format('Y-m-d'));
    }

    /**
     * Programmes d'aujourd'hui
     */
    public function scopeAujourdhui($query)
    {
        return $query->where('date_depart', now()->format('Y-m-d'));
    }

    /**
     * Programmes par compagnie
     */
    public function scopeParCompagnie($query, $compagnieId)
    {
        return $query->where('compagnie_id', $compagnieId);
    }

    /**
     * Programmes par date
     */
    public function scopeParDate($query, $date)
    {
        return $query->where('date_depart', '<=', $date)
                     ->where('date_fin', '>=', $date);
    }

    /**
     * Programmes par itinéraire (recherche)
     */
    public function scopeParTrajet($query, $pointDepart, $pointArrive)
    {
        return $query->where('point_depart', 'like', "%{$pointDepart}%")
                     ->where('point_arrive', 'like', "%{$pointArrive}%");
    }

    /**
     * Programmes disponibles (actifs)
     */
    public function scopeDisponible($query)
    {
        return $query->where('statut', 'actif');
    }

    // ========================================
    // HELPERS
    // ========================================

    /**
     * Vérifier si le programme est en cours
     */
    public function getEstEnCoursAttribute()
    {
        $now = now();
        $dateHeureDepart = $this->date_depart->format('Y-m-d') . ' ' . $this->heure_depart;
        $dateHeureArrivee = $this->date_depart->format('Y-m-d') . ' ' . $this->heure_arrive;

        return $now->between($dateHeureDepart, $dateHeureArrivee);
    }

    /**
     * Vérifier si le programme est terminé
     */
    public function getEstTermineAttribute()
    {
        $dateHeureArrivee = $this->date_depart->format('Y-m-d') . ' ' . $this->heure_arrive;
        return now()->greaterThan($dateHeureArrivee);
    }

    /**
     * Mettre à jour le statut si complet
     */
    public function updateStatutSiComplet()
    {
        if ($this->places_disponibles <= 0) {
            $this->update(['statut' => 'complet']);
        }
    }
}
