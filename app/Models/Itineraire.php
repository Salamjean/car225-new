<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Itineraire extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'point_depart',
        'point_arrive',
        'durer_parcours',
        'compagnie_id',
    ];

    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class); 
    }
}
