<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetCodePasswordGare extends Model
{
     protected $fillable = [
        'code',
        'email',
    ];
}
