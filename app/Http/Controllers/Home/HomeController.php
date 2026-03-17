<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Compagnie;
use App\Models\Programme;
use App\Models\Itineraire;
use App\Models\Reservation;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon; // Import nécessaire pour gérer les dates proprement
use App\Models\User;

class HomeController extends Controller
{
     public function home()
    {
        $compagnies = Compagnie::get();
        $usersCount = User::count();
        
        // Trajets populaires - programmes actifs
        $today = Carbon::now()->format('Y-m-d');
        $trajetsPopulaires = Programme::with(['compagnie', 'gareDepart', 'gareArrivee'])
            ->where('statut', '=', 'actif', 'and')
            ->whereRaw('DATE(date_depart) <= ?', [$today])
            ->whereRaw('DATE(date_fin) >= ?', [$today])
            ->orderBy('montant_billet', 'asc')
            ->take(4)
            ->get();
        
        return view('home.home', compact('compagnies', 'usersCount', 'trajetsPopulaires'));
    }

      public function search(Request $request)
    {
        $request->validate([
            'point_depart' => 'required|string|max:255',
            'point_arrive' => 'required|string|max:255',
            'date_depart' => 'required|date',
            'is_aller_retour' => 'nullable',
            'date_retour' => 'required_if:is_aller_retour,1|nullable|date',
        ]);

        $point_depart = $request->point_depart;
        $point_arrive = $request->point_arrive;
        $date_depart = $request->date_depart;
        $is_aller_retour = $request->is_aller_retour == '1';
        $date_retour = $request->date_retour;

        Log::info('====== DEBUT RECHERCHE PROGRAMME ALLER-RETOUR ======');

        // Fonction locale pour la requête (évite la duplication)
        $executeQuery = function($from, $to, $date) {
            $formattedDate = date('Y-m-d', strtotime($date));
            $fromClean = trim(explode(',', $from)[0]);
            $toClean = trim(explode(',', $to)[0]);

            return Programme::with(['compagnie', 'itineraire', 'gareDepart', 'gareArrivee'])
                ->where(function($q) use ($fromClean) {
                    $q->where('point_depart', 'like', "%{$fromClean}%", 'and');
                })
                ->where(function($q) use ($toClean) {
                    $q->where('point_arrive', 'like', "%{$toClean}%", 'and');
                })
                ->whereRaw('DATE(date_depart) <= ?', [$formattedDate])
                ->whereRaw('DATE(date_fin) >= ?', [$formattedDate])
                ->where('statut', '=', 'actif', 'and')
                ->orderBy('heure_depart', 'asc')
                ->get();
        };

        $programmes_aller = $executeQuery($point_depart, $point_arrive, $date_depart);
        $programmes_retour = $is_aller_retour ? $executeQuery($point_arrive, $point_depart, $date_retour) : collect();

        $totalResults = $programmes_aller->count() + $programmes_retour->count();

        $searchParams = [
            'point_depart' => $point_depart,
            'point_arrive' => $point_arrive,
            'date_depart' => $date_depart,
            'is_aller_retour' => $is_aller_retour,
            'date_retour' => $date_retour,
        ];

        // Pour la compatibilité avec la vue existante, on passe $programmes qui contient $programmes_aller
        $programmes = $programmes_aller;

        return view('home.programmes.results', compact('programmes', 'programmes_aller', 'programmes_retour', 'totalResults', 'searchParams'));
    }

    /**
     * Afficher tous les programmes
     */
    public function all()
    {
        $today = Carbon::now()->format('Y-m-d');

        // On récupère les programmes valides AUJOURD'HUI
        // C'est-à-dire : Commencés avant ou aujourd'hui ET finissant aujourd'hui ou après
        $programmes = Programme::with(['compagnie', 'itineraire', 'gareDepart', 'gareArrivee'])
            ->whereRaw('DATE(date_depart) <= ?', [$today])
            ->whereRaw('DATE(date_fin) >= ?', [$today])
            ->where('statut', '=', 'actif', 'and')
            ->orderBy('heure_depart', 'asc') // Tri par heure puisque c'est pour la journée courante
            ->paginate(12);

        return view('home.programmes.all', compact('programmes'));
    }

