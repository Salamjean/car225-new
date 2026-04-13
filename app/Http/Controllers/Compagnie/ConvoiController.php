<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Convoi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConvoiController extends Controller
{
    public function index(Request $request)
    {
        $compagnie = Auth::guard('compagnie')->user();
        $statut = $request->query('statut', 'all');

        $query = Convoi::with(['user', 'itineraire', 'gare'])
            ->withCount('passagers')
            ->where('compagnie_id', $compagnie->id)
            ->latest();

        if (in_array($statut, ['en_attente', 'valide', 'annule'])) {
            $query->where('statut', $statut);
        }

        $convois = $query->paginate(12)->withQueryString();

        return view('compagnie.convois.index', compact('convois', 'statut'));
    }

    public function show(Convoi $convoi)
    {
        $compagnie = Auth::guard('compagnie')->user();

        if ($convoi->compagnie_id !== $compagnie->id) {
            abort(403);
        }

        $convoi->load(['user', 'itineraire', 'passagers', 'chauffeur', 'vehicule', 'latestLocation']);

        return view('compagnie.convois.show', compact('convoi'));
    }

    public function location(Convoi $convoi): JsonResponse
    {
        $compagnie = Auth::guard('compagnie')->user();

        if ($convoi->compagnie_id !== $compagnie->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        $convoi->load(['latestLocation', 'chauffeur', 'vehicule', 'itineraire', 'gare']);
        $location = $convoi->latestLocation;

        return response()->json([
            'success' => true,
            'convoi_id' => $convoi->id,
            'statut' => $convoi->statut,
            'latitude' => $location ? (float) $location->latitude : null,
            'longitude' => $location ? (float) $location->longitude : null,
            'speed' => $location ? $location->speed : null,
            'heading' => $location ? $location->heading : null,
            'last_update' => $location ? $location->updated_at->diffForHumans() : 'Jamais',
            'chauffeur' => $convoi->chauffeur ? trim(($convoi->chauffeur->prenom ?? '') . ' ' . ($convoi->chauffeur->name ?? '')) : 'Inconnu',
            'vehicule' => $convoi->vehicule->immatriculation ?? 'N/A',
            'trajet' => $convoi->itineraire ? ($convoi->itineraire->point_depart . ' -> ' . $convoi->itineraire->point_arrive) : '-',
            'gare' => $convoi->gare->nom_gare ?? '-',
        ]);
    }
}

