<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reservation extends Model
{
    protected $fillable = [
        'paiement_id',
        'payment_transaction_id',
        'user_id',
        'programme_id',
        'seat_number',
        'passager_nom',
        'passager_prenom',
        'passager_email',
        'passager_telephone',
        'passager_urgence',
        'is_aller_retour',
        'date_voyage',
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
    ];

    /**
     * Relation avec le paiement
     */
    public function paiement()
    {
        return $this->belongsTo(Paiement::class);
    }


    protected $casts = [
        'montant' => 'decimal:2',
        'is_aller_retour' => 'boolean',
        'date_voyage' => 'date',
        'embarquement_scanned_at' => 'datetime',
        'qr_code_data' => 'array',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le programme
     */
    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    /**
     * Relation avec le programme retour
     */
    public function programmeRetour()
    {
        return $this->belongsTo(Programme::class, 'programme_retour_id');
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

    /**
     * Générer une référence unique pour une réservation
     * Format: RES-YYYYMMDD-RANDOM-SEAT
     */
    public static function generateReference(int $seatNumber = 1): string
    {
        return 'RES-' . date('Ymd') . '-' . strtoupper(Str::random(6)) . '-' . $seatNumber;
    }

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
}
