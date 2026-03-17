<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Models\Gare;
use App\Models\OtpVerification;
use App\Models\ResetCodePasswordGare;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AuthenticateGare extends Controller
{
    /**
     * Show OTP verification page for new gare managers
     */
    public function verifyOtp(Request $request)
    {
        $email = $request->query('email');
        if (!$email) {
            return redirect()->route('gare-espace.login');
        }

        $gare = Gare::where('email', $email)->first();
        if (!$gare) {
            return redirect()->route('gare-espace.login')->with('error', 'Email inconnu.');
        }

        return view('gare-espace.auth.verify-otp', compact('email'));
    }

    /**
     * Handle OTP verification submission
     */
    public function handleVerifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:gares,email',
            'otp' => 'required|string|min:6|max:6',
        ], [
            'email.exists' => "Cette adresse email n'existe pas.",
            'otp.required' => 'Le code OTP est obligatoire.',
        ]);

        $verified = OtpVerification::verify($request->email, $request->otp, 'gare');

        if ($verified) {
            // Create a reset code for the next step (define password)
            ResetCodePasswordGare::where('email', $request->email)->delete();
            $generatedCode = rand(100000, 999999);
            ResetCodePasswordGare::create([
                'email' => $request->email,
                'code' => $generatedCode,
            ]);

            return redirect()
                ->route('gare-espace.defineAccess', ['email' => $request->email])
                ->with('success', 'Code OTP vérifié avec succès ! Définissez maintenant votre mot de passe.')
                ->with('verification_code', $generatedCode);
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Le code OTP est invalide ou expiré.');
    }

    /**
     * Show the define access page (password setup)
     */
    public function defineAccess($email)
    {
        $gare = Gare::where('email', $email)->first();
        if ($gare) {
            // Retrieve code from session or from DB
            $code = session('verification_code');
            if (!$code) {
                $resetEntry = ResetCodePasswordGare::where('email', $email)->latest()->first();
                $code = $resetEntry ? $resetEntry->code : null;
            }
            return view('gare-espace.auth.defineAcces', compact('email', 'code'));
        } else {
            return redirect()->route('gare-espace.login');
        }
    }

    /**
     * Handle password setup submission
     */
    public function submitDefineAccess(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|exists:reset_code_password_gares,code',
            'password' => 'required|min:8|same:confirme_password',
            'confirme_password' => 'required|min:8|same:password',
        ], [
            'code.exists' => 'Le code de vérification est invalide.',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
            'confirme_password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
            'password.same' => 'Les mots de passe doivent être identiques.',
            'confirme_password.same' => 'Les mots de passe doivent être identiques.',
        ]);

        try {
            $gare = Gare::where('email', $request->email)->first();

            if ($gare) {
                $gare->password = Hash::make($request->password);

                if ($request->hasFile('profile_image')) {
                    if ($gare->profile_image) {
                        Storage::delete('public/' . $gare->profile_image);
                    }
                    $imagePath = $request->file('profile_image')->store('gares', 'public');
                    $gare->profile_image = $imagePath;
                }

                $gare->update();

                // Clean up reset codes
                $existingCodes = ResetCodePasswordGare::where('email', $gare->email)->count();
                if ($existingCodes > 1) {
                    ResetCodePasswordGare::where('email', $gare->email)->delete();
                }

                return redirect()->route('portail.login')->with('success', 'Compte configuré avec succès ! Connectez-vous maintenant.');
            } else {
                return redirect()->route('gare-espace.login')->with('error', 'Email inconnu');
            }
        } catch (\Exception $e) {
            Log::error('Error setting up gare account: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue : ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show login page
     */
    public function login()
    {
        if (auth('gare')->check()) {
            return redirect()->route('gare-espace.dashboard');
        }
        return view('gare-espace.auth.login');
    }

    /**
     * Handle login attempt
     */
    public function handleLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:gares,email',
            'password' => 'required|min:8',
        ], [
            'email.required' => "Le mail est obligatoire.",
            'email.exists' => "Cette adresse mail n'existe pas.",
            'password.required' => "Le mot de passe est obligatoire.",
            'password.min' => "Le mot de passe doit avoir au moins 8 caractères.",
        ]);

        try {
            if (auth('gare')->attempt($request->only('email', 'password'))) {
                return redirect()->route('gare-espace.dashboard')->with('success', 'Bienvenue dans votre espace gare');
            } else {
                return redirect()->back()->with('error', 'Votre mot de passe est incorrect.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', "Une erreur s'est produite lors de la connexion.");
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        auth('gare')->logout();
        return redirect()->route('portail.login')->with('success', 'Déconnexion réussie.');
    }
}
