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
                'nom_urgence' => $user->nom_urgence,
                'prenom_urgence' => $user->prenom_urgence,
                'contact_urgence' => $user->contact_urgence,
                'pays' => $user->pays,
                'photo_profile_path' => $user->photo_profile_path ? 'storage/' . $user->photo_profile_path : null,
                'photo_profile_url' => $user->photo_profile_path 
                    ? asset('storage/' . $user->photo_profile_path) 
                    : null,
                'nom_device' => $user->nom_device,
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
            'nom_urgence' => 'nullable|string|max:255',
            'prenom_urgence' => 'nullable|string|max:255',
            'contact_urgence' => 'nullable|string|max:20|different:contact',
            'photo_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'contact_urgence.different' => 'Le contact d\'urgence doit être différent de votre contact principal.',
        ]);

        // Gestion de l'upload de la photo de profil
        if ($request->hasFile('photo_profile')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->photo_profile_path && Storage::disk('public')->exists($user->photo_profile_path)) {
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
                'nom_urgence' => $user->nom_urgence,
                'prenom_urgence' => $user->prenom_urgence,
                'contact_urgence' => $user->contact_urgence,
                'photo_profile_path' => $user->photo_profile_path ? 'storage/' . $user->photo_profile_path : null,
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
            ->with(['programme.compagnie'])
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

    /**
     * API JSON: Returns the current location of the active voyage for the user.
     */
    public function getTrackingLocation()
    {
        $user = Auth::user();

        // Find the active trip for the user
        $currentTrip = \App\Models\Reservation::where('user_id', $user->id)
            ->whereIn('statut', ['confirmee', 'terminee'])
            ->whereHas('programme.voyages', function($q) {
                $q->where('statut', 'en_cours')
                  ->whereColumn('voyages.date_voyage', 'reservations.date_voyage');
            })
            ->with(['programme.voyages' => function($q) {
                $q->where('statut', 'en_cours');
            }])
            ->first();

        if (!$currentTrip || !$currentTrip->programme || $currentTrip->programme->voyages->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Aucun voyage en cours.']);
        }

        $voyage = $currentTrip->programme->voyages->first();
        $location = $voyage->latestLocation;

        if (!$location) {
            return response()->json(['success' => false, 'message' => 'Aucune position GPS disponible.']);
        }

        return response()->json([
            'success' => true,
            'location' => [
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
                'speed' => $location->speed,
                'heading' => $location->heading,
                'last_update' => $location->updated_at->diffForHumans(),
                'chauffeur' => $voyage->chauffeur ? $voyage->chauffeur->nom . ' ' . $voyage->chauffeur->prenom : 'Inconnu',
                'vehicule' => $voyage->vehicule ? $voyage->vehicule->immatriculation : 'N/A',
                'depart' => optional($voyage->programme->gareDepart)->nom_gare ?? $voyage->programme->point_depart,
                'arrivee' => optional($voyage->programme->gareArrivee)->nom_gare ?? $voyage->programme->point_arrive,
                'heure_depart' => $voyage->programme->heure_depart,
                'heure_arrivee' => $voyage->programme->heure_arrive,
                'date_voyage' => \Carbon\Carbon::parse($voyage->date_voyage)->format('d/m/Y'),
                'temps_restant' => $voyage->temps_restant,
            ]
        ]);
    }

    /**
     * Obtenir la liste des appareils connectés
     */
    public function getDevices(Request $request)
    {
        $user = $request->user();
        
        $devices = $user->devices()
            ->orderBy('last_login_at', 'desc')
            ->get()
            ->map(function ($device) {
                return [
                    'nom_device' => $device->nom_device,
                    'ip_address' => $device->ip_address,
                    'last_login_at' => $device->last_login_at->format('d/m/Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'devices' => $devices
        ]);
    }

    /**
     * Désactiver le compte utilisateur
     */
    public function deactivateAccount(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Deactivate Account Payload:', $request->all());

        $user = $request->user();

        // Validation du mot de passe pour confirmer la désactivation
        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mot de passe incorrect.',
            ], 422);
        }

        $user->update([
            'is_active' => false,
            'deactivated_at' => now(),
        ]);

        // On peut révoquer les tokens ici si on veut forcer la déconnexion
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Votre compte a été désactivé. Il sera supprimé définitivement dans 30 jours si vous ne vous reconnectez pas.',
        ]);
    }
}
