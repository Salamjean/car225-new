<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    protected $smsService;

    public function __construct(\App\Services\SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Afficher la page de réinitialisation (3 steps)
     */
    public function showResetForm()
    {
        return view('user.auth.password-reset');
    }

    /**
     * Step 1: Envoyer le code OTP par email et SMS
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'identity' => 'required'
        ]);

        $identity = $request->identity;
        
        // Trouver l'utilisateur par email, contact ou code_id
        $user = User::where('email', $identity)
            ->orWhere('contact', $identity)
            ->orWhere('code_id', $identity)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun compte n\'existe avec ces informations.'
            ]);
        }

        $email = $user->email ?? $user->contact ?? $user->code_id;
        $contact = $user->contact;
        
        // Générer un code OTP à 6 chiffres
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Supprimer les anciens codes OTP pour cet identifiant
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();
        
        // Enregistrer le nouveau code OTP
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($otpCode),
            'created_at' => now()
        ]);

        $successMail = false;
        $successSms = false;

        // Envoyer l'email si disponible
        if ($user->email) {
            try {
                Mail::send('emails.otp', ['otp' => $otpCode], function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Code de réinitialisation de mot de passe - Car225');
                });
                $successMail = true;
            } catch (\Exception $e) {
                Log::error('Erreur envoi Email reset password: ' . $e->getMessage());
            }
        }

        // Envoyer le SMS si disponible
        if ($contact) {
            try {
                $message = "Votre code de reinitialisation Car225 est : {$otpCode}. Il expire dans 10 minutes.";
                $successSms = $this->smsService->sendSms($contact, $message);
            } catch (\Exception $e) {
                Log::error('Erreur envoi SMS reset password: ' . $e->getMessage());
            }
        }

        if ($successMail || $successSms) {
            $msg = 'Un code OTP a été envoyé';
            if ($successMail && $successSms) $msg .= ' par email et SMS.';
            elseif ($successMail) $msg .= ' par email.';
            elseif ($successSms) $msg .= ' par SMS.';

            return response()->json([
                'success' => true,
                'message' => $msg,
                'email' => $email // Cet identifiant sera utilisé pour les étapes suivantes
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'envoi du code. Aucun moyen d\'envoi disponible.'
        ], 500);
    }


    /**
     * Step 2: Vérifier le code OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'otp' => 'required|string|size:6'
        ]);

        $email = $request->email;
        $otp = $request->otp;

        // Récupérer le token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Code OTP invalide ou expiré.'
            ], 422);
        }

        // Vérifier l'expiration (10 minutes)
        $createdAt = Carbon::parse($resetRecord->created_at);
        if ($createdAt->addMinutes(10)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Le code OTP a expiré. Veuillez en demander un nouveau.'
            ], 422);
        }

        // Vérifier le code OTP avec Hash::check
        if (!Hash::check($otp, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Le code OTP est incorrect.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Code OTP vérifié avec succès.'
        ]);
    }

    /**
     * Step 3: Réinitialiser le mot de passe
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'otp' => 'required|string|size:6',
            'password' => 'required|min:8|confirmed'
        ], [
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.'
        ]);

        $email = $request->email;
        $otp = $request->otp;

        // Vérifier à nouveau le code OTP
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord || !Hash::check($otp, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Code OTP invalide.'
            ], 422);
        }

        // Vérifier l'expiration
        $createdAt = Carbon::parse($resetRecord->created_at);
        if ($createdAt->addMinutes(10)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Le code OTP a expiré.'
            ], 422);
        }

        // Mettre à jour le mot de passe
        $user = User::where('email', $email)
            ->orWhere('contact', $email)
            ->orWhere('code_id', $email)
            ->first();
            
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Supprimer le token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Votre mot de passe a été réinitialisé avec succès.'
        ]);
    }
}
