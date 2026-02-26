<?php

namespace App\Models;

use App\Traits\HasCodeId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Personnel extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasCodeId;

    protected $hidden = [
        'password',
    ];

    protected $fillable = [
        'code_id',
        'name',
        'prenom',
        'type_personnel',
        'email',
        'contact',
        'contact_urgence',
        'statut',
        'profile_image',
        'compagnie_id',
        'gare_id',
        'password',
        'fcm_token',
    ];

    /**
     * Relation avec la compagnie
     */
    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }

    public function gare()
    {
        return $this->belongsTo(Gare::class);
    }

    // Dans app/Models/Personnel.php
    public function getProfileImageUrlAttribute()
    {
        if (!$this->profile_image) {
            return null;
        }

        return asset('storage/' . $this->profile_image);
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
