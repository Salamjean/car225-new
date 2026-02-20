<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use App\Models\Vehicule;
use App\Models\Voyage;
use App\Models\Programme;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GareDashboardController extends Controller
{
    public function index()
    {
        $gare = Auth::guard('gare')->user();
        $compagnieId = $gare->compagnie_id;

        // Stats for this specific gare
        $totalPersonnel = Personnel::where('gare_id', $gare->id)->count();
        $totalChauffeurs = Personnel::where('gare_id', $gare->id)->where('type_personnel', 'Chauffeur')->count();
        $chauffeursDisponibles = Personnel::where('gare_id', $gare->id)->where('type_personnel', 'Chauffeur')->where('statut', 'disponible')->count();
        $totalVehicules = Vehicule::where('gare_id', $gare->id)->where('is_active', true)->count();
        $vehiculesDisponibles = Vehicule::where('gare_id', $gare->id)->where('is_active', true)->where('statut', 'disponible')->count();

        // Today voyages concerning this gare
        $today = Carbon::today()->toDateString();
        $voyagesAujourdhui = Voyage::where('gare_depart_id', $gare->id)
            ->whereDate('date_voyage', $today)
            ->with(['programme.gareDepart', 'programme.gareArrivee', 'chauffeur', 'vehicule'])
            ->get();

        $programmesActifs = Programme::where('compagnie_id', $compagnieId)
            ->where(function($query) use ($gare) {
                $query->where('gare_depart_id', $gare->id);
            })
            ->where('statut', 'actif')
            ->whereDate('date_depart', '<=', $today)
            ->whereDate('date_fin', '>=', $today)
            ->count();

        return view('gare-espace.dashboard', compact(
            'gare', 'totalPersonnel', 'totalChauffeurs', 'chauffeursDisponibles',
            'totalVehicules', 'vehiculesDisponibles', 'voyagesAujourdhui', 'programmesActifs'
        ));
    }
}
