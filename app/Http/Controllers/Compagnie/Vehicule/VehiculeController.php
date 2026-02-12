<?php

namespace App\Http\Controllers\Compagnie\Vehicule;

use App\Http\Controllers\Controller;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehiculeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicules = Vehicule::where('compagnie_id', Auth::guard('compagnie')->user()->id)
                            ->latest()
                            ->paginate(10);
        
        return view('compagnie.vehicule.index', compact('vehicules'));
    }

    public function create(){
        return view('compagnie.vehicule.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'immatriculation' => 'required|string|max:20|unique:vehicules,immatriculation',
            'numero_serie' => 'nullable|string|max:255',
            'type_range' => 'required|string|in:2x2,2x3,2x4',
            'nombre_place' => 'required|integer|min:4|max:30',
        ], [
            'immatriculation.required' => 'L\'immatriculation est obligatoire.',
            'immatriculation.unique' => 'Cette immatriculation est déjà utilisée.',
            'type_range.required' => 'Le type de rangée est obligatoire.',
            'type_range.in' => 'Le type de rangée doit être 2x2, 2x3 ou 2x4.',
            'nombre_place.required' => 'Le nombre de places est obligatoire.',
            'nombre_place.integer' => 'Le nombre de places doit être un nombre entier.',
            'nombre_place.min' => 'Le nombre de places doit être d\'au moins 4.',
            'nombre_place.max' => 'Le nombre de places ne peut pas dépasser 30.',
        ]);

        try {
            // Création du véhicule
            $vehicule = Vehicule::create([
                'immatriculation' => strtoupper($request->immatriculation),
                'numero_serie' => $request->numero_serie,
                'type_range' => $request->type_range,
                'nombre_place' => $request->nombre_place,
                'compagnie_id' => Auth::guard('compagnie')->user()->id,
            ]);

            // Redirection avec message de succès
            return redirect()
                ->route('vehicule.index')
                ->with('success', 'Véhicule créé avec succès!');

        } catch (\Exception $e) {
            // En cas d'erreur, redirection avec message d'erreur
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du véhicule: ' . $e->getMessage());
        }
    }


    /**
     * Activate the specified resource.
     */
    public function activate($id)
    {
        try {
            $vehicule = Vehicule::where('compagnie_id', Auth::guard('compagnie')->user()->id)
                            ->findOrFail($id);
            
            $vehicule->update([
                'is_active' => '1',
                'motif' => null
            ]);
            
            return redirect()->route('vehicule.index')
                            ->with('success', 'Véhicule activé avec succès!');
                            
        } catch (\Exception $e) {
            return redirect()->route('vehicule.index')
                            ->with('error', 'Erreur lors de l\'activation du véhicule.');
        }
    }

    /**
     * Deactivate the specified resource.
     */
    public function deactivate(Request $request, $id)
    {
        $request->validate([
            'motif' => 'required|string|max:255'
        ], [
            'motif.required' => 'Le motif est obligatoire pour désactiver un véhicule.'
        ]);
        
        try {
            $vehicule = Vehicule::where('compagnie_id', Auth::guard('compagnie')->user()->id)
                            ->findOrFail($id);
            
            $vehicule->update([
                'is_active' => '0',
                'motif' => $request->motif
            ]);
            
            return redirect()->route('vehicule.index')
                            ->with('success', 'Véhicule désactivé avec succès!');
                            
        } catch (\Exception $e) {
            return redirect()->route('vehicule.index')
                            ->with('error', 'Erreur lors de la désactivation du véhicule.');
        }
    }
}
