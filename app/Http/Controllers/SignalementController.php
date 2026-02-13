<?php

namespace App\Http\Controllers;

use App\Models\Signalement;
use App\Models\SapeurPompier;
use App\Models\Programme;
use App\Notifications\NewSignalementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SignalementController extends Controller
{
    /**
     * Affiche le formulaire de signalement.
     */
    public function create()
    {
        // Récupérer les réservations du jour "VOYAGE DU JOUR"
        $user = Auth::user();
        $today = now()->format('Y-m-d');

        $reservations = \App\Models\Reservation::with(['programme.compagnie'])
            ->where('user_id', $user->id)
            ->whereDate('date_voyage', $today)
            ->where('statut', 'confirmee')
            ->get();

        return view('user.signalement.create', compact('reservations'));
    }

    public function getCompanyVehicles($compagnieId)
    {
        $vehicules = \App\Models\Vehicule::where('compagnie_id', $compagnieId)->get(['id', 'immatriculation', 'marque', 'modele']);
        return response()->json($vehicules);
    }

    public function apiProgramOccupancy($programmeId)
    {
        $today = now()->format('Y-m-d');
        $reservations = \App\Models\Reservation::where('programme_id', $programmeId)
            ->whereDate('date_voyage', $today)
            ->where('statut', 'confirmee')
            ->get(['id', 'passager_nom', 'passager_prenom', 'seat_number', 'passager_telephone']);

        return response()->json([
            'success' => true,
            'count' => $reservations->count(),
            'passengers' => $reservations
        ]);
    }

    /**
     * Enregistre le signalement.
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
            'photo' => 'nullable|image|max:10240', // Max 10MB
        ]);

        try {
            DB::beginTransaction();

            $signalement = new Signalement();
            $signalement->user_id = Auth::id(); // Assure que l'utilisateur est connecté
            $signalement->programme_id = $validated['programme_id'];

            if (isset($validated['vehicule_id']) && !empty($validated['vehicule_id'])) {
                $signalement->vehicule_id = $validated['vehicule_id'];
            } else {
                // Par défaut, lier au véhicule prévu du programme
                $programme = Programme::find($validated['programme_id']);
                if ($programme) {
                    $signalement->vehicule_id = $programme->vehicule_id;
                }
            }
            $signalement->type = $validated['type'];
            $signalement->description = $validated['description'];
            $signalement->latitude = $validated['latitude'];
            $signalement->longitude = $validated['longitude'];
            $signalement->statut = 'nouveau';

            // Gestion de l'upload de photo
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('signalements', 'public');
                $signalement->photo_path = $path;
            }

            // Sauvegarder d'abord pour avoir un ID
            $signalement->save();

            // Logique Sapeur Pompier
            if ($signalement->type === 'accident' && $signalement->latitude && $signalement->longitude) {
                $nearestFirefighter = $this->findNearestSapeurPompier($signalement->latitude, $signalement->longitude);

                if ($nearestFirefighter) {
                    $signalement->sapeur_pompier_id = $nearestFirefighter->id;
                    $signalement->save(); // Mettre à jour avec l'ID du pompier

                    // Notifier le sapeur pompier
                    try {
                        Notification::send($nearestFirefighter, new NewSignalementNotification($signalement));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Erreur notification pompier: ' . $e->getMessage());
                    }
                }
            }

            // Notifier la compagnie par email (pour TOUS les types d'incidents)
            try {
                $programme = $signalement->programme;
                if ($programme && $programme->compagnie && $programme->compagnie->email) {
                    \Illuminate\Support\Facades\Mail::to($programme->compagnie->email)
                        ->send(new \App\Mail\SignalementCompagnieNotification($signalement));
                    \Illuminate\Support\Facades\Log::info('Email envoyé à la compagnie: ' . $programme->compagnie->email);
                } else {
                    \Illuminate\Support\Facades\Log::warning('Impossible d\'envoyer email compagnie: email manquant ou programme introuvable.');
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Erreur envoi email compagnie: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('user.dashboard')->with('success', 'Votre signalement a bien été pris en compte.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors du signalement : ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Trouve le groupe de sapeur-pompier le plus proche.
     */
    private function findNearestSapeurPompier($lat, $lon)
    {
        // On prend tous les sapeurs avec coordonnées
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
     * Formule de Haversine pour calculer la distance en km.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Rayon de la terre en km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
