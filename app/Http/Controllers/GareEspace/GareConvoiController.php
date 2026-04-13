<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Models\Convoi;
use App\Models\Personnel;
use App\Models\Vehicule;
use App\Models\Voyage;
use App\Notifications\ConvoiAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class GareConvoiController extends Controller
{
    public function index(Request $request)
    {
        $gare = Auth::guard('gare')->user();
        $statut = $request->query('statut', 'all');

        $query = Convoi::with(['user', 'compagnie', 'itineraire', 'chauffeur', 'vehicule'])
            ->withCount('passagers')
            ->where('gare_id', $gare->id)
            ->latest();

        if (in_array($statut, ['en_attente', 'valide', 'annule'])) {
            $query->where('statut', $statut);
        }

        $convois = $query->paginate(12)->withQueryString();

        $busyPersonnelIds = Voyage::where('statut', 'en_cours')
            ->whereNotNull('personnel_id')
            ->pluck('personnel_id');

        $busyVehiculeIds = Voyage::where('statut', 'en_cours')
            ->whereNotNull('vehicule_id')
            ->pluck('vehicule_id');

        $chauffeurs = Personnel::where('gare_id', $gare->id)
            ->where('type_personnel', 'chauffeur')
            ->where('statut', 'disponible')
            ->whereNull('archived_at')
            ->whereNotIn('id', $busyPersonnelIds)
            ->orderBy('prenom')
            ->orderBy('name')
            ->get(['id', 'name', 'prenom']);

        $vehicules = Vehicule::where('gare_id', $gare->id)
            ->where('is_active', true)
            ->where('statut', 'disponible')
            ->whereNotIn('id', $busyVehiculeIds)
            ->orderBy('immatriculation')
            ->get(['id', 'immatriculation', 'modele', 'nombre_place']);

        return view('gare-espace.convois.index', compact('convois', 'statut', 'chauffeurs', 'vehicules'));
    }

    public function show(Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id) {
            abort(403);
        }

        $convoi->load(['user', 'compagnie', 'itineraire', 'passagers', 'chauffeur', 'vehicule', 'latestLocation']);

        return view('gare-espace.convois.show', compact('convoi'));
    }

    public function location(Convoi $convoi): JsonResponse
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id) {
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

    public function assign(Request $request, Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id) {
            abort(403);
        }

        $validated = $request->validate([
            'personnel_id' => 'required|exists:personnels,id',
            'vehicule_id' => 'required|exists:vehicules,id',
        ]);

        $busyPersonnelIds = Voyage::where('statut', 'en_cours')
            ->whereNotNull('personnel_id')
            ->pluck('personnel_id')
            ->toArray();

        $busyVehiculeIds = Voyage::where('statut', 'en_cours')
            ->whereNotNull('vehicule_id')
            ->pluck('vehicule_id')
            ->toArray();

        if (in_array((int) $validated['personnel_id'], $busyPersonnelIds, true)) {
            return back()->with('error', 'Ce chauffeur n est plus disponible.');
        }
        if (in_array((int) $validated['vehicule_id'], $busyVehiculeIds, true)) {
            return back()->with('error', 'Ce véhicule n est plus disponible.');
        }

        $convoi->update([
            'personnel_id' => $validated['personnel_id'],
            'vehicule_id' => $validated['vehicule_id'],
            'statut' => 'valide',
        ]);

        Personnel::where('id', $validated['personnel_id'])->update(['statut' => 'indisponible']);
        Vehicule::where('id', $validated['vehicule_id'])->update(['statut' => 'indisponible']);

        try {
            $chauffeur = Personnel::find($validated['personnel_id']);
            if ($chauffeur) {
                $convoi->loadMissing(['itineraire', 'vehicule', 'gare']);
                $chauffeur->notify(new ConvoiAssignedNotification($convoi));

                if ($chauffeur->fcm_token) {
                    $fcmService = app(\App\Services\FcmService::class);
                    $route = ($convoi->itineraire->point_depart ?? 'N/A') . ' -> ' . ($convoi->itineraire->point_arrive ?? 'N/A');
                    $title = 'Nouveau Convoi Assigne';
                    $body = "Reference : " . ($convoi->reference ?? '-') . "\n"
                        . "Trajet : {$route}\n"
                        . "Passagers : " . ($convoi->nombre_personnes ?? 0) . "\n"
                        . "Gare : " . ($convoi->gare->nom_gare ?? 'N/A') . "\n"
                        . "Date : " . Carbon::now()->format('d/m/Y');

                    $fcmService->sendNotification(
                        $chauffeur->fcm_token,
                        $title,
                        $body,
                        [
                            'type' => 'convoi_assigned',
                            'convoi_id' => (string) $convoi->id,
                            'reference' => (string) ($convoi->reference ?? ''),
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur notification convoi (GareConvoiController@assign): ' . $e->getMessage());
        }

        return back()->with('success', 'Affectation effectuée avec succès.');
    }
}

