<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'compagnie_id',
        'recipient_id',
        'recipient_type',
        'subject',
        'message',
        'is_read',
    ];

    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }

    public function recipient()
    {
        return $this->morphTo();
    }
}
