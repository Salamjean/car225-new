<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use App\Models\Vehicule;
use App\Models\Voyage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChauffeurController extends Controller
{
    public function dashboard()
    {
        $chauffeur = Auth::guard('chauffeur')->user();
        
        $todayVoyages = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('date_voyage', Carbon::today())
            ->where('statut', '!=', 'terminé') // On ne cache que les terminés
            ->with(['programme', 'vehicule', 'gareDepart', 'gareArrivee'])
            ->get();

        $upcomingVoyages = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('date_voyage', '>', Carbon::today())
            ->where('statut', '!=', 'terminé')
            ->with(['programme', 'vehicule', 'gareDepart', 'gareArrivee'])
            ->orderBy('date_voyage', 'asc')
            ->get();

        return view('chauffeur.dashboard', compact('todayVoyages', 'upcomingVoyages'));
    }

    public function myVoyages()
    {
        $chauffeur = Auth::guard('chauffeur')->user();
        $voyages = Voyage::where('personnel_id', $chauffeur->id)
            ->with(['programme', 'vehicule', 'gareDepart', 'gareArrivee'])
            ->orderBy('date_voyage', 'desc')
            ->paginate(10);

        return view('chauffeur.voyages.history', compact('voyages'));
    }
}
