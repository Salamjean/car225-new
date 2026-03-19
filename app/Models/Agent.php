<?php

namespace App\Models;

use App\Traits\HasCodeId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Agent extends Authenticatable
{
     use HasFactory, Notifiable, HasApiTokens, HasCodeId;
    protected $appends = ['nom_complet'];

    protected $fillable = [
        'code_id',
        'name',
        'prenom',
        'email',
        'contact',
        'password',
        'profile_picture',
        'commune', 
        'cas_urgence', 
        'nom_urgence',
        'lien_parente_urgence',
        'compagnie_id',
        'gare_id',
        'archived_at',
        'fcm_token',
        'nom_device',
    ];

    protected $casts = [
        'password' => 'hashed',
        'archived_at' => 'datetime',
    ];

    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->name;
    }

    public function getIsActiveAttribute()
    {
        return $this->archived_at === null;
    }

    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class, 'compagnie_id'); 
    }

    public function gare()
    {
        return $this->belongsTo(Gare::class, 'gare_id');
    }

    public function messages()
    {
        return $this->morphMany(CompanyMessage::class, 'recipient');
    }

    public function receivedGareMessages()
    {
        return $this->morphMany(GareMessage::class, 'recipient');
    }
}
