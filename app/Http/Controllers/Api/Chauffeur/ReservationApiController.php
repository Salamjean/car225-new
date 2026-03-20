<?php

namespace App\Http\Controllers\Api\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Voyage;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationApiController extends Controller
{
    /**
     * Page de scan — Voyage actif + derniers scans
     */
    public function scanInfo(Request $request)
    {
        $chauffeur = $request->user();

        $voyageActif = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('date_voyage', Carbon::today())
            ->with(['programme.gareDepart', 'programme.gareArrivee', 'vehicule'])
            ->first();

        $derniersScans = collect();
        if ($voyageActif) {
            $derniersScans = Reservation::with('programme')
                ->where('programme_id', $voyageActif->programme_id)
                ->whereDate('embarquement_scanned_at', Carbon::today())
                ->whereNotNull('embarquement_scanned_at')
                ->orderBy('embarquement_scanned_at', 'desc')
                ->limit(15)
                ->get()
                ->map(function($r) {
                    return [
                        'id' => $r->id,
                        'reference' => $r->reference,
                        'passager_nom' => $r->passager_prenom . ' ' . $r->passager_nom,
                        'seat_number' => $r->seat_number,
                        'scanned_at' => Carbon::parse($r->embarquement_scanned_at)->format('H:i'),
                        'statut_aller' => $r->statut_aller,
                        'statut_retour' => $r->statut_retour,
                    ];
                });
        }

        return response()->json([
            'success' => true,
            'voyage_actif' => $voyageActif ? [
                'id' => $voyageActif->id,
                'date_voyage' => $voyageActif->date_voyage,
                'statut' => $voyageActif->statut,
                'programme' => $voyageActif->programme ? [
                    'id' => $voyageActif->programme->id,
                    'point_depart' => $voyageActif->programme->point_depart,
                    'point_arrive' => $voyageActif->programme->point_arrive,
                    'heure_depart' => $voyageActif->programme->heure_depart,
                    'gare_depart' => optional($voyageActif->programme->gareDepart)->nom_gare ?? '',
                    'gare_arrivee' => optional($voyageActif->programme->gareArrivee)->nom_gare ?? '',
                ] : null,
                'vehicule' => $voyageActif->vehicule ? [
                    'id' => $voyageActif->vehicule->id,
                    'marque' => $voyageActif->vehicule->marque,
                    'immatriculation' => $voyageActif->vehicule->immatriculation,
                ] : null,
            ] : null,
            'derniers_scans' => $derniersScans,
        ]);
    }

    /**
     * Rechercher une réservation par référence QR (scan)
     */
    public function search(Request $request)
    {
        $request->validate(['reference' => 'required|string']);

        $chauffeur = $request->user();

        $voyageActif = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('date_voyage', Carbon::today())
            ->with(['programme', 'vehicule'])
            ->first();

        if (!$voyageActif) {
            return response()->json([
                'success' => false,
                'message' => "Vous n'avez aucun voyage actif ou planifié aujourd'hui.",
            ], 403);
        }

        $reservation = Reservation::with(['programme.gareDepart', 'programme.gareArrivee'])
            ->where('reference', $request->reference)
            ->first();

        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Réservation introuvable.'], 404);
        }

        // Vérifier que la réservation appartient au programme du voyage
        $programmeDuVoyage = $voyageActif->programme_id;
        $programmeRetourId = optional($voyageActif->programme)->programme_retour_id;

        $appartientAuVoyage = ($reservation->programme_id === $programmeDuVoyage)
            || ($programmeRetourId && $reservation->programme_id === $programmeRetourId);

        $compagnieOk = $reservation->programme
            && $reservation->programme->compagnie_id === optional($voyageActif->programme)->compagnie_id;

        if (!$appartientAuVoyage && !$compagnieOk) {
            return response()->json([
                'success' => false,
                'message' => "Ce billet n'est pas associé à votre voyage d'aujourd'hui.",
            ], 403);
        }

