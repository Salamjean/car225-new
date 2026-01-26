<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Agent extends Authenticatable
{
     use HasFactory, Notifiable, HasApiTokens;
    protected $fillable = [
        'name',
        'prenom',
        'email',
        'contact',
        'password',
        'profile_picture',
        'commune', 
        'cas_urgence', 
        'compagnie_id',
        'archived_at',
        'fcm_token',
    ];

    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class, 'compagnie_id'); 
    }
}
