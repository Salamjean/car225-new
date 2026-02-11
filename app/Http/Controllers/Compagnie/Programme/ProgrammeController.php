<?php

namespace App\Http\Controllers\Compagnie\Programme;

use App\Http\Controllers\Controller;
use App\Models\Itineraire;
use App\Models\Personnel;
use App\Models\Programme;
use App\Models\ProgrammeHistorique;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProgrammeController extends Controller
{
    /**
     * Liste des lignes de transport - Groupé par route
     */
    public function index()
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;

        $programmes = Programme::with(['vehicule', 'chauffeur', 'convoyeur', 'itineraire'])
            ->where('compagnie_id', $compagnieId)
            ->where('statut', '!=', 'annule')
            ->orderBy('heure_depart', 'asc')
            ->get();

        // Grouper par itinéraire (route bidirectionnelle)
        $groupedProgrammes = $programmes->groupBy('itineraire_id')->map(function($group) {
            $first = $group->first();
            $itineraire = $first->itineraire;
            
            if (!$itineraire) return null;
            
            // Séparer aller (même sens que l'itinéraire) et retour (sens inversé)
            $aller = $group->filter(fn($p) => $p->point_depart == $itineraire->point_depart)
                           ->sortBy('heure_depart')
                           ->values();
            $retour = $group->filter(fn($p) => $p->point_depart != $itineraire->point_depart)
                            ->sortBy('heure_depart')
                            ->values();
            
            return (object)[
                'itineraire_id' => $first->itineraire_id,
                'itineraire' => $itineraire,
                'point_depart' => $itineraire->point_depart,
                'point_arrive' => $itineraire->point_arrive,
                'montant_billet' => $first->montant_billet,
                'durer_parcours' => $first->durer_parcours,
                'date_depart' => $first->date_depart,
                'date_fin' => $first->date_fin,
                'aller' => $aller,
                'retour' => $retour,
                'total_horaires' => $aller->count() + $retour->count(),
                'statut' => $group->contains('statut', 'actif') ? 'actif' : 'annule',
            ];
        })->filter()->values();

         // AJOUTEZ CECI AVANT LE RETURN VIEW :
    $vehicules = Vehicule::where('compagnie_id', $compagnieId)->where('is_active', true)->get();
    $chauffeurs = Personnel::where('compagnie_id', $compagnieId)->get(); // Filtrez par rôle si nécessaire

    // Modifiez le return pour inclure vehicules et chauffeurs
    return view('compagnie.programme.index', compact('groupedProgrammes', 'programmes', 'vehicules', 'chauffeurs'));
    }

    /**
     * Historique des programmes passés
     */
    public function history()
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;
        $today = Carbon::now()->format('Y-m-d');

        $logs = ProgrammeHistorique::orderBy('created_at', 'desc')->paginate(10, ['*'], 'logs_page');

        $programmesExpires = Programme::with(['vehicule', 'itineraire'])
            ->where('compagnie_id', $compagnieId)
            ->where('date_fin', '<', $today)
            ->orderBy('date_depart', 'desc')
            ->paginate(10, ['*'], 'prog_page');

        return view('compagnie.programme.historique', compact('logs', 'programmesExpires'));
    }

    /**
     * Supprimer un programme
     */
    public function destroy($id)
    {
        try {
            $compagnieId = Auth::guard('compagnie')->user()->id;
            $programme = Programme::where('id', $id)->where('compagnie_id', $compagnieId)->firstOrFail();

            if ($programme->reservations()->where('statut', 'confirmee')->count() > 0) {
                return back()->with('error', 'Impossible de supprimer cette ligne car elle contient des réservations.');
            }

            $programme->delete();

            return back()->with('success', 'Ligne supprimée avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur suppression ligne : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Formulaire d'édition
     */
    public function edit($id)
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;
        
        $programme = Programme::where('id', $id)->where('compagnie_id', $compagnieId)->firstOrFail();
        $itineraires = Itineraire::where('compagnie_id', $compagnieId)->get();

        return view('compagnie.programme.edit', compact('programme', 'itineraires'));
    }

    /**
     * Mettre à jour une ligne
     */
    public function update(Request $request, $id)
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;
        $programme = Programme::where('id', $id)->where('compagnie_id', $compagnieId)->firstOrFail();

        $validated = $request->validate([
            'montant_billet' => 'required|numeric|min:0',
            'date_fin' => 'required|date|after_or_equal:date_depart',
            'statut' => 'required|in:actif,annule',
        ]);

        $programme->update($validated);

        return redirect()->route('programme.index')->with('success', 'Ligne mise à jour avec succès');
    }

    /**
     * Formulaire de création
     */
    public function create(Request $request)
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;
        $itineraires = Itineraire::where('compagnie_id', $compagnieId)->get();
        // Fetch vehicles and drivers
        $vehicules = Vehicule::where('compagnie_id', $compagnieId)->where('is_active', true)->get();
        $chauffeurs = Personnel::where('compagnie_id', $compagnieId)->get(); // Filter by type if needed

        $existingAller = [];
        $existingRetour = [];
        $preselectedItineraireId = $request->get('itineraire_id');

        $existingMontantBillet = null;

        if ($preselectedItineraireId) {
            $programmes = Programme::where('compagnie_id', $compagnieId)
                ->where('itineraire_id', $preselectedItineraireId)
                ->where('statut', 'actif')
                ->orderBy('heure_depart')
                ->get();
                
            if ($programmes->count() > 0) {
                // Récupérer le prix du premier programme trouvé
                $existingMontantBillet = $programmes->first()->montant_billet;

                // Trouver l'itinéraire pour savoir le sens
                $itineraire = Itineraire::find($preselectedItineraireId);
                if ($itineraire) {
                    foreach ($programmes as $prog) {
                        $p = [
                            'heure_depart' => date('H:i', strtotime($prog->heure_depart)),
                            'heure_arrive' => date('H:i', strtotime($prog->heure_arrive))
                        ];
                        
                        if ($prog->point_depart == $itineraire->point_depart) {
                            $existingAller[] = $p;
                        } else {
                            $existingRetour[] = $p;
                        }
                    }
                }
            }
        }

        return view('compagnie.programme.create', compact('itineraires', 'existingAller', 'existingRetour', 'preselectedItineraireId', 'existingMontantBillet', 'vehicules', 'chauffeurs'));
    }

    /**
     * Parse duration string to minutes
     */
    private function parseDuration($durationStr)
    {
         if (!$durationStr) return 90;
         
         $hours = 0;
         $minutes = 0;
         
         // PHP Implementation
         if (preg_match('/(\d+)\s*heure/i', $durationStr, $matches)) {
             $hours = (int)$matches[1];
         }
         if (preg_match('/(\d+)\s*min/i', $durationStr, $matches)) {
             $minutes = (int)$matches[1];
         }
         
         return ($hours * 60) + $minutes;
    }

    /**
     * Check if driver is available
     */
    private function isDriverAvailable($personnelId, $heureDepart, $dureeMinutes, $excludeProgrammeId = null)
    {
        if (!$personnelId) return true;

        $newStart = Carbon::parse($heureDepart);
        $newEnd = $newStart->copy()->addMinutes($dureeMinutes);

        // Fetch all active programmes for this driver
        $programmes = Programme::where('personnel_id', $personnelId)
            ->where('statut', 'actif')
            ->when($excludeProgrammeId, function($q) use ($excludeProgrammeId) {
                return $q->where('id', '!=', $excludeProgrammeId);
            })
            ->get();

        foreach ($programmes as $prog) {
            // Calculate program times
            // Need to parse its duration or use heure_arrive if reliable (assuming same day)
            // Using heure_depart and calculate end seems safer if duration is consistent
            // But heure_arrive is stored.
            
            $progStart = Carbon::parse($prog->heure_depart);
            $progEnd = Carbon::parse($prog->heure_arrive);
            
            // Handle day crossing if needed (assuming simpel day time for now as per context)
            if ($progEnd->lt($progStart)) {
                $progEnd->addDay();
            }
            
            // Check overlap
            // (StartA < EndB) and (EndA > StartB)
            if ($newStart->lt($progEnd) && $newEnd->gt($progStart)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Créer des programmes de transport - Aller/Retour avec multiples horaires
     */
    public function store(Request $request)
    {
        Log::info('Création programmes avec multiples horaires', ['data' => $request->all()]);

        $validated = $request->validate([
            'itineraire_id' => 'required|exists:itineraires,id',
            'montant_billet' => 'required|numeric|min:0',
            'aller_horaires' => 'required|array|min:1',
            'aller_horaires.*.heure_depart' => 'required|date_format:H:i',
            'aller_horaires.*.heure_arrive' => 'required|date_format:H:i',
            'aller_horaires.*.vehicule_id' => 'nullable|exists:vehicules,id',
            'aller_horaires.*.personnel_id' => 'nullable|exists:personnels,id',
            
            // Retour optionnel
            'with_retour' => 'nullable',
            'retour_horaires' => 'required_if:with_retour,on|array',
            'retour_horaires.*.heure_depart' => 'required_if:with_retour,on|date_format:H:i',
            'retour_horaires.*.heure_arrive' => 'required_if:with_retour,on|date_format:H:i',
            'retour_horaires.*.vehicule_id' => 'nullable|exists:vehicules,id',
            'retour_horaires.*.personnel_id' => 'nullable|exists:personnels,id',
        ]);

        $compagnieId = Auth::guard('compagnie')->user()->id;
        $itineraire = Itineraire::findOrFail($validated['itineraire_id']);

        // Parse duration
        $dureeMinutes = 0;
        if (preg_match('/(\d+)\s*heure/i', $itineraire->durer_parcours, $m)) $dureeMinutes += $m[1] * 60;
        if (preg_match('/(\d+)\s*min/i', $itineraire->durer_parcours, $m)) $dureeMinutes += $m[1];
        if ($dureeMinutes == 0) $dureeMinutes = 90; // Fallback

        // Dates par défaut: aujourd'hui jusqu'à fin d'année
        $dateDebut = Carbon::now()->format('Y-m-d');
        $dateFin = Carbon::now()->addYears(5)->format('Y-m-d');

        try {
            $createdAller = 0;
            $createdRetour = 0;

            // === CRÉER LES PROGRAMMES ALLER ===
            foreach ($validated['aller_horaires'] as $index => $horaire) {
                // Check driver availability
                if (!empty($horaire['personnel_id'])) {
                    if (!$this->isDriverAvailable($horaire['personnel_id'], $horaire['heure_depart'], $dureeMinutes)) {
                        return back()->withInput()->with('error', "Le chauffeur sélectionné pour l'horaire Aller " . ($index+1) . " (" . $horaire['heure_depart'] . ") est déjà occupé sur ce créneau.");
                    }
                }

                // Vérifier si ce programme existe déjà
                $exists = Programme::where('compagnie_id', $compagnieId)
                    ->where('itineraire_id', $itineraire->id)
                    ->where('point_depart', $itineraire->point_depart)
                    ->where('point_arrive', $itineraire->point_arrive)
                    ->where('heure_depart', $horaire['heure_depart'])
                    ->where('statut', 'actif')
                    ->exists();

                if (!$exists) {
                    Programme::create([
                        'compagnie_id' => $compagnieId,
                        'itineraire_id' => $itineraire->id,
                        'vehicule_id' => $horaire['vehicule_id'] ?? null,
                        'personnel_id' => $horaire['personnel_id'] ?? null,
                        'convoyeur_id' => null,
                        'point_depart' => $itineraire->point_depart,
                        'point_arrive' => $itineraire->point_arrive,
                        'durer_parcours' => $itineraire->durer_parcours,
                        'montant_billet' => $validated['montant_billet'],
                        'date_depart' => $dateDebut,
                        'date_fin' => $dateFin,
                        'heure_depart' => $horaire['heure_depart'],
                        'heure_arrive' => $horaire['heure_arrive'],
                        'statut' => 'actif',
                    ]);
                    $createdAller++;
                }
            }

            // === CRÉER LES PROGRAMMES RETOUR (points inversés) - SEULEMENT SI DEMANDÉ ===
            if ($request->has('with_retour') && !empty($validated['retour_horaires'])) {
                foreach ($validated['retour_horaires'] as $index => $horaire) {
                    // Check driver availability
                    if (!empty($horaire['personnel_id'])) {
                        if (!$this->isDriverAvailable($horaire['personnel_id'], $horaire['heure_depart'], $dureeMinutes)) {
                            return back()->withInput()->with('error', "Le chauffeur sélectionné pour l'horaire Retour " . ($index+1) . " (" . $horaire['heure_depart'] . ") est déjà occupé sur ce créneau.");
                        }
                    }

                    // Vérifier si ce programme existe déjà
                    $exists = Programme::where('compagnie_id', $compagnieId)
                        ->where('itineraire_id', $itineraire->id)
                        ->where('point_depart', $itineraire->point_arrive) // Inversé
                        ->where('point_arrive', $itineraire->point_depart) // Inversé
                        ->where('heure_depart', $horaire['heure_depart'])
                        ->where('statut', 'actif')
                        ->exists();

                    if (!$exists) {
                        Programme::create([
                            'compagnie_id' => $compagnieId,
                            'itineraire_id' => $itineraire->id,
                            'vehicule_id' => $horaire['vehicule_id'] ?? null,
                            'personnel_id' => $horaire['personnel_id'] ?? null,
                            'convoyeur_id' => null,
                            'point_depart' => $itineraire->point_arrive, // Inversé
                            'point_arrive' => $itineraire->point_depart, // Inversé
                            'durer_parcours' => $itineraire->durer_parcours,
                            'montant_billet' => $validated['montant_billet'],
                            'date_depart' => $dateDebut,
                            'date_fin' => $dateFin,
                            'heure_depart' => $horaire['heure_depart'],
                            'heure_arrive' => $horaire['heure_arrive'],
                            'statut' => 'actif',
                        ]);
                        $createdRetour++;
                    }
                }
            }

            $total = $createdAller + $createdRetour;

            if ($total === 0) {
                return back()->withInput()->with('error', 
                    'Tous les programmes existent déjà pour cet itinéraire avec ces horaires.');
            }

            return redirect()->route('programme.index')
                ->with('success', "{$total} programme(s) créé(s) : {$createdAller} aller + {$createdRetour} retour pour {$itineraire->point_depart} ↔ {$itineraire->point_arrive}");

        } catch (\Exception $e) {
            Log::error('Erreur création programmes: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur système : ' . $e->getMessage());
        }
    }

    /**
     * Annuler un programme
     */
    public function annuler(Request $request, $id)
    {
        try {
            $compagnieId = Auth::guard('compagnie')->user()->id;
            $programme = Programme::where('id', $id)->where('compagnie_id', $compagnieId)->firstOrFail();

            $programme->update(['statut' => 'annule']);

            return response()->json([
                'success' => true,
                'message' => 'Ligne annulée avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur annulation', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation'
            ], 500);
        }
    }
}
