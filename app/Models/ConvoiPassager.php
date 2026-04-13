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
        'email',
    ];

    public function convoi()
    {
        return $this->belongsTo(Convoi::class);
    }
}

