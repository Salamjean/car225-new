<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Signalement;
use App\Models\Vehicule;
use App\Models\Programme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CompagnieDashboard extends Controller
{
    public function dashboard()
    {
        $compagnieId = Auth::guard('compagnie')->id();

        // 1. Statistiques Globales
        $totalRevenue = Reservation::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->where('statut', 'confirmee')->sum('montant_total');

        $totalReservations = Reservation::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->count();

        $totalVehicles = Vehicule::where('compagnie_id', $compagnieId)->count();

        $totalSignalements = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->count();

        // 2. Activités Récentes
        $recentReservations = Reservation::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->with(['user', 'programme'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentSignalements = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->with(['user', 'programme'])
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        // 3. Données pour le graphique (7 derniers jours)
        $days = [];
        $revenuePerDay = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('d M');

            $revenue = Reservation::whereHas('programme', function ($q) use ($compagnieId) {
                $q->where('compagnie_id', $compagnieId);
            })
                ->where('statut', 'confirmee')
                ->whereDate('created_at', $date)
                ->sum('montant_total');

            $revenuePerDay[] = (float) $revenue;
        }

        return view('compagnie.dashboard', compact(
            'totalRevenue',
            'totalReservations',
            'totalVehicles',
            'totalSignalements',
            'recentReservations',
            'recentSignalements',
            'days',
            'revenuePerDay'
        ));
    }

    public function logout()
    {
        Auth::guard('compagnie')->logout();
        return redirect()->route('compagnie.login');
    }
}
