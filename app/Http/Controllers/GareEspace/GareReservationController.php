<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GareReservationController extends Controller
{
    /**
     * Liste des réservations liées à la gare
     */
    public function index(Request $request)
    {
        $gare = Auth::guard('gare')->user();
        $compagnieId = $gare->compagnie_id;
        $gareNom = $gare->nom_gare;
        $date = $request->get('date_voyage', Carbon::today()->toDateString());
        $tab = $request->get('tab', 'en-cours');

        // Fetch all active programs for this gare to show in the selection grid
        $availableProgrammes = \App\Models\Programme::where('gare_depart_id', $gare->id)
            ->where('statut', 'actif')
            ->whereDate('date_depart', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereDate('date_fin', '>=', $date)
                    ->orWhereNull('date_fin');
            })
            ->with(['gareArrivee'])
            ->get()
            ->groupBy('gare_arrivee_id');

        $query = Reservation::where(function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId)
                ->orWhereNull('compagnie_id');
        })
            ->where(function ($q) use ($gare, $gareNom) {
                // 1. Réservations avec gare_depart_id direct (généralement hôtesses/caisses)
                $q->where('gare_depart_id', $gare->id)
                    // 2. Ou via le programme lié (cas des utilisateurs/web)
                    ->orWhereHas('programme', function ($sub) use ($gare, $gareNom) {
                        $sub->where('gare_depart_id', $gare->id)
                            ->orWhere('point_depart', 'LIKE', '%' . $gareNom . '%');
                    });
            })
            ->with(['programme.gareDepart', 'programme.gareArrivee', 'user', 'voyage'])
            ->latest();

        // Specific Programme filter
        if ($request->filled('programme_id')) {
            $query->where('programme_id', $request->programme_id);
        }

        // Filtre par référence
        if ($request->filled('reference')) {
            $query->where(function ($q) use ($request) {
                $q->where('reference', 'LIKE', '%' . $request->reference . '%')
                    ->orWhere('payment_transaction_id', 'LIKE', '%' . $request->reference . '%');
            });
        }

        // Filtre par passager
        if ($request->filled('passager')) {
            $query->where(function ($q) use ($request) {
                $q->where('passager_nom', 'LIKE', '%' . $request->passager . '%')
                    ->orWhere('passager_prenom', 'LIKE', '%' . $request->passager . '%');
            });
        }

        // Filtre par date de voyage
        if ($tab !== 'details' || $request->filled('date_voyage')) {
            $query->whereDate('date_voyage', $date);
        }

        // Calculate stats
        $baseQuery = clone $query;

        // Filtre de recherche par statut explicite
        if ($request->filled('statut') && $request->statut !== 'all') {
            $query->where('statut', $request->statut);
        } else {
            if ($tab === 'en-cours') {
                $query->where('statut', 'confirmee');
            } elseif ($tab === 'terminees') {
                $query->where('statut', 'terminee');
            }
        }

        $reservations = $query->paginate(20)->withQueryString();

        // Calculate Global Stats for the Station (Unfiltered by date/program)
        $globalQuery = Reservation::where(function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId)
                ->orWhereNull('compagnie_id');
        })
            ->where(function ($q) use ($gare, $gareNom) {
                $q->where('gare_depart_id', $gare->id)
                    ->orWhereHas('programme', function ($sub) use ($gare, $gareNom) {
                        $sub->where('gare_depart_id', $gare->id)
                            ->orWhere('point_depart', 'LIKE', '%' . $gareNom . '%');
                    });
            });

        $stats = [
            'total' => (clone $globalQuery)->count(),
            'today' => (clone $globalQuery)->whereDate('date_voyage', Carbon::today())->count(),
            'pending' => (clone $globalQuery)->where('statut', 'en_attente')->whereDate('date_voyage', $date)->count(),
            'confirmed' => (clone $globalQuery)->where('statut', 'confirmee')->whereDate('date_voyage', $date)->count()
        ];

        // Indicate if we are in "Selection Mode" (no program selected)
        $selection_mode = !$request->filled('programme_id') && !$request->filled('reference') && !$request->filled('passager');

        return view('gare-espace.reservation.reservation', compact('reservations', 'stats', 'tab', 'availableProgrammes', 'date', 'selection_mode'));
    }

    /**
     * Voir les réservations pour un programme spécifique
     */
    public function programDetails(Request $request, $programmeId)
    {
        $gare = Auth::guard('gare')->user();
        $compagnieId = $gare->compagnie_id;
        $date = $request->get('date', Carbon::today()->toDateString());
        $tab = $request->get('tab', 'en-cours');

        $programme = \App\Models\Programme::with(['gareDepart', 'gareArrivee'])->findOrFail($programmeId);

        // Authorization check
        if ($programme->gare_depart_id !== $gare->id && strpos($programme->point_depart, $gare->nom_gare) === false) {
            abort(403);
        }

        $query = $programme->reservations()
            ->whereDate('date_voyage', $date)
            ->with(['user', 'voyage', 'programme.gareDepart', 'programme.gareArrivee'])
            ->latest();

        // Optional filters
        if ($request->filled('reference')) {
            $query->where(function ($q) use ($request) {
                $q->where('reference', 'LIKE', '%' . $request->reference . '%')
                    ->orWhere('payment_transaction_id', 'LIKE', '%' . $request->reference . '%');
            });
        }
        if ($request->filled('passager')) {
            $query->where(function ($q) use ($request) {
                $q->where('passager_nom', 'LIKE', '%' . $request->passager . '%')
                    ->orWhere('passager_prenom', 'LIKE', '%' . $request->passager . '%');
            });
        }
        if ($request->filled('statut') && $request->statut !== 'all') {
            $query->where('statut', $request->statut);
        } else {
            if ($tab === 'en-cours') {
                // For en-cours, we include confirmee and en_attente as they are active reservations
                $query->whereIn('statut', ['confirmee', 'en_attente']);
            } elseif ($tab === 'terminees') {
                $query->where('statut', 'terminee');
            }
        }

        $reservations = $query->paginate(50)->withQueryString();

        return view('gare-espace.reservation.program_details', compact('programme', 'reservations', 'date', 'gare', 'tab'));
    }

    /**
     * Voir les détails d'une réservation (AJAX)
     */
    public function show(Reservation $reservation)
    {
        $gare = Auth::guard('gare')->user();
        $compagnieId = $gare->compagnie_id;

        // Vérification d'autorisation : Doit appartenir à la compagnie
        // On vérifie soit le compagnie_id de la résa, soit celui du programme lié
        $belongsToCompagnie = ($reservation->compagnie_id == $compagnieId) ||
            ($reservation->programme && $reservation->programme->compagnie_id == $compagnieId) ||
            ($reservation->compagnie_id === null);

        if (!$belongsToCompagnie) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $reservation->load(['programme.gareDepart', 'programme.gareArrivee', 'user', 'voyage.vehicule', 'voyage.chauffeur', 'paiement']);

        return response()->json([
            'success' => true,
            'data' => [
                'reference' => $reservation->reference,
                'transaction_id' => $reservation->payment_transaction_id,
                'passager' => [
                    'nom' => $reservation->passager_nom . ' ' . $reservation->passager_prenom,
                    'telephone' => $reservation->passager_telephone,
                    'email' => $reservation->passager_email,
                    'urgence' => $reservation->passager_urgence . ' (' . $reservation->nom_passager_urgence . ')',
                    'photo' => $reservation->user && $reservation->user->photo_profile_path
                        ? asset('storage/' . $reservation->user->photo_profile_path)
                        : null,
                ],
                'trajet' => [
                    'depart' => $reservation->programme->point_depart,
                    'arrivee' => $reservation->programme->point_arrive,
                    'date' => $reservation->date_voyage->format('d/m/Y'),
                    'heure' => \Carbon\Carbon::parse($reservation->heure_depart ?? $reservation->programme->heure_depart)->format('H:i'),
                    'siege' => $reservation->seat_number,
                ],
                'paiement' => [
                    'montant' => number_format($reservation->montant, 0, ',', ' ') . ' FCFA',
                    'methode' => $reservation->paiement?->payment_method ?? 'N/A',
                    'statut' => $reservation->statut,
                ],
                'voyage' => $reservation->voyage ? [
                    'vehicule' => $reservation->voyage->vehicule->immatriculation . ' (' . $reservation->voyage->vehicule->marque . ')',
                    'chauffeur' => $reservation->voyage->chauffeur->prenom . ' ' . $reservation->voyage->chauffeur->name,
                ] : null
            ]
        ]);
    }

    /**
     * Afficher les réservations du mois avec sélection de date
     */
    public function byMonth(Request $request, $month)
    {
        $gare = Auth::guard('gare')->user();
        $tab = $request->query('tab', 'en-cours');

        // $month est au format YYYY-MM
        $query = Reservation::where(function ($q) use ($gare) {
            $q->where('gare_depart_id', $gare->id)
                ->orWhereHas('programme', function ($sub) use ($gare) {
                    $sub->where('gare_depart_id', $gare->id);
                });
        })
            ->where('date_voyage', 'LIKE', "{$month}%")
            ->with(['programme', 'user', 'programme.gareDepart', 'programme.gareArrivee']);

        // IMPORTANT: Récupérer toutes les dates AVANT d'appliquer le filtre de statut
        $allDates = (clone $query)->pluck('date_voyage')->unique()->values();

        // Filtre par statut
        if ($tab === 'terminees') {
            $query->where('statut', 'terminee');
        } else {
            $query->whereIn('statut', ['confirmee', 'en_attente']);
        }

        $selectedDate = $allDates->first() ?? ($month . '-01');

        $reservations = $query->whereDate('date_voyage', $selectedDate)
            ->orderBy('created_at', 'desc')
            ->paginate(10)->withQueryString();

        return view('gare-espace.reservations.by_date', [
            'reservations' => $reservations,
            'allDates' => $allDates,
            'date' => $selectedDate,
            'heure' => 'all',
            'tab' => $tab,
            'isFullMonth' => true,
            'monthLabel' => \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y')
        ]);
    }

    /**
     * Afficher les réservations d'une date spécifique
     */
    public function byDate(Request $request, $date)
    {
        $gare = Auth::guard('gare')->user();
        $heure = $request->query('heure');
        $tab = $request->query('tab', 'en-cours');

        $query = Reservation::where(function ($q) use ($gare) {
            $q->where('gare_depart_id', $gare->id)
                ->orWhereHas('programme', function ($sub) use ($gare) {
                    $sub->where('gare_depart_id', $gare->id);
                });
        })
            ->whereDate('date_voyage', $date)
            ->with(['programme', 'user', 'programme.gareDepart', 'programme.gareArrivee']);

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

        $reservations = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('gare-espace.reservations.by_date', compact('reservations', 'date', 'heure', 'tab'));
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
        ]);

        $gare = Auth::guard('gare')->user();
        $date = $request->date;
        $heure = $request->heure;
        $tab = $request->tab ?? 'en-cours';

        $query = Reservation::where(function ($q) use ($gare) {
            $q->where('gare_depart_id', $gare->id)
                ->orWhereHas('programme', function ($sub) use ($gare) {
                    $sub->where('gare_depart_id', $gare->id);
                });
        })
            ->whereDate('date_voyage', $date)
            ->with(['programme', 'user', 'programme.gareDepart', 'programme.gareArrivee']);

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

    /**
     * Récupérer les places occupées pour un programme et une date donnés (AJAX)
     */
    public function getOccupiedSeats(Request $request)
    {
        \Log::info('getOccupiedSeats called', [
            'programme_id' => $request->programme_id,
            'date_voyage' => $request->date_voyage,
            'user' => auth('gare')->check() ? auth('gare')->user()->id : 'not authenticated'
        ]);

        $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'date_voyage' => 'required|date',
        ]);

        $gare = Auth::guard('gare')->user();
        if (!$gare) {
            \Log::error('User not authenticated');
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $programme = \App\Models\Programme::findOrFail($request->programme_id);

        // Vérifier que le programme appartient à la gare
        if ($programme->gare_depart_id !== $gare->id) {
            \Log::error('Programme does not belong to gare', [
                'programme_gare' => $programme->gare_depart_id,
                'user_gare' => $gare->id
            ]);
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $occupiedSeats = Reservation::where('programme_id', $request->programme_id)
            ->whereDate('date_voyage', $request->date_voyage)
            ->whereIn('statut', ['confirmee', 'terminee'])
            ->pluck('seat_number')
            ->map(fn($seat) => (int)$seat)
            ->toArray();

        $vehicule = $programme->getVehiculeForDate($request->date_voyage);

        \Log::info('getOccupiedSeats response', [
            'vehicle_name' => $vehicule ? $vehicule->immatriculation . ' (' . $vehicule->marque . ')' : 'Véhicule non assigné',
            'occupied_count' => count($occupiedSeats)
        ]);

        return response()->json([
            'vehicle_name' => $vehicule ? $vehicule->immatriculation . ' (' . $vehicule->marque . ')' : 'Véhicule non assigné',
            'total_seats' => $vehicule ? $vehicule->nombre_place : 70,
            'occupied' => $occupiedSeats,
        ]);
    }

    /**
     * Récupérer les informations du véhicule pour un programme et une date donnés (AJAX)
     */
    public function getProgrammeVehicle(Request $request)
    {
        \Log::info('getProgrammeVehicle called', [
            'programme_id' => $request->programme_id,
            'date_voyage' => $request->date_voyage,
            'user' => auth('gare')->check() ? auth('gare')->user()->id : 'not authenticated'
        ]);

        $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'date_voyage' => 'required|date',
        ]);

        $gare = Auth::guard('gare')->user();
        if (!$gare) {
            \Log::error('User not authenticated for vehicle');
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $programme = \App\Models\Programme::findOrFail($request->programme_id);

        // Vérifier que le programme appartient à la gare
        if ($programme->gare_depart_id !== $gare->id) {
            \Log::error('Programme does not belong to gare for vehicle', [
                'programme_gare' => $programme->gare_depart_id,
                'user_gare' => $gare->id
            ]);
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $vehicule = $programme->getVehiculeForDate($request->date_voyage);

        if ($vehicule) {
            \Log::info('Vehicle found', ['vehicle' => $vehicule->immatriculation]);
            return response()->json([
                'id' => $vehicule->id,
                'immatriculation' => $vehicule->immatriculation,
                'marque' => $vehicule->marque,
                'nombre_place' => $vehicule->nombre_place,
                'type_range' => $vehicule->type_range,
            ]);
        }

        \Log::info('No vehicle found for programme');
        return response()->json(['error' => 'Véhicule non trouvé'], 404);
    }
}
