<?php

namespace App\Http\Controllers\Api\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
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
        $agent = Agent::where('email', $identity)
            ->orWhere('contact', $identity)
            ->orWhere('code_id', $identity)
            ->first();

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun agent n\'existe avec cet identifiant.'
            ], 404);
        }

        $identifier = $agent->email ?? $agent->contact ?? $agent->code_id;
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        DB::table('password_reset_tokens')->where('email', $identifier)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $identifier,
            'token' => Hash::make($otpCode),
            'created_at' => now()
        ]);

        $successMail = false;
        $successSms = false;

        if ($agent->email) {
            try {
                Mail::send('emails.otp', ['otp' => $otpCode], function ($message) use ($agent) {
                    $message->to($agent->email)->subject('Code de réinitialisation Agent - Car225');
                });
                $successMail = true;
            } catch (\Exception $e) {
                Log::error('Erreur API envoi Email reset password Agent: ' . $e->getMessage());
            }
        }

        if ($agent->contact) {
            try {
                $smsService = app(\App\Services\SmsService::class);
                $messageSms = "Votre code de reinitialisation Car225 Agent est : {$otpCode}.";
                $successSms = $smsService->sendSms($agent->contact, $messageSms);
            } catch (\Exception $e) {
                Log::error('Erreur API envoi SMS reset password Agent: ' . $e->getMessage());
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

        $agent = Agent::where('email', $request->email)
            ->orWhere('contact', $request->email)
            ->orWhere('code_id', $request->email)
            ->first();

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent introuvable.'
            ], 404);
        }

        $agent->update([
            'password' => Hash::make($request->password)
        ]);
        
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Mot de passe réinitialisé avec succès.'
        ]);
    }
}
