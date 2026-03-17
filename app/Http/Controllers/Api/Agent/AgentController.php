<?php

namespace App\Http\Controllers\Api\Agent;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AgentController extends Controller
{
    /**
     * Obtenir le profil de l'agent
     */
    public function profile(Request $request)
    {
        $agent = $request->user();
        $agent->load(['compagnie', 'gare']);

        return response()->json([
            'success' => true,
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->name,
                'prenom' => $agent->prenom,
                'email' => $agent->email,
                'contact' => $agent->contact,
                'code_id' => $agent->code_id,
                'commune' => $agent->commune,
                'cas_urgence' => $agent->cas_urgence,
                'profile_picture' => $agent->profile_picture ? 'storage/' . $agent->profile_picture : null,
                'profile_picture_url' => $agent->profile_picture 
                    ? 'storage/' . $agent->profile_picture 
                    : null,
                'nom_device' => $agent->nom_device,
                'compagnie' => $agent->compagnie ? [
                    'id' => $agent->compagnie->id,
                    'name' => $agent->compagnie->name,
                    'logo' => $agent->compagnie->logo ? 'storage/' . $agent->compagnie->logo : null,
                ] : null,
                'gare' => $agent->gare ? [
                    'id' => $agent->gare->id,
                    'nom_gare' => $agent->gare->nom_gare,
                ] : null,
            ],
        ]);
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(Request $request)
    {
        $agent = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'contact' => 'sometimes|string|max:20',
            'cas_urgence' => 'sometimes|string|max:20',
            'commune' => 'sometimes|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Gestion de l'upload de la photo de profil
        if ($request->hasFile('profile_picture')) {
            // Supprimer l'ancienne photo si elle existe
            if ($agent->profile_picture) {
                Storage::disk('public')->delete($agent->profile_picture);
            }

            $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = $imagePath;
        }

        $agent->update($validated);
        $agent->load(['compagnie', 'gare']);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->name,
                'prenom' => $agent->prenom,
                'email' => $agent->email,
                'contact' => $agent->contact,
                'code_id' => $agent->code_id,
                'commune' => $agent->commune,
                'cas_urgence' => $agent->cas_urgence,
                'profile_picture' => $agent->profile_picture ? 'storage/' . $agent->profile_picture : null,
                'profile_picture_url' => $agent->profile_picture 
                    ? 'storage/' . $agent->profile_picture 
                    : null,
                'nom_device' => $agent->nom_device,
                'compagnie' => $agent->compagnie ? [
                    'id' => $agent->compagnie->id,
                    'name' => $agent->compagnie->name,
                    'logo' => $agent->compagnie->logo ? 'storage/' . $agent->compagnie->logo : null,
                ] : null,
                'gare' => $agent->gare ? [
                    'id' => $agent->gare->id,
                    'nom_gare' => $agent->gare->nom_gare,
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
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $agent = $request->user();

        if (!Hash::check($request->current_password, $agent->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Le mot de passe actuel est incorrect.',
            ], 422);
        }

        $agent->update([
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

        $agent = $request->user();
        $agent->update([
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
        $agent = $request->user();

        if (!$agent->fcm_token) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun token FCM enregistré. Veuillez d\'abord enregistrer votre token.',
            ], 400);
        }

        $fcmService = app(\App\Services\FcmService::class);
        
        $result = $fcmService->sendNotification(
            $agent->fcm_token,
            '🎫 Test Notification Agent',
            'Votre application agent est bien connectée ! Message de test de Car225.',
            [
                'type' => 'test',
                'timestamp' => now()->toISOString(),
            ]
        );

        return response()->json($result);
    }

    /**
     * Dashboard - Statistiques agent
     */
    /**
     * Dashboard - Statistiques agent
     */
    public function dashboard(Request $request)
    {
        $agent = $request->user();
        $today = Carbon::today();
        $currentTime = Carbon::now()->format('H:i');

        // BILLETS SCANNÉS: Réservations terminées (scannées) pour aujourd'hui
        $billetsScannes = Reservation::whereHas('programme', function($query) use ($agent) {
            $query->where('compagnie_id', $agent->compagnie_id);
        })
        ->whereNotNull('embarquement_scanned_at')
        ->whereDate('embarquement_scanned_at', $today)
        ->count();

        // À SCANNER: Réservations confirmées mais non scannées pour aujourd'hui
        $aScanner = Reservation::whereHas('programme', function($query) use ($agent) {
            $query->where('compagnie_id', $agent->compagnie_id);
        })
        ->where('statut', 'confirmee')
        ->where(function($q) use ($today) {
            $q->whereDate('date_voyage', $today)
              ->orWhereDate('date_retour', $today);
        })
        ->count();

        // Programmes du jour
        $programmesQuery = Programme::where('compagnie_id', $agent->compagnie_id)
            ->where('statut', 'actif')
            ->where(function ($query) use ($today) {
                $query->whereDate('date_depart', $today)
                      ->orWhere(function ($q) use ($today) {
                          $q->where('date_depart', '<=', $today)
                            ->where('date_fin', '>=', $today);
                      });
            })
            ->with(['gareDepart', 'gareArrivee', 'voyages' => function($q) use ($today) {
                $q->whereDate('date_voyage', $today)->with(['vehicule', 'chauffeur']);
            }])
            ->orderBy('heure_depart');

        $programmes = $programmesQuery->get()->map(function($p) use ($today) {
            $voyage = $p->voyages->first();
            $vehicule = $voyage ? $voyage->vehicule : $p->getVehiculeAttribute();
            $chauffeur = $voyage ? $voyage->chauffeur : null;
            
            $placesReservees = $p->getPlacesReserveesForDate($today);
            $totalPlaces = $p->getTotalSeats($today);

            return [
                'id' => $p->id,
                'num_car' => $vehicule ? '#' . $vehicule->immatriculation : '#---',
                'point_depart' => $p->point_depart,
                'point_arrivee' => $p->point_arrive,
                'heure_depart' => $p->heure_depart,
                'heure_arrivee' => $p->heure_arrive,
                'gare_depart' => $p->gareDepart ? $p->gareDepart->nom_gare : 'N/A',
                'gare_arrivee' => $p->gareArrivee ? $p->gareArrivee->nom_gare : 'N/A',
                'chauffeur' => $chauffeur ? $chauffeur->prenom . ' ' . $chauffeur->name : 'Non assigné',
                'occupation' => $placesReservees . ' / ' . $totalPlaces . ' Places',
                'places_reservees' => $placesReservees,
                'total_places' => $totalPlaces,
                'statut' => $voyage ? 'ASSIGNÉ' : 'EN ATTENTE',
                'immatriculation' => $vehicule ? $vehicule->immatriculation : 'N/A',
            ];
        });

        return response()->json([
            'success' => true,
            'stats' => [
                'billets_scannes' => $billetsScannes,
                'a_scanner' => $aScanner,
                'total_programmes' => $programmes->count(),
            ],
            'programmes_du_jour' => $programmes,
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->name,
                'role' => 'AGENT ' . ($agent->compagnie ? $agent->compagnie->name : ''),
                'is_online' => true,
            ]
        ]);
    }
}
