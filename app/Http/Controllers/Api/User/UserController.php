<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Signalement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Obtenir le profil de l'utilisateur
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'contact' => $user->contact,
                'adresse' => $user->adresse,
                'pays' => $user->pays,
                'photo_profile_path' => $user->photo_profile_path,
                'photo_profile_url' => $user->photo_profile_path 
                    ? asset('storage/' . $user->photo_profile_path) 
                    : null,
            ],
        ]);
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'contact' => 'sometimes|string|max:255',
            'adresse' => 'sometimes|string|max:255',
            'photo_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Gestion de l'upload de la photo de profil
        if ($request->hasFile('photo_profile')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->photo_profile_path) {
                Storage::disk('public')->delete($user->photo_profile_path);
            }

            $photoFile = $request->file('photo_profile');
            $photoName = 'profile_' . time() . '_' . Str::random(10) . '.' . $photoFile->getClientOriginalExtension();
            $photoPath = $photoFile->storeAs('users/profiles', $photoName, 'public');
            $validated['photo_profile_path'] = $photoPath;
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'contact' => $user->contact,
                'adresse' => $user->adresse,
                'photo_profile_path' => $user->photo_profile_path,
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
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Le mot de passe actuel est incorrect.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe modifié avec succès',
        ]);
    }

    /**
     * Mettre à jour le token FCM pour les notifications push
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = $request->user();
        $user->update([
            'fcm_token' => $request->fcm_token,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token FCM mis à jour avec succès',
        ]);
    }

    /**
     * Tester l'envoi d'une notification push
     */
    public function testNotification(Request $request)
    {
        $user = $request->user();

        if (!$user->fcm_token) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun token FCM enregistré. Veuillez d\'abord enregistrer votre token.',
            ], 400);
        }

        $fcmService = app(\App\Services\FcmService::class);
        
        $result = $fcmService->sendNotification(
            $user->fcm_token,
            '🔔 Test Notification',
            'Votre application mobile est bien connectée ! Message de test de Car225.',
            [
                'type' => 'test',
                'timestamp' => now()->toISOString(),
            ]
        );

        return response()->json($result);
    }

    /**
     * Dashboard - Statistiques utilisateur
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        // Statistiques globales
        $totalReservations = Reservation::where('user_id', $user->id)
            ->where('statut', 'confirmee')
            ->count();

        $totalSpent = Reservation::where('user_id', $user->id)
            ->where('statut', 'confirmee')
            ->sum('montant');

        $activeReservations = Reservation::where('user_id', $user->id)
            ->where('statut', 'confirmee')
            ->where('date_voyage', '>=', now())
            ->count();

        $totalSignalements = Signalement::where('user_id', $user->id)->count();

        // Réservations récentes
        $recentReservations = Reservation::where('user_id', $user->id)
            ->where('statut', 'confirmee')
            ->with(['programme.compagnie', 'programme.vehicule'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'reference' => $reservation->reference,
                    'date_voyage' => $reservation->date_voyage,
                    'seat_number' => $reservation->seat_number,
                    'montant' => $reservation->montant,
                    'statut' => $reservation->statut,
                    'programme' => $reservation->programme ? [
                        'point_depart' => $reservation->programme->point_depart,
                        'point_arrive' => $reservation->programme->point_arrive,
                        'heure_depart' => $reservation->programme->heure_depart,
                        'compagnie' => $reservation->programme->compagnie->name ?? null,
                    ] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'stats' => [
                'total_reservations' => $totalReservations,
                'total_spent' => $totalSpent,
                'active_reservations' => $activeReservations,
                'total_signalements' => $totalSignalements,
            ],
            'recent_reservations' => $recentReservations,
        ]);
    }
}
