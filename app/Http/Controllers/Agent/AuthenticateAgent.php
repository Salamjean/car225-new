<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\ResetCodePasswordAgent;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AuthenticateAgent extends Controller
{
    public function defineAccess($email){
        $checkSousadminExiste = Agent::where('email', $email)->first();
        if($checkSousadminExiste){
            return view('agent.auth.defineAcces', compact('email'));
        }else{
            return redirect()->route('agent.login');
        };
    }

    public function submitDefineAccess(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
                'code' => 'required|exists:reset_code_password_agents,code',
                'password' => 'required|min:8|same:confirme_password',
                'confirme_password' => 'required|min:8|same:password',
                ], [
                'code.exists' => 'Le code de réinitialisation est invalide.',
                'password.min'=> 'Le mot de passe doit avoir au moins 8 caractères.',
                'confirme_password.min'=> 'Le mot de passe doit avoir au moins 8 caractères.',
                'password.same' => 'Les mots de passe doivent être identiques.',
                'confirme_password.same' => 'Les mots de passe doivent être identiques.',
        ]);

        try {
            $agent = Agent::where('email', $request->email)->first();

            if ($agent) {
                // Mise à jour du mot de passe
                $agent->password = Hash::make($request->password);

                // Traitement de l'image de profil
                if ($request->hasFile('profile_picture')) {
                    // Supprimer l'ancienne photo si elle existe
                    if ($agent->profile_picture) {
                        Storage::delete('public/' . $agent->profile_picture); // Assurez-vous du 'public/' ici
                    }

                    // Stocker la nouvelle image
                    $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
                    $agent->profile_picture = $imagePath;
                }

                $agent->update();

                if ($agent) {
                    $existingcodeagent = ResetCodePasswordAgent::where('email', $agent->email)->count();

                    if ($existingcodeagent > 1) {
                        ResetCodePasswordAgent::where('email', $agent->email)->delete();
                    }
                }

                return redirect()->route('agent.login')->with('success', 'Compte mis à jour avec succès');
            } else {
                return redirect()->route('agent.login')->with('error', 'Email inconnu');
            }
        } catch (\Exception $e) {
            Log::error('Error updating agent profile: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue : ' . $e->getMessage())->withInput();
        }
    }

    public function login(){
        if (auth('agent')->check()) {
            return redirect()->route('agent.dashboard');
        }
        return view('agent.auth.login');
    }

    public function handleLogin(Request $request)
    {
        // Validation des champs du formulaire
        $request->validate([
            'email' => 'required|exists:agents,email',
            'password' => 'required|min:8',
            'nom_device' => 'nullable|string|max:255',
        ], [
            'email.required' => 'Le mail est obligatoire.',
            'email.exists' => 'Cette adresse mail n\'existe pas.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
        ]);

        try {
            // Récupérer l'agent par son email
            $agent = Agent::where('email', $request->email)->first();

            // Vérifier si l'agent est archivé
            if ($agent && $agent->archived_at !== null) {
                return redirect()->back()->with('error', 'Votre compte a été supprimé. Vous ne pouvez pas vous connecter.');
            }

            if (auth('agent')->attempt($request->only('email', 'password'))) {
                // Mettre à jour le nom de l'appareil si fourni
                if ($request->filled('nom_device')) {
                    $agent->update(['nom_device' => $request->nom_device]);
                }
                
                return redirect()->route('agent.dashboard')->with('success', 'Bienvenue sur la page des demandes en attente');
            } else {
                return redirect()->back()->with('error', 'Votre mot de passe est incorrect.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la connexion.');
        }
    }
}
