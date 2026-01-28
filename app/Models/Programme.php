<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'heure_depart',
        'heure_arrive',
        'montant_billet',
        'nbre_siege_occupe',
        'staut_place',
        'date_fin_programmation',
        'type_programmation',
        'jours_recurrence',
        'is_aller_retour',
        'programme_retour_id',
        
    ];

    protected $casts = [
        'date_depart' => 'date',
        'nbre_siege_occupe' => 'integer',
    ];

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

    public function historiques()
    {
        return $this->hasMany(ProgrammeHistorique::class);
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

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
  /**
     * Réservations où ce programme est l'ALLER (lié via programme_id)
     */
    public function reservationsAller()
    {
        return $this->hasMany(Reservation::class, 'programme_id');
    }

    /**
     * Réservations où ce programme est le RETOUR (lié via programme_retour_id)
     */
    public function reservationsRetour()
    {
        return $this->hasMany(Reservation::class, 'programme_retour_id');
    }
    /**
     * Relation avec le convoyeur (personnel)
     */
    public function convoyeur()
    {
        return $this->belongsTo(Personnel::class, 'convoyeur_id');
    }

    public function programmeRetour()
    {
        return $this->belongsTo(Programme::class, 'programme_retour_id');
    }

    /**
     * Accessor pour le statut des places
     */
    public function getStatutPlaceAttribute()
    {
        return $this->staut_place;
    }

    /**
     * Accessor pour la date formatée
     */
    public function getDateDepartFormateeAttribute()
    {
        return $this->date_depart->format('d/m/Y');
    }

    /**
     * Accessor pour l'heure de départ formatée
     */
    public function getHeureDepartFormateeAttribute()
    {
        return date('H:i', strtotime($this->heure_depart));
    }

    /**
     * Accessor pour l'heure d'arrivée formatée
     */
    public function getHeureArriveeFormateeAttribute()
    {
        return date('H:i', strtotime($this->heure_arrive));
    }

    /**
     * Accessor pour le trajet complet
     */
    public function getTrajetCompletAttribute()
    {
        return $this->point_depart . ' → ' . $this->point_arrive;
    }

    /**
     * Accessor pour l'équipage complet
     */
    public function getEquipageCompletAttribute()
    {
        $equipage = $this->chauffeur->prenom . ' ' . $this->chauffeur->name;

        if ($this->convoyeur) {
            $equipage .= ' + ' . $this->convoyeur->prenom . ' ' . $this->convoyeur->name;
        }

        return $equipage;
    }

    /**
     * Scope pour les programmes à venir
     */
    public function scopeAVenir($query)
    {
        return $query->where('date_depart', '>=', now()->format('Y-m-d'));
    }

    /**
     * Scope pour les programmes passés
     */
    public function scopePasses($query)
    {
        return $query->where('date_depart', '<', now()->format('Y-m-d'));
    }

    /**
     * Scope pour les programmes d'aujourd'hui
     */
    public function scopeAujourdhui($query)
    {
        return $query->where('date_depart', now()->format('Y-m-d'));
    }

    /**
     * Scope pour les programmes par compagnie
     */
    public function scopeParCompagnie($query, $compagnieId)
    {
        return $query->where('compagnie_id', $compagnieId);
    }

    /**
     * Vérifier si le programme est complet
     */
    public function getEstCompletAttribute()
    {
        return $this->staut_place === 'rempli';
    }

    /**
     * Vérifier si le programme est presque complet
     */
    public function getEstPresqueCompletAttribute()
    {
        return $this->staut_place === 'presque_complet';
    }

    /**
     * Vérifier si le programme a des places disponibles
     */
    public function getAPlacesDisponiblesAttribute()
    {
        return $this->staut_place === 'vide' || $this->staut_place === 'presque_complet';
    }

    /**
     * Calculer le nombre de places disponibles
     */
    public function getPlacesDisponiblesAttribute()
    {
        if ($this->vehicule) {
            return $this->vehicule->nombre_place - $this->nbre_siege_occupe;
        }
        return 0;
    }

    /**
     * Calculer le pourcentage d'occupation
     */
    public function getPourcentageOccupationAttribute()
    {
        if ($this->vehicule && $this->vehicule->nombre_place > 0) {
            return round(($this->nbre_siege_occupe / $this->vehicule->nombre_place) * 100);
        }
        return 0;
    }

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
     * Boot du modèle
     */
    protected static function boot()
    {
        parent::boot();

        // Mettre à jour automatiquement le statut des places quand le nombre de sièges occupés change
        static::saving(function ($programme) {
            if ($programme->vehicule) {
                $nombrePlaces = $programme->vehicule->nombre_place;
                $siegesOccupes = $programme->nbre_siege_occupe;

                if ($siegesOccupes >= $nombrePlaces) {
                    $programme->staut_place = 'rempli';
                } elseif ($siegesOccupes >= ($nombrePlaces * 0.7)) {
                    $programme->staut_place = 'presque_complet';
                } else {
                    $programme->staut_place = 'vide';
                }
            }
        });
    }

    // Dans App/Models/Programme.php
    public function updateStatutPlaces()
    {
        if (!$this->vehicule) {
            return;
        }

        $totalPlaces = $this->vehicule->nombre_place ?? 0;

        // Compter les places réservées
        $placesReservees = 0;
        foreach ($this->reservations as $reservation) {
            $places = $reservation->places_reservees;
            if (is_array($places)) {
                $placesReservees += count($places);
            }
        }

        // Calculer le pourcentage
        $pourcentage = $totalPlaces > 0 ? ($placesReservees / $totalPlaces) * 100 : 0;

        // Déterminer le statut
        if ($pourcentage >= 100) {
            $statut = 'rempli';
        } elseif ($pourcentage >= 80) {
            $statut = 'presque_complet';
        } else {
            $statut = 'vide';
        }

        $this->staut_place = $statut;
        $this->save();
    }

    /**
     * Relation avec les statuts par date (pour programmes récurrents)
     */
    public function statutsDate()
    {
        return $this->hasMany(ProgrammeStatutDate::class);
    }

    /**
     * Obtenir le statut pour une date spécifique
     */
    public function getStatutForDate($date)
    {
        if ($this->type_programmation === 'ponctuel') {
            // Pour ponctuel, retourner le statut global
            return [
                'nbre_siege_occupe' => $this->nbre_siege_occupe,
                'staut_place' => $this->staut_place
            ];
        }

        // Pour récurrent, chercher dans la table des statuts par date
        $formattedDate = date('Y-m-d', strtotime($date));
        return $this->statutsDate()
            ->where('date_voyage', $formattedDate)
            ->first();
    }
}
