<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GareMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'gare_id',
        'recipient_type',
        'recipient_id',
        'subject',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function gare()
    {
        return $this->belongsTo(Gare::class);
    }

    public function recipient()
    {
        return $this->morphTo();
    }

    /**
     * Get the formatted recipient type name for display.
     */
    public function getRecipientTypeLabelAttribute()
    {
        $map = [
            'App\Models\Agent' => 'Agent',
            'App\Models\Caisse' => 'Caisse',
            'App\Models\Personnel' => 'Chauffeur',
            'App\Models\Compagnie' => 'Compagnie',
        ];

        return $map[$this->recipient_type] ?? 'Inconnu';
    }
}
