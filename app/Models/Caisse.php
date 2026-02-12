<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Caisse extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'prenom',
        'email',
        'contact',
        'cas_urgence',
        'commune',
        'password',
        'profile_picture',
        'compagnie_id',
        'archived_at',
        'tickets',
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
        'password' => 'hashed',
        'archived_at' => 'datetime',
    ];

    /**
     * Get the company that owns the cashier.
     */
    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }

    /**
     * Check if cashier is archived.
     */
    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    /**
     * Archive the cashier.
     */
    public function archive()
    {
        $this->update(['archived_at' => now()]);
    }

    /**
     * Unarchive the cashier.
     */
    public function unarchive()
    {
        $this->update(['archived_at' => null]);
    }

    /**
     * Add tickets to cashier balance.
     */
    public function addTickets(int $quantity)
    {
        $this->increment('tickets', $quantity);
    }

    /**
     * Deduct tickets from cashier balance.
     */
    public function deductTickets(int $quantity)
    {
        $this->decrement('tickets', $quantity);
    }
}
