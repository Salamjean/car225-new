<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OtpVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'otp',
        'type',
        'expires_at',
        'verified'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified' => 'boolean',
    ];

    /**
     * Create or update an OTP for a given email and type
     */
    public static function createOtp(string $email, string $type = 'chauffeur', int $expiryMinutes = 10): self
    {
        // Delete any existing valid OTPs for this email/type to avoid confusion
        self::where('email', $email)
            ->where('type', $type)
            ->delete();

        return self::create([
            'email' => $email,
            'otp' => (string) rand(100000, 999999),
            'type' => $type,
            'expires_at' => Carbon::now()->addMinutes($expiryMinutes), // Valid for 10 minutes by default
            'verified' => false
        ]);
    }

    /**
     * Verify an OTP
     */
    public static function verify(string $email, string $otp, string $type = 'chauffeur'): bool
    {
        $record = self::where('email', $email)
            ->where('type', $type)
            ->where('otp', $otp)
            ->where('verified', false)
            ->where('expires_at', '>', Carbon::now()) // Check if OTP is still valid
            ->first();

        if ($record) {
            $record->update(['verified' => true]);
            return true;
        }

        return false;
    }
}
