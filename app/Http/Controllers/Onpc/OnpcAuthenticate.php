<?php

namespace App\Http\Controllers\Onpc;

use App\Http\Controllers\Controller;
use App\Models\Onpc;
use App\Models\ResetCodePasswordOnpc;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Authentification des agents ONPC :
 *  - login / logout (email + mot de passe)
 *  - define-access : 1ʳᵉ connexion via OTP reçu par email
 */
class OnpcAuthenticate extends Controller
{
    /**
     * Page de définition initiale du mot de passe via OTP.
     */
    public function defineAccess($email)
    {
        $exists = Onpc::where('email', $email)->first();

        if (!$exists) {
            return redirect()->route('onpc.login');
        }

        return view('onpc.auth.defineAcces', compact('email'));
    }

    public function submitDefineAccess(Request $request)
    {
        $request->validate([
            'email'             => 'required|email',
            'code'              => 'required|exists:reset_code_password_onpcs,code',
            'password'          => 'required|min:8|same:confirme_password',
            'confirme_password' => 'required|same:password',
        ], [
            'code.exists'              => 'Le code de validation est invalide.',
            'password.same'            => 'Les mots de passe doivent être identiques.',
            'confirme_password.same'   => 'Les mots de passe doivent être identiques.',
        ]);

        try {
            $onpc = Onpc::where('email', $request->email)->first();

            if (!$onpc) {
                return redirect()->route('onpc.login')->with('error', 'Email inconnu.');
            }

            // Vérifie que le code appartient bien à cet email.
            $codeRow = ResetCodePasswordOnpc::where('email', $onpc->email)
                ->where('code', $request->code)
                ->first();

            if (!$codeRow) {
                return redirect()->back()->with('error', 'Le code ne correspond pas à cet email.');
            }

            $onpc->password = Hash::make($request->password);
            $onpc->save();

            ResetCodePasswordOnpc::where('email', $onpc->email)->delete();

            return redirect()->route('onpc.login')
                ->with('success', 'Mot de passe défini avec succès. Vous pouvez maintenant vous connecter.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function login()
    {
        if (Auth::guard('onpc')->check()) {
            return redirect()->route('onpc.dashboard');
        }
        return view('onpc.auth.login');
    }

    public function handleLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'L\'email est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        try {
            $credentials = $request->only('email', 'password');

            // On bloque les comptes désactivés.
            $onpc = Onpc::where('email', $credentials['email'])->first();
            if ($onpc && $onpc->statut === 'desactive') {
                return redirect()->back()->withInput()
                    ->with('error', 'Votre compte est désactivé. Contactez l\'administrateur.');
            }

            if (Auth::guard('onpc')->attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->route('onpc.dashboard')
                    ->with('success', 'Bienvenue dans l\'espace ONPC.');
            }

            return redirect()->back()->withInput()
                ->with('error', 'Email ou mot de passe incorrect.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la connexion.');
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('onpc')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('onpc.login');
    }
}
