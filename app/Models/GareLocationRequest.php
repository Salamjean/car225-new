<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GareLocationRequest extends Model
{
    protected $fillable = [
        'gare_id',
        'compagnie_id',
        'latitude',
        'longitude',
        'statut',
        'rejected_reason',
        'approved_at',
        'gare_notified',
    ];

    protected $casts = [
        'latitude'      => 'float',
        'longitude'     => 'float',
        'approved_at'   => 'datetime',
        'gare_notified' => 'boolean',
    ];

    public function gare()
    {
        return $this->belongsTo(Gare::class);
    }

    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }
}
