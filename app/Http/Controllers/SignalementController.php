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
    public function create(Request $request)
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');
        $preselectedReservationId = $request->query('reservation_id');

        // Récupérer toutes les réservations du jour (Scannées/Terminées ou Confirmées)
        $reservations = \App\Models\Reservation::with(['programme.compagnie', 'programme.voyages'])
            ->where('user_id', $user->id)
            ->whereDate('date_voyage', $today)
            ->whereIn('statut', ['confirmee', 'terminee'])
            ->get();

        // Chercher celle qui est "en voyage" (le Voyage associé est 'en_cours')
        $activeReservation = $reservations->first(function($res) {
            // Un utilisateur est "en voyage" si sa réservation est 'terminee' (scannée) 
            // ET que le voyage (mission) est 'en_cours'
            return $res->statut === 'terminee' && $res->mission && $res->mission->statut === 'en_cours';
        });

        // Si on a une réservation active, elle devient la présélectionnée par défaut
        if ($activeReservation && !$preselectedReservationId) {
            $preselectedReservationId = $activeReservation->id;
        }

        $enVoyage = !is_null($activeReservation);

        $selectedReservation = null;
        if ($preselectedReservationId) {
            $selectedReservation = $reservations->firstWhere('id', $preselectedReservationId);
            
            if ($selectedReservation) {
                // 1. Est-ce que le passager est déjà lié à un véhicule via le scan ?
                if ($selectedReservation->embarquement_vehicule_id) {
                    $actualVehicule = \App\Models\Vehicule::find($selectedReservation->embarquement_vehicule_id);
                }

                // 2. On cherche le Voyage correspondant (Source de vérité pour le car et le chauffeur)
                $actualVoyage = $selectedReservation->mission;
                
                if (!$actualVehicule && $actualVoyage) {
                    $actualVehicule = $actualVoyage->vehicule;
                }
            }
        }

        return view('user.signalement.create', compact('reservations', 'preselectedReservationId', 'selectedReservation', 'actualVehicule', 'actualVoyage', 'enVoyage'));
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
            ->where('statut', 'terminee')
            ->get(['id', 'passager_nom', 'passager_prenom', 'seat_number', 'passager_telephone']);

        if ($reservations->isNotEmpty()) {
            $prog = \App\Models\Programme::find($programmeId);
            $total = $prog ? $prog->getTotalSeats($today) : 50;
        } else {
            $total = 50;
        }

        return response()->json([
            'success' => true,
            'count' => $reservations->count(),
            'total_capacity' => $total,
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
            $signalement->user_id = Auth::id(); 
            $signalement->programme_id = $validated['programme_id'];
            $signalement->type = $validated['type'];
            $signalement->description = $validated['description'];
            $signalement->latitude = $validated['latitude'];
            $signalement->longitude = $validated['longitude'];
            $signalement->statut = 'nouveau';

            // --- RECHERCHE DU VOYAGE RÉEL (Source de vérité) ---
            $today = now()->format('Y-m-d');
            $voyage = \App\Models\Voyage::where('programme_id', $validated['programme_id'])
                ->whereDate('date_voyage', $today)
                ->first();

            if ($voyage) {
                $signalement->voyage_id = $voyage->id;
                $signalement->personnel_id = $voyage->personnel_id; // Le chauffeur concerné
                $signalement->compagnie_id = $voyage->compagnie_id; // Hérité via le programme
                $signalement->vehicule_id = $voyage->vehicule_id;
            } else {
                // Fallback si pas de voyage spécifique créé (programmation simple)
                $programme = Programme::with('compagnie')->find($validated['programme_id']);
                if ($programme) {
                    $signalement->compagnie_id = $programme->compagnie_id;
                    $signalement->vehicule_id = $validated['vehicule_id'] ?? $programme->vehicule_id;
                }
            }

            // Gestion de l'upload de photo
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('signalements', 'public');
                $signalement->photo_path = $path;
            }

            $signalement->save();

            // Logique Sapeur Pompier
            if ($signalement->type === 'accident' && $signalement->latitude && $signalement->longitude) {
                $nearestFirefighter = $this->findNearestSapeurPompier($signalement->latitude, $signalement->longitude);

                if ($nearestFirefighter) {
                    $signalement->sapeur_pompier_id = $nearestFirefighter->id;
                    $signalement->save();

                    try {
                        Notification::send($nearestFirefighter, new \App\Notifications\SendSignalementToSapeurPompierNotification($signalement));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Erreur notification pompier: ' . $e->getMessage());
                    }
                }
            }

            // Notifier la compagnie par email
            try {
                $compagnie = \App\Models\Compagnie::find($signalement->compagnie_id);
                if ($compagnie && $compagnie->email) {
                    \Illuminate\Support\Facades\Mail::to($compagnie->email)
                        ->send(new \App\Mail\SignalementCompagnieNotification($signalement));
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
