<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Models\Itineraire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GareItineraireController extends Controller
{
    public function index(Request $request)
    {
        $gare = Auth::guard('gare')->user();

        $query = Itineraire::where('gare_id', $gare->id);

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

        return view('gare-espace.itineraire.index', compact('itineraires'));
    }

    public function create()
    {
        return view('gare-espace.itineraire.create');
    }

    public function store(Request $request)
    {
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
            $gare = Auth::guard('gare')->user();

            Itineraire::create([
                'point_depart' => $validated['point_depart'],
                'point_arrive' => $validated['point_arrive'],
                'durer_parcours' => $validated['durer_parcours'],
                'compagnie_id' => $gare->compagnie_id,
                'gare_id' => $gare->id,
            ]);

            return redirect()->route('gare-espace.itineraire.index')
                ->with('success', 'Itinéraire créé avec succès!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de l\'itinéraire: ' . $e->getMessage())
                ->withInput();
        }
    }
}
