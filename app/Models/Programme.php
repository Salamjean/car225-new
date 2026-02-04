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
        'vehicule_id',
        'itineraire_id',
        'personnel_id',
        'convoyeur_id',
        'point_depart',
        'point_arrive',
        'durer_parcours',
        'date_depart',
        'date_fin', // Fin de validité pour lignes continues
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

    /**
     * Relation avec le véhicule
     */
    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    /**
     * Relation avec l'itinéraire
     */
    public function itineraire()
    {
        return $this->belongsTo(Itineraire::class);
    }

    /**
     * Relation avec le chauffeur (personnel)
     */
    public function chauffeur()
    {
        return $this->belongsTo(Personnel::class, 'personnel_id');
    }

    /**
     * Relation avec le convoyeur (personnel)
     */
    public function convoyeur()
    {
        return $this->belongsTo(Personnel::class, 'convoyeur_id');
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

    // ========================================
    // ACCESSORS - Places disponibles (calculés dynamiquement)
    // ========================================

    /**
     * Nombre de places réservées (confirmées)
     */
    public function getPlacesReserveesAttribute()
    {
        return $this->reservations()
            ->where('statut', 'confirmee')
            ->count();
    }

    /**
     * Nombre de places disponibles
     */
    public function getPlacesDisponiblesAttribute()
    {
        if (!$this->vehicule) {
            return 0;
        }
        return max(0, $this->vehicule->nombre_place - $this->places_reservees);
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
     * Équipage complet
     */
    public function getEquipageCompletAttribute()
    {
        $equipage = $this->chauffeur ? $this->chauffeur->prenom . ' ' . $this->chauffeur->name : 'Non assigné';

        if ($this->convoyeur) {
            $equipage .= ' + ' . $this->convoyeur->prenom . ' ' . $this->convoyeur->name;
        }

        return $equipage;
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
        return $query->where('date_depart', '>=', now()->format('Y-m-d'));
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
        return $query->whereDate('date_depart', $date);
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
     * Programmes disponibles (actifs avec places)
     */
    public function scopeDisponible($query)
    {
        return $query->where('statut', 'actif')
                     ->whereHas('vehicule', function($q) {
                         $q->whereRaw('vehicules.nombre_place > (
                             SELECT COUNT(*) FROM reservations 
                             WHERE reservations.programme_id = programmes.id 
                             AND reservations.statut = "confirmee"
                         )');
                     });
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
