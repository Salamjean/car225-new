<?php

namespace App\Http\Controllers\Api\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Voyage;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ChauffeurApiController extends Controller
{
    /**
     * Profil du chauffeur
     */
    public function profile(Request $request)
    {
        $chauffeur = $request->user();

        return response()->json([
            'success' => true,
            'chauffeur' => [
                'id' => $chauffeur->id,
                'code_id' => $chauffeur->code_id,
                'nom' => $chauffeur->nom,
                'prenom' => $chauffeur->prenom,
                'email' => $chauffeur->email,
                'telephone' => $chauffeur->telephone,
                'role' => $chauffeur->role,
                'statut' => $chauffeur->statut,
                'commune' => $chauffeur->commune,
                'compagnie' => $chauffeur->compagnie ? [
                    'id' => $chauffeur->compagnie->id,
                    'name' => $chauffeur->compagnie->name,
                    'logo' => $chauffeur->compagnie->logo,
                ] : null,
                'gare' => $chauffeur->gare ? [
                    'id' => $chauffeur->gare->id,
                    'nom_gare' => $chauffeur->gare->nom_gare,
                    'ville' => $chauffeur->gare->ville,
                ] : null,
            ],
        ]);
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(Request $request)
    {
        $chauffeur = $request->user();

        $request->validate([
            'nom' => 'nullable|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'commune' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nom', 'prenom', 'telephone', 'commune']);

        if ($request->hasFile('profile_picture')) {
            if ($chauffeur->profile_picture) {
                Storage::disk('public')->delete($chauffeur->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('chauffeurs/profiles', 'public');
        }

        $chauffeur->update(array_filter($data));

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès.',
        ]);
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $chauffeur = $request->user();

        if (!Hash::check($request->current_password, $chauffeur->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Le mot de passe actuel est incorrect.',
            ], 422);
        }

        $chauffeur->update(['password' => Hash::make($request->new_password)]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe modifié avec succès.',
        ]);
    }

    /**
     * Mettre à jour le token FCM
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);
        $request->user()->update(['fcm_token' => $request->fcm_token]);

        return response()->json([
            'success' => true,
            'message' => 'Token FCM mis à jour.',
        ]);
    }

    /**
     * Dashboard - Statistiques chauffeur
     */
    public function dashboard(Request $request)
    {
        $chauffeur = $request->user();
        $today = Carbon::today();

        // Voyages du jour
        $todayVoyages = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('date_voyage', $today)
            ->where('statut', '!=', 'terminé')
            ->with(['programme.gareDepart', 'programme.gareArrivee', 'vehicule'])
            ->get()
            ->map(function($v) {
                return $this->formatVoyage($v);
            });

        // Voyages à venir
        $upcomingVoyages = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('date_voyage', '>', $today)
            ->where('statut', '!=', 'terminé')
            ->with(['programme.gareDepart', 'programme.gareArrivee', 'vehicule'])
            ->orderBy('date_voyage', 'asc')
            ->limit(10)
            ->get()
            ->map(function($v) {
                return $this->formatVoyage($v);
            });

        // Stats
        $totalVoyages = Voyage::where('personnel_id', $chauffeur->id)->count();
        $completedVoyages = Voyage::where('personnel_id', $chauffeur->id)->where('statut', 'terminé')->count();

        return response()->json([
            'success' => true,
            'today_voyages' => $todayVoyages,
            'upcoming_voyages' => $upcomingVoyages,
            'stats' => [
                'total_voyages' => $totalVoyages,
                'completed_voyages' => $completedVoyages,
                'statut' => $chauffeur->statut,
            ],
        ]);
    }

    /**
     * Helper: formater un voyage
     */
    private function formatVoyage($voyage)
    {
        return [
            'id' => $voyage->id,
            'date_voyage' => $voyage->date_voyage,
            'statut' => $voyage->statut,
            'occupancy' => $voyage->occupancy ?? 0,
            'programme' => $voyage->programme ? [
                'id' => $voyage->programme->id,
                'point_depart' => $voyage->programme->point_depart,
                'point_arrive' => $voyage->programme->point_arrive,
                'heure_depart' => $voyage->programme->heure_depart,
                'gare_depart' => optional($voyage->programme->gareDepart)->nom_gare ?? '',
                'gare_arrivee' => optional($voyage->programme->gareArrivee)->nom_gare ?? '',
            ] : null,
            'vehicule' => $voyage->vehicule ? [
                'id' => $voyage->vehicule->id,
                'marque' => $voyage->vehicule->marque,
                'modele' => $voyage->vehicule->modele,
                'immatriculation' => $voyage->vehicule->immatriculation,
            ] : null,
        ];
    }
}
