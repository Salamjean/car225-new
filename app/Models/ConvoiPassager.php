<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvoiPassager extends Model
{
    use HasFactory;

    protected $fillable = [
        'convoi_id',
        'nom',
        'prenoms',
        'contact',
        'contact_urgence',
        'email',
        'device_id',
        'date_naissance',
        'genre',
        'piece_identite',
        'photo_path',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    public function convoi()
    {
        return $this->belongsTo(Convoi::class);
    }
}

