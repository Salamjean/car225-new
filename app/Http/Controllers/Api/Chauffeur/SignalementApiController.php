<?php

namespace App\Http\Controllers\Api\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use App\Models\Voyage;
use App\Models\SapeurPompier;
use App\Notifications\NewSignalementNotification;
use App\Mail\SignalementCompagnieNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SignalementApiController extends Controller
{
    /**
     * Liste des signalements du chauffeur
     */
    public function index(Request $request)
    {
        $chauffeur = $request->user();

        $signalements = Signalement::where('personnel_id', $chauffeur->id)
            ->whereNotNull('compagnie_id')
            ->whereNull('user_id')
            ->with(['voyage.gareDepart', 'voyage.gareArrivee', 'compagnie', 'vehicule'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'signalements' => $signalements->map(function($s) {
                return [
                    'id' => $s->id,
                    'type' => $s->type,
                    'description' => $s->description,
                    'statut' => $s->statut,
                    'photo' => $s->photo_path ? asset($s->photo_path) : null,
                    'latitude' => $s->latitude,
                    'longitude' => $s->longitude,
                    'created_at' => $s->created_at->format('d/m/Y H:i'),
                    'voyage' => $s->voyage ? [
                        'id' => $s->voyage->id,
                        'gare_depart' => optional($s->voyage->gareDepart)->nom_gare ?? '',
                        'gare_arrivee' => optional($s->voyage->gareArrivee)->nom_gare ?? '',
                    ] : null,
                    'vehicule' => $s->vehicule ? $s->vehicule->immatriculation : null,
                ];
            }),
            'pagination' => [
                'current_page' => $signalements->currentPage(),
                'last_page' => $signalements->lastPage(),
                'total' => $signalements->total(),
            ],
        ]);
    }

    /**
     * Détails d'un signalement
     */
    public function show(Request $request, Signalement $signalement)
    {
        $chauffeur = $request->user();
        if ($signalement->personnel_id !== $chauffeur->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
        }

        $signalement->load(['voyage.programme', 'voyage.gareDepart', 'vehicule', 'compagnie']);

        return response()->json([
            'success' => true,
            'signalement' => [
                'id' => $signalement->id,
                'type' => $signalement->type,
                'description' => $signalement->description,
                'statut' => $signalement->statut,
                'photo' => $signalement->photo_path ? asset($signalement->photo_path) : null,
                'latitude' => $signalement->latitude,
                'longitude' => $signalement->longitude,
                'created_at' => $signalement->created_at->format('d/m/Y H:i'),
                'voyage' => $signalement->voyage ? [
                    'id' => $signalement->voyage->id,
                    'programme' => optional($signalement->voyage->programme)->point_depart . ' → ' . optional($signalement->voyage->programme)->point_arrive,
                    'gare_depart' => optional($signalement->voyage->gareDepart)->nom_gare ?? '',
                ] : null,
                'vehicule' => $signalement->vehicule ? $signalement->vehicule->immatriculation : null,
                'compagnie' => $signalement->compagnie ? $signalement->compagnie->name : null,
            ],
        ]);
    }

    /**
     * Voyages disponibles pour signalement
     */
    public function getVoyagesForSignalement(Request $request)
    {
        $chauffeur = $request->user();

        $voyages = Voyage::where('personnel_id', $chauffeur->id)
            ->whereIn('statut', ['confirmé', 'en_cours'])
            ->with(['programme', 'vehicule', 'gareDepart'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($v) {
                return [
                    'id' => $v->id,
                    'date_voyage' => $v->date_voyage,
                    'statut' => $v->statut,
                    'programme' => $v->programme ? $v->programme->point_depart . ' → ' . $v->programme->point_arrive : '',
                    'vehicule' => $v->vehicule ? $v->vehicule->immatriculation : 'N/A',
                    'gare_depart' => optional($v->gareDepart)->nom_gare ?? '',
                ];
            });

        return response()->json([
            'success' => true,
            'voyages' => $voyages,
        ]);
    }

    /**
     * Créer un signalement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'voyage_id' => 'required|exists:voyages,id',
            'type' => 'required|in:accident,panne,retard,comportement,autre',
            'description' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'required_if:type,accident|image|max:10240',
        ]);

        try {
            DB::beginTransaction();

            $chauffeur = $request->user();
            $voyage = Voyage::findOrFail($validated['voyage_id']);

            $signalement = new Signalement();
            $signalement->personnel_id = $chauffeur->id;
            $signalement->compagnie_id = $chauffeur->compagnie_id;
            $signalement->voyage_id = $voyage->id;
            $signalement->programme_id = $voyage->programme_id;
            $signalement->vehicule_id = $voyage->vehicule_id;
            $signalement->type = $validated['type'];
            $signalement->description = $validated['description'];
            $signalement->latitude = $validated['latitude'] ?? null;
            $signalement->longitude = $validated['longitude'] ?? null;
            $signalement->statut = 'nouveau';

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('signalements', 'public');
                $signalement->photo_path = 'storage/' . $path;
            }

            $signalement->save();

            // Logique Sapeur Pompier pour les accidents
            if ($signalement->type === 'accident' && $signalement->latitude && $signalement->longitude) {
                $nearestFirefighter = $this->findNearestSapeurPompier($signalement->latitude, $signalement->longitude);
                if ($nearestFirefighter) {
                    $signalement->sapeur_pompier_id = $nearestFirefighter->id;
                    $signalement->save();
                    try {
                        Notification::send($nearestFirefighter, new NewSignalementNotification($signalement));
                    } catch (\Exception $e) {
                        Log::error('Erreur notification pompier API: ' . $e->getMessage());
                    }
                }
            }

            // Notification Compagnie
            try {
                if ($chauffeur->compagnie && $chauffeur->compagnie->email) {
                    Mail::to($chauffeur->compagnie->email)
                        ->send(new SignalementCompagnieNotification($signalement));
                }
            } catch (\Exception $e) {
                Log::error('Erreur email compagnie API: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Signalement envoyé avec succès.',
                'signalement' => [
                    'id' => $signalement->id,
                    'type' => $signalement->type,
                    'statut' => $signalement->statut,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur SignalementApiController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'envoi du signalement.',
            ], 500);
        }
    }

    private function findNearestSapeurPompier($lat, $lon)
    {
        $pompiers = SapeurPompier::whereNotNull('latitude')->whereNotNull('longitude')->get();
        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($pompiers as $pompier) {
            $distance = $this->calculateDistance($lat, $lon, $pompier->latitude, $pompier->longitude);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $pompier;
            }
        }

        return $nearest;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
