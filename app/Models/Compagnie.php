<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Compagnie extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'commune',
        'adresse',
        'contact',
        'prefix',
        'sigle',
        'slogan',
        'statut',
        'path_logo',
        'tickets',
        'solde_convoie',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'statut' => 'string',
    ];

    public function gares()
    {
        return $this->hasMany(Gare::class);
    }

    public function agents()
    {
        return $this->hasMany(Agent::class);
    }

    public function historiqueTickets()
    {
        return $this->hasMany(HistoriqueTicket::class);
    }

    public function caisses()
    {
        return $this->hasMany(Caisse::class);
    }

    public function programmes()
    {
        return $this->hasMany(Programme::class);
    }

    public function itineraires()
    {
        return $this->hasMany(Itineraire::class);
    }

    /**
     * Deduct tickets from company balance
     */
    public function deductTickets($amount, $motif)
    {
        // Vérifier si le système de tickets est activé
        if (!\App\Models\Setting::isTicketSystemEnabled()) {
            return;
        }

        // DEBUG: Trace balance deduction
        \Illuminate\Support\Facades\Log::info("DEDUCTION SOLDE APPELÉE", [
            'target_company_id' => $this->id,
            'target_company_name' => $this->name,
            'balance_before' => $this->tickets,
            'deducting' => $amount,
            'motif' => $motif,
        ]);

        $this->decrement('tickets', $amount);
        
        $this->historiqueTickets()->create([
            'quantite' => -$amount, // Valeur négative pour la déduction
            'montant' => $amount,
            'motif' => $motif
        ]);
    }

    public function sentMessages()
    {
        return $this->hasMany(CompanyMessage::class);
    }

    public function receivedGareMessages()
    {
        return $this->morphMany(GareMessage::class, 'recipient');
    }

    public function vehicules()
    {
        return $this->hasMany(Vehicule::class);
    }

    public function convois()
    {
        return $this->hasMany(Convoi::class);
    }

    /**
     * Get the logo path (accessor for backward compatibility)
     */
    public function getLogoAttribute()
    {
        return $this->path_logo;
    }
}
