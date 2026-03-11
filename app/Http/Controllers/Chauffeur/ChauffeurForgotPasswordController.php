<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ChauffeurForgotPasswordController extends Controller
{
    protected $smsService;

    public function __construct(\App\Services\SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Afficher le formulaire de demande de réinitialisation
     */
    public function showLinkRequestForm()
    {
        return view('chauffeur.auth.forgot-password');
    }

    /**
     * Envoyer un OTP par email et/ou SMS pour la réinitialisation
     */
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required']);

        $identity = $request->email; // Le champ s'appelle toujours 'email' dans le form mais peut contenir le Code ID ou contact

        // Trouver le personnel par email, contact ou code_id
        $personnel = Personnel::where(function($query) use ($identity) {
                $query->where('email', $identity)
                      ->orWhere('contact', $identity)
                      ->orWhere('code_id', $identity);
            })
            ->where('type_personnel', 'Chauffeur')
            ->first();

        if (!$personnel) {
            return back()->with('error', 'Aucun compte chauffeur n\'est associé à cet identifiant.');
        }

        // Utiliser l'email comme identifiant principal pour le token, ou le contact/code_id si absent
        $identifier = $personnel->email ?? $personnel->contact ?? $personnel->code_id;

        // Créer un OTP (type 'password_reset_chauffeur' pour éviter les conflits avec l'activation)
        $otpRecord = OtpVerification::createOtp($identifier, 'password_reset_chauffeur');

        $successMail = false;
        $successSms = false;

        // Envoyer l'email si dispo
        if ($personnel->email) {
            try {
                Mail::to($personnel->email)->send(new \App\Mail\ChauffeurResetPasswordMail(
                    $otpRecord->otp,
                    $personnel->prenom . ' ' . $personnel->name,
                    $personnel->email
                ));
                $successMail = true;
            } catch (\Exception $e) {
                Log::error('Erreur envoi OTP réinitialisation Email Chauffeur: ' . $e->getMessage());
            }
        }

        // Envoyer le SMS si dispo
        if ($personnel->contact) {
            try {
                $message = "Votre code de reinitialisation Car225 Chauffeur est : {$otpRecord->otp}.";
                $successSms = $this->smsService->sendSms($personnel->contact, $message);
            } catch (\Exception $e) {
                Log::error('Erreur envoi OTP réinitialisation SMS Chauffeur: ' . $e->getMessage());
            }
        }

        if ($successMail || $successSms) {
            $msg = 'Un code de réinitialisation a été envoyé';
            if ($successMail && $successSms) $msg .= ' par email et SMS.';
            elseif ($successMail) $msg .= ' par email.';
            else $msg .= ' par SMS.';

            return redirect()->route('chauffeur.password.reset', ['email' => $identifier])
                ->with('status', $msg);
        }

        return back()->with('error', 'Impossible d\'envoyer le code. Aucun moyen d\'envoi disponible.');
    }

    /**
     * Afficher le formulaire de réinitialisation
     */
    public function showResetForm(Request $request)
    {
        $email = $request->email;
        if (!$email) {
            return redirect()->route('chauffeur.password.request');
        }
        return view('chauffeur.auth.reset-password', compact('email'));
    }

    /**
     * Réinitialiser le mot de passe après vérification de l'OTP
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'otp.size' => 'Le code OTP doit contenir 6 chiffres.',
        ]);

        // Vérifier l'OTP
        $isValid = OtpVerification::verify($request->email, $request->otp, 'password_reset_chauffeur');

        if (!$isValid) {
            return back()->with('error', 'Code de vérification invalide ou expiré.');
        }

        // Trouver le personnel par l'identifiant
        $personnel = Personnel::where(function($query) use ($request) {
                $query->where('email', $request->email)
                      ->orWhere('contact', $request->email)
                      ->orWhere('code_id', $request->email);
            })
            ->where('type_personnel', 'Chauffeur')
            ->first();

        if (!$personnel) {
            return back()->with('error', 'Chauffeur non trouvé.');
        }

        // Mettre à jour le mot de passe
        $personnel->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('chauffeur.login')
            ->with('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }
}
