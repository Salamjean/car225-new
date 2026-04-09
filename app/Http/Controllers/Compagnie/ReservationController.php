<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Programme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $compagnie = Auth::guard('compagnie')->user();
        $tab = $request->get('tab', 'en-cours');
        $date = $request->get('date_voyage', Carbon::today()->toDateString());

        $query = Reservation::whereHas('programme', function ($q) use ($compagnie) {
            $q->where('compagnie_id', $compagnie->id);
        })->with(['programme']);

        // Filtre de date (comme côté gare, hors vue détails)
        if ($tab !== 'details' || $request->filled('date_voyage')) {
            $query->whereDate('date_voyage', $date);
        }

        // Calcul des stats avant filtre de statut de sous-onglet
        $baseQuery = clone $query;

        if ($request->filled('statut') && $request->statut !== 'all') {
            $query->where('statut', $request->statut);
        } elseif ($tab === 'terminees') {
            $query->where('statut', 'terminee')
                ->orderBy('date_voyage', 'desc');
        } else { // en-cours
            $query->whereIn('statut', ['confirmee', 'en_attente'])
                ->orderBy('date_voyage', 'asc');
        }

        $reservationsEnCours = $query->paginate(20)->withQueryString();

        // Programmes actifs de la compagnie pour la date (grille de sélection "Réservés")
        $availableProgrammes = Programme::where('compagnie_id', $compagnie->id)
            ->where('statut', 'actif')
            ->whereDate('date_depart', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereDate('date_fin', '>=', $date)
                    ->orWhereNull('date_fin');
            })
            ->with(['gareArrivee'])
            ->orderBy('heure_depart')
            ->get()
            ->groupBy('gare_arrivee_id');

        // Stats globales compagnie (cohérentes avec l'espace gare)
        $globalQuery = Reservation::whereHas('programme', function ($q) use ($compagnie) {
            $q->where('compagnie_id', $compagnie->id);
        });

        $stats = [
            'total' => (clone $globalQuery)->count(),
            'today' => (clone $globalQuery)->whereDate('date_voyage', Carbon::today())->count(),
            'pending' => (clone $baseQuery)->where('statut', 'en_attente')->count(),
            'confirmed' => (clone $baseQuery)->where('statut', 'confirmee')->count(),
        ];

        return view('compagnie.reservations.index', compact(
            'reservationsEnCours',
            'tab',
            'availableProgrammes',
            'date',
            'stats'
        ));
    }

    public function byDate(Request $request, $date)
    {
        $compagnie = Auth::guard('compagnie')->user();
        $heure = $request->query('heure');
        $tab = $request->query('tab', 'en-cours');
        $programmeId = $request->query('programme_id');

        $query = Reservation::whereHas('programme', function ($q) use ($compagnie) {
            $q->where('compagnie_id', $compagnie->id);
        })
            ->whereDate('date_voyage', $date)
            ->with(['programme', 'user', 'programme.itineraire']);

        if ($tab === 'terminees') {
            $query->where('statut', 'terminee');
        } else {
            $query->whereIn('statut', ['confirmee', 'en_attente']);
        }

        if ($heure && $heure !== 'all') {
            $query->whereHas('programme', function ($q) use ($heure) {
                $q->where('heure_depart', $heure);
            });
        }

        if ($programmeId && $programmeId !== 'all') {
            $query->where('programme_id', $programmeId);
        }

        $reservations = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('compagnie.reservations.by_date', compact('reservations', 'date', 'heure', 'tab', 'programmeId'));
    }

    public function byMonth(Request $request, $month)
    {
        $compagnie = Auth::guard('compagnie')->user();
        $tab = $request->query('tab', 'en-cours');

        // $month est au format YYYY-MM
        $query = Reservation::whereHas('programme', function ($q) use ($compagnie) {
            $q->where('compagnie_id', $compagnie->id);
        })
            ->where('date_voyage', 'LIKE', "{$month}%")
            ->with(['programme', 'user', 'programme.itineraire']);

        // IMPORTANT: Récupérer toutes les dates AVANT d'appliquer le filtre de statut
        // Cela permet au calendrier d'afficher toutes les dates avec réservations
        $allDates = (clone $query)->pluck('date_voyage')->unique()->values();

        // Maintenant appliquer le filtre de statut pour l'affichage
        if ($tab === 'terminees') {
            $query->where('statut', 'terminee');
        } else {
            $query->whereIn('statut', ['confirmee', 'en_attente']);
        }

        $selectedDate = $allDates->first() ?? ($month . '-01');

        $reservations = $query->whereDate('date_voyage', $selectedDate)
            ->orderBy('created_at', 'desc')
            ->paginate(10)->withQueryString();

        return view('compagnie.reservations.by_date', [
            'reservations' => $reservations,
            'allDates' => $allDates,
            'date' => $selectedDate,
            'heure' => 'all',
            'tab' => $tab,
            'isFullMonth' => true,
            'monthLabel' => \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y')
        ]);
    }

    public function show(Reservation $reservation)
    {
        $compagnie = Auth::guard('compagnie')->user();
        $reservation->load(['programme', 'programme.gareDepart', 'programme.gareArrivee', 'programme.voyages', 'user', 'paiement', 'voyage']);

        if (!$reservation->programme || $reservation->programme->compagnie_id !== $compagnie->id) {
            return response()->json(['success' => false, 'message' => 'Réservation introuvable.'], 404);
        }

        $mission = $reservation->mission;
        $voyageInfo = null;

        if ($mission) {
            $voyageInfo = [
                'vehicule' => $mission->vehicule ? ($mission->vehicule->immatriculation . ' - ' . $mission->vehicule->marque) : 'N/A',
                'chauffeur' => $mission->chauffeur ? ($mission->chauffeur->nom ?? $mission->chauffeur->prenom ?? 'N/A') : 'N/A',
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'reference' => $reservation->reference,
                'passager' => [
                    'nom' => trim($reservation->passager_nom . ' ' . $reservation->passager_prenom),
                    'telephone' => $reservation->passager_telephone,
                    'urgence' => trim($reservation->passager_urgence . ' ' . $reservation->nom_passager_urgence),
                    'photo' => $reservation->user ? $reservation->user->photo : null,
                ],
                'trajet' => [
                    'depart' => optional($reservation->programme)->point_depart,
                    'arrivee' => optional($reservation->programme)->point_arrive,
                    'date' => $reservation->date_voyage ? $reservation->date_voyage->format('d/m/Y') : null,
                    'heure' => $reservation->heure_depart ?? optional($reservation->programme)->heure_depart,
                    'siege' => $reservation->seat_number,
                ],
                'paiement' => [
                    'montant' => $reservation->montant,
                    'methode' => optional($reservation->paiement)->methode ?? 'N/A',
                ],
                'voyage' => $voyageInfo,
            ],
        ]);
    }

    /**
     * Afficher les détails et statistiques des réservations
     */
    public function details(Request $request)
    {
        $compagnie = Auth::guard('compagnie')->user();

        // Requête de base pour les réservations de la compagnie
        $query = Reservation::query()
            ->whereHas('programme', function ($q) use ($compagnie) {
                $q->where('compagnie_id', $compagnie->id);
            })
            ->with(['programme', 'programme.itineraire', 'user', 'paiement']);

        // --- FILTRES ---

        // 1. Recherche par mot-clé (Nom passager, référence, téléphone)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
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

        $programme = \App\Models\Programme::findOrFail($request->programme_id);

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

        $vehicule = $programme->getVehiculeForDate($request->date_voyage);

        return response()->json([
            'vehicle_name' => $vehicule ? $vehicule->immatriculation . ' (' . $vehicule->marque . ')' : 'Véhicule non assigné',
            'total_seats' => $vehicule ? $vehicule->nombre_place : 70, // Par défaut 70 si pas de véhicule
            'occupied' => $occupiedSeats,
        ]);
    }

    /**
     * Récupérer les informations du véhicule pour un programme et une date donnés (AJAX)
     */
    public function getProgrammeVehicle(Request $request)
    {
        $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'date_voyage' => 'required|date',
        ]);

        $programme = \App\Models\Programme::findOrFail($request->programme_id);

        // Vérifier que le programme appartient à la compagnie connectée
        if ($programme->compagnie_id !== Auth::guard('compagnie')->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $vehicule = $programme->getVehiculeForDate($request->date_voyage);

        if ($vehicule) {
            return response()->json([
                'id' => $vehicule->id,
                'immatriculation' => $vehicule->immatriculation,
                'marque' => $vehicule->marque,
                'nombre_place' => $vehicule->nombre_place,
                'type_range' => $vehicule->type_range,
            ]);
        }

        return response()->json(['error' => 'Véhicule non trouvé'], 404);
    }

    /**
     * Récupérer les réservations pour une date donnée (AJAX)
     */
    public function getReservationsByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'tab' => 'nullable|string|in:en-cours,terminees',
            'heure' => 'nullable|string',
            'programme_id' => 'nullable|integer|exists:programmes,id',
        ]);

        $compagnie = Auth::guard('compagnie')->user();
        $date = $request->date;
        $heure = $request->heure;
        $tab = $request->tab ?? 'en-cours';
        $programmeId = $request->programme_id;

        $query = Reservation::whereHas('programme', function ($q) use ($compagnie) {
            $q->where('compagnie_id', $compagnie->id);
        })
            ->whereDate('date_voyage', $date)
            ->with(['programme', 'user', 'programme.itineraire']);

        if ($tab === 'terminees') {
            $query->where('statut', 'terminee');
        } else {
            $query->whereIn('statut', ['confirmee', 'en_attente']);
        }

        if ($heure && $heure !== 'all') {
            $query->whereHas('programme', function ($q) use ($heure) {
                $q->where('heure_depart', $heure);
            });
        }

        if ($programmeId && $programmeId !== 'all') {
            $query->where('programme_id', $programmeId);
        }

        $reservations = $query->orderBy('created_at', 'desc')->get();

        // Calculer les statistiques
        $totalReservations = $reservations->count();
        $totalRevenue = $reservations->sum('montant');

        // Grouper par programme pour la disposition des véhicules
        $reservationsByProgramme = $reservations->groupBy('programme_id');

        return response()->json([
            'reservations' => $reservations->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'reference' => $reservation->reference,
                    'passager_nom' => $reservation->passager_nom,
                    'passager_prenom' => $reservation->passager_prenom,
                    'passager_telephone' => $reservation->passager_telephone,
                    'montant' => $reservation->montant,
                    'statut' => $reservation->statut,
                    'seat_number' => $reservation->seat_number,
                    'date_voyage' => $reservation->date_voyage,
                    'programme_id' => $reservation->programme_id,
                    'programme' => $reservation->programme ? [
                        'point_depart' => $reservation->programme->point_depart,
                        'point_arrive' => $reservation->programme->point_arrive,
                        'heure_depart' => $reservation->programme->heure_depart,
                    ] : null,
                ];
            }),
            'stats' => [
                'count' => $totalReservations,
                'revenue' => $totalRevenue,
            ],
            'reservationsByProgramme' => $reservationsByProgramme->map(function ($reservations, $programmeId) {
                return [
                    'programme_id' => $programmeId,
                    'count' => $reservations->count(),
                    'occupied_seats' => $reservations->pluck('seat_number')->filter()->toArray(),
                ];
            })->values(),
        ]);
    }
}
