<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use App\Models\SapeurPompier;
use App\Models\Programme;
use App\Models\Reservation;
use App\Notifications\NewSignalementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SignalementCompagnieNotification;

class SignalementApiController extends Controller
{
    /**
     * Liste les réservations du jour pour lesquelles un utilisateur peut faire un signalement.
     */
    public function getActiveReservations()
    {
        try {
            $user = Auth::user();
            $today = now()->format('Y-m-d');

            $reservations = Reservation::with(['programme.compagnie', 'programme.vehicule'])
                ->where('user_id', $user->id)
                ->whereDate('date_voyage', $today)
                ->where('statut', 'confirmee')
                ->get();

            return response()->json([
                'success' => true,
                'reservations' => $reservations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des réservations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enregistre un signalement via l'API.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'type' => 'required|in:accident,panne,retard,comportement,autre',
            'description' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'vehicule_id' => 'nullable|exists:vehicules,id',
            'photo' => 'required_if:type,accident|image|max:10240', // Obligatoire si accident
        ]);

        try {
            DB::beginTransaction();

            $signalement = new Signalement();
            $signalement->user_id = Auth::id();
            $signalement->programme_id = $validated['programme_id'];

            if (!empty($validated['vehicule_id'])) {
                $signalement->vehicule_id = $validated['vehicule_id'];
            } else {
                $programme = Programme::find($validated['programme_id']);
                if ($programme) {
                    $signalement->vehicule_id = $programme->vehicule_id;
                }
            }

            $signalement->type = $validated['type'];
            $signalement->description = $validated['description'];
            $signalement->latitude = $validated['latitude'] ?? null;
            $signalement->longitude = $validated['longitude'] ?? null;
            $signalement->statut = 'nouveau';

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('signalements', 'public');
                $signalement->photo_path = 'storage/' . $path; // Ajout du préfixe storage/
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
                        Log::error('Erreur notification API pompier: ' . $e->getMessage());
                    }
                }
            }

            // Notification Compagnie
            try {
                $programme = $signalement->programme;
                if ($programme && $programme->compagnie && $programme->compagnie->email) {
                    Mail::to($programme->compagnie->email)
                        ->send(new SignalementCompagnieNotification($signalement));
                }
            } catch (\Exception $e) {
                Log::error('Erreur envoi email API compagnie: ' . $e->getMessage());
            }

            // Notification de confirmation à l'utilisateur (Database pour le "Bell" icon)
            try {
                Auth::user()->notify(new \App\Notifications\GeneralNotification(
                    'Signalement enregistré ⚠️',
                    "Votre signalement de type '" . ucfirst($signalement->type) . "' a été reçu et est en cours de traitement.",
                    'warning'
                ));
            } catch (\Exception $e) {
                Log::error('Erreur notification utilisateur signalement: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Signalement enregistré avec succès.',
                'signalement' => $signalement
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur SignalementApiController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste les signalements de l'utilisateur.
     */
    public function index()
    {
        try {
            $signalements = Signalement::with(['programme.compagnie', 'vehicule'])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'signalements' => $signalements
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    private function findNearestSapeurPompier($lat, $lon)
    {
        $pompiers = SapeurPompier::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

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
