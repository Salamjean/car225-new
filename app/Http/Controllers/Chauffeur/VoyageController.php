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
        $tab = $request->input('tab', 'active'); // 'active' (default), 'non_effectues', 'effectues'
        $date = $request->input('date', Carbon::today()->toDateString());
        
        $query = Voyage::where('personnel_id', '=', $chauffeur->id)
            ->with(['programme.gareDepart', 'programme.gareArrivee', 'vehicule'])
            ->orderBy('date_voyage', 'desc')
            ->orderBy('created_at', 'desc');

        if ($tab === 'effectues') {
            // Completed
            $query->whereIn('statut', ['terminé', 'succès']);
        } elseif ($tab === 'non_effectues') {
            // Failed
            $query->whereIn('statut', ['annulé', 'interrompu', 'arrêté', 'non_effectué']);
            if ($request->filled('date')) {
                $query->whereDate('date_voyage', $request->date);
            }
        } else {
            // Active / Planned (Default)
            $query->whereIn('statut', ['en_attente', 'confirmé', 'en_cours'])
                  ->whereDate('date_voyage', $date);
        }

        $voyages = $query->paginate(10);

        return view('chauffeur.programmes.index', compact('voyages', 'tab', 'date'));
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

        // Only allow starting if voyage is confirmed or pending
        if (!in_array($voyage->statut, ['en_attente', 'confirmé'])) {
            return back()->with('error', 'Ce voyage ne peut pas être démarré.');
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
        if ($voyage->vehicule_id) {
            \App\Models\Vehicule::where('id', $voyage->vehicule_id)->update(['statut' => 'disponible']);
        }

        return redirect()->route('chauffeur.voyages.index', ['tab' => 'effectues'])
            ->with('success', 'Voyage terminé avec succès. Vous êtes maintenant disponible.');
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

        $latestLoc = $voyage->latestLocation;
        if ($latestLoc) {
            $elapsedSeconds = now()->diffInSeconds($latestLoc->updated_at);
            
            // Si la vitesse est quasi nulle (< 5 km/h) et que la dernière maj est récente (< 10 min)
            // On considère que le véhicule est à l'arrêt et on accumule du retard
            if (($request->speed ?? 0) < 5 && $elapsedSeconds < 600) {
                $voyage->updateEstimatedArrival($elapsedSeconds);
            }
        }

        \App\Models\DriverLocation::updateOrCreate(
            ['voyage_id' => $voyage->id, 'personnel_id' => $chauffeur->id],
            [
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'speed'     => $request->speed,
                'heading'   => $request->heading,
            ]
        );

        return response()->json([
            'success' => true,
            'estimated_arrival' => $voyage->estimated_arrival_at ? $voyage->estimated_arrival_at->toIso8601String() : null,
            'temps_restant' => $voyage->temps_restant
        ]);
    }

    /**
     * Annuler un voyage (confirmé ou en_attente -> annulé)
     */
    public function annuler(Request $request, Voyage $voyage)
    {
        $request->validate([
            'reason' => 'required|string|min:5|max:1000',
        ]);

        $chauffeur = Auth::guard('chauffeur')->user();

        if ($voyage->personnel_id !== $chauffeur->id) {
            return back()->with('error', 'Ce voyage ne vous appartient pas.');
        }

        if (!in_array($voyage->statut, ['en_attente', 'confirmé'])) {
            return back()->with('error', 'Ce voyage ne peut plus être annulé.');
        }

        $voyage->update(['statut' => 'annulé']);

        // Envoyer le motif à la gare
        $gareId = $voyage->gare_depart_id ?? ($voyage->programme ? $voyage->programme->gare_depart_id : null);
        
        if ($gareId) {
            \App\Models\GareMessage::create([
                'gare_id' => $gareId,
                'sender_type' => 'App\Models\Personnel',
                'sender_id' => $chauffeur->id,
                'recipient_type' => 'App\Models\Gare',
                'recipient_id' => $gareId,
                'subject' => 'Annulation de voyage #' . $voyage->id,
                'message' => "Le chauffeur {$chauffeur->prenom} {$chauffeur->name} a annulé le voyage #{$voyage->id}. \n\nMotif : " . $request->reason,
                'is_read' => false,
            ]);
        }

        // Libérer le chauffeur
        $chauffeur->update(['statut' => 'disponible']);

        // Libérer le véhicule
        if ($voyage->vehicule_id) {
            \App\Models\Vehicule::where('id', $voyage->vehicule_id)->update(['statut' => 'disponible']);
        }

        return redirect()->route('chauffeur.voyages.index', ['tab' => 'non_effectues'])
            ->with('success', 'Le voyage a été annulé et le motif a été transmis à la gare.');
    }
}
