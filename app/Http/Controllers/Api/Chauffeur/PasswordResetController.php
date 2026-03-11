<?php

namespace App\Http\Controllers\Api\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use App\Models\OtpVerification;
use Illuminate\Http\Request;
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
        $personnel = Personnel::where(function($query) use ($identity) {
                $query->where('email', $identity)
                      ->orWhere('contact', $identity)
                      ->orWhere('code_id', $identity);
            })
            ->where('type_personnel', 'Chauffeur')
            ->first();

        if (!$personnel) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun chauffeur n\'existe avec cet identifiant.'
            ], 404);
        }

        $identifier = $personnel->email ?? $personnel->contact ?? $personnel->code_id;
        
        // Criar OTP (usando o sistema existente de OtpVerification se disponível, senão fallback manual)
        // Note: Chauffeur web uses OtpVerification::createOtp
        $otpRecord = OtpVerification::createOtp($identifier, 'password_reset_chauffeur');

        $successMail = false;
        $successSms = false;

        if ($personnel->email) {
            try {
                Mail::to($personnel->email)->send(new \App\Mail\ChauffeurResetPasswordMail(
                    $otpRecord->otp,
                    $personnel->prenom . ' ' . $personnel->name,
                    $personnel->email
                ));
                $successMail = true;
            } catch (\Exception $e) {
                Log::error('Erreur API envoi Email reset password Chauffeur: ' . $e->getMessage());
            }
        }

        if ($personnel->contact) {
            try {
                $smsService = app(\App\Services\SmsService::class);
                $messageSms = "Votre code de reinitialisation Car225 Chauffeur est : {$otpRecord->otp}.";
                $successSms = $smsService->sendSms($personnel->contact, $messageSms);
            } catch (\Exception $e) {
                Log::error('Erreur API envoi SMS reset password Chauffeur: ' . $e->getMessage());
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

        $isValid = OtpVerification::verify($request->email, $request->otp, 'password_reset_chauffeur');

        if (!$isValid) {
            return response()->json([
                'success' => false, 
                'message' => 'OTP incorrect ou expiré.'
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

        $isValid = OtpVerification::verify($request->email, $request->otp, 'password_reset_chauffeur');
        if (!$isValid) {
            return response()->json([
                'success' => false, 
                'message' => 'OTP invalide.'
            ], 422);
        }

        $chauffeur = Personnel::where(function($query) use ($request) {
                $query->where('email', $request->email)
                      ->orWhere('contact', $request->email)
                      ->orWhere('code_id', $request->email);
            })
            ->where('type_personnel', 'Chauffeur')
            ->first();

        if (!$chauffeur) {
            return response()->json([
                'success' => false,
                'message' => 'Chauffeur introuvable.'
            ], 404);
        }

        $chauffeur->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Mot de passe réinitialisé avec succès.'
        ]);
    }
}
