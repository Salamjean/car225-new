<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Models\Convoi;
use App\Models\Personnel;
use App\Models\Vehicule;
use App\Models\Voyage;
use App\Notifications\ConvoiAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class GareConvoiController extends Controller
{
    /**
     * Récupérer les IDs de personnel occupés par un convoi qui chevauche la plage de dates donnée.
     */
    private function busyPersonnelIdsForDateRange(?string $dateDepart, ?string $dateRetour, ?int $excludeConvoiId = null): array
    {
        $query = Convoi::whereNotNull('personnel_id')
            ->whereIn('statut', ['paye', 'en_cours']);

        if ($excludeConvoiId) {
            $query->where('id', '!=', $excludeConvoiId);
        }

        if ($dateDepart && $dateRetour) {
            $query->where(function ($q) use ($dateDepart, $dateRetour) {
                $q->where('date_depart', '<=', $dateRetour)
                  ->where('date_retour', '>=', $dateDepart);
            });
        }

        return $query->pluck('personnel_id')->toArray();
    }

    /**
     * Récupérer les IDs de véhicules occupés par un convoi qui chevauche la plage de dates donnée.
     */
    private function busyVehiculeIdsForDateRange(?string $dateDepart, ?string $dateRetour, ?int $excludeConvoiId = null): array
    {
        $query = Convoi::whereNotNull('vehicule_id')
            ->whereIn('statut', ['paye', 'en_cours']);

        if ($excludeConvoiId) {
            $query->where('id', '!=', $excludeConvoiId);
        }

        if ($dateDepart && $dateRetour) {
            $query->where(function ($q) use ($dateDepart, $dateRetour) {
                $q->where('date_depart', '<=', $dateRetour)
                  ->where('date_retour', '>=', $dateDepart);
            });
        }

        return $query->pluck('vehicule_id')->toArray();
    }

    public function index(Request $request)
    {
        $gare = Auth::guard('gare')->user();
        $statut = $request->query('statut', 'all');

        $query = Convoi::with(['user', 'compagnie', 'itineraire', 'chauffeur', 'vehicule'])
            ->withCount('passagers')
            ->where('gare_id', $gare->id)
            ->latest();

        if (in_array($statut, ['paye', 'en_cours', 'termine', 'annule'])) {
            $query->where('statut', $statut);
        }

        $convois = $query->paginate(12)->withQueryString();

        // Pour le dropdown d'affectation, on récupère les chauffeurs/vehicules disponibles
        // On ne peut pas filtrer par date ici car chaque convoi a ses propres dates
        // Le vrai check se fait dans assign()
        $busyPersonnelIds = Voyage::where('statut', 'en_cours')
            ->whereNotNull('personnel_id')
            ->pluck('personnel_id');

        $busyVehiculeIds = Voyage::where('statut', 'en_cours')
            ->whereNotNull('vehicule_id')
            ->pluck('vehicule_id');

        $chauffeurs = Personnel::where('gare_id', $gare->id)
            ->where('type_personnel', 'chauffeur')
            ->where('statut', 'disponible')
            ->whereNull('archived_at')
            ->whereNotIn('id', $busyPersonnelIds)
            ->orderBy('prenom')
            ->orderBy('name')
            ->get(['id', 'name', 'prenom']);

        $vehicules = Vehicule::where('gare_id', $gare->id)
            ->where('is_active', true)
            ->where('statut', 'disponible')
            ->whereNotIn('id', $busyVehiculeIds)
            ->orderBy('immatriculation')
            ->get(['id', 'immatriculation', 'modele', 'nombre_place']);

        return view('gare-espace.convois.index', compact('convois', 'statut', 'chauffeurs', 'vehicules'));
    }

    public function show(Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id) {
            abort(403);
        }

        $convoi->load(['user', 'compagnie', 'itineraire', 'passagers', 'chauffeur', 'vehicule', 'latestLocation']);

        return view('gare-espace.convois.show', compact('convoi'));
    }

    public function location(Convoi $convoi): JsonResponse
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        $convoi->load(['latestLocation', 'chauffeur', 'vehicule', 'itineraire', 'gare']);
        $location = $convoi->latestLocation;

        return response()->json([
            'success' => true,
            'convoi_id' => $convoi->id,
            'statut' => $convoi->statut,
            'latitude' => $location ? (float) $location->latitude : null,
            'longitude' => $location ? (float) $location->longitude : null,
            'speed' => $location ? $location->speed : null,
            'heading' => $location ? $location->heading : null,
            'last_update' => $location ? $location->updated_at->diffForHumans() : 'Jamais',
            'chauffeur' => $convoi->chauffeur ? trim(($convoi->chauffeur->prenom ?? '') . ' ' . ($convoi->chauffeur->name ?? '')) : 'Inconnu',
            'vehicule' => $convoi->vehicule->immatriculation ?? 'N/A',
            'trajet' => $convoi->itineraire ? ($convoi->itineraire->point_depart . ' -> ' . $convoi->itineraire->point_arrive) : '-',
            'gare' => $convoi->gare->nom_gare ?? '-',
        ]);
    }

    public function assign(Request $request, Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id) {
            abort(403);
        }

        $validated = $request->validate([
            'personnel_id' => 'required|exists:personnels,id',
            'vehicule_id' => 'required|exists:vehicules,id',
        ]);

        // Vérifier que le chauffeur n'a pas un voyage en_cours maintenant
        $chauffeurOnActiveVoyage = Voyage::where('personnel_id', $validated['personnel_id'])
            ->where('statut', 'en_cours')
            ->exists();
        if ($chauffeurOnActiveVoyage) {
            return back()->with('error', 'Ce chauffeur est actuellement en course (voyage en cours).');
        }

        // Vérifier chevauchement de dates avec d'autres convois
        $busyPersonnel = $this->busyPersonnelIdsForDateRange($convoi->date_depart, $convoi->date_retour);
        if (in_array((int) $validated['personnel_id'], $busyPersonnel, true)) {
            return back()->with('error', 'Ce chauffeur est déjà assigné à un autre convoi sur cette période.');
        }

        $busyVehicules = $this->busyVehiculeIdsForDateRange($convoi->date_depart, $convoi->date_retour);
        if (in_array((int) $validated['vehicule_id'], $busyVehicules, true)) {
            return back()->with('error', 'Ce véhicule est déjà assigné à un autre convoi sur cette période.');
        }

        // Vérifier chevauchement avec voyages programmés sur les mêmes dates
        $voyageDates = $this->getDateRange($convoi->date_depart, $convoi->date_retour);
        $chauffeurBusyVoyage = Voyage::where('personnel_id', $validated['personnel_id'])
            ->whereIn('date_voyage', $voyageDates)
            ->whereNotIn('statut', ['annulé', 'terminé'])
            ->exists();
        if ($chauffeurBusyVoyage) {
            return back()->with('error', 'Ce chauffeur a des voyages programmés pendant la période du convoi.');
        }

        $vehiculeBusyVoyage = Voyage::where('vehicule_id', $validated['vehicule_id'])
            ->whereIn('date_voyage', $voyageDates)
            ->whereNotIn('statut', ['annulé', 'terminé'])
            ->exists();
        if ($vehiculeBusyVoyage) {
            return back()->with('error', 'Ce véhicule a des voyages programmés pendant la période du convoi.');
        }

        $convoi->update([
            'personnel_id' => $validated['personnel_id'],
            'vehicule_id'  => $validated['vehicule_id'],
        ]);

        // PAS de changement de statut personnel/vehicule — la disponibilité est gérée par dates

        try {
            $chauffeur = Personnel::find($validated['personnel_id']);
            if ($chauffeur) {
                $convoi->loadMissing(['itineraire', 'vehicule']);

                $depart  = $convoi->lieu_depart  ?? ($convoi->itineraire->point_depart  ?? 'N/A');
                $arrivee = $convoi->lieu_retour   ?? ($convoi->itineraire->point_arrive  ?? 'N/A');
                $route   = "{$depart} → {$arrivee}";

                $chauffeur->notify(new ConvoiAssignedNotification($convoi));

                if ($chauffeur->fcm_token) {
                    $fcmService = app(\App\Services\FcmService::class);
                    $title = 'Nouveau Convoi Assigné';
                    $body  = "Référence : " . ($convoi->reference ?? '-') . "\n"
                           . "Trajet : {$route}\n"
                           . "Passagers : " . ($convoi->nombre_personnes ?? 0) . "\n"
                           . "Départ : " . ($convoi->date_depart ? Carbon::parse($convoi->date_depart)->format('d/m/Y') : '-') . " à " . ($convoi->heure_depart ?? '-');

                    $fcmService->sendNotification(
                        $chauffeur->fcm_token,
                        $title,
                        $body,
                        [
                            'type'      => 'convoi_assigned',
                            'convoi_id' => (string) $convoi->id,
                            'reference' => (string) ($convoi->reference ?? ''),
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur notification convoi (GareConvoiController@assign): ' . $e->getMessage());
        }

        return back()->with('success', 'Affectation effectuée avec succès.');
    }

    /** Modifier l'affectation (changer chauffeur / véhicule) */
    public function reassign(Request $request, Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();
        if ($convoi->gare_id !== $gare->id) abort(403);
        if ($convoi->statut !== 'paye') {
            return back()->with('error', 'Modification impossible : le convoi n\'est plus en attente d\'affectation.');
        }

        $validated = $request->validate([
            'personnel_id' => 'required|exists:personnels,id',
            'vehicule_id'  => 'required|exists:vehicules,id',
        ]);

        // Vérifier chevauchement de dates (exclure le convoi courant)
        $busyPersonnel = $this->busyPersonnelIdsForDateRange($convoi->date_depart, $convoi->date_retour, $convoi->id);
        if (in_array((int) $validated['personnel_id'], $busyPersonnel, true)) {
            return back()->with('error', 'Ce chauffeur est déjà assigné à un autre convoi sur cette période.');
        }

        $busyVehicules = $this->busyVehiculeIdsForDateRange($convoi->date_depart, $convoi->date_retour, $convoi->id);
        if (in_array((int) $validated['vehicule_id'], $busyVehicules, true)) {
            return back()->with('error', 'Ce véhicule est déjà assigné à un autre convoi sur cette période.');
        }

        // Vérifier chevauchement avec voyages programmés
        $voyageDates = $this->getDateRange($convoi->date_depart, $convoi->date_retour);
        if (!empty($voyageDates)) {
            $chauffeurBusyVoyage = Voyage::where('personnel_id', $validated['personnel_id'])
                ->whereIn('date_voyage', $voyageDates)
                ->whereNotIn('statut', ['annulé', 'terminé'])
                ->exists();
            if ($chauffeurBusyVoyage) {
                return back()->with('error', 'Ce chauffeur a des voyages programmés pendant la période du convoi.');
            }

            $vehiculeBusyVoyage = Voyage::where('vehicule_id', $validated['vehicule_id'])
                ->whereIn('date_voyage', $voyageDates)
                ->whereNotIn('statut', ['annulé', 'terminé'])
                ->exists();
            if ($vehiculeBusyVoyage) {
                return back()->with('error', 'Ce véhicule a des voyages programmés pendant la période du convoi.');
            }
        }

        $convoi->update([
            'personnel_id' => $validated['personnel_id'],
            'vehicule_id'  => $validated['vehicule_id'],
        ]);

        // Notifier le nouveau chauffeur
        try {
            $chauffeur = Personnel::find($validated['personnel_id']);
            if ($chauffeur) {
                $convoi->loadMissing(['itineraire', 'vehicule']);
                $chauffeur->notify(new ConvoiAssignedNotification($convoi));

                if ($chauffeur->fcm_token) {
                    $depart  = $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? 'N/A');
                    $arrivee = $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? 'N/A');

                    $fcmService = app(\App\Services\FcmService::class);
                    $fcmService->sendNotification(
                        $chauffeur->fcm_token,
                        'Convoi Réassigné',
                        "Référence : " . ($convoi->reference ?? '-') . "\nTrajet : {$depart} → {$arrivee}\nPassagers : " . ($convoi->nombre_personnes ?? 0),
                        ['type' => 'convoi_assigned', 'convoi_id' => (string) $convoi->id, 'reference' => (string) ($convoi->reference ?? '')]
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur notification convoi (reassign): ' . $e->getMessage());
        }

        return back()->with('success', 'Affectation modifiée avec succès.');
    }

    /** Annuler l'affectation pour reprogrammer */
    public function unassign(Request $request, Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();
        if ($convoi->gare_id !== $gare->id) abort(403);
        if ($convoi->statut !== 'paye') {
            return back()->with('error', 'Impossible d\'annuler l\'affectation : le convoi est déjà en cours ou terminé.');
        }

        $convoi->update([
            'personnel_id' => null,
            'vehicule_id'  => null,
        ]);

        return back()->with('success', 'Affectation annulée. Le convoi peut être réaffecté.');
    }

    /**
     * Générer un tableau de dates entre date_depart et date_retour (inclus).
     */
    private function getDateRange(?string $dateDepart, ?string $dateRetour): array
    {
        if (!$dateDepart) return [];

        $start = Carbon::parse($dateDepart);
        $end   = $dateRetour ? Carbon::parse($dateRetour) : $start->copy();
        $dates = [];

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $dates[] = $d->format('Y-m-d');
        }

        return $dates;
    }
}
