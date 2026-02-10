<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HotesseOtpService
{
    /**
     * Generate a 6-digit OTP code
     */
    public function generateCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Store OTP code for an email
     */
    public function storeOtp(string $email, string $code, int $expiryMinutes = 525600): void // 1 year in minutes
    {
        // Delete any existing OTPs for this email
        DB::table('hotesse_otp_codes')
            ->where('email', $email)
            ->delete();

        // Insert new OTP
        DB::table('hotesse_otp_codes')->insert([
            'email' => $email,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes($expiryMinutes),
            'verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(string $email, string $code): bool
    {
        $otp = DB::table('hotesse_otp_codes')
            ->where('email', $email)
            ->where('code', $code)
            ->where('verified', false)
            ->first();

        if ($otp) {
            // Mark as verified
            DB::table('hotesse_otp_codes')
                ->where('id', $otp->id)
                ->update(['verified' => true, 'updated_at' => now()]);

            return true;
        }

        return false;
    }

    /**
     * Check if email has a verified OTP
     */
    public function hasVerifiedOtp(string $email): bool
    {
        return DB::table('hotesse_otp_codes')
            ->where('email', $email)
            ->where('verified', true)
            ->exists();
    }

    /**
     * Clean up expired OTPs
     */
    public function cleanExpiredOtps(): int
    {
        return DB::table('hotesse_otp_codes')
            ->where('expires_at', '<', Carbon::now())
            ->delete();
    }

    /**
     * Delete OTP for email (after successful password setup)
     */
    public function deleteOtp(string $email): void
    {
        DB::table('hotesse_otp_codes')
            ->where('email', $email)
            ->delete();
    }
}
