<?php

namespace App\Http\Controllers\Api\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Connexion chauffeur
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required|min:6',
            'fcm_token' => 'nullable|string',
        ]);

        $loginValue = $request->login;
        $chauffeur = Personnel::where(function($query) use ($loginValue) {
                $query->where('contact', $loginValue)
                      ->orWhere('code_id', $loginValue);
            })
            ->where('type_personnel', 'Chauffeur')
            ->first();

        if (!$chauffeur) {
            throw ValidationException::withMessages([
                'login' => ['Cet identifiant n\'existe pas ou ne correspond pas à un chauffeur.'],
            ]);
        }

        if (!Hash::check($request->password, $chauffeur->password)) {
            throw ValidationException::withMessages([
                'password' => ['Le mot de passe est incorrect.'],
            ]);
        }

        // Mettre à jour le token FCM si fourni
        if ($request->filled('fcm_token')) {
            $chauffeur->update(['fcm_token' => $request->fcm_token]);
        }

        $token = $chauffeur->createToken('chauffeur-mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'chauffeur' => [
                'id' => $chauffeur->id,
                'code_id' => $chauffeur->code_id,
                'nom' => $chauffeur->nom,
                'prenom' => $chauffeur->prenom,
                'email' => $chauffeur->email,
                'telephone' => $chauffeur->telephone,
                'role' => $chauffeur->role,
                'statut' => $chauffeur->statut,
                'compagnie_id' => $chauffeur->compagnie_id,
                'gare_id' => $chauffeur->gare_id,
                'compagnie' => $chauffeur->compagnie ? [
                    'id' => $chauffeur->compagnie->id,
                    'name' => $chauffeur->compagnie->name,
                    'logo' => $chauffeur->compagnie->logo,
                ] : null,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Déconnexion chauffeur
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
