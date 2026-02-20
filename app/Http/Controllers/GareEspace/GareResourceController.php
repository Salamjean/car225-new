<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use App\Models\Vehicule;
use Illuminate\Support\Facades\Auth;

class GareResourceController extends Controller
{
    /**
     * List personnel of the gare's company
     */
    public function personnelIndex()
    {
        $gare = Auth::guard('gare')->user();
        $personnels = Personnel::where('compagnie_id', $gare->compagnie_id)
            ->orderBy('type_personnel')
            ->orderBy('name')
            ->get();

        return view('gare-espace.personnel.index', compact('personnels'));
    }

    /**
     * List vehicles of the gare's company
     */
    public function vehiculesIndex()
    {
        $gare = Auth::guard('gare')->user();
        $vehicules = Vehicule::where('compagnie_id', $gare->compagnie_id)
            ->where('is_active', true)
            ->orderBy('immatriculation')
            ->get();

        return view('gare-espace.vehicules.index', compact('vehicules'));
    }
}
