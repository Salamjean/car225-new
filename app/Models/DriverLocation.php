<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
    protected $fillable = [
        'voyage_id',
        'convoi_id',
        'personnel_id',
        'latitude',
        'longitude',
        'speed',
        'heading',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'speed' => 'float',
        'heading' => 'float',
    ];

    public function voyage()
    {
        return $this->belongsTo(Voyage::class);
    }

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function convoi()
    {
        return $this->belongsTo(Convoi::class);
    }
}
