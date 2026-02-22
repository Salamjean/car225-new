<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Voyage;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('success', 'Paramètres mis à jour avec succès.');
    }

    public function voyagesEnCours()
    {
        $voyages = Voyage::where('statut', 'en_cours')
            ->with(['programme.compagnie', 'vehicule', 'chauffeur', 'gareDepart', 'gareArrivee', 'latestLocation'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.voyages.en-cours', compact('voyages'));
    }

    public function voyagesEnCoursApi()
    {
        $voyages = Voyage::where('statut', 'en_cours')
            ->with(['programme.compagnie', 'vehicule', 'chauffeur', 'gareDepart', 'gareArrivee', 'latestLocation'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($voyage) {
                return [
                    'id' => $voyage->id,
                    'date_voyage' => $voyage->date_voyage,
                    'statut' => $voyage->statut,
                    'compagnie' => $voyage->programme->compagnie->name ?? 'N/A',
                    'chauffeur' => $voyage->chauffeur->nom ?? 'N/A',
                    'chauffeur_prenom' => $voyage->chauffeur->prenom ?? '',
                    'vehicule_marque' => $voyage->vehicule->marque ?? 'N/A',
                    'vehicule_immat' => $voyage->vehicule->immatriculation ?? 'N/A',
                    'depart' => $voyage->programme->point_depart ?? ($voyage->gareDepart->nom ?? 'N/A'),
                    'arrivee' => $voyage->programme->point_arrive ?? ($voyage->gareArrivee->nom ?? 'N/A'),
                    'heure_depart' => $voyage->programme->heure_depart ?? null,
                    'heure_arrivee' => $voyage->programme->heure_arrive ?? null,
                    'occupancy' => $voyage->occupancy,
                    'temps_restant' => $voyage->temps_restant,
                    'location' => $voyage->latestLocation ? [
                        'latitude' => $voyage->latestLocation->latitude,
                        'longitude' => $voyage->latestLocation->longitude,
                        'speed' => $voyage->latestLocation->speed,
                        'updated_at' => $voyage->latestLocation->updated_at->diffForHumans(),
                    ] : null,
                ];
            });

        return response()->json(['voyages' => $voyages, 'count' => $voyages->count()]);
    }
}
