<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GareVehiculeController extends Controller
{
    public function index()
    {
        $gare = Auth::guard('gare')->user();
        $vehicules = Vehicule::where('gare_id', '=', $gare->id)
            ->where('is_active', '=', true)
            ->orderBy('immatriculation')
            ->get();

        return view('gare-espace.vehicules.index', compact('vehicules'));
    }

    public function create()
    {
        return view('gare-espace.vehicules.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'immatriculation' => 'required|string|min:5|unique:vehicules,immatriculation',
            'numero_serie' => 'nullable|string|max:255',
            'type_range' => 'required|string|in:2x2,2x3,2x4',
            'nombre_place' => 'required|integer|min:15',
        ], [
            'immatriculation.required' => 'L\'immatriculation est obligatoire.',
            'immatriculation.unique' => 'Cette immatriculation est déjà utilisée.',
            'immatriculation.min' => 'L\'immatriculation doit contenir au moins 5 caractères.',
            'type_range.required' => 'Le type de rangée est obligatoire.',
            'type_range.in' => 'Le type de rangée doit être 2x2, 2x3 ou 2x4.',
            'nombre_place.required' => 'Le nombre de places est obligatoire.',
            'nombre_place.integer' => 'Le nombre de places doit être un nombre entier.',
            'nombre_place.min' => 'Le nombre de places doit être d\'au moins 15.',
        ]);

        try {
            $gare = Auth::guard('gare')->user();

            Vehicule::create([
                'immatriculation' => strtoupper($request->immatriculation),
                'numero_serie' => $request->numero_serie,
                'type_range' => $request->type_range,
                'nombre_place' => $request->nombre_place,
                'compagnie_id' => $gare->compagnie_id,
                'gare_id' => $gare->id,
            ]);

            return redirect()
                ->route('gare-espace.vehicules.index')
                ->with('success', 'Véhicule créé avec succès!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du véhicule: ' . $e->getMessage());
        }
    }

    public function edit(Vehicule $vehicule)
    {
        $gare = Auth::guard('gare')->user();
        if ($vehicule->gare_id !== $gare->id) {
            return redirect()->route('gare-espace.vehicules.index')->with('error', 'Accès non autorisé.');
        }

        return view('gare-espace.vehicules.edit', compact('vehicule'));
    }

    public function update(Request $request, Vehicule $vehicule)
    {
        $gare = Auth::guard('gare')->user();
        if ($vehicule->gare_id !== $gare->id) {
            return redirect()->route('gare-espace.vehicules.index')->with('error', 'Accès non autorisé.');
        }

        $request->validate([
            'immatriculation' => 'required|string|min:5|unique:vehicules,immatriculation,' . $vehicule->id,
            'numero_serie' => 'nullable|string|max:255',
            'type_range' => 'required|string|in:2x2,2x3,2x4',
            'nombre_place' => 'required|integer|min:15',
        ]);

        try {
            $vehicule->update([
                'immatriculation' => strtoupper($request->immatriculation),
                'numero_serie' => $request->numero_serie,
                'type_range' => $request->type_range,
                'nombre_place' => $request->nombre_place,
            ]);

            return redirect()
                ->route('gare-espace.vehicules.index')
                ->with('success', 'Véhicule mis à jour avec succès!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour: ' . $e->getMessage());
        }
    }

    public function destroy(Vehicule $vehicule)
    {
        $gare = Auth::guard('gare')->user();
        if ($vehicule->gare_id !== $gare->id) {
            return redirect()->route('gare-espace.vehicules.index')->with('error', 'Accès non autorisé.');
        }

        // Vérifier si le véhicule est assigné à un voyage en cours ou futur
        $hasVoyages = \App\Models\Voyage::where('vehicule_id', '=', $vehicule->id)
            ->where('statut', '!=', 'terminé')
            ->exists();

        if ($hasVoyages) {
            return redirect()->back()->with('error', 'Impossible de supprimer ce véhicule car il est assigné à un voyage en attente ou en cours.');
        }

        try {
            $vehicule->delete();
            return redirect()->route('gare-espace.vehicules.index')->with('success', 'Véhicule supprimé avec succès!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
}
