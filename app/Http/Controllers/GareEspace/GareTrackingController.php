<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Models\Voyage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GareTrackingController extends Controller
{
    /**
     * Page de suivi des voyages en cours pour la gare
     */
    public function index()
    {
        $gare = Auth::guard('gare')->user();

        $activeVoyages = Voyage::where('statut', 'en_cours')
            ->whereHas('programme', function ($q) use ($gare) {
                $q->where('compagnie_id', $gare->compagnie_id)
                  ->where(function ($q2) use ($gare) {
                      $q2->where('gare_depart_id', $gare->id)
                         ->orWhere('gare_arrivee_id', $gare->id);
                  });
            })
            ->with([
                'programme.gareDepart',
                'programme.gareArrivee',
                'chauffeur',
                'vehicule',
                'latestLocation',
            ])
            ->get();

        return view('gare-espace.tracking.index', compact('activeVoyages'));
    }

    /**
     * API JSON : positions GPS des voyages en cours pour cette gare
     */
    public function getActiveLocations()
    {
        $gare = Auth::guard('gare')->user();

        $activeVoyages = Voyage::where('statut', 'en_cours')
            ->whereHas('programme', function ($q) use ($gare) {
                $q->where('compagnie_id', $gare->compagnie_id)
                  ->where(function ($q2) use ($gare) {
                      $q2->where('gare_depart_id', $gare->id)
                         ->orWhere('gare_arrivee_id', $gare->id);
                  });
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
            $gareDepart  = $voyage->programme->gareDepart;
            $gareArrivee = $voyage->programme->gareArrivee;
            return [
                'voyage_id'   => $voyage->id,
                'latitude'    => $location ? (float) $location->latitude : null,
                'longitude'   => $location ? (float) $location->longitude : null,
                'speed'       => $location ? $location->speed : null,
                'heading'     => $location ? $location->heading : null,
                'last_update' => $location ? $location->updated_at->diffForHumans() : 'Jamais',
                'chauffeur'   => $voyage->chauffeur ? $voyage->chauffeur->nom . ' ' . $voyage->chauffeur->prenom : 'Inconnu',
                'vehicule'    => $voyage->vehicule ? $voyage->vehicule->immatriculation : 'N/A',
                'depart'      => optional($gareDepart)->nom_gare ?? $voyage->programme->point_depart,
                'arrivee'     => optional($gareArrivee)->nom_gare ?? $voyage->programme->point_arrive,
                'gare_depart_lat'  => $gareDepart ? (float) $gareDepart->latitude  : null,
                'gare_depart_lng'  => $gareDepart ? (float) $gareDepart->longitude : null,
                'gare_arrivee_lat' => $gareArrivee ? (float) $gareArrivee->latitude  : null,
                'gare_arrivee_lng' => $gareArrivee ? (float) $gareArrivee->longitude : null,
                'heure_depart'  => $voyage->programme->heure_depart,
                'heure_arrivee' => $voyage->programme->heure_arrive,
                'date_voyage'   => Carbon::parse($voyage->date_voyage)->format('d/m/Y'),
                'statut'        => $voyage->statut,
                'temps_restant' => $voyage->temps_restant,
            ];
        })->filter(fn($loc) => $loc['latitude'] !== null)->values();

        return response()->json([
            'success'       => true,
            'locations'     => $locations,
            'total_en_cours' => $activeVoyages->count(),
            'total_tracked'  => $locations->count(),
        ]);
    }
}
