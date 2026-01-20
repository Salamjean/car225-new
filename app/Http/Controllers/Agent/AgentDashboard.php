<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Programme;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgentDashboard extends Controller
{
    public function dashboard(Request $request)
    {
        $agent = Auth::guard('agent')->user();
        $compagnieId = $agent->compagnie_id;
        $today = Carbon::today();

        // Statistiques des scans de l'agent
        $scansToday = Reservation::where('embarquement_agent_id', $agent->id)
            ->whereDate('embarquement_scanned_at', $today)
            ->count();

        $totalScans = Reservation::where('embarquement_agent_id', $agent->id)->count();

        // Réservations de la compagnie
        $reservationsEnCours = Reservation::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->where('statut', 'confirmee')->count();

        $reservationsTerminees = Reservation::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->where('statut', 'terminee')->count();

        // Programmes du jour de la compagnie
        $programmesToday = Programme::where('compagnie_id', $compagnieId)
            ->whereDate('date_depart', $today)
            ->count();

        $programmesDuJour = Programme::where('compagnie_id', $compagnieId)
            ->whereDate('date_depart', $today)
            ->with('vehicule')
            ->orderBy('heure_depart')
            ->get();

        // Véhicules actifs de la compagnie
        $vehiculesActifs = Vehicule::where('compagnie_id', $compagnieId)
            ->where('is_active', true)
            ->count();

        // Revenus des réservations scannées par l'agent
        $revenueAgent = Reservation::where('embarquement_agent_id', $agent->id)
            ->sum('montant');

        // 5 dernières réservations scannées par l'agent
        $recentScans = Reservation::where('embarquement_agent_id', $agent->id)
            ->with(['programme', 'user'])
            ->orderBy('embarquement_scanned_at', 'desc')
            ->take(5)
            ->get();

        return view('agent.dashboard', compact(
            'agent',
            'scansToday',
            'totalScans',
            'reservationsEnCours',
            'reservationsTerminees',
            'programmesToday',
            'programmesDuJour',
            'vehiculesActifs',
            'revenueAgent',
            'recentScans'
        ));
    }

    public function logout()
    {
        Auth::guard('agent')->logout();
        return redirect()->route('agent.login');
    }
}
