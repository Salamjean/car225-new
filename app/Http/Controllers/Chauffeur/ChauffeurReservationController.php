<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Voyage;
use App\Models\Programme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChauffeurReservationController extends Controller
{
    /**
     * Afficher la page de scan QR du chauffeur
     */
    public function scanPage()
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        // Récupérer le voyage actif du chauffeur aujourd'hui
      $voyageActif = Voyage::where('personnel_id', $chauffeur->id)
    ->whereDate('date_voyage', Carbon::today())
    ->whereNotIn('statut', ['terminé', 'annulé']) // <-- LA CORRECTION EST ICI
    ->with(['programme.gareDepart', 'programme.gareArrivee', 'vehicule']) // Adapte les relations selon la méthode (search, confirm, etc.)
    ->orderBy('created_at', 'asc') // Optionnel: pour s'assurer de prendre le plus ancien non terminé si le chauffeur a 2 voyages le même jour
    ->first();

        // Derniers scans effectués par le chauffeur aujourd'hui
        // On utilise embarquement_agent_id avec une valeur personnalisée préfixée pour les chauffeurs
        // ou on filtre par voyage actif (programme + date)
        $derniersScans = collect();
        if ($voyageActif) {
            $derniersScans = Reservation::with('programme')
                ->where('programme_id', $voyageActif->programme_id)
                ->whereDate('embarquement_scanned_at', Carbon::today())
                ->whereNotNull('embarquement_scanned_at')
                ->orderBy('embarquement_scanned_at', 'desc')
                ->limit(15)
                ->get();
        }

        return view('chauffeur.reservations.scan', compact('voyageActif', 'derniersScans'));
    }

    /**
     * Rechercher une réservation par référence (appel AJAX)
     */
    public function search(Request $request)
    {
        $request->validate(['reference' => 'required|string']);

        $chauffeur = Auth::guard('chauffeur')->user();

        // Voyage actif du chauffeur aujourd'hui
      $voyageActif = Voyage::where('personnel_id', $chauffeur->id)
    ->whereDate('date_voyage', Carbon::today())
    ->whereNotIn('statut', ['terminé', 'annulé']) // <-- LA CORRECTION EST ICI
    ->with(['programme.gareDepart', 'programme.gareArrivee', 'vehicule']) // Adapte les relations selon la méthode (search, confirm, etc.)
    ->orderBy('created_at', 'asc') // Optionnel: pour s'assurer de prendre le plus ancien non terminé si le chauffeur a 2 voyages le même jour
    ->first();
        if (!$voyageActif) {
            return response()->json([
                'success' => false,
                'message' => "Vous n'avez aucun voyage actif ou planifié aujourd'hui. Le scan n'est pas disponible."
            ], 403);
        }

        $reservation = Reservation::with(['programme.gareDepart', 'programme.gareArrivee'])
            ->where('reference', $request->reference)
            ->first();

        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Réservation introuvable.'], 404);
        }

        // Vérifier que la réservation appartient au même programme (ou programme retour) que le voyage actif
        $programmeDuVoyage = $voyageActif->programme_id;
        $programmeRetourId = optional($voyageActif->programme)->programme_retour_id;

        $appartientAuVoyage = ($reservation->programme_id === $programmeDuVoyage)
            || ($programmeRetourId && $reservation->programme_id === $programmeRetourId);

        // Fallback: vérifier que la compagnie correspond
        $compagnieOk = $reservation->programme
            && $reservation->programme->compagnie_id === optional($voyageActif->programme)->compagnie_id;

        if (!$appartientAuVoyage && !$compagnieOk) {
            return response()->json([
                'success' => false,
                'message' => "Ce billet n'est pas associé à votre voyage d'aujourd'hui."
            ], 403);
        }

        // Déterminer le scan cible (aller ou retour)
        $today      = Carbon::today();
        $dateAller  = Carbon::parse($reservation->date_voyage)->startOfDay();
        $dateRetour = $reservation->date_retour ? Carbon::parse($reservation->date_retour)->startOfDay() : null;

        $isDayAller  = $dateAller->equalTo($today);
        $isDayRetour = $dateRetour && $dateRetour->equalTo($today);

        if (!$isDayAller && !$isDayRetour) {
            return response()->json([
                'success' => false,
                'message' => "Cette réservation n'est pas valable pour aujourd'hui."
            ], 400);
        }

        if ($isDayAller && $isDayRetour) {
            $targetScan = ($reservation->statut_aller === 'terminee') ? 'retour' : 'aller';
        } elseif ($isDayAller) {
            $targetScan = 'aller';
        } else {
            $targetScan = 'retour';
        }

        $statutActuel = ($targetScan === 'aller') ? $reservation->statut_aller : $reservation->statut_retour;

        if ($statutActuel === 'terminee') {
            return response()->json([
                'success' => false,
                'message' => "Le trajet " . strtoupper($targetScan) . " a déjà été validé.",
                'already_scanned' => true
            ], 400);
        }

        $prog = $reservation->programme;
        if ($prog && !$prog->relationLoaded('gareDepart')) {
            $prog->load(['gareDepart', 'gareArrivee']);
        }

        return response()->json([
            'success' => true,
            'reservation' => [
                'id'                 => $reservation->id,
                'reference'          => $reservation->reference,
                'passager_nom_complet' => $reservation->passager_prenom . ' ' . $reservation->passager_nom,
                'passager_telephone'   => $reservation->passager_telephone,
                'seat_number'        => $reservation->seat_number,
                'trajet'             => optional($prog)->point_depart . ' → ' . optional($prog)->point_arrive,
                'heure_depart'       => optional($prog)->heure_depart,
                'gare_depart'        => optional(optional($prog)->gareDepart)->nom_gare,
                'gare_arrivee'       => optional(optional($prog)->gareArrivee)->nom_gare,
                'montant'            => $reservation->montant_formatted,
                'is_aller_retour'    => $reservation->is_aller_retour,
                'type_scan'          => strtoupper($targetScan),
                'statut'             => $statutActuel,
                'vehicule_id'        => optional($voyageActif->vehicule)->id,
            ]
        ]);
    }

    /**
     * Confirmer l'embarquement (appel AJAX)
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
        ]);

        $chauffeur = Auth::guard('chauffeur')->user();

       $voyageActif = Voyage::where('personnel_id', $chauffeur->id)
    ->whereDate('date_voyage', Carbon::today())
    ->whereNotIn('statut', ['terminé', 'annulé']) // <-- LA CORRECTION EST ICI
    ->with(['programme.gareDepart', 'programme.gareArrivee', 'vehicule']) // Adapte les relations selon la méthode (search, confirm, etc.)
    ->orderBy('created_at', 'asc') // Optionnel: pour s'assurer de prendre le plus ancien non terminé si le chauffeur a 2 voyages le même jour
    ->first();

        if (!$voyageActif || !$voyageActif->vehicule) {
            return response()->json([
                'success' => false,
                'message' => "Aucun voyage ou véhicule actif trouvé pour aujourd'hui."
            ], 403);
        }

        $reservation = Reservation::with('programme')->where('reference', $request->reference)->first();

        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Réservation non trouvée.'], 404);
        }

        // Déterminer aller ou retour
        $today      = Carbon::today();
        $dateAller  = Carbon::parse($reservation->date_voyage)->startOfDay();
        $dateRetour = $reservation->date_retour ? Carbon::parse($reservation->date_retour)->startOfDay() : null;

        $isDayAller  = $dateAller->equalTo($today);
        $isDayRetour = $dateRetour && $dateRetour->equalTo($today);

        if (!$isDayAller && !$isDayRetour) {
            return response()->json([
                'success' => false,
                'message' => "Cette réservation n'est pas valable pour aujourd'hui."
            ], 400);
        }

        if ($isDayAller && $isDayRetour) {
            $targetScan = ($reservation->statut_aller === 'terminee') ? 'retour' : 'aller';
        } elseif ($isDayAller) {
            $targetScan = 'aller';
        } else {
            $targetScan = 'retour';
        }

        $updateData = [
            'embarquement_scanned_at'  => now(),
            'embarquement_agent_id'    => null, // pas un agent
            'embarquement_vehicule_id' => $voyageActif->vehicule->id,
            'voyage_id'                => $voyageActif->id, // ATTRIDUTION DU VOYAGE_ID
            'embarquement_status'      => 'scanned',
            'statut' => 'terminee', // <-- AJOUTE CETTE LIGNE ICI
        ];

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

        $reservation->update($updateData);

       

        return response()->json([
            'success' => true,
            'message' => $message . ' Passager: ' . $reservation->passager_nom . ' (Siège ' . $reservation->seat_number . ')'
        ]);
    }
}
