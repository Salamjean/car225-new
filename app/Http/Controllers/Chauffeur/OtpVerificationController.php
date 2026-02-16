<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OtpVerificationController extends Controller
{
    /**
     * Afficher le formulaire de vérification OTP
     */
    public function showVerifyForm(Request $request)
    {
        $email = $request->query('email');
        
        if (!$email) {
            return redirect()->route('chauffeur.login')
                ->with('error', 'Email manquant.');
        }

        // Vérifier que cet email existe dans la table personnels et est un chauffeur
        $personnel = Personnel::where('email', $email)
            ->where('type_personnel', 'Chauffeur')
            ->first();

        if (!$personnel) {
            return redirect()->route('chauffeur.login')
                ->with('error', 'Chauffeur non trouvé.');
        }

        return view('chauffeur.auth.verify-otp', compact('email'));
    }

    /**
     * Vérifier l'OTP et activer le compte
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'otp.required' => 'Le code OTP est requis.',
            'otp.size' => 'Le code OTP doit contenir exactement 6 chiffres.',
        ]);

        $email = $request->email;
        $otp = $request->otp;

        // Vérifier l'OTP
        $isValid = OtpVerification::verify($email, $otp, 'chauffeur');

        if (!$isValid) {
            return back()->with('error', 'Code OTP invalide ou expiré.');
        }

        // Trouver le personnel
        $personnel = Personnel::where('email', $email)
            ->where('type_personnel', 'Chauffeur')
            ->first();

        if (!$personnel) {
            return back()->with('error', 'Chauffeur non trouvé.');
        }

        // Mettre à jour le mot de passe et le statut
        $personnel->password = Hash::make($request->password);
        $personnel->statut = 'disponible';
        $personnel->save();

        // Connecter l'utilisateur
        \Illuminate\Support\Facades\Auth::guard('chauffeur')->login($personnel);

        return redirect()->route('chauffeur.dashboard')
            ->with('success', 'Compte vérifié et mot de passe défini avec succès ! Bienvenue.');
    }

    /**
     * Renvoyer un nouveau code OTP
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;

        // Vérifier que cet email existe dans la table personnels et est un chauffeur
        $personnel = Personnel::where('email', $email)
            ->where('type_personnel', 'Chauffeur')
            ->first();

        if (!$personnel) {
            return back()->with('error', 'Chauffeur non trouvé.');
        }

        // Créer un nouvel OTP
        $otpRecord = OtpVerification::createOtp($email, 'chauffeur', 10);

        // Envoyer l'email
        try {
            Mail::to($email)
                ->send(new \App\Mail\ChauffeurOtpMail(
                    $otpRecord->otp,
                    $personnel->prenom . ' ' . $personnel->name,
                    $email
                ));

            return back()->with('success', 'Un nouveau code OTP a été envoyé à votre adresse email.');
        } catch (\Exception $e) {
            \Log::error('Erreur renvoi OTP: ' . $e->getMessage());
            return back()->with('error', 'Impossible d\'envoyer le code. Veuillez réessayer plus tard.');
        }
    }
}
