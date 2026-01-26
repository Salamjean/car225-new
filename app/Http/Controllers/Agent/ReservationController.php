<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Programme;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index()
    {
        $agent = Auth::guard('agent')->user();
        
        $reservations = Reservation::with(['programme', 'user'])
            ->whereHas('programme', function($query) use ($agent) {
                $query->where('compagnie_id', $agent->compagnie_id);
            })
            ->orderBy('date_voyage', 'desc')
            ->get();

        $enCours = $reservations->where('statut', '!=', 'terminee')->where('statut', '!=', 'annulee');
        $terminees = $reservations->where('statut', 'terminee');

        $programmesDuJour = Programme::where('compagnie_id', $agent->compagnie_id)
            ->whereDate('date_depart', Carbon::today())
            ->with('vehicule')
            ->orderBy('heure_depart')
            ->get();

        return view('agent.reservations.reservation', compact('enCours', 'terminees', 'programmesDuJour'));
    }
    /**
     * Rechercher une réservation pour scan (AJAX)
     */
     public function search(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
            'vehicule_id' => 'nullable|integer',
        ]);

        $reservation = Reservation::with(['programme.vehicule', 'user', 'embarquementVehicule'])
            ->where('reference', $request->reference)
            ->first();

        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Réservation non trouvée.'], 404);
        }

        $agent = Auth::guard('agent')->user();
        if ($reservation->programme->compagnie_id !== $agent->compagnie_id) {
            return response()->json(['success' => false, 'message' => 'Ce billet n\'appartient pas à votre compagnie.'], 403);
        }

        // --- LOGIQUE INTELLIGENTE ALLER / RETOUR ---
        $today = Carbon::today();
        $dateAller = Carbon::parse($reservation->date_voyage)->startOfDay();
        $dateRetour = $reservation->date_retour ? Carbon::parse($reservation->date_retour)->startOfDay() : null;

        $isDayAller = $dateAller->equalTo($today);
        $isDayRetour = $reservation->is_aller_retour && $dateRetour && $dateRetour->equalTo($today);

        $targetScan = null; // 'aller' ou 'retour'

        // 1. Cas Spécial : Aller-Retour le MÊME JOUR
        if ($isDayAller && $isDayRetour) {
            // Si l'aller est déjà terminé, on vise le retour
            if ($reservation->statut_aller === 'terminee') {
                $targetScan = 'retour';
            } else {
                $targetScan = 'aller';
            }
        }
        // 2. Cas Standard : C'est le jour de l'aller
        elseif ($isDayAller) {
            $targetScan = 'aller';
        }
        // 3. Cas Standard : C'est le jour du retour
        elseif ($isDayRetour) {
            $targetScan = 'retour';
        }
        // 4. Erreur de date
        else {
            return response()->json([
                'success' => false,
                'message' => 'Date invalide pour ce billet. Aller prévu le ' . $dateAller->format('d/m/Y') . 
                             ($dateRetour ? ' et Retour le ' . $dateRetour->format('d/m/Y') : '')
            ], 400);
        }

        // --- Vérification du statut selon la cible (Aller ou Retour) ---
        if ($targetScan === 'aller') {
            $statutActuel = $reservation->statut_aller;
            $trajetLabel = "ALLER : " . $reservation->programme->point_depart . ' → ' . $reservation->programme->point_arrive;
        } else {
            $statutActuel = $reservation->statut_retour;
            // On inverse le libellé pour le retour
            $trajetLabel = "RETOUR : " . $reservation->programme->point_arrive . ' → ' . $reservation->programme->point_depart;
        }

        // Si ce trajet spécifique est déjà terminé
        if ($statutActuel === 'terminee') {
            return response()->json([
                'success' => false,
                'message' => "Le trajet " . strtoupper($targetScan) . " a déjà été validé et consommé.",
                'already_scanned' => true
            ], 400);
        }

        if ($statutActuel !== 'confirmee') {
            return response()->json([
                'success' => false,
                'message' => "Le statut du trajet $targetScan n'est pas valide ($statutActuel)."
            ], 400);
        }

        // Réponse succès
        return response()->json([
            'success' => true,
            'reservation' => [
                'id' => $reservation->id,
                'reference' => $reservation->reference,
                'passager_nom_complet' => $reservation->passager_prenom . ' ' . $reservation->passager_nom,
                'passager_telephone' => $reservation->passager_telephone,
                'seat_number' => $reservation->seat_number,
                'date_voyage' => $today->format('d/m/Y'),
                'trajet' => $trajetLabel,
                'heure_depart' => $reservation->programme->heure_depart,
                'type_scan' => strtoupper($targetScan) // Important pour le debug visuel
            ]
        ]);
    }
    /**
     * Confirmer l'embarquement
     */
   public function confirm(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
            'vehicule_id' => 'required|integer',
        ]);

        $reservation = Reservation::where('reference', $request->reference)->first();
        $agent = Auth::guard('agent')->user();
        $vehicule = Vehicule::find($request->vehicule_id);

        if (!$reservation || !$vehicule) {
            return response()->json(['success' => false, 'message' => 'Données invalides.'], 400);
        }

        // --- MÊME LOGIQUE DE DÉTECTION QUE SEARCH ---
        $today = Carbon::today();
        $dateAller = Carbon::parse($reservation->date_voyage)->startOfDay();
        $dateRetour = $reservation->date_retour ? Carbon::parse($reservation->date_retour)->startOfDay() : null;

        $isDayAller = $dateAller->equalTo($today);
        $isDayRetour = $reservation->is_aller_retour && $dateRetour && $dateRetour->equalTo($today);

        $targetScan = null;

        if ($isDayAller && $isDayRetour) {
            // Priorité au retour si aller déjà fait
            $targetScan = ($reservation->statut_aller === 'terminee') ? 'retour' : 'aller';
        } elseif ($isDayAller) {
            $targetScan = 'aller';
        } elseif ($isDayRetour) {
            $targetScan = 'retour';
        } else {
            return response()->json(['success' => false, 'message' => 'Date invalide.'], 400);
        }

        $updateData = [
            'embarquement_scanned_at' => now(),
            'embarquement_agent_id' => $agent->id,
            'embarquement_vehicule_id' => $vehicule->id,
            'embarquement_status' => 'scanned'
        ];

        $message = "";

        if ($targetScan === 'aller') {
            if ($reservation->statut_aller === 'terminee') {
                return response()->json(['success' => false, 'message' => 'Trajet Aller déjà scanné.'], 400);
            }
            $updateData['statut_aller'] = 'terminee';
            $message = "Embarquement ALLER validé.";
        } else {
            if ($reservation->statut_retour === 'terminee') {
                return response()->json(['success' => false, 'message' => 'Trajet Retour déjà scanné.'], 400);
            }
            $updateData['statut_retour'] = 'terminee';
            $message = "Embarquement RETOUR validé.";
        }

        // Mise à jour
        $reservation->update($updateData);

        // Mise à jour du statut global
        if (!$reservation->is_aller_retour && $reservation->statut_aller === 'terminee') {
            $reservation->update(['statut' => 'terminee']);
        }
        elseif ($reservation->is_aller_retour && $reservation->statut_aller === 'terminee' && $reservation->statut_retour === 'terminee') {
            $reservation->update(['statut' => 'terminee']);
        }

        return response()->json([
            'success' => true,
            'message' => $message . ' Passager: ' . $reservation->passager_nom . ' (Siège ' . $reservation->seat_number . ')'
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
