<?php

namespace App\Http\Controllers\SapeurPompier;

use App\Http\Controllers\Controller;
use App\Models\SapeurPompier;
use App\Models\ResetCodePasswordSapeurPompier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SapeurPompierAuthenticate extends Controller
{
    public function defineAccess($email)
    {
        $checkExiste = SapeurPompier::where('email', $email)->first();

        if ($checkExiste) {
            return view('sapeur_pompier.auth.defineAcces', compact('email'));
        } else {
            return redirect()->route('sapeur-pompier.login');
        }
        ;
    }

    public function submitDefineAccess(Request $request)
    {

        // Validation des données
        $request->validate([
            'code' => 'required|exists:reset_code_password_sapeur_pompiers,code',
            'password' => 'required|same:confirme_password',
            'confirme_password' => 'required|same:password',
        ], [
            'code.exists' => 'Le code de réinitialisation est invalide.',
            'password.same' => 'Les mots de passe doivent être identiques.',
            'confirme_password.same' => 'Les mots de passe doivent être identiques.',
        ]);
        try {
            $sapeurPompier = SapeurPompier::where('email', $request->email)->first();

            if ($sapeurPompier) {
                // Mise à jour du mot de passe
                $sapeurPompier->password = Hash::make($request->password);
                $sapeurPompier->update();

                if ($sapeurPompier) {
                    $existingcode = ResetCodePasswordSapeurPompier::where('email', $sapeurPompier->email)->count();

                    if ($existingcode >= 1) {
                        ResetCodePasswordSapeurPompier::where('email', $sapeurPompier->email)->delete();
                    }
                }

                return redirect()->route('sapeur-pompier.login')->with('success', 'Compte mis à jour avec succès');
            } else {
                return redirect()->route('sapeur-pompier.login')->with('error', 'Email inconnu');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function login()
    {
        if (Auth::guard('sapeur_pompier')->check()) {
            return redirect()->route('sapeur-pompier.dashboard');
        }
        return view('sapeur_pompier.auth.login');
    }

    public function handleLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'L\'email est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        try {
            $credentials = $request->only('email', 'password');

            if (Auth::guard('sapeur_pompier')->attempt($credentials)) {
                return redirect()->route('sapeur-pompier.dashboard')
                    ->with('success', 'Bienvenue sur votre tableau de bord !');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Email ou mot de passe incorrect.');
            }

        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la connexion.');
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('sapeur_pompier')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('sapeur-pompier.login');
    }
}
