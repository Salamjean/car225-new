<?php

namespace App\Http\Controllers\Api\Caisse;

use App\Http\Controllers\Controller;
use App\Models\Caisse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Send OTP for password reset
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'identity' => 'required'
        ]);

        $identity = $request->identity;
        $caisse = Caisse::where('email', $identity)
            ->orWhere('contact', $identity)
            ->orWhere('code_id', $identity)
            ->first();

        if (!$caisse) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun caissier n\'existe avec cet identifiant.'
            ], 404);
        }

        $identifier = $caisse->email ?? $caisse->contact ?? $caisse->code_id;
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        DB::table('password_reset_tokens')->where('email', $identifier)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $identifier,
            'token' => Hash::make($otpCode),
            'created_at' => now()
        ]);

        $successMail = false;
        $successSms = false;

        if ($caisse->email) {
            try {
                Mail::send('emails.otp', ['otp' => $otpCode], function ($message) use ($caisse) {
                    $message->to($caisse->email)->subject('Code de réinitialisation Caissier - Car225');
                });
                $successMail = true;
            } catch (\Exception $e) {
                Log::error('Erreur API envoi Email reset password Caisse: ' . $e->getMessage());
            }
        }

        if ($caisse->contact) {
            try {
                $smsService = app(\App\Services\SmsService::class);
                $messageSms = "Votre code de reinitialisation Car225 Caissier est : {$otpCode}.";
                $successSms = $smsService->sendSms($caisse->contact, $messageSms);
            } catch (\Exception $e) {
                Log::error('Erreur API envoi SMS reset password Caisse: ' . $e->getMessage());
            }
        }

        if ($successMail || $successSms) {
            $msg = 'Code de réinitialisation envoyé';
            if ($successMail && $successSms) $msg .= ' par email et SMS.';
            elseif ($successMail) $msg .= ' par email.';
            else $msg .= ' par SMS.';

            return response()->json([
                'success' => true, 
                'message' => $msg, 
                'email' => $identifier
            ]);
        }

        return response()->json([
            'success' => false, 
            'message' => 'Erreur lors de l\'envoi du code. Aucun moyen d\'envoi disponible.'
        ], 500);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required', 
            'otp' => 'required|string|size:6'
        ]);

        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(10)->isPast()) {
            return response()->json([
                'success' => false, 
                'message' => 'OTP expiré ou introuvable.'
            ], 422);
        }

        if (!Hash::check($request->otp, $resetRecord->token)) {
            return response()->json([
                'success' => false, 
                'message' => 'OTP incorrect.'
            ], 422);
        }

        return response()->json([
            'success' => true, 
            'message' => 'OTP vérifié avec succès.'
        ]);
    }

    /**
     * Reset the password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'otp' => 'required|string|size:6',
            'password' => 'required|min:8|confirmed'
        ]);

        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (!$resetRecord || !Hash::check($request->otp, $resetRecord->token)) {
            return response()->json([
                'success' => false, 
                'message' => 'OTP invalide.'
            ], 422);
        }

        $caisse = Caisse::where('email', $request->email)
            ->orWhere('contact', $request->email)
            ->orWhere('code_id', $request->email)
            ->first();

        if (!$caisse) {
            return response()->json([
                'success' => false,
                'message' => 'Caissier introuvable.'
            ], 404);
        }

        $caisse->update([
            'password' => Hash::make($request->password)
        ]);
        
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Mot de passe réinitialisé avec succès.'
        ]);
    }
}
