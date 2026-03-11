<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\Caisse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    protected $smsService;

    public function __construct(\App\Services\SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function showResetForm()
    {
        return view('caisse.auth.password-reset');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'identity' => 'required'
        ]);

        $identity = $request->identity;

        // Rechercher le caissier par email, contact ou code_id
        $caisse = Caisse::where('email', $identity)
            ->orWhere('contact', $identity)
            ->orWhere('code_id', $identity)
            ->first();

        if (!$caisse) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun caissier n\'existe avec cet identifiant.'
            ]);
        }

        // Utiliser l'email comme identifiant principal pour le token, ou le contact/code_id si absent
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

        // Envoyer par email si disponible
        if ($caisse->email) {
            try {
                Mail::send('emails.otp', ['otp' => $otpCode, 'email' => $caisse->email], function ($message) use ($caisse) {
                    $message->to($caisse->email)->subject('Code de réinitialisation Caissier - Car225');
                });
                $successMail = true;
            } catch (\Exception $e) {
                Log::error('Erreur envoi Email reset password Caisse: ' . $e->getMessage());
            }
        }

        // Envoyer par SMS si disponible
        if ($caisse->contact) {
            try {
                $message = "Votre code de reinitialisation Car225 Caisse est : {$otpCode}.";
                $successSms = $this->smsService->sendSms($caisse->contact, $message);
            } catch (\Exception $e) {
                Log::error('Erreur envoi SMS reset password Caisse: ' . $e->getMessage());
            }
        }

        if ($successMail || $successSms) {
            $msg = 'Un code OTP a été envoyé';
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

    public function verifyOtp(Request $request)
    {
        $request->validate(['email' => 'required', 'otp' => 'required|string|size:6']);
        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(10)->isPast()) {
            return response()->json(['success' => false, 'message' => 'OTP expiré.'], 422);
        }

        if (!Hash::check($request->otp, $resetRecord->token)) {
            return response()->json(['success' => false, 'message' => 'OTP incorrect.'], 422);
        }

        return response()->json(['success' => true, 'message' => 'OTP vérifié.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'otp' => 'required|string|size:6',
            'password' => 'required|min:8|confirmed'
        ]);

        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (!$resetRecord || !Hash::check($request->otp, $resetRecord->token)) {
            return response()->json(['success' => false, 'message' => 'OTP invalide.'], 422);
        }

        // Trouver la caisse par l'identifiant utilisé
        $caisse = Caisse::where('email', $request->email)
            ->orWhere('contact', $request->email)
            ->orWhere('code_id', $request->email)
            ->first();

        if (!$caisse) {
            return response()->json(['success' => false, 'message' => 'Compte caisse introuvable.'], 404);
        }

        $caisse->update(['password' => Hash::make($request->password)]);
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['success' => true, 'message' => 'Mot de passe réinitialisé.']);
    }
}
