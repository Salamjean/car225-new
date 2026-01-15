<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'programme_id',
        'places_reservees',
        'places',
        'date_voyage',
        'nombre_places',
        'montant_total',
        'statut',
        'reference',
        'qr_code',
        'qr_code_path',
        'qr_code_data',
        'embarquement_scanned_at',
        'embarquement_agent_id',
        'embarquement_location',
        'embarquement_status',
        'passagers',
        'is_aller_retour',
    ];

    protected $casts = [
        'places_reservees' => 'array',
        'passagers' => 'array',
        'montant_total' => 'decimal:2',
        'is_aller_retour' => 'boolean',
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
     * Générer une référence unique
     */
    public static function generateReference(): string
    {
        return 'RES-' . time() . '-' . strtoupper(Str::random(6));
    }

    /**
     * Accessor pour les places réservées formatées
     */
    public function getPlacesReserveesFormattedAttribute(): string
    {
        $places = $this->places_reservees ?? [];
        if (empty($places)) {
            return 'Aucune';
        }

        sort($places);
        return implode(', ', $places);
    }

    /**
     * Accessor pour le montant formaté
     */
    public function getMontantTotalFormattedAttribute(): string
    {
        return number_format((float) $this->montant_total, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Vérifier si la réservation est confirmée
     */
    public function isConfirmed(): bool
    {
        return $this->statut === 'confirmee';
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
}
