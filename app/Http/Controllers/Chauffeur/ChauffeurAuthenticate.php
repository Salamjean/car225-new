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
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('chauffeur')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('chauffeur.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Les informations d\'identification fournies ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('chauffeur')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('chauffeur.login');
    }
}
