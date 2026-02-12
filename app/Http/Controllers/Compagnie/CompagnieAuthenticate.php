<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Compagnie;
use App\Models\ResetCodePasswordCompagnie;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompagnieAuthenticate extends Controller
{
    public function defineAccess($email){
        $checkSousadminExiste = Compagnie::where('email', $email)->first();

        if($checkSousadminExiste){
            return view('compagnie.auth.defineAcces', compact('email'));
        }else{
            return redirect()->route('compagnie.login');
        };
    }

    public function submitDefineAccess(Request $request){

        // Validation des données
        $request->validate([
                'code'=>'required|exists:reset_code_password_compagnies,code',
                'password' => 'required|same:confirme_password',
                'confirme_password' => 'required|same:password',
            ], [
                'code.exists' => 'Le code de réinitialisation est invalide.',
                'password.same' => 'Les mots de passe doivent être identiques.',
                'confirme_password.same' => 'Les mots de passe doivent être identiques.',
        ]);
        try {
            $compagnie = Compagnie::where('email', $request->email)->first();

            if ($compagnie) {
                // Mise à jour du mot de passe
                $compagnie->password = Hash::make($request->password);
                $compagnie->update();

                if($compagnie){
                $existingcodehop =  ResetCodePasswordCompagnie::where('email', $compagnie->email)->count();

                if($existingcodehop > 1){
                    ResetCodePasswordCompagnie::where('email', $compagnie->email)->delete();
                }
                }

                return redirect()->route('compagnie.login')->with('success', 'Compte mis à jour avec succès');
            } else {
                return redirect()->route('compagnie.login')->with('error', 'Email inconnu');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function login(){
        if (auth('compagnie')->check()) {
            return redirect()->route('compagnie.dashboard');
        }
        return view('compagnie.auth.login');
    }

    public function handleLogin(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|min:8',
        ], [
            'login.required' => 'L\'email, le contact ou le nom d\'utilisateur est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
        ]);

        try {
            // Déterminer le type de credential
            $login = $request->login;
            $password = $request->password;

            // Vérifier si c'est un email
            if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
                $credentials = ['email' => $login, 'password' => $password];
            }
            // Vérifier si c'est un numéro de téléphone (contient uniquement des chiffres et espaces)
            elseif (preg_match('/^[\+\d\s\-\(\)]+$/', $login)) {
                $credentials = ['contact' => $login, 'password' => $password];
            }
            // Sinon, considérer comme username
            else {
                $credentials = ['username' => $login, 'password' => $password];
            }

            // Vérifier si le credential existe dans la base
            $compagnie = Compagnie::where('email', $login)
                ->orWhere('contact', $login)
                ->orWhere('username', $login)
                ->first();

            if (!$compagnie) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Aucun compte trouvé avec ces identifiants.');
            }

            // Tenter la connexion
            if (auth('compagnie')->attempt($credentials)) {
                return redirect()->route('compagnie.dashboard')
                    ->with('success', 'Bienvenue sur votre tableau de bord !');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Mot de passe incorrect.');
            }

        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la connexion.');
        }
    }
}
