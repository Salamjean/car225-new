<?php

namespace App\Http\Controllers\Api\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Voyage;
use App\Models\DriverLocation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VoyageApiController extends Controller
{
    /**
     * Liste des voyages du chauffeur
     */
    public function index(Request $request)
    {
        $chauffeur = $request->user();
        $date = $request->input('date', Carbon::today()->toDateString());

        $voyages = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('date_voyage', $date)
            ->with(['programme.gareDepart', 'programme.gareArrivee', 'vehicule'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'voyages' => $voyages->map(function($v) {
                return $this->formatVoyage($v);
            }),
            'pagination' => [
                'current_page' => $voyages->currentPage(),
                'last_page' => $voyages->lastPage(),
                'per_page' => $voyages->perPage(),
                'total' => $voyages->total(),
            ],
        ]);
    }

    /**
     * Historique complet des voyages
     */
    public function history(Request $request)
    {
        $chauffeur = $request->user();

        $voyages = Voyage::where('personnel_id', $chauffeur->id)
            ->with(['programme.gareDepart', 'programme.gareArrivee', 'vehicule'])
            ->orderBy('date_voyage', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'voyages' => $voyages->map(function($v) {
                return $this->formatVoyage($v);
            }),
            'pagination' => [
                'current_page' => $voyages->currentPage(),
                'last_page' => $voyages->lastPage(),
                'per_page' => $voyages->perPage(),
                'total' => $voyages->total(),
            ],
        ]);
    }

    /**
     * Confirmer un voyage (en_attente -> confirmé)
     */
    public function confirm(Request $request, Voyage $voyage)
    {
        $chauffeur = $request->user();

        if ($voyage->personnel_id !== $chauffeur->id) {
            return response()->json(['success' => false, 'message' => 'Ce voyage ne vous appartient pas.'], 403);
        }

        if ($voyage->statut !== 'en_attente') {
            return response()->json(['success' => false, 'message' => 'Ce voyage ne peut plus être confirmé.'], 400);
        }

        $voyage->update(['statut' => 'confirmé']);

        return response()->json([
            'success' => true,
            'message' => 'Voyage confirmé avec succès.',
            'voyage' => $this->formatVoyage($voyage->fresh(['programme.gareDepart', 'programme.gareArrivee', 'vehicule'])),
        ]);
    }

    /**
     * Démarrer un voyage (confirmé -> en_cours)
     */
    public function start(Request $request, Voyage $voyage)
    {
        $chauffeur = $request->user();

        if ($voyage->personnel_id !== $chauffeur->id) {
            return response()->json(['success' => false, 'message' => 'Ce voyage ne vous appartient pas.'], 403);
        }

        if (!in_array($voyage->statut, ['en_attente', 'confirmé'])) {
            return response()->json(['success' => false, 'message' => 'Ce voyage ne peut pas être démarré.'], 400);
        }

        if ($voyage->occupancy < 1) {
            return response()->json(['success' => false, 'message' => 'Au moins 1 passager doit être présent.'], 400);
        }

        $voyage->update(['statut' => 'en_cours']);

        return response()->json([
            'success' => true,
            'message' => 'Bon voyage ! Le voyage a été démarré.',
            'voyage' => $this->formatVoyage($voyage->fresh(['programme.gareDepart', 'programme.gareArrivee', 'vehicule'])),
        ]);
    }

    /**
     * Terminer un voyage (en_cours -> terminé)
     */
    public function complete(Request $request, Voyage $voyage)
    {
        $chauffeur = $request->user();

        if ($voyage->personnel_id !== $chauffeur->id) {
            return response()->json(['success' => false, 'message' => 'Ce voyage ne vous appartient pas.'], 403);
        }

        if ($voyage->statut !== 'en_cours') {
            return response()->json(['success' => false, 'message' => 'Ce voyage n\'est pas en cours.'], 400);
        }

        $voyage->update(['statut' => 'terminé']);
        $chauffeur->update(['statut' => 'disponible']);

        if ($voyage->vehicule) {
            $voyage->vehicule->update(['statut' => 'disponible']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Voyage terminé avec succès. Vous êtes maintenant disponible.',
        ]);
    }

    /**
     * Annuler un voyage
     */
    public function annuler(Request $request, Voyage $voyage)
    {
        $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        $chauffeur = $request->user();

        if ($voyage->personnel_id !== $chauffeur->id) {
            return response()->json(['success' => false, 'message' => 'Ce voyage ne vous appartient pas.'], 403);
        }

        if (!in_array($voyage->statut, ['en_attente', 'confirmé'])) {
            return response()->json(['success' => false, 'message' => 'Ce voyage ne peut plus être annulé.'], 400);
        }

        $voyage->update(['statut' => 'annulé']);

        // Libérer les réservations liées : remettre voyage_id à null
        // 1. Réservations directement liées via voyage_id
        $countDirect = \App\Models\Reservation::where('voyage_id', $voyage->id)->count();
        \App\Models\Reservation::where('voyage_id', $voyage->id)
            ->update(['voyage_id' => null]);

        // 2. Réservations liées par programme + date (n'ayant pas encore été nullifiées)
        $countProgramme = \App\Models\Reservation::where('programme_id', $voyage->programme_id)
            ->whereDate('date_voyage', $voyage->date_voyage)
            ->where('voyage_id', $voyage->id)
            ->count();
        \App\Models\Reservation::where('programme_id', $voyage->programme_id)
            ->whereDate('date_voyage', $voyage->date_voyage)
            ->where('voyage_id', $voyage->id)
            ->update(['voyage_id' => null]);

        \Illuminate\Support\Facades\Log::info("Annulation voyage #{$voyage->id}: {$countDirect} réservations directes, {$countProgramme} par programme libérées");

        // Envoyer le motif à la gare
        $gareId = $voyage->gare_depart_id ?? ($voyage->programme ? $voyage->programme->gare_depart_id : null);
        $motif = $request->reason ?? 'Aucun motif fourni';

        if ($gareId) {
            \App\Models\GareMessage::create([
                'gare_id' => $gareId,
                'sender_type' => 'App\Models\Personnel',
                'sender_id' => $chauffeur->id,
                'recipient_type' => 'App\Models\Gare',
                'recipient_id' => $gareId,
                'subject' => 'Annulation de voyage #' . $voyage->id,
                'message' => "Le chauffeur {$chauffeur->prenom} {$chauffeur->name} a annulé le voyage #{$voyage->id} via Mobile. \n\nMotif : " . $motif,
                'is_read' => false,
            ]);
        }

        $chauffeur->update(['statut' => 'disponible']);

        if ($voyage->vehicule) {
            $voyage->vehicule->update(['statut' => 'disponible']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Le voyage a été annulé et le motif transmis à la gare.',
        ]);
    }

    /**
     * Mettre à jour la position GPS
     */
    public function updateLocation(Request $request, Voyage $voyage)
    {
        $chauffeur = $request->user();

        if ($voyage->personnel_id !== $chauffeur->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
        }

        if ($voyage->statut !== 'en_cours') {
            return response()->json(['success' => false, 'message' => 'Le voyage n\'est pas en cours.'], 422);
        }

        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|between:0,360',
        ]);

        DriverLocation::updateOrCreate(
            ['voyage_id' => $voyage->id, 'personnel_id' => $chauffeur->id],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'speed' => $request->speed,
                'heading' => $request->heading,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Position mise à jour.']);
    }

    /**
     * Helper: formater un voyage
     */
    private function formatVoyage($voyage)
    {
        return [
            'id' => $voyage->id,
            'date_voyage' => $voyage->date_voyage,
            'statut' => $voyage->statut,
            'occupancy' => $voyage->occupancy,
            'estimated_arrival_at' => $voyage->estimated_arrival_at,
            'temps_restant' => $voyage->temps_restant,
            'programme' => $voyage->programme ? [
                'id' => $voyage->programme->id,
                'point_depart' => $voyage->programme->point_depart,
                'point_arrive' => $voyage->programme->point_arrive,
                'heure_depart' => $voyage->programme->heure_depart,
                'heure_arrive' => $voyage->programme->heure_arrive,
                'gare_depart' => optional($voyage->programme->gareDepart)->nom_gare ?? '',
                'gare_arrivee' => optional($voyage->programme->gareArrivee)->nom_gare ?? '',
                'gare_depart_lat' => optional($voyage->programme->gareDepart)->latitude,
                'gare_depart_lng' => optional($voyage->programme->gareDepart)->longitude,
                'gare_arrivee_lat' => optional($voyage->programme->gareArrivee)->latitude,
                'gare_arrivee_lng' => optional($voyage->programme->gareArrivee)->longitude,
                'montant_billet' => $voyage->programme->montant_billet,
                'capacity' => $voyage->programme->capacity ?? ($voyage->vehicule->nombre_place ?? 50),
            ] : null,
            'vehicule' => $voyage->vehicule ? [
                'id' => $voyage->vehicule->id,
                'marque' => $voyage->vehicule->marque,
                'modele' => $voyage->vehicule->modele,
                'immatriculation' => $voyage->vehicule->immatriculation,
                'nombre_place' => $voyage->vehicule->nombre_place,
            ] : null,
        ];
    }
}
