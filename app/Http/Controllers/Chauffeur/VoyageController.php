<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Voyage;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VoyageController extends Controller
{
    /**
     * Display driver's assigned voyages
     */
    public function index(Request $request)
    {
        $chauffeur = Auth::guard('chauffeur')->user();
        $date = $request->input('date', Carbon::today()->toDateString());

        $voyages = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('date_voyage', $date)
            ->with(['programme.gareDepart', 'programme.gareArrivee', 'vehicule'])
            ->orderBy('created_at', 'desc')
            ->paginate(3);

        return view('chauffeur.programmes.index', compact('voyages', 'date'));
    }

    /**
     * Confirm a voyage (en_attente -> confirmé)
     */
    public function confirm(Voyage $voyage)
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        // Verify voyage belongs to this driver
        if ($voyage->personnel_id !== $chauffeur->id) {
            return back()->with('error', 'Ce voyage ne vous appartient pas.');
        }

        // Only allow confirmation if voyage is pending
        if ($voyage->statut !== 'en_attente') {
            return back()->with('error', 'Ce voyage ne peut plus être confirmé.');
        }

        $voyage->update(['statut' => 'confirmé']);

        return back()->with('success', 'Voyage confirmé avec succès.');
    }

    /**
     * Start a voyage (confirmé -> en_cours)
     */
    public function start(Voyage $voyage)
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        // Verify voyage belongs to this driver
        if ($voyage->personnel_id !== $chauffeur->id) {
            return back()->with('error', 'Ce voyage ne vous appartient pas.');
        }

        // Only allow starting if voyage is confirmed
        if ($voyage->statut !== 'confirmé') {
            return back()->with('error', 'Vous devez d\'abord confirmer ce voyage avant de le démarrer.');
        }

        if ($voyage->occupancy < 1) {
            return back()->with('error', 'Impossible de démarrer le voyage : au moins 1 passager doit être présent dans le car.');
        }

        $voyage->update(['statut' => 'en_cours']);

        return back()->with('success', 'Bon voyage ! Le voyage a été démarré.');
    }

    /**
     * Complete a voyage (en_cours -> terminé) and update driver status to disponible
     */
    public function complete(Voyage $voyage)
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        // Verify voyage belongs to this driver
        if ($voyage->personnel_id !== $chauffeur->id) {
            return back()->with('error', 'Ce voyage ne vous appartient pas.');
        }

        // Only allow completion if voyage is in progress
        if ($voyage->statut !== 'en_cours') {
            return back()->with('error', 'Ce voyage n\'est pas en cours.');
        }

        // Update voyage status
        $voyage->update(['statut' => 'terminé']);

        // Update driver status to disponible
        $chauffeur->update(['statut' => 'disponible']);

        // Update vehicle status to disponible
        if ($voyage->vehicule) {
            $voyage->vehicule->update(['statut' => 'disponible']);
        }

        return back()->with('success', 'Voyage terminé avec succès. Vous êtes maintenant disponible pour de nouveaux voyages.');
    }

    /**
     * Update driver GPS location for a voyage in progress
     */
    public function updateLocation(Request $request, Voyage $voyage)
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        if ($voyage->personnel_id !== $chauffeur->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        if ($voyage->statut !== 'en_cours') {
            return response()->json(['success' => false, 'message' => 'Le voyage n\'est pas en cours'], 422);
        }

        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed'     => 'nullable|numeric|min:0',
            'heading'   => 'nullable|numeric|between:0,360',
        ]);

        \App\Models\DriverLocation::updateOrCreate(
            ['voyage_id' => $voyage->id, 'personnel_id' => $chauffeur->id],
            [
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'speed'     => $request->speed,
                'heading'   => $request->heading,
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Annuler un voyage (confirmé ou en_attente -> annulé)
     */
    public function annuler(Voyage $voyage)
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        if ($voyage->personnel_id !== $chauffeur->id) {
            return back()->with('error', 'Ce voyage ne vous appartient pas.');
        }

        if (!in_array($voyage->statut, ['en_attente', 'confirmé'])) {
            return back()->with('error', 'Ce voyage ne peut plus être annulé.');
        }

        $voyage->update(['statut' => 'annulé']);

        // Libérer le chauffeur
        $chauffeur->update(['statut' => 'disponible']);

        // Libérer le véhicule
        if ($voyage->vehicule) {
            $voyage->vehicule->update(['statut' => 'disponible']);
        }

        return back()->with('success', 'Le voyage a été annulé.');
    }
}
