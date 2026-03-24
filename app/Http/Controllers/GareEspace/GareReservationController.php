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

        // Filtre par référence
        if ($request->filled('reference')) {
            $query->where('reference', 'LIKE', '%' . $request->reference . '%')
                  ->orWhere('payment_transaction_id', 'LIKE', '%' . $request->reference . '%');
        }

        // Filtre par passager
        if ($request->filled('passager')) {
            $query->where(function($q) use ($request) {
                $q->where('passager_nom', 'LIKE', '%' . $request->passager . '%')
                  ->orWhere('passager_prenom', 'LIKE', '%' . $request->passager . '%');
            });
        }

        // Filtre par date de voyage
        if ($request->filled('date_voyage')) {
            $query->whereDate('date_voyage', $request->date_voyage);
        }

        $tab = $request->get('tab', 'en-cours');

        // Calculate stats on a baseline query (before filtering by status)
        $baseQuery = clone $query;

        // Filtre de recherche par statut explicite (si présent via formulaire)
        if ($request->filled('statut') && $request->statut !== 'all') {
            $query->where('statut', $request->statut);
        } else {
            // Logique stricte des onglets
            if ($tab === 'en-cours') {
                $query->where('statut', 'confirmee');
            } elseif ($tab === 'terminees') {
                $query->where('statut', 'terminee');
            }
            // Onglet 'details' : Pas de filtre de statut (on affiche tout)
        }

        $reservations = $query->paginate(10)->withQueryString();

        // Stats pour les badges basées sur la requête non filtrée par statut
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'today' => (clone $baseQuery)->whereDate('date_voyage', Carbon::today())->count(),
            'pending' => (clone $baseQuery)->where('statut', 'en_attente')->count(),
            'confirmed' => (clone $baseQuery)->where('statut', 'confirmee')->count()
        ];

        return view('gare-espace.reservation.reservation', compact('reservations', 'stats', 'tab'));
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
