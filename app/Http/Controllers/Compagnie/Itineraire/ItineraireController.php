<?php

namespace App\Http\Controllers\Compagnie\Itineraire;

use App\Http\Controllers\Controller;
use App\Models\Itineraire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItineraireController extends Controller
{

    /**
     * Afficher la liste des itinéraires
     */
    public function index(Request $request)
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;
        
        $query = Itineraire::where('compagnie_id', $compagnieId);
        
        // Recherche
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('point_depart', 'like', "%{$search}%")
                ->orWhere('point_arrive', 'like', "%{$search}%")
                ->orWhere('durer_parcours', 'like', "%{$search}%");
            });
        }
        
        $itineraires = $query->latest()->paginate(10);
        
        return view('compagnie.itineraire.index', compact('itineraires'));
    }

    /**
     * creer une itineraire
     */
    public function create(){
        return view('compagnie.itineraire.create');
    }

    /**
     * Stocker un nouvel itinéraire
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'point_depart' => 'required|string|max:255',
            'point_arrive' => 'required|string|max:255',
            'durer_parcours' => 'required|string|max:50',
        ], [
            'point_depart.required' => 'Le point de départ est obligatoire.',
            'point_arrive.required' => 'Le point d\'arrivée est obligatoire.',
            'durer_parcours.required' => 'La durée du parcours est obligatoire.',
        ]);

        try {
            // Création de l'itinéraire
            $itineraire = Itineraire::create([
                'point_depart' => $validated['point_depart'],
                'point_arrive' => $validated['point_arrive'],
                'durer_parcours' => $validated['durer_parcours'],
                'compagnie_id' => Auth::guard('compagnie')->user()->id,
            ]);

            // Redirection avec message de succès
            return redirect()->route('itineraire.index')
                ->with('success', 'Itinéraire créé avec succès!');

        } catch (\Exception $e) {
            // En cas d'erreur, redirection avec message d'erreur
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de l\'itinéraire: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * Afficher les détails d'un itinéraire
     */
    public function show(Itineraire $itineraire)
    {
        // Vérifier que l'itinéraire appartient à la compagnie connectée
        if ($itineraire->compagnie_id !== Auth::guard('compagnie')->user()->id) {
            abort(403, 'Accès non autorisé.');
        }

        return view('compagnie.itineraire.show', compact('itineraire'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Itineraire $itineraire)
    {
        // Vérifier que l'itinéraire appartient à la compagnie connectée
        if ($itineraire->compagnie_id !== Auth::guard('compagnie')->user()->id) {
            abort(403, 'Accès non autorisé.');
        }

        return view('compagnie.itineraire.edit', compact('itineraire'));
    }

    /**
     * Mettre à jour un itinéraire
     */
    public function update(Request $request, Itineraire $itineraire)
    {
        // Vérifier que l'itinéraire appartient à la compagnie connectée
        if ($itineraire->compagnie_id !== Auth::guard('compagnie')->user()->id) {
            abort(403, 'Accès non autorisé.');
        }

        // Validation des données
        $validated = $request->validate([
            'point_depart' => 'required|string|max:255',
            'point_arrive' => 'required|string|max:255',
            'durer_parcours' => 'required|string|max:50',
        ], [
            'point_depart.required' => 'Le point de départ est obligatoire.',
            'point_arrive.required' => 'Le point d\'arrivée est obligatoire.',
            'durer_parcours.required' => 'La durée du parcours est obligatoire.',
        ]);

        try {
            // Mise à jour de l'itinéraire
            $itineraire->update($validated);

            return redirect()->route('itineraires.index')
                ->with('success', 'Itinéraire mis à jour avec succès!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprimer un itinéraire
     */
    public function destroy(Itineraire $itineraire)
    {
        // Vérifier que l'itinéraire appartient à la compagnie connectée
        if ($itineraire->compagnie_id !== Auth::guard('compagnie')->user()->id) {
            abort(403, 'Accès non autorisé.');
        }

        try {
            $itineraire->delete();

            return redirect()->route('itineraires.index')
                ->with('success', 'Itinéraire supprimé avec succès!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

}
