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
        $vehicules = Vehicule::where('gare_id', $gare->id)
            ->where('is_active', true)
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
}
