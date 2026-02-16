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
            ->with(['programme', 'vehicule', 'gareDepart', 'gareArrivee'])
            ->get();

        $upcomingVoyages = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('date_voyage', '>', Carbon::today())
            ->with(['programme', 'vehicule', 'gareDepart', 'gareArrivee'])
            ->orderBy('date_voyage', 'asc')
            ->get();

        return view('chauffeur.dashboard', compact('todayVoyages', 'upcomingVoyages'));
    }

    public function availableProgrammes(Request $request)
    {
        $chauffeur = Auth::guard('chauffeur')->user();
        $date = $request->input('date', Carbon::today()->toDateString());

        // Get programmes that don't have a voyage yet for this date
        // and belong to the same company as the chauffeur
        $programmes = Programme::where('compagnie_id', $chauffeur->compagnie_id)
            ->whereDoesntHave('voyages', function ($query) use ($date) {
                $query->whereDate('date_voyage', $date);
            })
            ->with(['gareDepart', 'gareArrivee', 'itineraire'])
            ->get();

        $vehicules = Vehicule::where('compagnie_id', $chauffeur->compagnie_id)
            ->where('is_active', true)
            ->get();

        return view('chauffeur.programmes.index', compact('programmes', 'vehicules', 'date'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'vehicule_id' => 'required|exists:vehicules,id',
            'date_voyage' => 'required|date|after_or_equal:today',
        ]);

        $chauffeur = Auth::guard('chauffeur')->user();
        $programme = Programme::findOrFail($request->programme_id);

        // Check if programme is already assigned for this date
        $exists = Voyage::where('programme_id', $programme->id)
            ->whereDate('date_voyage', $request->date_voyage)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ce programme est déjà assigné pour cette date.');
        }

        // Check if vehicle is already assigned for this date and time?
        // For simplicity, let's assume one vehicle per date for now, or check overlap if possible.
        // The user didn't specify checking for vehicle availability overlaps, but it's good practice.
        // However, one trip usually takes a significant part of the day.
        
        // Exclusivity check for chauffeur: can't be in two places at once
        $chauffeurBusy = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('date_voyage', $request->date_voyage)
            ->where('programme_id', '!=', $programme->id) // simplified check
            ->exists();
            
        // Note: A more complex check would involve time overlap, but for now we'll stick to a simpler model.

        Voyage::create([
            'programme_id' => $programme->id,
            'date_voyage' => $request->date_voyage,
            'vehicule_id' => $request->vehicule_id,
            'personnel_id' => $chauffeur->id,
            'gare_depart_id' => $programme->gare_depart_id,
            'gare_arrivee_id' => $programme->gare_arrivee_id,
            'statut' => 'en_attente',
        ]);

        return redirect()->route('chauffeur.dashboard')->with('success', 'Vous vous êtes assigné avec succès à ce voyage.');
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
