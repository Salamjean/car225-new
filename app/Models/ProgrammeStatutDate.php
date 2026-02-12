<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgrammeStatutDate extends Model
{
    use HasFactory;

    protected $table = 'programme_statuts_date';

    protected $fillable = [
        'programme_id',
        'date_voyage',
        'nbre_siege_occupe',
        'staut_place'
    ];

    protected $casts = [
        'date_voyage' => 'date',
    ];

    /**
     * Relation avec le programme
     */
    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }
}