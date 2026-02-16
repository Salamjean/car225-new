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
    /**
     * Afficher le formulaire de demande de réinitialisation
     */
    public function showLinkRequestForm()
    {
        return view('chauffeur.auth.forgot-password');
    }

    /**
     * Envoyer un OTP par email pour la réinitialisation
     */
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->email;

        // Vérifier que c'est un chauffeur
        $personnel = Personnel::where('email', $email)
            ->where('type_personnel', 'Chauffeur')
            ->first();

        if (!$personnel) {
            return back()->with('error', 'Aucun compte chauffeur n\'est associé à cette adresse email.');
        }

        // Créer un OTP (type 'password_reset_chauffeur' pour éviter les conflits avec l'activation)
        $otpRecord = OtpVerification::createOtp($email, 'password_reset_chauffeur');

        // Envoyer l'email
        try {
            Mail::to($email)->send(new \App\Mail\ChauffeurResetPasswordMail(
                $otpRecord->otp,
                $personnel->prenom . ' ' . $personnel->name,
                $email
            ));

            return redirect()->route('chauffeur.password.reset', ['email' => $email])
                ->with('status', 'Un code de réinitialisation a été envoyé à votre adresse email.');
        } catch (\Exception $e) {
            Log::error('Erreur envoi OTP réinitialisation Chauffeur: ' . $e->getMessage());
            return back()->with('error', 'Impossible d\'envoyer l\'email. Veuillez réessayer plus tard.');
        }
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
            'email' => 'required|email',
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

        // Trouver le personnel
        $personnel = Personnel::where('email', $request->email)
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
