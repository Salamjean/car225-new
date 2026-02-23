<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Paiement;
use App\Models\Signalement;
use App\Models\SupportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistiqueApiController extends Controller
{
    /**
     * Get global statistics for the authenticated user.
     */
    public function index()
    {
        try {
            $userId = Auth::id();

            // 1. Statistiques des réservations
            $reservationStats = Reservation::where('user_id', $userId)
                ->select('statut', DB::raw('count(*) as total'))
                ->groupBy('statut')
                ->get()
                ->pluck('total', 'statut')
                ->toArray();

            $totalReservations = array_sum($reservationStats);
            $voyagesEffectues = $reservationStats['terminee'] ?? 0;
            $voyagesAnnules = $reservationStats['annulee'] ?? 0;
            $voyagesConfirmes = $reservationStats['confirmee'] ?? 0;
            $voyagesEnAttente = $reservationStats['en_attente'] ?? 0;

            // 2. Statistiques financières
            $totalDepense = Paiement::where('user_id', $userId)
                ->where('status', 'ACCEPTED')
                ->sum('amount');

            // 3. Statistiques des interactions (Signalements et Support)
            $totalSignalements = Signalement::where('user_id', $userId)->count();
            $totalSupportRequests = SupportRequest::where('user_id', $userId)->count();

            // 4. Dernier voyage effectué
            $dernierVoyage = Reservation::with('programme')
                ->where('user_id', $userId)
                ->where('statut', 'terminee')
                ->orderBy('date_voyage', 'desc')
                ->first();

            // 5. Statistiques par mois (6 derniers mois)
            $statsParMois = Reservation::where('user_id', $userId)
                ->where('statut', 'terminee')
                ->where('date_voyage', '>=', Carbon::now()->subMonths(6))
                ->select(
                    DB::raw("DATE_FORMAT(date_voyage, '%Y-%m') as mois"),
                    DB::raw('count(*) as total')
                )
                ->groupBy('mois')
                ->orderBy('mois', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'global' => [
                        'total_reservations' => $totalReservations,
                        'voyages_effectues' => $voyagesEffectues,
                        'voyages_annules' => $voyagesAnnules,
                        'voyages_confirmes' => $voyagesConfirmes,
                        'voyages_en_attente' => $voyagesEnAttente,
                    ],
                    'interactions' => [
                        'total_signalements' => $totalSignalements,
                        'total_support_requests' => $totalSupportRequests,
                    ],
                    'finance' => [
                        'total_depense' => (float) $totalDepense,
                        'devise' => 'FCFA',
                    ],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed trip statistics.
     */
    public function tripStats()
    {
        try {
            $userId = Auth::id();

            // Statistiques par ville de départ
            $villesDepart = Reservation::where('user_id', $userId)
                ->join('programmes', 'reservations.programme_id', '=', 'programmes.id')
                ->select('programmes.point_depart', DB::raw('count(*) as count'))
                ->groupBy('programmes.point_depart')
                ->orderBy('count', 'desc')
                ->get();

            // Statistiques par ville d'arrivée
            $villesArrivee = Reservation::where('user_id', $userId)
                ->join('programmes', 'reservations.programme_id', '=', 'programmes.id')
                ->select('programmes.point_arrive', DB::raw('count(*) as count'))
                ->groupBy('programmes.point_arrive')
                ->orderBy('count', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'villes_depart_frequentes' => $villesDepart,
                    'villes_arrivee_frequentes' => $villesArrivee,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}
