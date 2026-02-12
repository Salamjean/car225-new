<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetCodePasswordCompagnie extends Model
{
    protected $fillable = ['code', 'email'];
}
