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

        // Formatez la date au même format que dans la base de données
        $formattedDate = date('Y-m-d', strtotime($date_depart_recherche));

        Log::info('====== DEBUT RECHERCHE PROGRAMME ======');
        Log::info('Paramètres de recherche:', [
            'point_depart' => $point_depart,
            'point_arrive' => $point_arrive,
            'date_depart_recherche' => $date_depart_recherche,
            'formattedDate' => $formattedDate,
        ]);

        // Recherche simplifiée : Programmes correspondant aux critères
        $query = Programme::with(['compagnie', 'vehicule', 'itineraire'])
            ->where('point_depart', 'like', "%{$point_depart}%")
            ->where('point_arrive', 'like', "%{$point_arrive}%")
            ->whereDate('date_depart', $formattedDate)
            ->where('statut', 'actif'); // Seulement les programmes actifs

        if ($request->has('is_aller_retour') && $request->is_aller_retour != '') {
            $query->where('is_aller_retour', $request->is_aller_retour);
        }

        $query->orderBy('heure_depart', 'asc');

        $programmes = $query->paginate(10);
        $totalResults = $programmes->total();

        Log::info('Résultats finaux:', [
            'total_results' => $totalResults,
            'has_pages' => $programmes->hasPages(),
        ]);

        Log::info('====== FIN RECHERCHE PROGRAMME ======');

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
        $programmes = Programme::with(['compagnie', 'vehicule', 'itineraire'])
            ->where('date_depart', '>=', now()->format('Y-m-d'))
            ->where('statut', 'actif')
            ->orderBy('date_depart', 'asc')
            ->orderBy('heure_depart', 'asc')
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
            Log::info('=== DEBUT getVehicleDetails ===');
            Log::info('Paramètres:', [
                'vehicle_id' => $id,
                'date' => $request->get('date'),
                'url' => $request->fullUrl()
            ]);

            $vehicule = Vehicule::find($id);

            if (!$vehicule) {
                Log::warning('Véhicule non trouvé', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'error' => 'Véhicule non trouvé'
                ], 404);
            }

            // Récupérer les places réservées si une date est fournie
            $reservedSeats = [];
            $dateVoyage = $request->get('date');

            if ($dateVoyage) {
                $formattedDate = date('Y-m-d', strtotime($dateVoyage));

                // Trouver tous les programmes associés à ce véhicule pour cette date
                $programmes = Programme::where('vehicule_id', $vehicule->id)
                    ->whereDate('date_depart', $formattedDate)
                    ->where('statut', 'actif')
                    ->get();
                
                // Pour chaque programme, récupérer les places réservées
                foreach ($programmes as $programme) {
                    $programReservations = Reservation::where('programme_id', $programme->id)
                        ->whereIn('statut', ['confirmee', 'en_attente']) // Inclure en_attente aussi pour être sûr
                        ->pluck('seat_number')
                        ->toArray();

                    $reservedSeats = array_merge($reservedSeats, $programReservations);
                }

                $reservedSeats = array_unique($reservedSeats);
                Log::info('Places réservées totales:', [
                    'count' => count($reservedSeats),
                    'places' => $reservedSeats
                ]);
            }

            $response = [
                'success' => true,
                'vehicule' => $vehicule,
                'reservedSeats' => array_values($reservedSeats), // Assurer que c'est un tableau indexé
                'date' => $dateVoyage
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Erreur getVehicleDetails:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'vehicleId' => $id
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du chargement des données du véhicule: ' . $e->getMessage()
            ], 500);
        }
    }
}