        // Déterminer aller ou retour
        $today = Carbon::today();
        $dateAller = Carbon::parse($reservation->date_voyage)->startOfDay();
        $dateRetour = $reservation->date_retour ? Carbon::parse($reservation->date_retour)->startOfDay() : null;

        $isDayAller = $dateAller->equalTo($today);
        $isDayRetour = $dateRetour && $dateRetour->equalTo($today);

        if (!$isDayAller && !$isDayRetour) {
            return response()->json([
                'success' => false,
                'message' => "Cette réservation n'est pas valable pour aujourd'hui.",
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
                'already_scanned' => true,
            ], 400);
        }

        $prog = $reservation->programme;
        if ($prog && !$prog->relationLoaded('gareDepart')) {
            $prog->load(['gareDepart', 'gareArrivee']);
        }

        return response()->json([
            'success' => true,
            'reservation' => [
                'id' => $reservation->id,
                'reference' => $reservation->reference,
                'passager_nom_complet' => $reservation->passager_prenom . ' ' . $reservation->passager_nom,
                'passager_telephone' => $reservation->passager_telephone,
                'seat_number' => $reservation->seat_number,
                'trajet' => optional($prog)->point_depart . ' → ' . optional($prog)->point_arrive,
                'heure_depart' => optional($prog)->heure_depart,
                'gare_depart' => optional(optional($prog)->gareDepart)->nom_gare,
                'gare_arrivee' => optional(optional($prog)->gareArrivee)->nom_gare,
                'montant' => number_format($reservation->montant ?? 0, 0, ',', ' ') . ' FCFA',
                'is_aller_retour' => $reservation->is_aller_retour,
                'type_scan' => strtoupper($targetScan),
                'statut' => $statutActuel,
                'vehicule_id' => optional($voyageActif->vehicule)->id,
            ],
        ]);
    }

    /**
     * Confirmer l'embarquement
     */
    public function confirm(Request $request)
    {
        $request->validate(['reference' => 'required|string']);

        $chauffeur = $request->user();

        $voyageActif = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('date_voyage', Carbon::today())
            ->with(['programme', 'vehicule'])
            ->first();

        if (!$voyageActif || !$voyageActif->vehicule) {
            return response()->json([
                'success' => false,
                'message' => "Aucun voyage ou véhicule actif trouvé pour aujourd'hui.",
            ], 403);
        }

        $reservation = Reservation::with('programme')->where('reference', $request->reference)->first();

        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Réservation non trouvée.'], 404);
        }

        // Déterminer aller ou retour
        $today = Carbon::today();
        $dateAller = Carbon::parse($reservation->date_voyage)->startOfDay();
        $dateRetour = $reservation->date_retour ? Carbon::parse($reservation->date_retour)->startOfDay() : null;

        $isDayAller = $dateAller->equalTo($today);
        $isDayRetour = $dateRetour && $dateRetour->equalTo($today);

        if (!$isDayAller && !$isDayRetour) {
            return response()->json([
                'success' => false,
                'message' => "Cette réservation n'est pas valable pour aujourd'hui.",
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
            'embarquement_scanned_at' => now(),
            'embarquement_agent_id' => null,
            'embarquement_vehicule_id' => $voyageActif->vehicule->id,
            'voyage_id' => $voyageActif->id, // On lie directement au voyage actif
            'embarquement_status' => 'scanned',
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

        // Mise à jour du statut global
        if (!$reservation->is_aller_retour && $reservation->statut_aller === 'terminee') {
            $reservation->update(['statut' => 'terminee']);
        } elseif ($reservation->is_aller_retour && $reservation->statut_aller === 'terminee' && $reservation->statut_retour === 'terminee') {
            $reservation->update(['statut' => 'terminee']);
        }

        return response()->json([
            'success' => true,
            'message' => $message . ' Passager: ' . $reservation->passager_nom . ' (Siège ' . $reservation->seat_number . ')',
            'reservation' => [
                'id' => $reservation->id,
                'reference' => $reservation->reference,
                'statut' => $reservation->statut,
                'statut_aller' => $reservation->statut_aller,
                'statut_retour' => $reservation->statut_retour,
            ],
        ]);
    }
}
