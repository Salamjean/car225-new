<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index()
    {
        $agent = Auth::guard('agent')->user();
        
        // Récupérer les réservations de la compagnie de l'agent
        $reservations = Reservation::with(['programme', 'user'])
            ->whereHas('programme', function($query) use ($agent) {
                $query->where('compagnie_id', $agent->compagnie_id);
            })
            ->orderBy('date_voyage', 'desc')
            ->get();

        $enCours = $reservations->where('statut', 'confirmee');
        $terminees = $reservations->where('statut', 'terminee');

        return view('agent.reservations.reservation', compact('enCours', 'terminees'));
    }

    /**
     * Rechercher une réservation par référence (pour afficher les infos avant confirmation)
     * Appelé en AJAX
     */
    public function search(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
        ]);

        $reservation = Reservation::with(['programme', 'user'])
            ->where('reference', $request->reference)
            ->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Réservation non trouvée avec cette référence.'
            ], 404);
        }

        // Vérifier si la réservation appartient à la compagnie de l'agent
        $agent = Auth::guard('agent')->user();
        if ($reservation->programme->compagnie_id !== $agent->compagnie_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cette réservation n\'appartient pas à votre compagnie.'
            ], 403);
        }

        if ($reservation->statut === 'terminee') {
            return response()->json([
                'success' => false,
                'message' => 'Cette réservation a déjà été scannée.',
                'already_scanned' => true,
                'scanned_at' => $reservation->embarquement_scanned_at?->format('d/m/Y H:i'),
            ], 400);
        }

        if ($reservation->statut !== 'confirmee') {
            return response()->json([
                'success' => false,
                'message' => 'Statut de réservation invalide: ' . $reservation->statut
            ], 400);
        }

        // Retourner les infos du passager pour confirmation
        return response()->json([
            'success' => true,
            'reservation' => [
                'id' => $reservation->id,
                'reference' => $reservation->reference,
                'passager_nom' => $reservation->passager_nom,
                'passager_prenom' => $reservation->passager_prenom,
                'passager_nom_complet' => $reservation->passager_prenom . ' ' . $reservation->passager_nom,
                'passager_email' => $reservation->passager_email,
                'passager_telephone' => $reservation->passager_telephone,
                'seat_number' => $reservation->seat_number,
                'date_voyage' => $reservation->date_voyage->format('d/m/Y'),
                'trajet' => $reservation->programme->point_depart . ' → ' . $reservation->programme->point_arrive,
                'heure_depart' => $reservation->programme->heure_depart,
                'montant' => number_format($reservation->montant, 0, ',', ' ') . ' FCFA',
            ]
        ]);
    }

    /**
     * Confirmer l'embarquement (après vérification visuelle par l'agent)
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'reference' => 'required|string|exists:reservations,reference',
        ]);

        $reservation = Reservation::where('reference', $request->reference)->first();
        $agent = Auth::guard('agent')->user();

        // Vérifier si la réservation appartient à la compagnie de l'agent
        if ($reservation->programme->compagnie_id !== $agent->compagnie_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cette réservation n\'appartient pas à votre compagnie.'
            ], 403);
        }

        if ($reservation->statut === 'terminee') {
            return response()->json([
                'success' => false,
                'message' => 'Cette réservation a déjà été scannée.'
            ], 400);
        }

        if ($reservation->statut !== 'confirmee') {
            return response()->json([
                'success' => false,
                'message' => 'Statut de réservation invalide pour le scan.'
            ], 400);
        }

        // Mettre à jour le statut
        $reservation->update([
            'statut' => 'terminee',
            'embarquement_scanned_at' => now(),
            'embarquement_agent_id' => $agent->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Embarquement confirmé pour ' . $reservation->passager_prenom . ' ' . $reservation->passager_nom . ' (Place ' . $reservation->seat_number . ')'
        ]);
    }

    /**
     * Ancienne méthode scan (conservée pour compatibilité)
     */
    public function scan(Request $request)
    {
        $request->validate([
            'reference' => 'required|string|exists:reservations,reference',
        ]);

        $reservation = Reservation::where('reference', $request->reference)->first();
        $agent = Auth::guard('agent')->user();

        // Vérifier si la réservation appartient à la compagnie de l'agent
        if ($reservation->programme->compagnie_id !== $agent->compagnie_id) {
            return back()->with('error', 'Cette réservation n\'appartient pas à votre compagnie.');
        }

        if ($reservation->statut === 'terminee') {
            return back()->with('warning', 'Cette réservation a déjà été scannée et terminée.');
        }

        if ($reservation->statut !== 'confirmee') {
            return back()->with('error', 'Statut de réservation invalide pour le scan.');
        }

        // Mettre à jour le statut
        $reservation->update([
            'statut' => 'terminee',
            'embarquement_scanned_at' => now(),
            'embarquement_agent_id' => $agent->id,
        ]);

        return back()->with('success', 'Réservation scannée et terminée avec succès.');
    }
}
