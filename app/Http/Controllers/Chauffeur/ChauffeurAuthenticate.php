<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChauffeurAuthenticate extends Controller
{
    public function login()
    {
        if (Auth::guard('chauffeur')->check()) {
            return redirect()->route('chauffeur.dashboard');
        }
        return view('chauffeur.auth.login');
    }

    public function handleLogin(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ], [
            'login.required' => 'L\'identifiant ou l\'email est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $loginValue = $request->input('login');

        // Find Personnel (Chauffeur) by email or code_id
        $chauffeur = \App\Models\Personnel::where('email', $loginValue)
            ->orWhere('code_id', $loginValue)
            ->first();

        if (!$chauffeur) {
            return back()->withErrors([
                'login' => 'Les informations d\'identification fournies ne correspondent pas à nos enregistrements.',
            ])->withInput($request->except('password'));
        }

        $field = $chauffeur->email === $loginValue ? 'email' : 'code_id';

        if (Auth::guard('chauffeur')->attempt([$field => $loginValue, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->intended(route('chauffeur.dashboard'));
        }

        return back()->withErrors([
            'password' => 'Le mot de passe fourni est incorrect.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::guard('chauffeur')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('chauffeur.login');
    }
}
