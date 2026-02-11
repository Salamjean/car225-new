<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Reservation Model - FlixBus Style
 * 
 * Chaque réservation = 1 siège sur 1 voyage (programme).
 * Pour un aller-retour, l'utilisateur fait 2 réservations séparées.
 */
class Reservation extends Model
{
    protected $fillable = [
        'paiement_id',
        'payment_transaction_id',
        'user_id',
        'caisse_id',
        'programme_id',
        'seat_number',
        'passager_nom',
        'passager_prenom',
        'passager_email',
        'passager_telephone',
        'passager_urgence',
        'date_voyage',
        'heure_depart',
        'heure_arrive',
        'montant',
        'statut',
        'reference',
        'qr_code',
        'qr_code_path',
        'qr_code_data',
        'embarquement_scanned_at',
        'embarquement_agent_id',
        'embarquement_vehicule_id',
        'embarquement_location',
        'embarquement_status',
        'hotesse_id',
        'compagnie_id',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_voyage' => 'date',
        'embarquement_scanned_at' => 'datetime',
        'qr_code_data' => 'array',
    ];

    // ========================================
    // RELATIONS
    // ========================================

    /**
     * Relation avec le paiement
     */
    public function paiement()
    {
        return $this->belongsTo(Paiement::class);
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec la caisse (si vente en caisse)
     */
    public function caisse()
    {
        return $this->belongsTo(Caisse::class);
    }

    public function hotesse()
    {
        return $this->belongsTo(Hotesse::class);
    }

    /**
     * Relation avec le programme (voyage)
     */
    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    /**
     * Relation avec l'agent qui a scanné
     */
    public function agentEmbarquement()
    {
        return $this->belongsTo(Agent::class, 'embarquement_agent_id');
    }

    /**
     * Relation avec le véhicule d'embarquement
     */
    public function embarquementVehicule()
    {
        return $this->belongsTo(Vehicule::class, 'embarquement_vehicule_id');
    }

    // ========================================
    // HELPERS
    // ========================================

    /**
     * Générer une référence unique pour une réservation
     * Format: RES-YYYYMMDD-RANDOM-SEAT
     */
    public static function generateReference(int $seatNumber = 1, string $prefix = 'RES'): string
    {
        return strtoupper($prefix) . '-' . date('Ymd') . '-' . strtoupper(Str::random(6)) . '-' . $seatNumber;
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Nom complet du passager
     */
    public function getPassagerNomCompletAttribute(): string
    {
        return $this->passager_prenom . ' ' . $this->passager_nom;
    }

    /**
     * Accessor pour le montant formaté
     */
    public function getMontantFormattedAttribute(): string
    {
        return number_format((float) $this->montant, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Informations du trajet (depuis le programme lié)
     */
    public function getTrajetAttribute()
    {
        return $this->programme ? $this->programme->trajet_complet : null;
    }

    // ========================================
    // STATUS CHECKS
    // ========================================

    /**
     * Vérifier si la réservation est confirmée
     */
    public function isConfirmed(): bool
    {
        return $this->statut === 'confirmee';
    }

    /**
     * Vérifier si la réservation est terminée (scannée)
     */
    public function isTerminee(): bool
    {
        return $this->statut === 'terminee';
    }

    /**
     * Vérifier si la réservation est annulée
     */
    public function isCancelled(): bool
    {
        return $this->statut === 'annulee';
    }

    /**
     * Vérifier si la réservation est en attente
     */
    public function isPending(): bool
    {
        return $this->statut === 'en_attente';
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope pour les réservations confirmées (prêtes à scanner)
     */
    public function scopeConfirmees($query)
    {
        return $query->where('statut', 'confirmee');
    }

    /**
     * Scope pour les réservations terminées (déjà scannées)
     */
    public function scopeTerminees($query)
    {
        return $query->where('statut', 'terminee');
    }

    /**
     * Scope pour un programme donné
     */
    public function scopeParProgramme($query, $programmeId)
    {
        return $query->where('programme_id', $programmeId);
    }

    /**
     * Scope pour une date de voyage donnée
     */
    public function scopeParDateVoyage($query, $date)
    {
        return $query->whereDate('date_voyage', $date);
    }
}
