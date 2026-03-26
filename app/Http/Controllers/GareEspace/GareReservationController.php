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
            ->where(function($q) use ($date) {
                $q->whereDate('date_fin', '>=', $date)
                  ->orWhereNull('date_fin');
            })
            ->with(['gareArrivee'])
            ->get()
            ->groupBy('gare_arrivee_id');

        $query = Reservation::where(function($q) use ($compagnieId) {
                $q->where('compagnie_id', $compagnieId)
                  ->orWhereNull('compagnie_id');
            })
            ->where(function($q) use ($gare, $gareNom) {
                // 1. Réservations avec gare_depart_id direct (généralement hôtesses/caisses)
                $q->where('gare_depart_id', $gare->id)
                // 2. Ou via le programme lié (cas des utilisateurs/web)
                ->orWhereHas('programme', function($sub) use ($gare, $gareNom) {
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
            $query->where(function($q) use ($request) {
                $q->where('reference', 'LIKE', '%' . $request->reference . '%')
                  ->orWhere('payment_transaction_id', 'LIKE', '%' . $request->reference . '%');
            });
        }

        // Filtre par passager
        if ($request->filled('passager')) {
            $query->where(function($q) use ($request) {
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
        $globalQuery = Reservation::where(function($q) use ($compagnieId) {
                $q->where('compagnie_id', $compagnieId)
                  ->orWhereNull('compagnie_id');
            })
            ->where(function($q) use ($gare, $gareNom) {
                $q->where('gare_depart_id', $gare->id)
                ->orWhereHas('programme', function($sub) use ($gare, $gareNom) {
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
        
        $query = Reservation::where('programme_id', '=', $programmeId, 'and')
            ->whereDate('date_voyage', $date)
            ->with(['user', 'voyage', 'programme.gareDepart', 'programme.gareArrivee'])
            ->latest();

        // Optional filters
        if ($request->filled('reference')) {
            $query->where('reference', 'LIKE', '%' . $request->reference . '%');
        }
        if ($request->filled('passager')) {
            $query->where(function($q) use ($request) {
                $q->where('passager_nom', 'LIKE', '%' . $request->passager . '%')
                  ->orWhere('passager_prenom', 'LIKE', '%' . $request->passager . '%');
            });
        }
        if ($request->filled('statut')) {
             $query->where('statut', '=', $request->statut, 'and');
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
                    'heure' => $reservation->heure_depart,
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
}
