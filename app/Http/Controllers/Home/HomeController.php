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
            ->where('statut', 'actif')
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
        ]);

        $point_depart = $request->point_depart;
        $point_arrive = $request->point_arrive;
        $date_depart_recherche = $request->date_depart;
        $formattedDate = date('Y-m-d', strtotime($date_depart_recherche));

        Log::info('====== DEBUT RECHERCHE PROGRAMME ======');

        $query = Programme::with(['compagnie', 'itineraire', 'gareDepart', 'gareArrivee'])
            ->where(function($q) use ($point_depart) {
                $q->where('point_depart', 'like', "%{$point_depart}%");
                if (strpos($point_depart, ',') !== false) {
                    $ville = trim(explode(',', $point_depart)[0]);
                    $q->orWhere('point_depart', 'like', "{$ville}%");
                }
            })
            ->where(function($q) use ($point_arrive) {
                $q->where('point_arrive', 'like', "%{$point_arrive}%");
                if (strpos($point_arrive, ',') !== false) {
                    $ville = trim(explode(',', $point_arrive)[0]);
                    $q->orWhere('point_arrive', 'like', "{$ville}%");
                }
            })
            // Vérifie que le programme est valide pour cette date
            ->whereRaw('DATE(date_depart) <= ?', [$formattedDate])
            ->whereRaw('DATE(date_fin) >= ?', [$formattedDate])
            ->where('statut', 'actif');

        $query->orderBy('heure_depart', 'asc');

        $programmes = $query->paginate(10);
        $totalResults = $programmes->total();

        $searchParams = [
            'point_depart' => $point_depart,
            'point_arrive' => $point_arrive,
            'date_depart' => $date_depart_recherche,
        ];

        return view('home.programmes.results', compact('programmes', 'totalResults', 'searchParams'));
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
            ->where('statut', 'actif')
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

             // CORRECTION : On vérifie explicitement si l'ID est valide (> 0)
        if ($id && $id !== '0' && $id !== 'null' && intval($id) > 0) {
            $vehicule = Vehicule::find($id);
        }

            // 2. Si pas d'ID ou véhicule non trouvé, chercher via le programme
            if (!$vehicule && $programmeId && $programmeId !== 'null') {
                $programme = Programme::find($programmeId);
                if ($programme) {
                    $vehicule = $programme->getVehiculeForDate($dateVoyage);
                    
                    // Fallback sur le premier véhicule de la compagnie
                    if (!$vehicule) {
                        $vehicule = Vehicule::where('compagnie_id', $programme->compagnie_id)
                            ->where('is_active', true)
                            ->first();
                    }
                }
            }

            if (!$vehicule) {
                // FALLBACK: Si aucun véhicule n'est trouvé, on utilise un modèle standard de 70 places
                // pour permettre l'affichage du plan des sièges (demande utilisateur).
                $vehicule = (object)[
                    'id' => 0,
                    'marque' => 'Bus',
                    'modele' => 'Standard',
                    'immatriculation' => 'N/A',
                    'nombre_place' => 70,
                    'type_range' => '2x3',
                    'is_default' => true
                ];
                Log::info('Véhicule par défaut utilisé pour le popup car aucun véhicule assigné.');
            }

            $reservedSeats = [];
            
            if ($dateVoyage) {
                $query = Reservation::query();
                // On compte les places réservées pour ce programme à cette date
                $query->where('date_voyage', $dateVoyage);
                $query->whereIn('statut', ['confirmee', 'en_attente', 'terminee']);

                if ($programmeId && $programmeId !== 'null') {
                    $query->where('programme_id', $programmeId);
                } elseif (isset($vehicule->id) && $vehicule->id > 0) {
                    $programmeIds = Programme::where('vehicule_id', $vehicule->id)
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
                'date' => $dateVoyage
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur getVehicleDetails:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Erreur serveur'], 500);
        }
    }
}