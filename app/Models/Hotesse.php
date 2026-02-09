<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Hotesse extends Authenticatable
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
     * Get the company that owns the hostess.
     */
    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }

    /**
     * Check if hostess is archived.
     */
    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    /**
     * Archive the hostess.
     */
    public function archive()
    {
        $this->update(['archived_at' => now()]);
    }

    /**
     * Unarchive the hostess.
     */
    public function unarchive()
    {
        $this->update(['archived_at' => null]);
    }

    /**
     * Add tickets to hostess balance (if applicable).
     */
    public function addTickets(int $quantity)
    {
        $this->increment('tickets', $quantity);
    }

    /**
     * Deduct tickets from hostess balance (if applicable).
     */
    public function deductTickets(int $quantity)
    {
        $this->decrement('tickets', $quantity);
    }
}
