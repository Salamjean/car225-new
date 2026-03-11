<?php

namespace App\Http\Controllers\Api\Caisse;

use App\Http\Controllers\Controller;
use App\Models\Caisse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Connexion caisse
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginValue = $request->login;
        $caisse = Caisse::where('contact', $loginValue)
            ->orWhere('code_id', $loginValue)
            ->first();

        if (!$caisse) {
            return response()->json([
                'success' => false,
                'message' => 'Les identifiants fournis sont incorrects.'
            ], 422);
        }

        if (!Hash::check($request->password, $caisse->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Les identifiants fournis sont incorrects.'
            ], 422);
        }

        // Créer un token
        $token = $caisse->createToken('caisse-mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'caisse' => $caisse,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Déconnexion caisse
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
