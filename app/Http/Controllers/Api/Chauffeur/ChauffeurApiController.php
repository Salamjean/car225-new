<?php

namespace App\Http\Controllers\Api\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Voyage;
use App\Models\Convoi;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ChauffeurApiController extends Controller
{
    /**
     * Profil du chauffeur
     */
    public function profile(Request $request)
    {
        $chauffeur = $request->user();
        $chauffeur->load(['compagnie', 'gare']);

        return response()->json([
            'success' => true,
            'chauffeur' => [
                'id' => $chauffeur->id,
                'code_id' => $chauffeur->code_id,
                'name' => $chauffeur->name,
                'prenom' => $chauffeur->prenom,
                'email' => $chauffeur->email,
                'contact' => $chauffeur->contact,
                'contact_urgence' => $chauffeur->contact_urgence,
                'role' => $chauffeur->type_personnel,
                'statut' => $chauffeur->statut,
                'commune' => $chauffeur->commune,
                'profile_picture' => $chauffeur->profile_image ? 'storage/' . $chauffeur->profile_image : null,
                'profile_picture_url' => $chauffeur->profile_image 
                    ? 'storage/' . $chauffeur->profile_image 
                    : null,
                'compagnie' => $chauffeur->compagnie ? [
                    'id' => $chauffeur->compagnie->id,
                    'name' => $chauffeur->compagnie->name,
                    'logo' => $chauffeur->compagnie->logo ? 'storage/' . $chauffeur->compagnie->logo : null,
                ] : null,
                'gare' => $chauffeur->gare ? [
                    'id' => $chauffeur->gare->id,
                    'nom_gare' => $chauffeur->gare->nom_gare,
                ] : null,
            ],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $chauffeur = $request->user();

        $request->validate([
            'name' => 'nullable|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:20',
            'contact_urgence' => 'nullable|string|max:20',
            'commune' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'prenom', 'contact', 'contact_urgence', 'commune']);

        if ($request->hasFile('profile_picture')) {
            if ($chauffeur->profile_image) {
                Storage::disk('public')->delete($chauffeur->profile_image);
            }
            $data['profile_image'] = $request->file('profile_picture')->store('chauffeurs/profiles', 'public');
        }

        $chauffeur->update(array_filter($data));
        $chauffeur->load(['compagnie', 'gare']);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès.',
            'chauffeur' => [
                'id' => $chauffeur->id,
                'code_id' => $chauffeur->code_id,
                'name' => $chauffeur->name,
                'prenom' => $chauffeur->prenom,
                'email' => $chauffeur->email,
                'contact' => $chauffeur->contact,
                'contact_urgence' => $chauffeur->contact_urgence,
                'role' => $chauffeur->type_personnel,
                'statut' => $chauffeur->statut,
                'commune' => $chauffeur->commune,
                'profile_picture' => $chauffeur->profile_image ? 'storage/' . $chauffeur->profile_image : null,
                'profile_picture_url' => $chauffeur->profile_image 
                    ? 'storage/' . $chauffeur->profile_image 
                    : null,
                'compagnie' => $chauffeur->compagnie ? [
                    'id' => $chauffeur->compagnie->id,
                    'name' => $chauffeur->compagnie->name,
                    'logo' => $chauffeur->compagnie->logo ? 'storage/' . $chauffeur->compagnie->logo : null,
                ] : null,
                'gare' => $chauffeur->gare ? [
                    'id' => $chauffeur->gare->id,
                    'nom_gare' => $chauffeur->gare->nom_gare,
                ] : null,
            ],
        ]);
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $chauffeur = $request->user();

        if (!Hash::check($request->current_password, $chauffeur->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Le mot de passe actuel est incorrect.',
            ], 422);
        }

        $chauffeur->update(['password' => Hash::make($request->new_password)]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe modifié avec succès.',
        ]);
    }

    /**
     * Mettre à jour le token FCM
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);
        $request->user()->update(['fcm_token' => $request->fcm_token]);

        return response()->json([
            'success' => true,
            'message' => 'Token FCM mis à jour.',
        ]);
    }

    /**
     * Dashboard - Statistiques chauffeur
     */
    public function dashboard(Request $request)
    {
        $chauffeur = $request->user();
        $today = Carbon::today();
        $todayStr = $today->toDateString();

        // ── Voyages du jour ───────────────────────────────────────────────────
        $todayVoyages = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('date_voyage', $today)
            ->with(['programme.gareDepart', 'programme.gareArrivee', 'vehicule'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(fn ($v) => $this->formatVoyage($v));

        // ── Voyages à venir ───────────────────────────────────────────────────
        $upcomingVoyages = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('date_voyage', '>', $today)
            ->whereNotIn('statut', ['terminé', 'annulé'])
            ->with(['programme.gareDepart', 'programme.gareArrivee', 'vehicule'])
            ->orderBy('date_voyage', 'asc')
            ->limit(10)
            ->get()
            ->map(fn ($v) => $this->formatVoyage($v));

        // ── Voyages bloqués (en_cours d'un jour passé : oubli de "Terminer") ─
        $blockedVoyages = Voyage::where('personnel_id', $chauffeur->id)
            ->where('statut', 'en_cours')
            ->whereDate('date_voyage', '<', $today)
            ->with(['programme.gareDepart', 'programme.gareArrivee', 'vehicule'])
            ->orderBy('date_voyage', 'asc')
            ->get()
            ->map(fn ($v) => $this->formatVoyage($v));

        // ── Convois du jour (actifs : aller du jour, retour du jour, en_cours,
        //     ou en retard non encore démarré) ─────────────────────────────────
        $todayConvois = Convoi::where('personnel_id', $chauffeur->id)
            ->whereIn('statut', ['paye', 'en_cours'])
            ->where(function ($q) use ($todayStr) {
                $q->where('statut', 'en_cours')
                  ->orWhere(function ($q2) use ($todayStr) {
                      // Aller du jour
                      $q2->where('statut', 'paye')
                         ->where('aller_done', false)
                         ->whereDate('date_depart', $todayStr);
                  })
                  ->orWhere(function ($q3) use ($todayStr) {
                      // Retour du jour
                      $q3->where('statut', 'paye')
                         ->where('aller_done', true)
                         ->whereDate('date_retour', $todayStr);
                  })
                  ->orWhere(function ($q4) use ($todayStr) {
                      // Aller en retard (jour passé, pas encore démarré)
                      $q4->where('statut', 'paye')
                         ->where('aller_done', false)
                         ->whereDate('date_depart', '<', $todayStr);
                  })
                  ->orWhere(function ($q5) use ($todayStr) {
                      // Retour en retard
                      $q5->where('statut', 'paye')
                         ->where('aller_done', true)
                         ->whereDate('date_retour', '<', $todayStr);
                  });
            })
            ->with(['itineraire', 'gare', 'vehicule'])
            ->orderBy('date_depart', 'asc')
            ->get()
            ->map(fn ($c) => $this->formatConvoiForDashboard($c));

        // ── Convois à venir (paye, dates futures) ────────────────────────────
        $upcomingConvois = Convoi::where('personnel_id', $chauffeur->id)
            ->where('statut', 'paye')
            ->where(function ($q) use ($todayStr) {
                $q->where(function ($q2) use ($todayStr) {
                    // Aller futur
                    $q2->where('aller_done', false)
                       ->whereDate('date_depart', '>', $todayStr);
                })->orWhere(function ($q3) use ($todayStr) {
                    // Retour futur
                    $q3->where('aller_done', true)
                       ->whereDate('date_retour', '>', $todayStr);
                });
            })
            ->with(['itineraire', 'gare', 'vehicule'])
            ->orderBy('date_depart', 'asc')
            ->limit(10)
            ->get()
            ->map(fn ($c) => $this->formatConvoiForDashboard($c));

        // ── Convois bloqués (en_cours, dates passées : oubli de "Terminer") ──
        $blockedConvois = Convoi::where('personnel_id', $chauffeur->id)
            ->where('statut', 'en_cours')
            ->where(function ($q) use ($todayStr) {
                $q->where(function ($q2) use ($todayStr) {
                    $q2->where('aller_done', false)
                       ->whereDate('date_depart', '<', $todayStr);
                })->orWhere(function ($q3) use ($todayStr) {
                    $q3->where('aller_done', true)
                       ->whereDate('date_retour', '<', $todayStr);
                });
            })
            ->with(['itineraire', 'gare', 'vehicule'])
            ->orderBy('date_depart', 'asc')
            ->get()
            ->map(fn ($c) => $this->formatConvoiForDashboard($c));

        // ── Stats ─────────────────────────────────────────────────────────────
        $totalVoyages = Voyage::where('personnel_id', $chauffeur->id)->count();
        $completedVoyages = Voyage::where('personnel_id', $chauffeur->id)->where('statut', 'terminé')->count();

        $totalConvois = Convoi::where('personnel_id', $chauffeur->id)->count();
        $completedConvois = Convoi::where('personnel_id', $chauffeur->id)->where('statut', 'termine')->count();

        return response()->json([
            'success' => true,
            'today_voyages'    => $todayVoyages,
            'upcoming_voyages' => $upcomingVoyages,
            'blocked_voyages'  => $blockedVoyages,
            'today_convois'    => $todayConvois,
            'upcoming_convois' => $upcomingConvois,
            'blocked_convois'  => $blockedConvois,
            'stats' => [
                'total_voyages'     => $totalVoyages,
                'completed_voyages' => $completedVoyages,
                'total_convois'     => $totalConvois,
                'completed_convois' => $completedConvois,
                'statut'            => $chauffeur->statut,
            ],
        ]);
    }

    /**
     * Helper : formater un convoi pour le dashboard mobile.
     * Mirroir simplifié de ConvoiApiController::formatConvoi (sans passagers ni location).
     * Garde la même forme JSON consommée par ConvoiModel.fromJson côté Flutter.
     */
    private function formatConvoiForDashboard(Convoi $convoi): array
    {
        $isRetour = (bool) $convoi->aller_done;

        $depart  = $convoi->lieu_depart  ?? ($convoi->itineraire->point_depart  ?? 'Départ');
        $arrivee = $convoi->lieu_retour  ?? ($convoi->itineraire->point_arrive ?? 'Arrivée');

        $trajetDepart  = $isRetour ? $arrivee : $depart;
        $trajetArrivee = $isRetour ? $depart  : $arrivee;

        $dateRef = $isRetour ? $convoi->date_retour : $convoi->date_depart;
        $canStart = false;
        $startBlockedReason = null;
        if ($convoi->statut === 'paye' && $dateRef) {
            $d = Carbon::parse($dateRef);
            $canStart = $d->isToday() || $d->isPast();
            if ($d->isFuture() && !$d->isToday()) {
                $startBlockedReason = ($isRetour ? 'Le retour' : 'Le convoi')
                    . ' ne peut être démarré qu\'à partir du ' . $d->format('d/m/Y') . '.';
            }
        } elseif ($convoi->statut === 'paye' && !$dateRef) {
            $canStart = true;
        }

        $statutLabels = [
            'nouveau'   => 'Nouveau',
            'valide'    => 'Validé',
            'refuse'    => 'Refusé',
            'accepte'   => 'Montant accepté',
            'paye'      => 'Payé',
            'en_cours'  => 'En cours',
            'termine'   => 'Terminé',
            'annule'    => 'Annulé',
        ];
        $statutLabel = $statutLabels[$convoi->statut] ?? ucfirst(str_replace('_', ' ', $convoi->statut));

        return [
            'id'                 => $convoi->id,
            'reference'          => $convoi->reference,
            'statut'             => $convoi->statut,
            'statut_label'       => $statutLabel,
            'nombre_personnes'   => $convoi->nombre_personnes,
            'montant'            => $convoi->montant !== null ? (float) $convoi->montant : null,
            'lieu_depart'        => $depart,
            'lieu_retour'        => $arrivee,
            'lieu_rassemblement' => $convoi->lieu_rassemblement,
            'lieu_rassemblement_retour' => $convoi->lieu_rassemblement_retour,
            'date_depart'        => $convoi->date_depart,
            'heure_depart'       => $convoi->heure_depart,
            'date_retour'        => $convoi->date_retour,
            'heure_retour'       => $convoi->heure_retour,
            'is_garant'          => (bool) $convoi->is_garant,
            'aller_done'         => $isRetour,
            'has_retour'         => !empty($convoi->date_retour),
            'passagers_soumis'   => (bool) $convoi->passagers_soumis,
            'motif_annulation_chauffeur' => $convoi->motif_annulation_chauffeur,

            'demandeur' => [
                'nom'     => $convoi->demandeur_nom,
                'contact' => $convoi->demandeur_contact,
            ],

            'trajet' => [
                'depart'    => $trajetDepart,
                'arrivee'   => $trajetArrivee,
                'is_retour' => $isRetour,
                'date'      => $dateRef,
                'heure'     => $isRetour
                    ? substr($convoi->heure_retour ?? '', 0, 5)
                    : substr($convoi->heure_depart ?? '', 0, 5),
            ],

            'gare' => $convoi->gare ? [
                'id'        => $convoi->gare->id,
                'nom_gare'  => $convoi->gare->nom_gare,
                'latitude'  => $convoi->gare->latitude,
                'longitude' => $convoi->gare->longitude,
            ] : null,

            'itineraire' => $convoi->itineraire ? [
                'id'           => $convoi->itineraire->id,
                'point_depart' => $convoi->itineraire->point_depart,
                'point_arrive' => $convoi->itineraire->point_arrive,
            ] : null,

            'vehicule' => $convoi->vehicule ? [
                'id'              => $convoi->vehicule->id,
                'marque'          => $convoi->vehicule->marque,
                'modele'          => $convoi->vehicule->modele,
                'immatriculation' => $convoi->vehicule->immatriculation,
                'nombre_place'    => $convoi->vehicule->nombre_place,
            ] : null,

            'can_start'    => $canStart,
            'can_complete' => $convoi->statut === 'en_cours',
            'can_cancel'   => in_array($convoi->statut, ['paye', 'en_cours'], true),
            'can_track'    => $convoi->statut === 'en_cours',
            'start_blocked_reason' => $startBlockedReason,

            // Pas de passagers ni de location au niveau dashboard (allégé)
            'passagers'       => [],
            'latest_location' => null,
        ];
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
