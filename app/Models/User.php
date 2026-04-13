<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasCodeId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasCodeId;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code_id',
        'name',
        'prenom',
        'email',
        'google_id',
        'google_token',
        'google_refresh_token',
        'contact',
        'contact_urgence',
        'nom_urgence',
        'prenom_urgence',
        'lien_parente_urgence',
        'email_verified_at',
        'photo_profile_path',
        'password',
        'fcm_token',
        'nom_device',
        'solde',
        'is_active',
        'deactivated_at',
        'phone_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'solde' => 'decimal:2',
            'is_active' => 'boolean',
            'deactivated_at' => 'datetime',
        ];
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function convois()
    {
        return $this->hasMany(Convoi::class);
    }
}
