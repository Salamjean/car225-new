<?php

namespace App\Http\Controllers\Api\Caisse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Caisse;
use App\Models\Reservation;

class CaisseController extends Controller
{
    /**
     * Voir le profil de la caissière
     */
    public function profile(Request $request)
    {
        $caisse = $request->user()->load(['compagnie', 'gare']);

        return response()->json([
            'success' => true,
            'caisse' => [
                'id' => $caisse->id,
                'name' => $caisse->name,
                'prenom' => $caisse->prenom,
                'email' => $caisse->email,
                'contact' => $caisse->contact,
                'code_id' => $caisse->code_id,
                'commune' => $caisse->commune,
                'cas_urgence' => $caisse->cas_urgence,
                'tickets' => $caisse->tickets,
                'profile_picture' => $caisse->profile_picture ? 'storage/' . $caisse->profile_picture : null,
                'profile_picture_url' => $caisse->profile_picture 
                    ? 'storage/' . $caisse->profile_picture 
                    : null,
                'compagnie' => $caisse->compagnie ? [
                    'id' => $caisse->compagnie->id,
                    'name' => $caisse->compagnie->name,
                    'logo' => $caisse->compagnie->logo ? 'storage/' . $caisse->compagnie->logo : null,
                ] : null,
                'gare' => $caisse->gare ? [
                    'id' => $caisse->gare->id,
                    'nom_gare' => $caisse->gare->nom_gare,
                    'ville' => $caisse->gare->ville,
                ] : null,
            ],
        ]);
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(Request $request)
    {
        $caisse = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'contact' => 'sometimes|string|max:20',
            'cas_urgence' => 'sometimes|string|max:20',
            'commune' => 'sometimes|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($caisse->profile_picture) {
                Storage::disk('public')->delete($caisse->profile_picture);
            }
            $imagePath = $request->file('profile_picture')->store('caisse_profiles', 'public');
            $validated['profile_picture'] = $imagePath;
        }

        $caisse->update($validated);
        $caisse->load(['compagnie', 'gare']);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'caisse' => [
                'id' => $caisse->id,
                'name' => $caisse->name,
                'prenom' => $caisse->prenom,
                'email' => $caisse->email,
                'contact' => $caisse->contact,
                'code_id' => $caisse->code_id,
                'commune' => $caisse->commune,
                'cas_urgence' => $caisse->cas_urgence,
                'tickets' => $caisse->tickets,
                'profile_picture' => $caisse->profile_picture ? 'storage/' . $caisse->profile_picture : null,
                'profile_picture_url' => $caisse->profile_picture 
                    ? 'storage/' . $caisse->profile_picture 
                    : null,
                'compagnie' => $caisse->compagnie ? [
                    'id' => $caisse->compagnie->id,
                    'name' => $caisse->compagnie->name,
                    'logo' => $caisse->compagnie->logo ? 'storage/' . $caisse->compagnie->logo : null,
                ] : null,
                'gare' => $caisse->gare ? [
                    'id' => $caisse->gare->id,
                    'nom_gare' => $caisse->gare->nom_gare,
                ] : null,
            ],
        ]);
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $caisse = $request->user();

        if (!Hash::check($request->current_password, $caisse->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Le mot de passe actuel est incorrect.',
            ], 422);
        }

        $caisse->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe modifié avec succès',
        ]);
    }
}