    /**
     * Afficher les détails d'un programme
     */
   public function show(Programme $programme)
    {
        $programme->load(['compagnie', 'itineraire', 'gareDepart', 'gareArrivee']);
        return view('home.programmes.show', compact('programme'));
    }


    /**
     * Obtenir les détails d'un véhicule avec les places réservées
     */
    public function getVehicleDetails($id, Request $request)
    {
        try {
            $dateVoyage = $request->get('date') ? date('Y-m-d', strtotime($request->get('date'))) : date('Y-m-d');
            $programmeId = $request->get('programme_id');
            $vehicule = null;
            $otherHours = [];

            // 1. Get the current program to find other hours
            $currentProgramme = Programme::find($programmeId);
            if ($currentProgramme) {
                $otherHours = Programme::query()
                    ->where('point_depart', '=', $currentProgramme->point_depart, 'and')
                    ->where('point_arrive', '=', $currentProgramme->point_arrive, 'and')
                    ->whereRaw('DATE(date_depart) <= ?', [$dateVoyage])
                    ->whereRaw('DATE(date_fin) >= ?', [$dateVoyage])
                    ->where('statut', '=', 'actif', 'and')
                    ->orderBy('heure_depart', 'asc')
                    ->get()
                    ->map(function($p) {
                        return [
                            'id' => $p->id,
                            'heure' => substr($p->heure_depart, 0, 5),
                            'vehicule_id' => $p->getVehiculeForDate(date('Y-m-d'))->id ?? 0
                        ];
                    });
            }

            // 2. Original logic for vehicle
            if ($id && $id !== '0' && $id !== 'null' && intval($id) > 0) {
                $vehicule = Vehicule::find($id);
            }

            if (!$vehicule && $currentProgramme) {
                $vehicule = $currentProgramme->getVehiculeForDate($dateVoyage);
                if (!$vehicule) {
                    $vehicule = Vehicule::where('compagnie_id', '=', $currentProgramme->compagnie_id, 'and')
                        ->where('is_active', '=', true, 'and')
                        ->first();
                }
            }

            if (!$vehicule) {
                $vehicule = (object)[
                    'id' => 0,
                    'marque' => 'Bus',
                    'modele' => 'Standard',
                    'immatriculation' => 'N/A',
                    'nombre_place' => 70,
                    'type_range' => '2x3',
                    'is_default' => true
                ];
            }

            $reservedSeats = [];
            if ($dateVoyage) {
                $query = Reservation::query();
                $query->where('date_voyage', '=', $dateVoyage, 'and');
                $query->whereIn('statut', ['confirmee', 'en_attente', 'terminee']);

                if ($programmeId && $programmeId !== 'null') {
                    $query->where('programme_id', '=', $programmeId, 'and');
                } elseif (isset($vehicule->id) && $vehicule->id > 0) {
                    $programmeIds = Programme::where('vehicule_id', '=', $vehicule->id, 'and')
                        ->whereRaw('DATE(date_depart) <= ?', [$dateVoyage])
                        ->whereRaw('DATE(date_fin) >= ?', [$dateVoyage])
                        ->pluck('id');
                    $query->whereIn('programme_id', $programmeIds);
                }

                $reservedSeats = $query->pluck('seat_number')->toArray();
                $reservedSeats = array_values(array_unique(array_map('intval', $reservedSeats)));
            }

            return response()->json([
                'success' => true,
                'vehicule' => $vehicule,
                'reservedSeats' => $reservedSeats,
                'date' => $dateVoyage,
                'otherHours' => $otherHours
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur getVehicleDetails:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Erreur serveur'], 500);
        }
    }
    public function getLocations(Request $request)
    {
        $search = $request->get('q');
        
        $departures = Programme::where('statut', '=', 'actif', 'and')
            ->where('point_depart', 'LIKE', "%{$search}%", 'and')
            ->distinct()
            ->pluck('point_depart')
            ->toArray();
            
        $arrivals = Programme::where('statut', '=', 'actif', 'and')
            ->where('point_arrive', 'LIKE', "%{$search}%", 'and')
            ->distinct()
            ->pluck('point_arrive')
            ->toArray();
            
        $locations = array_unique(array_merge($departures, $arrivals));
        sort($locations);
        
        return response()->json(array_values($locations));
    }
}