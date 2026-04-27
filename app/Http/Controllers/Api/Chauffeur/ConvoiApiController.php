<?php

namespace App\Http\Controllers\Api\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Convoi;
use App\Models\DriverLocation;
use App\Models\GareMessage;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ConvoiApiController extends Controller
{
    /**
     * Liste des convois assignés au chauffeur.
     *
     * Query params :
     *  - tab    : active (défaut) | effectues | non_effectues
     *  - date   : YYYY-MM-DD (sert pour l'onglet "active" pour filtrer aller/retour du jour)
     */
    public function index(Request $request)
    {
        $chauffeur = $request->user();
        $tab  = $request->input('tab', 'active');
        $date = $request->input('date', Carbon::today()->toDateString());

        $query = Convoi::where('personnel_id', $chauffeur->id)
            ->with(['itineraire', 'gare', 'vehicule', 'passagers'])
            ->orderBy('created_at', 'desc');

        if ($tab === 'effectues') {
            $query->where('statut', 'termine');
        } elseif ($tab === 'non_effectues') {
            $query->where('statut', 'annule');
        } else {
            // Actifs : aller du jour, retour du jour ou trajets en retard / en cours
            $query->whereIn('statut', ['paye', 'en_cours'])
                ->where(function ($q) use ($date) {
                    $q->where('statut', 'en_cours')
                      ->orWhere(function ($q2) use ($date) {
                          // Aller du jour
                          $q2->where('statut', 'paye')
                             ->where('aller_done', false)
                             ->whereDate('date_depart', $date);
                      })
                      ->orWhere(function ($q3) use ($date) {
                          // Retour du jour
                          $q3->where('statut', 'paye')
                             ->where('aller_done', true)
                             ->whereDate('date_retour', $date);
                      })
                      ->orWhere(function ($q4) use ($date) {
                          // Aller en retard
                          $q4->where('statut', 'paye')
                             ->where('aller_done', false)
                             ->whereDate('date_depart', '<', $date);
                      })
                      ->orWhere(function ($q5) use ($date) {
                          // Retour en retard
                          $q5->where('statut', 'paye')
                             ->where('aller_done', true)
                             ->whereDate('date_retour', '<', $date);
                      });
                });
        }

        $convois = $query->get();

        return response()->json([
            'success' => true,
            'tab'     => $tab,
            'date'    => $date,
            'convois' => $convois->map(fn ($c) => $this->formatConvoi($c))->values(),
        ]);
    }

    /**
     * Détails d'un convoi (doit appartenir au chauffeur)
     */
    public function show(Request $request, Convoi $convoi)
    {
        $chauffeur = $request->user();

        if ((int) $convoi->personnel_id !== (int) $chauffeur->id) {
            return response()->json([
                'success' => false,
                'message' => 'Ce convoi ne vous appartient pas.',
            ], 403);
        }

        $convoi->load(['itineraire', 'gare', 'vehicule', 'passagers', 'compagnie', 'latestLocation']);

        return response()->json([
            'success' => true,
            'convoi'  => $this->formatConvoi($convoi, withPassagers: true, withLocation: true),
        ]);
    }

    /**
     * Démarrer un convoi (paye -> en_cours).
     * Gère la logique aller / retour (aller_done).
     */
    public function start(Request $request, Convoi $convoi)
    {
        $chauffeur = $request->user();

        if ((int) $convoi->personnel_id !== (int) $chauffeur->id) {
            return response()->json([
                'success' => false,
                'message' => 'Ce convoi ne vous appartient pas.',
            ], 403);
        }

        if ($convoi->statut !== 'paye') {
            return response()->json([
                'success' => false,
                'message' => 'Ce convoi ne peut pas être démarré (statut : ' . $convoi->statut . ').',
            ], 400);
        }

        // Vérifier la date : aller = date_depart, retour = date_retour
        if ($convoi->aller_done) {
            if ($convoi->date_retour
                && Carbon::parse($convoi->date_retour)->isFuture()
                && !Carbon::parse($convoi->date_retour)->isToday()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le retour ne peut être démarré qu\'à partir du '
                        . Carbon::parse($convoi->date_retour)->format('d/m/Y') . '.',
                ], 400);
            }
        } else {
            if ($convoi->date_depart
                && Carbon::parse($convoi->date_depart)->isFuture()
                && !Carbon::parse($convoi->date_depart)->isToday()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le convoi ne peut être démarré qu\'à partir du '
                        . Carbon::parse($convoi->date_depart)->format('d/m/Y') . '.',
                ], 400);
            }
        }

        // Chauffeur + véhicule indisponibles
        $chauffeur->update(['statut' => 'indisponible']);
        if ($convoi->vehicule_id) {
            Vehicule::where('id', $convoi->vehicule_id)->update(['statut' => 'indisponible']);
        }

        $convoi->update(['statut' => 'en_cours']);

        $msg = $convoi->aller_done
            ? 'Retour démarré avec succès. Bon voyage !'
            : 'Convoi démarré avec succès.';

        return response()->json([
            'success' => true,
            'message' => $msg,
            'convoi'  => $this->formatConvoi($convoi->fresh(['itineraire', 'gare', 'vehicule', 'passagers'])),
        ]);
    }

    /**
     * Terminer un convoi (en_cours -> paye pour le retour, sinon termine).
     */
    public function complete(Request $request, Convoi $convoi)
    {
        $chauffeur = $request->user();

        if ((int) $convoi->personnel_id !== (int) $chauffeur->id) {
            return response()->json([
                'success' => false,
                'message' => 'Ce convoi ne vous appartient pas.',
            ], 403);
        }

        if ($convoi->statut !== 'en_cours') {
            return response()->json([
                'success' => false,
                'message' => 'Ce convoi n\'est pas en cours.',
            ], 400);
        }

        // Supprimer le GPS de ce trajet
        DriverLocation::where('convoi_id', $convoi->id)->delete();

        // Aller-retour : terminer l'aller seulement si aller_done = false et date_retour présente
        if ($convoi->date_retour && !$convoi->aller_done) {
            $convoi->update([
                'statut'     => 'paye',
                'aller_done' => true,
            ]);

            $chauffeur->update(['statut' => 'disponible']);
            if ($convoi->vehicule_id) {
                Vehicule::where('id', $convoi->vehicule_id)->update(['statut' => 'disponible']);
            }

            $dateRetour = Carbon::parse($convoi->date_retour)->translatedFormat('d F Y');
            $hRetour    = $convoi->heure_retour ? ' à ' . substr($convoi->heure_retour, 0, 5) : '';

            return response()->json([
                'success' => true,
                'message' => "Trajet aller terminé ✅ Le retour est prévu le {$dateRetour}{$hRetour}. Il apparaîtra sur votre tableau de bord à cette date.",
                'convoi'  => $this->formatConvoi($convoi->fresh(['itineraire', 'gare', 'vehicule', 'passagers'])),
                'is_aller_done' => true,
            ]);
        }

        // Pas de retour, ou c'est le retour qui se termine
        $convoi->update(['statut' => 'termine']);
        $chauffeur->update(['statut' => 'disponible']);

        if ($convoi->vehicule_id) {
            Vehicule::where('id', $convoi->vehicule_id)->update(['statut' => 'disponible']);
        }

        $msg = $convoi->aller_done
            ? 'Convoi retour terminé avec succès. Bienvenue !'
            : 'Convoi terminé avec succès.';

        return response()->json([
            'success' => true,
            'message' => $msg,
            'is_aller_done' => false,
        ]);
    }

    /**
     * Annulation / désistement d'un convoi par le chauffeur.
     * Motif requis. Le convoi repasse à "paye" sans chauffeur/véhicule -> la gare réaffecte.
     */
    public function annuler(Request $request, Convoi $convoi)
    {
        $chauffeur = $request->user();

        if ((int) $convoi->personnel_id !== (int) $chauffeur->id) {
            return response()->json([
                'success' => false,
                'message' => 'Ce convoi ne vous appartient pas.',
            ], 403);
        }

        if (!in_array($convoi->statut, ['paye', 'en_cours'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Ce convoi ne peut plus être annulé.',
            ], 400);
        }

        $request->validate([
            'motif_annulation' => 'required|string|min:10|max:500',
        ], [
            'motif_annulation.required' => 'Veuillez indiquer le motif d\'annulation.',
            'motif_annulation.min'      => 'Le motif doit contenir au moins 10 caractères.',
        ]);

        $oldVehiculeId = $convoi->vehicule_id;

        $convoi->update([
            'statut'                     => 'paye',
            'personnel_id'               => null,
            'vehicule_id'                => null,
            'motif_annulation_chauffeur' => $request->motif_annulation,
        ]);

        // Libérer le chauffeur + véhicule
        $chauffeur->update(['statut' => 'disponible']);
        if ($oldVehiculeId) {
            Vehicule::where('id', $oldVehiculeId)->update(['statut' => 'disponible']);
        }

        DriverLocation::where('convoi_id', $convoi->id)->delete();

        // Notifier la gare
        if ($convoi->gare_id) {
            try {
                GareMessage::create([
                    'gare_id'        => $convoi->gare_id,
                    'sender_type'    => 'App\Models\Personnel',
                    'sender_id'      => $chauffeur->id,
                    'recipient_type' => 'App\Models\Gare',
                    'recipient_id'   => $convoi->gare_id,
                    'subject'        => 'Désistement convoi ' . ($convoi->reference ?? '#' . $convoi->id),
                    'message'        => "Le chauffeur {$chauffeur->prenom} {$chauffeur->name} s'est désisté du convoi "
                        . ($convoi->reference ?? '#' . $convoi->id)
                        . " via Mobile.\n\nMotif : " . $request->motif_annulation
                        . "\n\nVeuillez réaffecter un autre chauffeur et véhicule.",
                    'is_read'        => false,
                ]);
            } catch (\Throwable $e) {
                Log::error('Erreur notification gare désistement convoi: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Désistement enregistré. La gare a été notifiée et pourra réaffecter le convoi.',
        ]);
    }

    /**
     * Mise à jour de la position GPS (uniquement quand en_cours)
     */
    public function updateLocation(Request $request, Convoi $convoi)
    {
        $chauffeur = $request->user();

        if ((int) $convoi->personnel_id !== (int) $chauffeur->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
        }

        if ($convoi->statut !== 'en_cours') {
            return response()->json([
                'success' => false,
                'message' => 'Le convoi n\'est pas en cours.',
            ], 422);
        }

        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed'     => 'nullable|numeric|min:0',
            'heading'   => 'nullable|numeric|between:0,360',
        ]);

        DriverLocation::updateOrCreate(
            ['convoi_id' => $convoi->id, 'personnel_id' => $chauffeur->id],
            [
                'voyage_id' => null,
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'speed'     => $request->speed,
                'heading'   => $request->heading,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Position mise à jour.']);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Helper : formater un convoi en tableau JSON-safe.
     */
    private function formatConvoi(Convoi $convoi, bool $withPassagers = true, bool $withLocation = false): array
    {
        $isRetour = (bool) $convoi->aller_done;

        $depart  = $convoi->lieu_depart  ?? ($convoi->itineraire->point_depart  ?? 'Départ');
        $arrivee = $convoi->lieu_retour  ?? ($convoi->itineraire->point_arrive ?? 'Arrivée');

        // Pour l'affichage aller/retour : si retour en cours/à faire, on inverse le sens
        $trajetDepart  = $isRetour ? $arrivee : $depart;
        $trajetArrivee = $isRetour ? $depart  : $arrivee;

        $data = [
            'id'                 => $convoi->id,
            'reference'          => $convoi->reference,
            'statut'             => $convoi->statut,
            'statut_label'       => $this->statutLabel($convoi->statut),
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
            'aller_done'         => (bool) $convoi->aller_done,
            'has_retour'         => !empty($convoi->date_retour),
            'passagers_soumis'   => (bool) $convoi->passagers_soumis,
            'motif_annulation_chauffeur' => $convoi->motif_annulation_chauffeur,

            // Demandeur : user (compte) ou client walk-in
            'demandeur' => [
                'nom'     => $convoi->demandeur_nom,
                'contact' => $convoi->demandeur_contact,
            ],

            // Trajet pour affichage direct
            'trajet' => [
                'depart'   => $trajetDepart,
                'arrivee'  => $trajetArrivee,
                'is_retour' => $isRetour,
                'date'     => $isRetour ? $convoi->date_retour : $convoi->date_depart,
                'heure'    => $isRetour
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
                'id'            => $convoi->itineraire->id,
                'point_depart'  => $convoi->itineraire->point_depart,
                'point_arrive'  => $convoi->itineraire->point_arrive,
            ] : null,

            'vehicule' => $convoi->vehicule ? [
                'id'               => $convoi->vehicule->id,
                'marque'           => $convoi->vehicule->marque,
                'modele'           => $convoi->vehicule->modele,
                'immatriculation'  => $convoi->vehicule->immatriculation,
                'nombre_place'     => $convoi->vehicule->nombre_place,
            ] : null,

            // Conditions d'affichage des actions (miroir du web)
            'can_start'    => $this->canStart($convoi),
            'can_complete' => $convoi->statut === 'en_cours',
            'can_cancel'   => in_array($convoi->statut, ['paye', 'en_cours'], true),
            'can_track'    => $convoi->statut === 'en_cours',
            'start_blocked_reason' => $this->startBlockedReason($convoi),
        ];

        if ($withPassagers) {
            $data['passagers'] = $convoi->passagers->map(fn ($p) => [
                'id'              => $p->id,
                'nom'             => $p->nom,
                'prenoms'         => $p->prenoms,
                'contact'         => $p->contact,
                'contact_urgence' => $p->contact_urgence,
                'email'           => $p->email,
            ])->values();
        }

        if ($withLocation) {
            $data['latest_location'] = $convoi->latestLocation ? [
                'latitude'  => (float) $convoi->latestLocation->latitude,
                'longitude' => (float) $convoi->latestLocation->longitude,
                'speed'     => $convoi->latestLocation->speed !== null ? (float) $convoi->latestLocation->speed : null,
                'heading'   => $convoi->latestLocation->heading !== null ? (float) $convoi->latestLocation->heading : null,
                'updated_at' => optional($convoi->latestLocation->updated_at)->toIso8601String(),
            ] : null;
        }

        return $data;
    }

    private function statutLabel(string $statut): string
    {
        return match ($statut) {
            'nouveau'   => 'Nouveau',
            'valide'    => 'Validé',
            'refuse'    => 'Refusé',
            'accepte'   => 'Montant accepté',
            'paye'      => 'Payé',
            'en_cours'  => 'En cours',
            'termine'   => 'Terminé',
            'annule'    => 'Annulé',
            default     => ucfirst(str_replace('_', ' ', $statut)),
        };
    }

    /**
     * Conditions pour démarrer un convoi (miroir de startConvoi web).
     */
    private function canStart(Convoi $convoi): bool
    {
        if ($convoi->statut !== 'paye') {
            return false;
        }

        $dateRef = $convoi->aller_done ? $convoi->date_retour : $convoi->date_depart;
        if (!$dateRef) {
            return true;
        }

        $d = Carbon::parse($dateRef);
        return $d->isToday() || $d->isPast();
    }

    private function startBlockedReason(Convoi $convoi): ?string
    {
        if ($convoi->statut !== 'paye') {
            return null;
        }
        $dateRef = $convoi->aller_done ? $convoi->date_retour : $convoi->date_depart;
        if (!$dateRef) {
            return null;
        }
        $d = Carbon::parse($dateRef);
        if ($d->isFuture() && !$d->isToday()) {
            return ($convoi->aller_done ? 'Le retour' : 'Le convoi')
                . ' ne peut être démarré qu\'à partir du ' . $d->format('d/m/Y') . '.';
        }
        return null;
    }
}
