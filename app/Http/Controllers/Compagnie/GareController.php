<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Gare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GareController extends Controller
{
    public function index()
    {
        $compagnieId = Auth::guard('compagnie')->id();
        $gares = Gare::where('compagnie_id', $compagnieId)->latest()->get();
        return view('compagnie.gare.index', compact('gares'));
    }

    public function create()
    {
        return view('compagnie.gare.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_gare' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
        ]);

        $validated['compagnie_id'] = Auth::guard('compagnie')->id();

        Gare::create($validated);

        return redirect()->route('gare.index')
            ->with('success', 'Gare créée avec succès !');
    }

    public function edit(Gare $gare)
    {
        if ($gare->compagnie_id !== Auth::guard('compagnie')->id()) {
            abort(403);
        }
        return view('compagnie.gare.edit', compact('gare'));
    }

    public function update(Request $request, Gare $gare)
    {
        if ($gare->compagnie_id !== Auth::guard('compagnie')->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'nom_gare' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
        ]);

        $gare->update($validated);

        return redirect()->route('gare.index')
            ->with('success', 'Gare mise à jour avec succès !');
    }

    public function destroy(Gare $gare)
    {
        if ($gare->compagnie_id !== Auth::guard('compagnie')->id()) {
            abort(403);
        }

        $gare->delete();

        return redirect()->route('gare.index')
            ->with('success', 'Gare supprimée avec succès !');
    }
}
