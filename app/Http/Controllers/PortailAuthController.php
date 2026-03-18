<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compagnie;
use App\Models\Agent;
use App\Models\Hotesse;
use App\Models\Personnel;
use Illuminate\Support\Facades\Auth;
use Exception;

class PortailAuthController extends Controller
{
    /**
     * Affiche la page de connexion unifiée.
     */
    public function showLogin()
    {
        // Si un utilisateur est déjà connecté, on le redirige vers son dashboard
        if (Auth::guard('compagnie')->check()) return redirect()->route('compagnie.dashboard');
        if (Auth::guard('agent')->check()) return redirect()->route('agent.dashboard');
        if (Auth::guard('hotesse')->check()) return redirect()->route('hotesse.dashboard');
        if (Auth::guard('chauffeur')->check()) return redirect()->route('chauffeur.dashboard');
        if (Auth::guard('gare')->check()) return redirect()->route('gare-espace.dashboard');

        return view('portail.auth.login');
    }

    /**
     * Traite la tentative de connexion.
     */
    public function login(Request $request)
    {
        $request->validate([
            'identifiant' => 'required|string',
            'password' => 'required|min:8',
        ], [
            'identifiant.required' => 'L\'email ou le code d\'identification est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit comporter au moins 8 caractères.',
        ]);

        try {
            $identifiant = $request->input('identifiant');
            $password = $request->input('password');

            // --- 1. Vérification côté Compagnie ---
            // Les compagnies se connectent généralement par email (ou contact, selon CompagnieAuthenticate)
            // On vérifie d'abord l'email
            $isEmail = filter_var($identifiant, FILTER_VALIDATE_EMAIL);
            
            if ($isEmail) {
                // --- UNIQUEMENT Compagnie par Email ---
                if (Auth::guard('compagnie')->attempt(['email' => $identifiant, 'password' => $password])) {
                    return redirect()->route('compagnie.dashboard')->with('success', 'Bienvenue sur votre tableau de bord Compagnie !');
                }
            } else {
                // --- UNIQUEMENT Autres par Code ID ---
                
                // 1. Agent
                if (Auth::guard('agent')->attempt(['code_id' => $identifiant, 'password' => $password])) {
                    return redirect()->route('agent.dashboard')->with('success', 'Bienvenue sur votre tableau de bord Agent !');
                }

                // 2. Hôtesse
                if (Auth::guard('hotesse')->attempt(['code_id' => $identifiant, 'password' => $password])) {
                    return redirect()->route('hotesse.dashboard')->with('success', 'Bienvenue sur votre tableau de bord Hôtesse !');
                }

                // 3. Chauffeur (Personnel)
                if (Auth::guard('chauffeur')->attempt(['code_id' => $identifiant, 'password' => $password])) {
                    return redirect()->route('chauffeur.dashboard')->with('success', 'Bienvenue sur votre tableau de bord Chauffeur !');
                }

                // 4. Gare
                if (Auth::guard('gare')->attempt(['code_id' => $identifiant, 'password' => $password])) {
                    return redirect()->route('gare-espace.dashboard')->with('success', 'Bienvenue sur votre espace Gare !');
                }
            }

            // Si aucune correspondance n'est trouvée
            return redirect()->back()
                ->withInput($request->only('identifiant'))
                ->with('error', 'Identifiants incorrects. Veuillez vérifier votre email/code et votre mot de passe.');

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Portail Login Error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput($request->only('identifiant'))
                ->with('error', 'Une erreur est survenue lors de la connexion. Veuillez réessayer.');
        }
    }
}
