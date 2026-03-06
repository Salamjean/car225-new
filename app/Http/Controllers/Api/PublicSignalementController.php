<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use App\Models\SapeurPompier;
use App\Models\Compagnie;
use App\Notifications\NewSignalementNotification;
use App\Mail\SignalementCompagnieNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class PublicSignalementController extends Controller
{
    /**
     * Signalement public d'accident (sans authentification requise).
     *
     * Permet à n'importe quel utilisateur ayant l'application
     * de signaler un accident en envoyant une photo et sa position GPS.
     * Le signalement est transmis aux sapeurs-pompiers les plus proches
     * et aux compagnies de transport.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description'  => 'required|string|max:2000',
            'photo'        => 'required|image|max:10240', // 10 Mo max
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
        ]);

        try {
            DB::beginTransaction();

            $signalement = new Signalement();
            $signalement->type        = 'accident'; // Par défaut accident
            $signalement->description = $validated['description'];
            $signalement->latitude    = $validated['latitude'];
            $signalement->longitude   = $validated['longitude'];
            $signalement->statut      = 'nouveau';

            // Pas de user_id, programme_id, etc. car l'utilisateur n'est pas connecté
            $signalement->user_id       = null;
            $signalement->programme_id  = null;

            // Upload de la photo
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('signalements', 'public');
                $signalement->photo_path = 'storage/' . $path;
            }

            $signalement->save();

            // ===================================================================
            // Notification aux Sapeurs-Pompiers les plus proches
            // ===================================================================
            if ($signalement->latitude && $signalement->longitude) {
                $nearestFirefighter = $this->findNearestSapeurPompier(
                    $signalement->latitude,
                    $signalement->longitude
                );

                if ($nearestFirefighter) {
                    $signalement->sapeur_pompier_id = $nearestFirefighter->id;
                    $signalement->save();

                    try {
                        Notification::send($nearestFirefighter, new NewSignalementNotification($signalement));
                    } catch (\Exception $e) {
                        Log::error('Erreur notification pompier (signalement public): ' . $e->getMessage());
                    }
                }
            }

            // ===================================================================
            // Notification à toutes les compagnies (pas de programme lié)
            // On notifie toutes les compagnies actives pour qu'elles soient au courant
            // ===================================================================
            try {
                $compagnies = Compagnie::whereNotNull('email')->get();
                foreach ($compagnies as $compagnie) {
                    try {
                        Mail::to($compagnie->email)
                            ->send(new SignalementCompagnieNotification($signalement));
                    } catch (\Exception $e) {
                        Log::error("Erreur envoi email compagnie {$compagnie->id} (signalement public): " . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                Log::error('Erreur notification compagnies (signalement public): ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Signalement d\'accident enregistré avec succès. Les secours ont été alertés.',
                'signalement'  => [
                    'id'          => $signalement->id,
                    'type'        => $signalement->type,
                    'description' => $signalement->description,
                    'photo_url'   => $signalement->photo_path ? asset($signalement->photo_path) : null,
                    'latitude'    => $signalement->latitude,
                    'longitude'   => $signalement->longitude,
                    'statut'      => $signalement->statut,
                    'created_at'  => $signalement->created_at,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur PublicSignalementController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'enregistrement du signalement: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Trouver le sapeur-pompier le plus proche via la formule de Haversine.
     */
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

    /**
     * Calcule la distance entre deux points GPS (formule de Haversine).
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
