<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $compagnie = Auth::guard('compagnie')->user();

        // Récupérer les réservations liées aux programmes de cette compagnie
        $query = Reservation::whereHas('programme', function ($q) use ($compagnie) {
            $q->where('compagnie_id', $compagnie->id);
        })->with(['programme', 'user', 'programme.itineraire']);

        // Séparer "En cours" et "Terminées"
        // En cours: 'en_attente', 'confirmee' ET date_voyage >= aujourd'hui
        // Terminées: 'terminee', 'annulee', OU date_voyage passée

        $reservationsEnCours = (clone $query)
            ->where('statut', 'confirmee')
            ->whereDate('date_voyage', '>=', now())
            ->orderBy('date_voyage', 'asc')
            ->paginate(10, ['*'], 'page_cours');

        // Terminées = statut terminee OU statut annulee OU date passée (peu importe le statut)
        $reservationsTerminees = (clone $query)
            ->where(function ($q) {
                $q->whereIn('statut', ['terminee', 'annulee'])
                    ->orWhereDate('date_voyage', '<', now());
            })
            ->orderBy('date_voyage', 'desc')
            ->paginate(10, ['*'], 'page_terminees');

        return view('compagnie.reservations.index', compact('reservationsEnCours', 'reservationsTerminees'));
    }
    /**
     * Afficher les détails et statistiques des réservations
     */
    public function details(Request $request)
    {
        $compagnie = Auth::guard('compagnie')->user();
        
        // Requête de base pour les réservations de la compagnie
        $query = Reservation::query()
            ->whereHas('programme', function($q) use ($compagnie) {
                $q->where('compagnie_id', $compagnie->id);
            })
            ->with(['programme', 'programme.itineraire', 'user', 'paiement', 'programme.vehicule']);

        // --- FILTRES ---
        
        // 1. Recherche par mot-clé (Nom passager, référence, téléphone)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'LIKE', "%{$search}%")
                  ->orWhere('passager_nom', 'LIKE', "%{$search}%")
                  ->orWhere('passager_prenom', 'LIKE', "%{$search}%")
                  ->orWhere('passager_telephone', 'LIKE', "%{$search}%");
            });
        }

        // 2. Filtre par Date de voyage (Période)
        if ($request->filled('date_debut')) {
            $query->whereDate('date_voyage', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_voyage', '<=', $request->date_fin);
        }

        // 3. Filtre par Statut
        if ($request->filled('statut') && $request->statut !== 'all') {
            $query->where('statut', $request->statut);
        }

        // 4. Filtre par Programme (Trajet)
        if ($request->filled('programme_id') && $request->programme_id !== 'all') {
            $query->where('programme_id', $request->programme_id);
        }

        // 5. Filtre par Type de vente (En ligne / Caisse)
        if ($request->filled('type_vente') && $request->type_vente !== 'all') {
            if ($request->type_vente === 'ligne') {
                $query->whereNull('caisse_id');
            } elseif ($request->type_vente === 'caisse') {
                $query->whereNotNull('caisse_id');
            }
        }

        // Récupérer les résultats pour la table (paginés)
        $reservations = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // --- STATISTIQUES ---
        
        // 1. Stock global de tickets (Indépendant des filtres de recherche)
        // On recalcule le stock global uniquement si aucun filtre restrictif sur le stock n'est appliqué (mais ici stock = compte compagnie)
        $stockTickets = $compagnie->tickets; 

        // 2. Tickets consommés et Revenu (Sur la sélection filtrée)
        // Correction : On ne prend en compte QUE les réservations confirmées ou terminées
        // Les réservations annulées ou en attente ne doivent pas compter dans les ventes "écoulées" ou le CA
        
        $statsQuery = clone $query;
        // Enlever la pagination et l'ordre pour les stats
        $statsQuery->getQuery()->orders = null; // Reset orders for performance
        $statsQuery->getQuery()->limit = null;
        $statsQuery->getQuery()->offset = null;
        
        $ticketsConsommes = (clone $statsQuery)->whereIn('statut', ['confirmee', 'terminee'])->count();
        
        $revenuTotal = (clone $statsQuery)->whereIn('statut', ['confirmee', 'terminee'])->sum('montant');


        // Liste des programmes pour le filtre
        $programmes = \App\Models\Programme::where('compagnie_id', $compagnie->id)
            ->where('statut', 'actif') // Ou tous ? Mieux vaut tous pour l'historique
            ->select('id', 'point_depart', 'point_arrive', 'heure_depart')
            ->get();

        return view('compagnie.reservations.details', compact(
            'reservations', 
            'stockTickets', 
            'ticketsConsommes', 
            'revenuTotal',
            'programmes'
        ));
    }

    /**
     * Récupérer les places occupées pour un programme et une date donnés (AJAX)
     */
    public function getOccupiedSeats(Request $request)
    {
        $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'date_voyage' => 'required|date',
        ]);

        $programme = \App\Models\Programme::with('vehicule')->findOrFail($request->programme_id);
        
        // Vérifier que le programme appartient à la compagnie connectée
        if ($programme->compagnie_id !== Auth::guard('compagnie')->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $occupiedSeats = Reservation::where('programme_id', $request->programme_id)
            ->whereDate('date_voyage', $request->date_voyage)
            ->whereIn('statut', ['confirmee', 'terminee']) // On compte les places payées
            ->pluck('seat_number')
            ->map(fn($seat) => (int)$seat) // S'assurer que ce sont des entiers
            ->toArray();

        return response()->json([
            'vehicle_name' => $programme->vehicule ? $programme->vehicule->immatriculation . ' (' . $programme->vehicule->marque . ')' : 'Véhicule non assigné',
            'total_seats' => $programme->vehicule ? $programme->vehicule->nombre_place : 70, // Par défaut 70 si pas de véhicule
            'occupied' => $occupiedSeats,
        ]);
    }
}
