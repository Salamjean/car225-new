<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Models\Itineraire;
use App\Models\Programme;
use App\Models\Gare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GareProgrammeController extends Controller
{
    /**
     * Liste des programmes ALLER au départ de cette gare uniquement
     */
    public function index()
    {
        $gare        = Auth::guard('gare')->user();
        $compagnieId = $gare->compagnie_id;

        // Uniquement les programmes où cette gare est le point de DÉPART
        $programmes = Programme::with(['gareDepart', 'gareArrivee', 'itineraire'])
            ->where('compagnie_id', $compagnieId)
            ->where('gare_depart_id', $gare->id)
            ->where('statut', '!=', 'annule')
            ->orderBy('heure_depart', 'asc')
            ->get();

        // Grouper par itinéraire
        $groupedProgrammes = $programmes->groupBy('itineraire_id')->map(function ($group) {
            $first      = $group->first();
            $itineraire = $first->itineraire;

            if (!$itineraire) return null;

            $horaires = $group->sortBy('heure_depart')->values();

            return (object) [
                'itineraire_id'  => $first->itineraire_id,
                'itineraire'     => $itineraire,
                'gare_depart'    => $first->gareDepart,
                'gare_arrivee'   => $first->gareArrivee,
                'montant_billet' => $first->montant_billet,
                'durer_parcours' => $first->durer_parcours,
                'horaires'       => $horaires,
                'statut'         => $group->contains('statut', 'actif') ? 'actif' : 'annule',
            ];
        })->filter()->values();

        return view('gare-espace.programme.index', compact('groupedProgrammes', 'programmes', 'gare'));
    }

    /**
     * Formulaire de création — uniquement les itinéraires de la gare, départ fixé
     */
    public function create(Request $request)
    {
        $gare        = Auth::guard('gare')->user();
        $compagnieId = $gare->compagnie_id;

        // Uniquement les itinéraires liés à cette gare (gare_id = $gare->id)
        $itineraires = Itineraire::where('gare_id', $gare->id)
            ->where('compagnie_id', $compagnieId)
            ->get();

        // Gares d'arrivée disponibles : toutes les gares de la compagnie sauf la gare actuelle
        $garesArrivee = Gare::where('compagnie_id', $compagnieId)
            ->where('id', '!=', $gare->id)
            ->get();

        $existingAller           = [];
        $preselectedItineraireId = $request->get('itineraire_id');
        $existingMontantBillet   = null;

        if ($preselectedItineraireId) {
            $programmes = Programme::where('compagnie_id', $compagnieId)
                ->where('itineraire_id', $preselectedItineraireId)
                ->where('gare_depart_id', $gare->id)
                ->where('statut', 'actif')
                ->orderBy('heure_depart')
                ->get();

            if ($programmes->count() > 0) {
                $existingMontantBillet = $programmes->first()->montant_billet;
                foreach ($programmes as $prog) {
                    $existingAller[] = [
                        'heure_depart' => date('H:i', strtotime($prog->heure_depart)),
                        'heure_arrive' => date('H:i', strtotime($prog->heure_arrive)),
                    ];
                }
            }
        }

        return view('gare-espace.programme.create', compact(
            'itineraires', 'garesArrivee', 'gare',
            'existingAller', 'preselectedItineraireId', 'existingMontantBillet'
        ));
    }

    /**
     * Enregistrer les programmes ALLER uniquement, départ = gare authentifiée
     */
    public function store(Request $request)
    {
        $gareAuth    = Auth::guard('gare')->user();
        $compagnieId = $gareAuth->compagnie_id;

        $validated = $request->validate([
            'itineraire_id'                  => 'required|exists:itineraires,id',
            'gare_arrivee_id'                => 'required|exists:gares,id',
            'montant_billet'                 => 'required|numeric|min:0',
            'capacity'                       => 'required|integer|min:1',
            'aller_horaires'                 => 'required|array|min:1',
            'aller_horaires.*.heure_depart'  => 'required|date_format:H:i',
            'aller_horaires.*.heure_arrive'  => 'required|date_format:H:i',
        ]);

        // Sécurité : empêcher d'arriver à la même gare que le départ
        if ($validated['gare_arrivee_id'] == $gareAuth->id) {
            return back()->withInput()->with('error', 'La gare d\'arrivée doit être différente de votre gare.');
        }

        // Vérifier que l'itinéraire appartient bien à cette gare
        $itineraire = Itineraire::where('id', $validated['itineraire_id'])
            ->where('gare_id', $gareAuth->id)
            ->where('compagnie_id', $compagnieId)
            ->firstOrFail();

        $dateDebut = Carbon::now()->format('Y-m-d');
        $dateFin   = Carbon::now()->addYears(5)->format('Y-m-d');

        try {
            $created = 0;

            foreach ($validated['aller_horaires'] as $horaire) {
                $exists = Programme::where('compagnie_id', $compagnieId)
                    ->where('itineraire_id', $itineraire->id)
                    ->where('gare_depart_id', $gareAuth->id)
                    ->where('gare_arrivee_id', $validated['gare_arrivee_id'])
                    ->where('heure_depart', $horaire['heure_depart'])
                    ->where('statut', 'actif')
                    ->exists();

                if (!$exists) {
                    Programme::create([
                        'compagnie_id'    => $compagnieId,
                        'itineraire_id'   => $itineraire->id,
                        'gare_depart_id'  => $gareAuth->id,   // Toujours la gare connectée
                        'gare_arrivee_id' => $validated['gare_arrivee_id'],
                        'point_depart'    => $itineraire->point_depart,
                        'point_arrive'    => $itineraire->point_arrive,
                        'durer_parcours'  => $itineraire->durer_parcours,
                        'montant_billet'  => $validated['montant_billet'],
                        'date_depart'     => $dateDebut,
                        'date_fin'        => $dateFin,
                        'heure_depart'    => $horaire['heure_depart'],
                        'heure_arrive'    => $horaire['heure_arrive'],
                        'capacity'        => $validated['capacity'],
                        'statut'          => 'actif',
                    ]);
                    $created++;
                }
            }

            if ($created === 0) {
                return back()->withInput()->with('error',
                    'Tous ces horaires existent déjà pour cet itinéraire.');
            }

            return redirect()->route('gare-espace.programme.index')
                ->with('success', "{$created} programme(s) créé(s) avec succès pour {$itineraire->point_depart} → {$itineraire->point_arrive}");

        } catch (\Exception $e) {
            Log::error('Erreur création programmes gare: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur système : ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour une route complète :
     * - montant_billet et capacity pour tous les programmes de la route
     * - horaires existants (heure_depart / heure_arrive)
     * - nouveaux horaires à ajouter
     */
    public function updateRoute(Request $request)
    {
        $gareAuth    = Auth::guard('gare')->user();
        $compagnieId = $gareAuth->compagnie_id;

        $request->validate([
            'itineraire_id'                         => 'required|exists:itineraires,id',
            'gare_arrivee_id'                       => 'required|exists:gares,id',
            'montant_billet'                        => 'required|numeric|min:0',
            'capacity'                              => 'required|integer|min:1',
            'existing_horaires'                     => 'nullable|array',
            'existing_horaires.*.heure_depart'      => 'required|date_format:H:i',
            'existing_horaires.*.heure_arrive'      => 'required|date_format:H:i',
            'new_horaires'                          => 'nullable|array',
            'new_horaires.*.heure_depart'           => 'required|date_format:H:i',
            'new_horaires.*.heure_arrive'           => 'required|date_format:H:i',
        ]);

        try {
            // Mettre à jour les horaires existants
            if (!empty($request->existing_horaires)) {
                foreach ($request->existing_horaires as $progId => $horaire) {
                    $programme = Programme::where('id', $progId)
                        ->where('compagnie_id', $compagnieId)
                        ->where('gare_depart_id', $gareAuth->id)
                        ->first();

                    if ($programme) {
                        $programme->update([
                            'heure_depart'   => $horaire['heure_depart'],
                            'heure_arrive'   => $horaire['heure_arrive'],
                            'montant_billet' => $request->montant_billet,
                            'capacity'       => $request->capacity,
                        ]);
                    }
                }
            } else {
                // Pas d'horaires existants dans le payload = mettre à jour le montant/capacité seulement
                Programme::where('compagnie_id', $compagnieId)
                    ->where('itineraire_id', $request->itineraire_id)
                    ->where('gare_depart_id', $gareAuth->id)
                    ->where('gare_arrivee_id', $request->gare_arrivee_id)
                    ->where('statut', 'actif')
                    ->update([
                        'montant_billet' => $request->montant_billet,
                        'capacity'       => $request->capacity,
                    ]);
            }

            // Ajouter les nouveaux horaires
            $created = 0;
            if (!empty($request->new_horaires)) {
                $itineraire = Itineraire::findOrFail($request->itineraire_id);
                $dateDebut  = Carbon::now()->format('Y-m-d');
                $dateFin    = Carbon::now()->addYears(5)->format('Y-m-d');

                foreach ($request->new_horaires as $horaire) {
                    $exists = Programme::where('compagnie_id', $compagnieId)
                        ->where('itineraire_id', $itineraire->id)
                        ->where('gare_depart_id', $gareAuth->id)
                        ->where('gare_arrivee_id', $request->gare_arrivee_id)
                        ->where('heure_depart', $horaire['heure_depart'])
                        ->where('statut', 'actif')
                        ->exists();

                    if (!$exists) {
                        Programme::create([
                            'compagnie_id'    => $compagnieId,
                            'itineraire_id'   => $itineraire->id,
                            'gare_depart_id'  => $gareAuth->id,
                            'gare_arrivee_id' => $request->gare_arrivee_id,
                            'point_depart'    => $itineraire->point_depart,
                            'point_arrive'    => $itineraire->point_arrive,
                            'durer_parcours'  => $itineraire->durer_parcours,
                            'montant_billet'  => $request->montant_billet,
                            'date_depart'     => $dateDebut,
                            'date_fin'        => $dateFin,
                            'heure_depart'    => $horaire['heure_depart'],
                            'heure_arrive'    => $horaire['heure_arrive'],
                            'capacity'        => $request->capacity,
                            'statut'          => 'actif',
                        ]);
                        $created++;
                    }
                }
            }

            $msg = 'Route mise à jour avec succès.';
            if ($created > 0) $msg .= " {$created} nouvel(s) horaire(s) ajouté(s).";

            return redirect()->route('gare-espace.programme.index')->with('success', $msg);

        } catch (\Exception $e) {
            Log::error('Erreur updateRoute gare: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur système : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un programme (uniquement au départ de cette gare)
     */
    public function destroy($id)
    {
        try {
            $gare        = Auth::guard('gare')->user();
            $compagnieId = $gare->compagnie_id;

            $programme = Programme::where('id', $id)
                ->where('compagnie_id', $compagnieId)
                ->where('gare_depart_id', $gare->id)
                ->firstOrFail();

            if ($programme->reservations()->where('statut', 'confirmee')->count() > 0) {
                return back()->with('error', 'Impossible de supprimer ce programme : il contient des réservations confirmées.');
            }

            $programme->delete();
            return back()->with('success', 'Programme supprimé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur suppression programme gare : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Annuler un programme (AJAX)
     */
    public function annuler(Request $request, $id)
    {
        try {
            $gare        = Auth::guard('gare')->user();
            $compagnieId = $gare->compagnie_id;

            $programme = Programme::where('id', $id)
                ->where('compagnie_id', $compagnieId)
                ->where('gare_depart_id', $gare->id)
                ->firstOrFail();

            $programme->update(['statut' => 'annule']);

            return response()->json(['success' => true, 'message' => 'Programme annulé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'annulation'], 500);
        }
    }
}
