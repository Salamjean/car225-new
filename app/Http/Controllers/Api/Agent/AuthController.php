<?php

namespace App\Http\Controllers\Api\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Connexion agent
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
            'fcm_token' => 'nullable|string',
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $agent = Agent::where('email', $request->email)->first();

        if (!$agent) {
            throw ValidationException::withMessages([
                'email' => ['Cette adresse email n\'existe pas.'],
            ]);
        }

        // Vérifier si l'agent est archivé
        if ($agent->archived_at !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte a été supprimé. Vous ne pouvez pas vous connecter.',
            ], 403);
        }

        if (!Hash::check($request->password, $agent->password)) {
            throw ValidationException::withMessages([
                'password' => ['Le mot de passe est incorrect.'],
            ]);
        }

        // Mettre à jour le token FCM si fourni
        if ($request->filled('fcm_token')) {
            $agent->update(['fcm_token' => $request->fcm_token]);
        }

        // Créer un nouveau token (on ne révoque plus les anciens pour permettre plusieurs sessions persistantes)
        $token = $agent->createToken('agent-mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->name,
                'prenom' => $agent->prenom,
                'email' => $agent->email,
                'contact' => $agent->contact,
                'commune' => $agent->commune,
                'profile_picture' => $agent->profile_picture,
                'profile_picture_url' => $agent->profile_picture 
                    ? asset('storage/' . $agent->profile_picture) 
                    : null,
                'compagnie_id' => $agent->compagnie_id,
                'compagnie' => $agent->compagnie ? [
                    'id' => $agent->compagnie->id,
                    'name' => $agent->compagnie->name,
                    'logo' => $agent->compagnie->logo,
                ] : null,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Déconnexion agent
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie',
        ]);
    }
}
