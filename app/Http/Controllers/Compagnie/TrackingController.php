<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\DriverLocation;
use App\Models\Voyage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TrackingController extends Controller
{
    /**
     * Affiche la page de suivi des voyages en cours
     */
    public function index()
    {
        $compagnie = Auth::guard('compagnie')->user();

        $activeVoyages = Voyage::where('statut', 'en_cours')
            ->whereHas('programme', function ($q) use ($compagnie) {
                $q->where('compagnie_id', $compagnie->id);
            })
            ->with([
                'programme.gareDepart',
                'programme.gareArrivee',
                'chauffeur',
                'vehicule',
                'latestLocation',
            ])
            ->get();

        return view('compagnie.tracking.index', compact('activeVoyages'));
    }

    /**
     * API JSON : Retourne toutes les positions actuelles des voyages en cours
     */
    public function getActiveLocations()
    {
        $compagnie = Auth::guard('compagnie')->user();

        $activeVoyages = Voyage::where('statut', 'en_cours')
            ->whereHas('programme', function ($q) use ($compagnie) {
                $q->where('compagnie_id', $compagnie->id);
            })
            ->with([
                'programme.gareDepart',
                'programme.gareArrivee',
                'chauffeur',
                'vehicule',
                'latestLocation',
            ])
            ->get();

        $locations = $activeVoyages->map(function ($voyage) {
            $location = $voyage->latestLocation;
            return [
                'voyage_id' => $voyage->id,
                'latitude' => $location ? (float) $location->latitude : null,
                'longitude' => $location ? (float) $location->longitude : null,
                'speed' => $location ? $location->speed : null,
                'heading' => $location ? $location->heading : null,
                'last_update' => $location ? $location->updated_at->diffForHumans() : 'Jamais',
                'chauffeur' => $voyage->chauffeur ? $voyage->chauffeur->nom . ' ' . $voyage->chauffeur->prenom : 'Inconnu',
                'vehicule' => $voyage->vehicule ? $voyage->vehicule->immatriculation : 'N/A',
                'depart' => optional($voyage->programme->gareDepart)->nom_gare ?? $voyage->programme->point_depart,
                'arrivee' => optional($voyage->programme->gareArrivee)->nom_gare ?? $voyage->programme->point_arrive,
                'heure_depart' => $voyage->programme->heure_depart,
                'heure_arrivee' => $voyage->programme->heure_arrive,
                'date_voyage' => Carbon::parse($voyage->date_voyage)->format('d/m/Y'),
                'statut' => $voyage->statut,
                'temps_restant' => $voyage->temps_restant,
            ];
        })->filter(function ($loc) {
            return $loc['latitude'] !== null;
        })->values();

        return response()->json([
            'success' => true,
            'locations' => $locations,
            'total_en_cours' => $activeVoyages->count(),
            'total_tracked' => $locations->count(),
        ]);
    }
}
