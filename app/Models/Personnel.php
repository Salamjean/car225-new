<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'prenom',
        'type_personnel',
        'email',
        'contact',
        'contact_urgence',
        'statut',
        'profile_image',
        'compagnie_id',
    ];

    /**
     * Relation avec la compagnie
     */
    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }

    // Dans app/Models/Personnel.php
    public function getProfileImageUrlAttribute()
    {
        if (!$this->profile_image) {
            return null;
        }

        return asset('storage/' . $this->profile_image);
    }
}
