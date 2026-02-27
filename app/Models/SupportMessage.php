<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    protected $fillable = [
        'support_request_id',
        'sender_type',
        'message',
    ];

    public function supportRequest()
    {
        return $this->belongsTo(SupportRequest::class);
    }
}
