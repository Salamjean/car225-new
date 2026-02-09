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
        return view('home.home', compact('compagnies', 'usersCount'));
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

        $query = Programme::with(['compagnie', 'vehicule', 'itineraire'])
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
        $programmes = Programme::with(['compagnie', 'vehicule', 'itineraire'])
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
        $programme->load(['compagnie', 'vehicule', 'itineraire', 'chauffeur', 'convoyeur']);
        return view('home.programmes.show', compact('programme'));
    }


    /**
     * Obtenir les détails d'un véhicule avec les places réservées
     */
     public function getVehicleDetails($id, Request $request)
    {
        try {
            $vehicule = Vehicule::find($id);

            if (!$vehicule) {
                return response()->json(['success' => false, 'error' => 'Véhicule non trouvé'], 404);
            }

            $reservedSeats = [];
            $dateVoyage = $request->get('date') ? date('Y-m-d', strtotime($request->get('date'))) : date('Y-m-d');
            
            // On récupère l'ID du programme spécifique s'il est envoyé (pour être plus précis)
            $programmeId = $request->get('programme_id');

            if ($dateVoyage) {
                $query = Reservation::query();

                // 1. Filtrer par date
                $query->where('date_voyage', $dateVoyage);

                // 2. Filtrer par statut (AJOUT DE 'terminee')
                // Les statuts qui bloquent une place sont : confirmee, en_attente, terminee
                $query->whereIn('statut', ['confirmee', 'en_attente', 'terminee']);

                // 3. Filtrage intelligent du programme
                if ($programmeId && $programmeId != 'null') {
                    // Si on sait exactement quel programme on regarde, on ne prend que ses réservations
                    $query->where('programme_id', $programmeId);
                } else {
                    // Sinon (fallback), on cherche toutes les réservations liées à ce véhicule pour ce jour-là
                    // Attention: cela peut mélanger les places si le bus fait 2 voyages/jour sans programme_id
                    $programmeIds = Programme::where('vehicule_id', $vehicule->id)
                        ->whereRaw('DATE(date_depart) <= ?', [$dateVoyage])
                        ->whereRaw('DATE(date_fin) >= ?', [$dateVoyage])
                        ->where('statut', 'actif')
                        ->pluck('id');
                    
                    $query->whereIn('programme_id', $programmeIds);
                }

                $reservedSeats = $query->pluck('seat_number')->toArray();
                
                // S'assurer que ce sont des entiers uniques
                $reservedSeats = array_values(array_unique(array_map('intval', $reservedSeats)));
            }

            $response = [
                'success' => true,
                'vehicule' => $vehicule,
                'reservedSeats' => $reservedSeats,
                'date' => $dateVoyage
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Erreur getVehicleDetails:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Erreur serveur'], 500);
        }
    }
}