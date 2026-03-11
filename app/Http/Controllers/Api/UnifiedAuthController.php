<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Agent;
use App\Models\Personnel;
use App\Models\Caisse;
use App\Models\Hotesse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UnifiedAuthController extends Controller
{
    /**
     * Connexion unifiée via code_id.
     * Cherche dans les 5 tables et retourne le rôle + token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required_without:code_id|string',
            'code_id' => 'required_without:login|string',
            'password' => 'required|string',
            'fcm_token' => 'nullable|string',
            'nom_device' => 'nullable|string|max:255',
        ], [
            'login.required_without' => 'L\'identifiant (Email, Contact ou Code ID) est obligatoire.',
            'code_id.required_without' => 'L\'identifiant (Email, Contact ou Code ID) est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $loginValue = $request->login ?? $request->code_id;
        $password = $request->password;

        // Rechercher dans chaque table par ordre de priorité
        $searchOrder = [
            ['model' => User::class, 'role' => 'user', 'guard' => 'web'],
            ['model' => Agent::class, 'role' => 'agent', 'guard' => 'agent'],
            ['model' => Personnel::class, 'role' => 'chauffeur', 'guard' => 'chauffeur'],
            ['model' => Caisse::class, 'role' => 'caisse', 'guard' => 'caisse'],
            ['model' => Hotesse::class, 'role' => 'hotesse', 'guard' => 'hotesse'],
        ];

        foreach ($searchOrder as $search) {
            $query = $search['model']::where('code_id', $loginValue);

            if ($search['role'] === 'user') {
                // Pour l'utilisateur : contact ou code_id
                $query->orWhere('contact', $loginValue);
            } else {
                // Pour caisse, hotesse, personnel et agent : mail ou code_id
                $query->orWhere('email', $loginValue);
                // Optionnellement on peut aussi garder le contact pour la flexibilité si besoin, 
                // mais la consigne dit "mail et code_id"
            }

            $entity = $query->first();

            if ($entity) {
                return $this->attemptLogin($entity, $password, $search['role'], $request);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Identifiant invalide.',
            'errors' => [
                'login' => ['Aucun compte trouvé avec cet identifiant.']
            ]
        ], 422);
    }

    /**
     * Tenter la connexion pour une entité trouvée.
     */
    private function attemptLogin($entity, string $password, string $role, Request $request)
    {
        // Vérifier le mot de passe
        if (!Hash::check($password, $entity->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Le mot de passe est incorrect.',
                'errors' => [
                    'password' => ['Le mot de passe est incorrect.']
                ]
            ], 422);
        }

        // Vérifications spécifiques selon le rôle
        if ($role === 'user') {
            if (!$entity->is_active) {
                if ($entity->deactivated_at) {
                    $deletionDate = $entity->deactivated_at->copy()->addDays(30);
                    if (now()->greaterThanOrEqualTo($deletionDate)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Votre compte a été supprimé définitivement.',
                        ], 403);
                    } else {
                        $entity->update(['is_active' => true, 'deactivated_at' => null]);
                    }
                } else {
                    $entity->update(['is_active' => true]);
                }
            }
        }

        // Vérifier si agent/caisse est archivé
        if (in_array($role, ['agent', 'caisse', 'hotesse'])) {
            if ($entity->archived_at !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte a été désactivé. Contactez votre compagnie.',
                ], 403);
            }
        }

        // Mettre à jour le FCM token si fourni
        if ($request->filled('fcm_token') && in_array('fcm_token', $entity->getFillable())) {
            $entity->update(['fcm_token' => $request->fcm_token]);
        }

        // Créer le token Sanctum
        $token = $entity->createToken($role . '-mobile-app')->plainTextToken;

        // Construire le profil selon le rôle
        $profile = $this->buildProfile($entity, $role);

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'role' => $role,
            'profile' => $profile,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Construire les données de profil selon le rôle.
     */
    private function buildProfile($entity, string $role): array
    {
        $base = [
            'id' => $entity->id,
            'code_id' => $entity->code_id,
            'name' => $entity->name,
            'prenom' => $entity->prenom,
            'email' => $entity->email,
            'contact' => $entity->contact ?? null,
        ];

        switch ($role) {
            case 'user':
                $base['photo_profile_path'] = $entity->photo_profile_path
                    ? (str_starts_with($entity->photo_profile_path, 'http') ? $entity->photo_profile_path : asset('storage/' . $entity->photo_profile_path))
                    : null;
                $base['solde'] = $entity->solde ?? '0.00';
                break;

            case 'agent':
                $base['profile_picture'] = $entity->profile_picture ? asset('storage/' . $entity->profile_picture) : null;
                $base['commune'] = $entity->commune;
                $base['compagnie_id'] = $entity->compagnie_id;
                $base['compagnie'] = $entity->compagnie ? [
                    'id' => $entity->compagnie->id,
                    'name' => $entity->compagnie->name,
                    'logo' => $entity->compagnie->logo,
                ] : null;
                break;

            case 'chauffeur':
                $base['profile_image'] = $entity->profile_image ? asset('storage/' . $entity->profile_image) : null;
                $base['type_personnel'] = $entity->type_personnel;
                $base['statut'] = $entity->statut;
                $base['compagnie_id'] = $entity->compagnie_id;
                $base['compagnie'] = $entity->compagnie ? [
                    'id' => $entity->compagnie->id,
                    'name' => $entity->compagnie->name,
                    'logo' => $entity->compagnie->logo,
                ] : null;
                break;

            case 'caisse':
                $base['profile_picture'] = $entity->profile_picture ? asset('storage/' . $entity->profile_picture) : null;
                $base['compagnie_id'] = $entity->compagnie_id;
                $base['gare_id'] = $entity->gare_id;
                $base['tickets'] = $entity->tickets ?? 0;
                break;

            case 'hotesse':
                $base['profile_picture'] = $entity->profile_picture ? asset('storage/' . $entity->profile_picture) : null;
                $base['compagnie_id'] = $entity->compagnie_id;
                $base['tickets'] = $entity->tickets ?? 0;
                break;
        }

        return $base;
    }
}
