<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Convoi;
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

        $activeConvois = Convoi::where('compagnie_id', $compagnie->id)
            ->where('statut', 'en_cours')
            ->with(['chauffeur', 'vehicule', 'itineraire', 'gare', 'latestLocation'])
            ->get();

        return view('compagnie.tracking.index', compact('activeVoyages', 'activeConvois'));
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

        $activeConvois = Convoi::where('compagnie_id', $compagnie->id)
            ->where('statut', 'en_cours')
            ->with(['chauffeur', 'vehicule', 'itineraire', 'gare', 'latestLocation'])
            ->get();

        $locations = $activeVoyages->map(function ($voyage) {
            $location    = $voyage->latestLocation;
            $gareDepart  = $voyage->programme->gareDepart;
            $gareArrivee = $voyage->programme->gareArrivee;
            $latitude = $location ? (float) $location->latitude : ($gareDepart ? (float) $gareDepart->latitude : null);
            $longitude = $location ? (float) $location->longitude : ($gareDepart ? (float) $gareDepart->longitude : null);

            return [
                'track_id'        => 'voyage_' . $voyage->id,
                'type'            => 'voyage',
                'voyage_id'       => $voyage->id,
                'latitude'        => $latitude,
                'longitude'       => $longitude,
                'speed'           => $location ? $location->speed : null,
                'heading'         => $location ? $location->heading : null,
                'last_update'     => $location ? $location->updated_at->diffForHumans() : 'Jamais',
                'chauffeur'       => $voyage->chauffeur ? $voyage->chauffeur->nom . ' ' . $voyage->chauffeur->prenom : 'Inconnu',
                'vehicule'        => $voyage->vehicule ? $voyage->vehicule->immatriculation : 'N/A',
                'depart'          => optional($gareDepart)->nom_gare ?? $voyage->programme->point_depart,
                'arrivee'         => optional($gareArrivee)->nom_gare ?? $voyage->programme->point_arrive,
                'heure_depart'    => $voyage->programme->heure_depart,
                'heure_arrivee'   => $voyage->programme->heure_arrive,
                'date_voyage'     => Carbon::parse($voyage->date_voyage)->format('d/m/Y'),
                'statut'          => $voyage->statut,
                'temps_restant'   => $voyage->temps_restant,
                'gare_depart_lat' => $gareDepart ? (float) $gareDepart->latitude  : null,
                'gare_depart_lng' => $gareDepart ? (float) $gareDepart->longitude : null,
                'gare_arrivee_lat'=> $gareArrivee ? (float) $gareArrivee->latitude  : null,
                'gare_arrivee_lng'=> $gareArrivee ? (float) $gareArrivee->longitude : null,
                'is_fallback_position' => $location ? false : true,
            ];
        })->values()->concat(
            $activeConvois->map(function ($convoi) {
                $location = $convoi->latestLocation;

                return [
                    'track_id' => 'convoi_' . $convoi->id,
                    'type' => 'convoi',
                    'voyage_id' => null,
                    'convoi_id' => $convoi->id,
                    'latitude' => $location ? (float) $location->latitude : null,
                    'longitude' => $location ? (float) $location->longitude : null,
                    'speed' => $location ? $location->speed : null,
                    'heading' => $location ? $location->heading : null,
                    'last_update' => $location ? $location->updated_at->diffForHumans() : 'Jamais',
                    'chauffeur' => $convoi->chauffeur ? trim(($convoi->chauffeur->prenom ?? '') . ' ' . ($convoi->chauffeur->name ?? '')) : 'Inconnu',
                    'vehicule' => $convoi->vehicule ? $convoi->vehicule->immatriculation : 'N/A',
                    'depart' => $convoi->itineraire->point_depart ?? 'Convoi',
                    'arrivee' => $convoi->itineraire->point_arrive ?? 'Destination',
                    'heure_depart' => null,
                    'heure_arrivee' => null,
                    'date_voyage' => Carbon::parse($convoi->updated_at)->format('d/m/Y'),
                    'statut' => $convoi->statut,
                    'temps_restant' => null,
                    'gare_depart_lat' => null,
                    'gare_depart_lng' => null,
                    'gare_arrivee_lat' => null,
                    'gare_arrivee_lng' => null,
                    'is_fallback_position' => false,
                ];
            })->filter(function ($loc) {
                return $loc['latitude'] !== null && $loc['longitude'] !== null;
            })->values()
        )->values();

        return response()->json([
            'success' => true,
            'locations' => $locations,
            'total_en_cours' => $activeVoyages->count() + $activeConvois->count(),
            'total_tracked' => $locations->count(),
        ]);
    }
}
