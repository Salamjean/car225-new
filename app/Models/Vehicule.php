<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    use HasFactory;

    protected $fillable = [
        'marque',
        'modele',
        'immatriculation',
        'numero_serie',
        'type_range',
        'nombre_place',
        'is_active',
        'motif',
        'compagnie_id',
    ];

    // Relation avec la compagnie
    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }
}