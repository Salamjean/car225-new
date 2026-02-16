<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gare extends Model
{
    protected $fillable = [
        'nom_gare',
        'ville',
        'adresse',
        'compagnie_id'
    ];

    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }
}
