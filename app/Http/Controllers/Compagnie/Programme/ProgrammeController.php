<?php

namespace App\Http\Controllers\Compagnie\Programme;

use App\Http\Controllers\Controller;
use App\Models\Itineraire;
use App\Models\Personnel;
use App\Models\Programme;
use App\Models\ProgrammeHistorique;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProgrammeController extends Controller
{
    /**
     * Liste des lignes de transport
     */
    public function index()
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;

        $programmes = Programme::with(['vehicule', 'chauffeur', 'convoyeur', 'itineraire'])
            ->where('compagnie_id', $compagnieId)
            ->where('statut', '!=', 'annule')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('compagnie.programme.index', compact('programmes'));
    }

    /**
     * Historique des programmes passés
     */
    public function history()
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;
        $today = Carbon::now()->format('Y-m-d');

        $logs = ProgrammeHistorique::orderBy('created_at', 'desc')->paginate(10, ['*'], 'logs_page');

        $programmesExpires = Programme::with(['vehicule', 'itineraire'])
            ->where('compagnie_id', $compagnieId)
            ->where('date_fin', '<', $today)
            ->orderBy('date_depart', 'desc')
            ->paginate(10, ['*'], 'prog_page');

        return view('compagnie.programme.historique', compact('logs', 'programmesExpires'));
    }

    /**
     * Supprimer un programme
     */
    public function destroy($id)
    {
        try {
            $compagnieId = Auth::guard('compagnie')->user()->id;
            $programme = Programme::where('id', $id)->where('compagnie_id', $compagnieId)->firstOrFail();

            if ($programme->reservations()->where('statut', 'confirmee')->count() > 0) {
                return back()->with('error', 'Impossible de supprimer cette ligne car elle contient des réservations.');
            }

            $programme->delete();

            return back()->with('success', 'Ligne supprimée avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur suppression ligne : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Formulaire d'édition
     */
    public function edit($id)
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;
        
        $programme = Programme::where('id', $id)->where('compagnie_id', $compagnieId)->firstOrFail();
        $itineraires = Itineraire::where('compagnie_id', $compagnieId)->get();

        return view('compagnie.programme.edit', compact('programme', 'itineraires'));
    }

    /**
     * Mettre à jour une ligne
     */
    public function update(Request $request, $id)
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;
        $programme = Programme::where('id', $id)->where('compagnie_id', $compagnieId)->firstOrFail();

        $validated = $request->validate([
            'montant_billet' => 'required|numeric|min:0',
            'date_fin' => 'required|date|after_or_equal:date_depart',
            'statut' => 'required|in:actif,annule',
        ]);

        $programme->update($validated);

        return redirect()->route('programme.index')->with('success', 'Ligne mise à jour avec succès');
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;
        $itineraires = Itineraire::where('compagnie_id', $compagnieId)->get();

        return view('compagnie.programme.create', compact('itineraires'));
    }

    /**
     * Créer une ligne de transport - Service continu 24h/24
     * Vérifie les doublons avant création
     */
    public function store(Request $request)
    {
        Log::info('Création ligne de transport', ['data' => $request->all()]);

        $validated = $request->validate([
            'itineraire_id' => 'required|exists:itineraires,id',
            'montant_billet' => 'required|numeric|min:0',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'heure_depart' => 'required|date_format:H:i',
            'heure_arrive' => 'required|date_format:H:i',
        ]);

        $compagnieId = Auth::guard('compagnie')->user()->id;
        $itineraire = Itineraire::findOrFail($validated['itineraire_id']);

        // === VÉRIFICATION DES DOUBLONS ===
        $existingAller = Programme::where('compagnie_id', $compagnieId)
            ->where('itineraire_id', $itineraire->id)
            ->where('point_depart', $itineraire->point_depart)
            ->where('point_arrive', $itineraire->point_arrive)
            ->where('statut', 'actif')
            ->first();

        if ($existingAller) {
            return back()->withInput()->with('error', 
                'Cette ligne existe déjà : ' . $itineraire->point_depart . ' → ' . $itineraire->point_arrive . 
                '. Vous pouvez la modifier depuis la liste.');
        }

        try {
            // Créer la ligne ALLER
            Programme::create([
                'compagnie_id' => $compagnieId,
                'itineraire_id' => $itineraire->id,
                'vehicule_id' => null,
                'personnel_id' => null,
                'convoyeur_id' => null,
                'point_depart' => $itineraire->point_depart,
                'point_arrive' => $itineraire->point_arrive,
                'durer_parcours' => $itineraire->durer_parcours,
                'montant_billet' => $validated['montant_billet'],
                'date_depart' => $validated['date_debut'],
                'date_fin' => $validated['date_fin'],
                'heure_depart' => $validated['heure_depart'],
                'heure_arrive' => $validated['heure_arrive'],
                'statut' => 'actif',
            ]);
            
            // Créer la ligne RETOUR (points inversés, mêmes horaires)
            Programme::create([
                'compagnie_id' => $compagnieId,
                'itineraire_id' => $itineraire->id,
                'vehicule_id' => null,
                'personnel_id' => null,
                'convoyeur_id' => null,
                'point_depart' => $itineraire->point_arrive,
                'point_arrive' => $itineraire->point_depart,
                'durer_parcours' => $itineraire->durer_parcours,
                'montant_billet' => $validated['montant_billet'],
                'date_depart' => $validated['date_debut'],
                'date_fin' => $validated['date_fin'],
                'heure_depart' => $validated['heure_depart'],
                'heure_arrive' => $validated['heure_arrive'],
                'statut' => 'actif',
            ]);

            return redirect()->route('programme.index')
                ->with('success', '2 lignes créées : ' . $itineraire->point_depart . ' ↔ ' . $itineraire->point_arrive);

        } catch (\Exception $e) {
            Log::error('Erreur création ligne: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur système : ' . $e->getMessage());
        }
    }

    /**
     * Annuler un programme
     */
    public function annuler(Request $request, $id)
    {
        try {
            $compagnieId = Auth::guard('compagnie')->user()->id;
            $programme = Programme::where('id', $id)->where('compagnie_id', $compagnieId)->firstOrFail();

            $programme->update(['statut' => 'annule']);

            return response()->json([
                'success' => true,
                'message' => 'Ligne annulée avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur annulation', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation'
            ], 500);
        }
    }
}
