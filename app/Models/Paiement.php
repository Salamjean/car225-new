<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reservation_group_id',
        'amount',
        'currency',
        'transaction_id',
        'payment_token',
        'payment_method',
        'status',
        'payment_details',
        'payment_date',
    ];

    protected $casts = [
        'payment_details' => 'array',
        'payment_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'paiement_id');
    }
}
