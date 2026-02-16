<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use App\Models\Personnel;
use App\Models\Vehicule;
use App\Models\Voyage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VoyageController extends Controller
{
    /**
     * Display voyage assignment page
     */
    public function index(Request $request)
    {
        $agent = Auth::guard('agent')->user();
        $date = $request->input('date', Carbon::today()->toDateString());

        // Get programmes from agent's company
        $programmes = Programme::where('compagnie_id', $agent->compagnie_id)
            ->where('statut', 'actif')
            ->whereDate('date_depart', '<=', $date)
            ->whereDate('date_fin', '>=', $date)
            ->with(['gareDepart', 'gareArrivee', 'voyages' => function ($query) use ($date) {
                $query->whereDate('date_voyage', $date);
            }])
            ->orderBy('heure_depart')
            ->get();

        // Get available drivers (disponible status only)
        $chauffeurs = Personnel::where('compagnie_id', $agent->compagnie_id)
            ->where('type_personnel', 'Chauffeur')
            ->where('statut', 'disponible')
            ->orderBy('name')
            ->get();

        // Get active vehicles
        $vehicules = Vehicule::where('compagnie_id', $agent->compagnie_id)
            ->where('is_active', true)
            ->orderBy('immatriculation')
            ->get();

        return view('agent.voyages.index', compact('programmes', 'chauffeurs', 'vehicules', 'date'));
    }

    /**
     * Store a new voyage assignment
     */
    public function store(Request $request)
    {
        $agent = Auth::guard('agent')->user();

        $validated = $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'personnel_id' => 'required|exists:personnels,id',
            'vehicule_id' => 'required|exists:vehicules,id',
            'date_voyage' => 'required|date|after_or_equal:today',
        ]);

        // Verify programme belongs to agent's company
        $programme = Programme::findOrFail($validated['programme_id']);
        if ($programme->compagnie_id !== $agent->compagnie_id) {
            return back()->with('error', 'Ce programme n\'appartient pas à votre compagnie.');
        }

        // Verify driver belongs to agent's company and is available
        $chauffeur = Personnel::findOrFail($validated['personnel_id']);
        if ($chauffeur->compagnie_id !== $agent->compagnie_id) {
            return back()->with('error', 'Ce chauffeur n\'appartient pas à votre compagnie.');
        }
        if ($chauffeur->statut !== 'disponible') {
            return back()->with('error', 'Ce chauffeur n\'est pas disponible.');
        }

        // Verify vehicle belongs to agent's company
        $vehicule = Vehicule::findOrFail($validated['vehicule_id']);
        if ($vehicule->compagnie_id !== $agent->compagnie_id) {
            return back()->with('error', 'Ce véhicule n\'appartient pas à votre compagnie.');
        }

        // Check if voyage already exists for this programme and date
        $exists = Voyage::where('programme_id', $programme->id)
            ->whereDate('date_voyage', $validated['date_voyage'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Un voyage est déjà assigné pour ce programme à cette date.');
        }

        // Check if driver is already assigned to another voyage on this date
        $chauffeurBusy = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('date_voyage', $validated['date_voyage'])
            ->exists();

        if ($chauffeurBusy) {
            return back()->with('error', 'Ce chauffeur est déjà assigné à un autre voyage pour cette date.');
        }

        // Create voyage
        $voyage = Voyage::create([
            'programme_id' => $programme->id,
            'date_voyage' => $validated['date_voyage'],
            'vehicule_id' => $vehicule->id,
            'personnel_id' => $chauffeur->id,
            'gare_depart_id' => $programme->gare_depart_id,
            'gare_arrivee_id' => $programme->gare_arrivee_id,
            'statut' => 'en_attente',
        ]);

        // Update driver status to indisponible
        $chauffeur->update(['statut' => 'indisponible']);

        return back()->with('success', 'Le voyage a été assigné avec succès au chauffeur ' . $chauffeur->prenom . ' ' . $chauffeur->name . '.');
    }

    /**
     * Cancel a voyage assignment
     */
    public function destroy(Voyage $voyage)
    {
        $agent = Auth::guard('agent')->user();

        // Verify voyage belongs to agent's company
        if ($voyage->programme->compagnie_id !== $agent->compagnie_id) {
            return back()->with('error', 'Ce voyage n\'appartient pas à votre compagnie.');
        }

        // Only allow cancellation if voyage is not started
        if (in_array($voyage->statut, ['en_cours', 'terminé'])) {
            return back()->with('error', 'Impossible d\'annuler un voyage déjà démarré ou terminé.');
        }

        // Update driver status back to disponible
        if ($voyage->chauffeur) {
            $voyage->chauffeur->update(['statut' => 'disponible']);
        }

        $voyage->delete();

        return back()->with('success', 'Le voyage a été annulé avec succès.');
    }
}
