<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Compagnie;
use App\Models\Programme;
use App\Models\Reservation;
use App\Models\Signalement;
use App\Models\User;
use App\Models\Vehicule;
use App\Models\Agent;
use App\Models\Personnel;
use App\Models\SapeurPompier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Controller
{
    public function dashboard()
    {
        // 1. Statistiques globales
        $totalUsers = User::count();
        $totalCompagnies = Compagnie::count();
        $totalVehicules = Vehicule::count();
        $totalProgrammes = Programme::count();
        $totalReservations = Reservation::count();
        $totalAgents = Agent::count();
        $totalPersonnel = Personnel::count();
        $totalSignalements = Signalement::count();
        $totalSapeurPompiers = SapeurPompier::count();
        
        // 2. Revenus
        $totalRevenue = Reservation::whereNotIn('statut', ['annulee'])->sum('montant');
        
        // Revenus du mois en cours
        $revenueThisMonth = Reservation::whereNotIn('statut', ['annulee'])
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('montant');
        
        // Revenus du mois précédent
        $revenueLastMonth = Reservation::whereNotIn('statut', ['annulee'])
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('montant');
        
        // Variation
        $revenueVariation = $revenueLastMonth > 0 
            ? (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100 
            : 100;

        // 2b. Revenus Tickets (Rechargement Compagnie)
        $totalTicketRevenue = \App\Models\HistoriqueTicket::where('montant', '>', 0)->sum('montant');
        $ticketRevenueMonth = \App\Models\HistoriqueTicket::where('montant', '>', 0)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('montant');
        
        // 3. Statistiques réservations
        $reservationsConfirmees = Reservation::where('statut', 'confirmee')->count();
        $reservationsEnAttente = Reservation::where('statut', 'en_attente')->count();
        $reservationsAnnulees = Reservation::where('statut', 'annulee')->count();
        $reservationsTerminees = Reservation::where('statut', 'terminee')->count();
        
        // 4. Statistiques signalements
        $signalementsNouveaux = Signalement::where('statut', 'nouveau')->count();
        $signalementsTraites = Signalement::where('statut', 'traite')->count();
        
        // 5. Activités récentes (dernières 10)
        $recentReservations = Reservation::with(['user', 'programme.compagnie'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        
        $recentCompagnies = Compagnie::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        $recentSignalements = Signalement::with(['user', 'programme.compagnie'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // 6. Top 5 Compagnies par revenus
        $topCompagnies = DB::table('reservations')
            ->join('programmes', 'reservations.programme_id', '=', 'programmes.id')
            ->join('compagnies', 'programmes.compagnie_id', '=', 'compagnies.id')
            ->where('reservations.statut', 'confirmee')
            ->select('compagnies.id', 'compagnies.name', DB::raw('SUM(reservations.montant) as total_revenue'), DB::raw('COUNT(reservations.id) as total_reservations'))
            ->groupBy('compagnies.id', 'compagnies.name')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();
        
        // 7. Graphiques - Réservations des 7 derniers jours
        $days = [];
        $reservationsPerDay = [];
        $revenuePerDay = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('d M');
            
            $reservationsPerDay[] = Reservation::whereDate('created_at', $date)->count();
            $revenuePerDay[] = (float) Reservation::whereNotIn('statut', ['annulee'])
                ->whereDate('created_at', $date)
                ->sum('montant');
        }
        
        // 8. Graphique - Répartition par compagnie (camembert)
        $compagnieStats = DB::table('reservations')
            ->join('programmes', 'reservations.programme_id', '=', 'programmes.id')
            ->join('compagnies', 'programmes.compagnie_id', '=', 'compagnies.id')
            ->where('reservations.statut', 'confirmee')
            ->select('compagnies.name', DB::raw('COUNT(reservations.id) as count'))
            ->groupBy('compagnies.name')
            ->orderByDesc('count')
            ->take(6)
            ->get();
        
        $compagnieLabels = $compagnieStats->pluck('name')->toArray();
        $compagnieCounts = $compagnieStats->pluck('count')->toArray();
        
        // 9. Statistiques véhicules
        $vehiculesActifs = Vehicule::where('is_active', true)->count();
        $vehiculesInactifs = Vehicule::where('is_active', false)->count();
        
        // 10. Programmes actifs aujourd'hui
        $programmesAujourdhui = Programme::whereDate('date_depart', Carbon::today())->count();
        $portefeuilleAdmin = Auth::guard('admin')->user()->portefeuille ?? 0;

        return view('admin.dashboard', compact(
            'portefeuilleAdmin',
            'totalUsers',
            'totalCompagnies',
            'totalVehicules',
            'totalProgrammes',
            'totalReservations',
            'totalAgents',
            'totalPersonnel',
            'totalSignalements',
            'totalSapeurPompiers',
            'totalRevenue',
            'revenueThisMonth',
            'revenueLastMonth',
            'revenueVariation',
            'reservationsConfirmees',
            'reservationsEnAttente',
            'reservationsAnnulees',
            'reservationsTerminees',
            'signalementsNouveaux',
            'signalementsTraites',
            'recentReservations',
            'recentCompagnies',
            'recentSignalements',
            'topCompagnies',
            'days',
            'reservationsPerDay',
            'revenuePerDay',
            'compagnieLabels',
            'compagnieCounts',
            'vehiculesActifs',
            'vehiculesInactifs',
            'programmesAujourdhui',
            'totalTicketRevenue',
            'ticketRevenueMonth'
        ));
    }

    public function revenusTickets(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\HistoriqueTicket::with('compagnie')
            ->where('montant', '>', 0);

        // Filtre par compagnie
        if ($request->filled('compagnie_id')) {
            $query->where('compagnie_id', $request->compagnie_id);
        }

        // Filtre par date
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        // Stats globales pour la vue de détail
        $totalTicketRevenue = (clone $query)->sum('montant');
        // 1. Revenus par compagnie (Breakdown) - On applique les mêmes filtres que la query principale
        $companyStats = DB::table('historique_tickets')
            ->join('compagnies', 'historique_tickets.compagnie_id', '=', 'compagnies.id')
            ->where('historique_tickets.montant', '>', 0);
            
        if ($request->filled('compagnie_id')) {
            $companyStats->where('historique_tickets.compagnie_id', $request->compagnie_id);
        }
        if ($request->filled('date_debut')) {
            $companyStats->whereDate('historique_tickets.created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $companyStats->whereDate('historique_tickets.created_at', '<=', $request->date_fin);
        }

        $companyStats = $companyStats->select('compagnies.name', DB::raw('SUM(historique_tickets.montant) as total_revenue'), DB::raw('COUNT(historique_tickets.id) as count'))
            ->groupBy('compagnies.name')
            ->orderByDesc('total_revenue')
            ->get();

        $totalRecharges = (clone $query)->count();
        $averageRecharge = $totalRecharges > 0 ? $totalTicketRevenue / $totalRecharges : 0;

        // 2. Portefeuille Admin
        $portefeuilleAdmin = Auth::guard('admin')->user()->portefeuille ?? 0;

        $recharges = $query->orderBy('created_at', 'desc')->paginate(20);
        $compagnies = Compagnie::orderBy('name', 'asc')->get();

        return view('admin.revenus.tickets', compact(
            'recharges',
            'compagnies',
            'totalTicketRevenue',
            'totalRecharges',
            'averageRecharge',
            'companyStats',
            'portefeuilleAdmin'
        ));
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}

