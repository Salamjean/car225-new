<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'compagnie_id',
        'quantite',
        'montant',
        'motif',
    ];

    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }
}
