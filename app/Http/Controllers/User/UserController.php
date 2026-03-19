<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Signalement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // 1. Statistiques Globales
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

        // 2. Activités Récentes
        $recentReservations = Reservation::where('user_id', $user->id)
            ->where('statut', 'confirmee')
            ->with(['programme.compagnie'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $recentSignalements = Signalement::where('user_id', $user->id)
            ->with(['programme.compagnie'])
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();

        // 3. Voyage en cours (pour le minuteur)
        $currentTrip = Reservation::where('user_id', $user->id)
            ->whereIn('statut', ['confirmee', 'terminee'])
            ->whereHas('programme.voyages', function($q) {
                $q->where('statut', 'en_cours')
                  ->whereColumn('voyages.date_voyage', 'reservations.date_voyage');
            })
            ->with(['programme.gareDepart', 'programme.gareArrivee', 'programme.compagnie'])
            ->first();

        // 4. Données pour le graphique (6 derniers mois)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Reservation::where('user_id', $user->id)
                ->where('statut', 'confirmee')
                ->whereYear('date_voyage', $month->year)
                ->whereMonth('date_voyage', $month->month)
                ->count();
            
            $chartData['labels'][] = $month->translatedFormat('M');
            $chartData['values'][] = $count;
        }

        return view('user.dashboard', compact(
            'user',
            'totalReservations',
            'totalSpent',
            'activeReservations',
            'totalSignalements',
            'recentReservations',
            'recentSignalements',
            'currentTrip',
            'chartData'
        ));
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect()->route('login');
    }

    public function markNotificationRead(Request $request)
    {
        $notification = Auth::user()->notifications()->findOrFail($request->id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    /**
     * API JSON: Returns the current location of the active voyage for the user.
     */
    public function getTrackingLocation()
    {
        $user = Auth::user();

        // Find the active trip for the user (same logic as dashboard)
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
                'estimated_arrival' => $voyage->estimated_arrival_at ? $voyage->estimated_arrival_at->toIso8601String() : null,
                'date_voyage' => \Carbon\Carbon::parse($voyage->date_voyage)->format('d/m/Y'),
                'temps_restant' => $voyage->temps_restant,
            ]
        ]);
    }
}
