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
    public function index()
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;
        $today = Carbon::now()->format('Y-m-d');

        $programmes = Programme::with(['vehicule', 'chauffeur', 'convoyeur', 'itineraire'])
            ->where('compagnie_id', $compagnieId)
            ->withCount(['reservationsAller' => function ($query) {
                $query->where('statut', '!=', 'annulee');
            }])
            ->withCount(['reservationsRetour' => function ($query) {
                $query->where('statut', '!=', 'annulee');
            }])
            ->where(function($query) use ($today) {
                $query->where(function($q) use ($today) {
                    $q->where('type_programmation', 'ponctuel')
                      ->where('date_depart', '>=', $today);
                })
                ->orWhere(function($q) use ($today) {
                    $q->where('type_programmation', 'recurrent')
                      ->where(function($sub) use ($today) {
                          $sub->where('date_fin_programmation', '>=', $today)
                              ->orWhereNull('date_fin_programmation');
                      });
                });
            })
            ->where(function($q) {
                $q->where('is_aller_retour', true)
                  ->orWhereNull('programme_retour_id');
            })
            ->orderBy('date_depart', 'asc')
            ->paginate(10);

        $programmes->getCollection()->transform(function ($programme) {
            $programme->total_reserves = $programme->reservations_aller_count + $programme->reservations_retour_count;
            
            $totalPlaces = $programme->vehicule ? $programme->vehicule->nombre_place : 0;
            if ($totalPlaces > 0) {
                if ($programme->total_reserves >= $totalPlaces) {
                    $programme->staut_place = 'rempli';
                } elseif ($programme->total_reserves >= ($totalPlaces * 0.8)) {
                    $programme->staut_place = 'presque_complet';
                } else {
                    $programme->staut_place = 'vide';
                }
            }
            return $programme;
        });

        return view('compagnie.programme.index', compact('programmes'));
    }

    public function history()
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;
        $today = Carbon::now()->format('Y-m-d');

        $logs = ProgrammeHistorique::orderBy('created_at', 'desc')->paginate(10, ['*'], 'logs_page');

        $programmesExpires = Programme::with(['vehicule', 'itineraire'])
            ->where('compagnie_id', $compagnieId)
            ->where(function($query) use ($today) {
                $query->where(function($q) use ($today) {
                    $q->where('type_programmation', 'ponctuel')
                      ->where('date_depart', '<', $today);
                })
                ->orWhere(function($q) use ($today) {
                    $q->where('type_programmation', 'recurrent')
                      ->where('date_fin_programmation', '<', $today);
                });
            })
            ->orderBy('date_depart', 'desc')
            ->paginate(10, ['*'], 'prog_page');

        return view('compagnie.programme.historique', compact('logs', 'programmesExpires'));
    }

    public function destroy($id)
    {
        try {
            $compagnieId = Auth::guard('compagnie')->user()->id;
            $programme = Programme::where('id', $id)->where('compagnie_id', $compagnieId)->firstOrFail();

            if ($programme->reservations()->count() > 0) {
                return back()->with('error', 'Impossible de supprimer ce programme car il contient des réservations actives ou passées.');
            }

            $programme->delete();

            return back()->with('success', 'Programme supprimé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur suppression programme : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    public function edit($id)
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;
        
        $programme = Programme::where('id', $id)->where('compagnie_id', $compagnieId)->firstOrFail();
        
        $itineraires = Itineraire::where('compagnie_id', $compagnieId)->get();
        $vehicules = Vehicule::where('compagnie_id', $compagnieId)
            ->where(function($q) use ($programme) {
                $q->where('is_active', true)->orWhere('id', $programme->vehicule_id);
            })->get();
            
        $chauffeurs = Personnel::where('compagnie_id', $compagnieId)
            ->where('type_personnel', 'Chauffeur')
            ->where('statut', 'disponible')
            ->orWhere('id', $programme->personnel_id)
            ->get();
            
        $convoyeurs = Personnel::where('compagnie_id', $compagnieId)
            ->where('type_personnel', 'Convoyeur')
            ->get();

        return view('compagnie.programme.edit', compact('programme', 'itineraires', 'vehicules', 'chauffeurs', 'convoyeurs'));
    }

    public function update(Request $request, $id)
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;
        $programme = Programme::where('id', $id)->where('compagnie_id', $compagnieId)->firstOrFail();

        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'personnel_id' => 'required|exists:personnels,id',
            'convoyeur_id' => 'nullable|exists:personnels,id',
            'montant_billet' => 'required|numeric|min:0',
            'heure_depart' => 'required',
        ]);

        $programme->update([
            'vehicule_id' => $validated['vehicule_id'],
            'personnel_id' => $validated['personnel_id'],
            'convoyeur_id' => $validated['convoyeur_id'],
            'montant_billet' => $validated['montant_billet'],
            'heure_depart' => $validated['heure_depart'],
        ]);

        ProgrammeHistorique::create([
            'programme_id' => $programme->id,
            'action' => 'modification_programme',
            'raison' => 'Mise à jour via formulaire d\'édition',
            'vehicule' => $programme->vehicule->immatriculation,
            'chauffeur' => $programme->chauffeur->name,
            'itineraire' => $programme->point_depart . ' - ' . $programme->point_arrive,
             'point_depart' => $programme->point_depart,
             'point_arrive' => $programme->point_arrive,
             'duree_parcours' => $programme->durer_parcours,
             'date_depart' => $programme->date_depart,
             'heure_depart' => $programme->heure_depart,
             'heure_arrivee' => $programme->heure_arrive,
        ]);

        return redirect()->route('programme.index')->with('success', 'Programme mis à jour avec succès');
    }

    public function showApi(Programme $programme)
    {
        Log::info('Appel API programme', ['programme_id' => $programme->id, 'user_id' => Auth::guard('compagnie')->user()->id]);

        try {
            $compagnieId = Auth::guard('compagnie')->user()->id;

            if ($programme->compagnie_id != $compagnieId) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            $programme->load(['vehicule', 'chauffeur', 'convoyeur']);
            $programme->loadCount(['reservationsAller', 'reservationsRetour']);

            if (!$programme->vehicule || !$programme->chauffeur) {
                return response()->json(['error' => 'Données incomplètes (Véhicule ou Chauffeur manquant)'], 404);
            }

            $totalOccupied = ($programme->reservations_aller_count ?? 0) + ($programme->reservations_retour_count ?? 0);
            
            $totalPlaces = $programme->vehicule->nombre_place;
            $statut = 'vide';
            if ($totalPlaces > 0) {
                if ($totalOccupied >= $totalPlaces) {
                    $statut = 'rempli';
                } elseif ($totalOccupied >= ($totalPlaces * 0.8)) {
                    $statut = 'presque_complet';
                }
            }

            $joursRecurrence = $programme->jours_recurrence;
            if (is_string($joursRecurrence) && !empty($joursRecurrence)) {
                try {
                    $joursRecurrence = json_decode($joursRecurrence, true);
                } catch (\Exception $e) {
                    $joursRecurrence = [];
                }
            }

            $data = [
                'id' => $programme->id,
                'point_depart' => $programme->point_depart,
                'point_arrive' => $programme->point_arrive,
                'durer_parcours' => $programme->durer_parcours,
                'date_depart' => $programme->date_depart,
                'date_depart_formatee' => \Carbon\Carbon::parse($programme->date_depart)->format('d/m/Y'),
                'heure_depart' => $programme->heure_depart,
                'heure_arrive' => $programme->heure_arrive,
                'nbre_siege_occupe' => $totalOccupied, 
                'staut_place' => $statut, 
                'montant_billet' => $programme->montant_billet,
                'type_programmation' => $programme->type_programmation,
                'is_aller_retour' => (bool) $programme->is_aller_retour,
                'date_fin_programmation' => $programme->date_fin_programmation,
                'jours_recurrence' => $joursRecurrence,
                'vehicule' => [
                    'marque' => $programme->vehicule->marque,
                    'modele' => $programme->vehicule->modele,
                    'immatriculation' => $programme->vehicule->immatriculation,
                    'nombre_place' => $programme->vehicule->nombre_place,
                ],
                'chauffeur' => [
                    'prenom' => $programme->chauffeur->prenom,
                    'name' => $programme->chauffeur->name,
                    'contact' => $programme->chauffeur->contact,
                ],
            ];

            if ($programme->convoyeur) {
                $data['convoyeur'] = [
                    'prenom' => $programme->convoyeur->prenom,
                    'name' => $programme->convoyeur->name,
                    'contact' => $programme->convoyeur->contact,
                ];
            } else {
                $data['convoyeur'] = null;
            }

            if ($programme->is_aller_retour && $programme->programme_retour_id) {
                $programmeRetour = Programme::find($programme->programme_retour_id);
                if ($programmeRetour) {
                    $joursRecurrenceRetour = $programmeRetour->jours_recurrence;
                    if (is_string($joursRecurrenceRetour) && !empty($joursRecurrenceRetour)) {
                        try {
                            $joursRecurrenceRetour = json_decode($joursRecurrenceRetour, true);
                        } catch (\Exception $e) {
                            $joursRecurrenceRetour = [];
                        }
                    }

                    $data['retour_details'] = [
                        'date_depart' => \Carbon\Carbon::parse($programmeRetour->date_depart)->format('d/m/Y'),
                        'heure_depart' => $programmeRetour->heure_depart,
                        'heure_arrive' => $programmeRetour->heure_arrive,
                        'date_fin_programmation' => $programmeRetour->date_fin_programmation,
                        'jours_recurrence' => $joursRecurrenceRetour,
                        'type_programmation' => $programmeRetour->type_programmation,
                    ];
                }
            }

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Erreur API programme', [
                'programme_id' => $programme->id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    public function programmesDisponiblesParDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'point_depart' => 'nullable|string',
            'point_arrive' => 'nullable|string',
        ]);

        try {
            $compagnieId = Auth::guard('compagnie')->user()->id;
            $dateRecherche = $request->date;
            $jourSemaine = strtolower(date('l', strtotime($dateRecherche)));

            $query = Programme::with(['vehicule', 'chauffeur', 'itineraire'])
                ->where('compagnie_id', $compagnieId)
                ->where(function ($q) use ($dateRecherche, $jourSemaine) {
                    $q->where(function ($sub) use ($dateRecherche) {
                        $sub->where('type_programmation', 'ponctuel')
                            ->where('date_depart', $dateRecherche);
                    });
                    $q->orWhere(function ($sub) use ($dateRecherche, $jourSemaine) {
                        $sub->where('type_programmation', 'recurrent')
                            ->where('date_depart', '<=', $dateRecherche)
                            ->where(function ($dateSub) use ($dateRecherche) {
                                $dateSub->where('date_fin_programmation', '>=', $dateRecherche)
                                    ->orWhereNull('date_fin_programmation');
                            })
                            ->whereJsonContains('jours_recurrence', $jourSemaine);
                    });
                });

            if ($request->filled('point_depart')) {
                $query->where('point_depart', 'like', '%' . $request->point_depart . '%');
            }

            if ($request->filled('point_arrive')) {
                $query->where('point_arrive', 'like', '%' . $request->point_arrive . '%');
            }

            $query->orderBy('heure_depart', 'asc');

            $programmes = $query->get();

            $formattedProgrammes = $programmes->map(function ($programme) {
                return [
                    'id' => $programme->id,
                    'itineraire' => $programme->point_depart . ' → ' . $programme->point_arrive,
                    'date_depart' => \Carbon\Carbon::parse($programme->date_depart)->format('d/m/Y'),
                    'heure_depart' => $programme->heure_depart,
                    'heure_arrive' => $programme->heure_arrive,
                    'durer_parcours' => $programme->durer_parcours,
                    'vehicule' => $programme->vehicule ?
                        $programme->vehicule->marque . ' ' . $programme->vehicule->modele . ' (' . $programme->vehicule->immatriculation . ')' :
                        'Non défini',
                    'chauffeur' => $programme->chauffeur ?
                        $programme->chauffeur->prenom . ' ' . $programme->chauffeur->name :
                        'Non défini',
                    'type_programmation' => $programme->type_programmation,
                    'statut_places' => $programme->staut_place,
                    'sieges_occupes' => $programme->nbre_siege_occupe . '/' . ($programme->vehicule ? $programme->vehicule->nombre_place : 'N/A'),
                ];
            });

            return response()->json([
                'success' => true,
                'date_recherche' => $dateRecherche,
                'jour_semaine' => $jour_semaine ?? '',
                'nombre_programmes' => $programmes->count(),
                'programmes' => $formattedProgrammes,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur récupération programmes par date', [
                'date' => $request->date,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des programmes: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;
        $itineraires = Itineraire::where('compagnie_id', $compagnieId)->get();
        $vehicules = Vehicule::where('compagnie_id', $compagnieId)->where('is_active', true)->get();
        
        $chauffeurs = Personnel::where('compagnie_id', $compagnieId)
            ->where('type_personnel', 'Chauffeur')
            ->where('statut', 'disponible')
            ->get();
        $convoyeurs = Personnel::where('compagnie_id', $compagnieId)
            ->where('type_personnel', 'Convoyeur')
            ->where('statut', 'disponible')
            ->get();

        return view('compagnie.programme.create', compact('itineraires', 'vehicules', 'chauffeurs', 'convoyeurs'));
    }

    // --- CORRECTION ET OPTIMISATION DE LA METHODE STORE ---
 public function store(Request $request)
    {
        Log::info('Début création programme', ['data' => $request->all()]);

        // 1. Validation de base
        $rules = [
            'itineraire_id' => 'required|exists:itineraires,id',
            'vehicule_id' => 'required|exists:vehicules,id',
            'personnel_id' => 'required|exists:personnels,id',
            'convoyeur_id' => 'nullable|exists:personnels,id',
            'montant_billet' => 'required|numeric|min:0',
            'durer_parcours' => 'required|string',
            'date_depart' => 'required|date',
            'heure_depart' => 'required|string',
            'heure_arrive' => 'required|string',
            'type_programmation' => 'required|in:ponctuel,recurrent',
            'is_aller_retour' => 'boolean',
        ];

        if ($request->input('type_programmation') === 'recurrent') {
            $rules['date_fin_programmation'] = 'required|date|after_or_equal:date_depart';
            $rules['jours_recurrence'] = 'required|array';
        }

        if ($request->has('is_aller_retour')) {
            if ($request->input('type_programmation') === 'ponctuel') {
                $rules['retour_heure_depart'] = 'required|string';
            } else {
                $rules['jours_retour'] = 'required|array';
                $rules['retour_date_debut_recurrent'] = 'required|date';
                $rules['retour_heure_depart_recurrent'] = 'required|string';
            }
            // Véhicule/Chauffeur différent pour le retour (optionnel, pour trajets longs)
            $rules['retour_vehicule_id'] = 'nullable|exists:vehicules,id';
            $rules['retour_personnel_id'] = 'nullable|exists:personnels,id';
        }

        $validated = $request->validate($rules);
        $compagnieId = Auth::guard('compagnie')->user()->id;
        
        // Formatage strict
        $dateDepartFormatted = Carbon::parse($validated['date_depart'])->toDateString(); 

        // =========================================================================
        // 🔒 SÉCURITÉ RENFORCÉE : CONFLIT VÉHICULE & PERSONNEL
        // =========================================================================

         // Calcul de l'heure d'arrivée estimée du NOUVEAU programme
        $heureDebutNouveau = $validated['heure_depart'];
        $heureFinNouveau = $this->calculerHeureArrivee($validated['heure_depart'], $validated['durer_parcours']);

        // 1. CONFLIT VÉHICULE (Vérifie si le bus est sur la route pendant ce créneau)
        // On cherche un programme existant pour ce véhicule, ce jour-là...
        $conflitVehicule = Programme::where('compagnie_id', $compagnieId)
            ->where('vehicule_id', $validated['vehicule_id'])
            ->where('date_depart', 'like', $dateDepartFormatted . '%')
            ->where('type_programmation', $validated['type_programmation']) // Comparer ce qui est comparable
            ->where(function ($query) use ($heureDebutNouveau, $heureFinNouveau) {
                // Logique de chevauchement :
                // (Départ Existant < Fin Nouveau) ET (Fin Existant > Début Nouveau)
                $query->where('heure_depart', '<', $heureFinNouveau)
                      ->where('heure_arrive', '>', $heureDebutNouveau);
            })
            ->first();

        if ($conflitVehicule) {
            $vehicule = Vehicule::find($validated['vehicule_id']);
            $msg = "Le vehicule " . $vehicule->immatriculation . " est deja en circulation de " . $conflitVehicule->heure_depart . " a " . $conflitVehicule->heure_arrive . " sur le trajet " . $conflitVehicule->point_depart . " - " . $conflitVehicule->point_arrive . ".";
            
            return back()->withInput()->withErrors(['vehicule_id' => $msg])->with('error', $msg);
        }


        // 2. CONFLIT CHAUFFEUR (avec chevauchement horaire)
        $conflitChauffeur = Programme::where('compagnie_id', $compagnieId)
            ->where('personnel_id', $validated['personnel_id'])
            ->where('date_depart', 'like', $dateDepartFormatted . '%')
            ->where('type_programmation', $validated['type_programmation'])
            ->where(function ($query) use ($heureDebutNouveau, $heureFinNouveau) {
                // Même logique de chevauchement que pour véhicule
                $query->where('heure_depart', '<', $heureFinNouveau)
                      ->where('heure_arrive', '>', $heureDebutNouveau);
            })
            ->first();

        if ($conflitChauffeur) {
            $chauffeur = Personnel::find($validated['personnel_id']);
            $msg = "Le chauffeur " . $chauffeur->prenom . " " . $chauffeur->name . " est deja occupe de " . $conflitChauffeur->heure_depart . " a " . $conflitChauffeur->heure_arrive . ".";
            
            return back()->withInput()->withErrors(['personnel_id' => $msg])->with('error', $msg);
        }

        // 3. BUS MAGIQUE (Retour avant arrivée) - Pour ponctuel ET récurrent
        $heureArriveeAller = $this->calculerHeureArrivee($validated['heure_depart'], $validated['durer_parcours']);
        
        if ($request->has('is_aller_retour')) {
            // Déterminer l'heure de départ retour selon le type
            $heureDepartRetour = $validated['type_programmation'] === 'ponctuel' 
                ? ($validated['retour_heure_depart'] ?? null)
                : ($validated['retour_heure_depart_recurrent'] ?? null);
            
            // Vérifier si le véhicule retour est le même que l'aller
            $vehiculeRetourId = !empty($request->input('retour_vehicule_id')) 
                ? $request->input('retour_vehicule_id') 
                : $validated['vehicule_id'];
            
            $memeVehicule = ($vehiculeRetourId == $validated['vehicule_id']);
            
            // Validation seulement si même véhicule ET heure retour <= heure arrivée aller
            if ($heureDepartRetour && $memeVehicule && $heureDepartRetour <= $heureArriveeAller) {
                $msg = "Le retour (" . $heureDepartRetour . ") ne peut pas partir avant que le bus arrive a destination (" . $heureArriveeAller . "). Choisissez une heure de retour apres " . $heureArriveeAller . " ou selectionnez un vehicule different pour le retour.";
                
                $fieldName = $validated['type_programmation'] === 'ponctuel' 
                    ? 'retour_heure_depart' 
                    : 'retour_heure_depart_recurrent';
                    
                return back()->withInput()->withErrors([$fieldName => $msg])->with('error', $msg);
            }
        }

        // =========================================================================
        // CRÉATION
        // =========================================================================

        try {
            $itineraireAller = Itineraire::findOrFail($validated['itineraire_id']);

            // --- ALLER ---
            $programmeAller = Programme::create([
                'compagnie_id' => $compagnieId,
                'itineraire_id' => $itineraireAller->id,
                'vehicule_id' => $validated['vehicule_id'],
                'personnel_id' => $validated['personnel_id'],
                'convoyeur_id' => $validated['convoyeur_id'] ?? null,
                'point_depart' => $itineraireAller->point_depart,
                'point_arrive' => $itineraireAller->point_arrive,
                'durer_parcours' => $validated['durer_parcours'],
                'montant_billet' => $validated['montant_billet'],
                'date_depart' => $dateDepartFormatted,
                'heure_depart' => $validated['heure_depart'],
                'heure_arrive' => $validated['heure_arrive'],
                'type_programmation' => $validated['type_programmation'],
                'nbre_siege_occupe' => 0,
                'staut_place' => 'vide',
                'is_aller_retour' => $request->has('is_aller_retour'),
                'date_fin_programmation' => $validated['date_fin_programmation'] ?? null,
                'jours_recurrence' => isset($validated['jours_recurrence']) ? json_encode($validated['jours_recurrence']) : null,
            ]);

            // --- RETOUR ---
            if ($request->has('is_aller_retour')) {
                
                $itineraireRetour = Itineraire::firstOrCreate(
                    [
                        'compagnie_id' => $compagnieId,
                        'point_depart' => trim($itineraireAller->point_arrive),
                        'point_arrive' => trim($itineraireAller->point_depart),
                    ],
                    [
                        'durer_parcours' => $itineraireAller->durer_parcours
                    ]
                );

                // Déterminer le véhicule et chauffeur pour le retour
                // Si véhicule/chauffeur différent spécifié (trajets longs), utiliser ces valeurs
                $vehiculeRetourId = !empty($request->input('retour_vehicule_id')) 
                    ? $request->input('retour_vehicule_id') 
                    : $validated['vehicule_id'];
                
                $personnelRetourId = !empty($request->input('retour_personnel_id')) 
                    ? $request->input('retour_personnel_id') 
                    : $validated['personnel_id'];

                $donneesRetour = [
                    'compagnie_id' => $compagnieId,
                    'itineraire_id' => $itineraireRetour->id,
                    'vehicule_id' => $vehiculeRetourId,
                    'personnel_id' => $personnelRetourId,
                    'convoyeur_id' => $validated['convoyeur_id'] ?? null,
                    'point_depart' => $itineraireRetour->point_depart,
                    'point_arrive' => $itineraireRetour->point_arrive,
                    'durer_parcours' => $validated['durer_parcours'],
                    'montant_billet' => $validated['montant_billet'],
                    'nbre_siege_occupe' => 0,
                    'staut_place' => 'vide',
                    'type_programmation' => $validated['type_programmation'],
                    'is_aller_retour' => false,
                    'programme_retour_id' => $programmeAller->id,
                ];

                if ($validated['type_programmation'] === 'ponctuel') {
                    $dateDepartAller = Carbon::parse($validated['date_depart']);
                    $heureArriveeAllerCarbon = Carbon::createFromFormat('H:i', $validated['heure_arrive']);
                    $heureDepartRetourCarbon = Carbon::createFromFormat('H:i', $validated['retour_heure_depart']);
                    $heureArriveeRetourStr = $this->calculerHeureArrivee($validated['retour_heure_depart'], $validated['durer_parcours']);

                    if ($heureDepartRetourCarbon->lessThanOrEqualTo($heureArriveeAllerCarbon)) {
                        $dateRetour = $dateDepartAller->addDay()->toDateString();
                    } else {
                        $dateRetour = $dateDepartFormatted;
                    }

                    $donneesRetour['date_depart'] = $dateRetour;
                    $donneesRetour['heure_depart'] = $validated['retour_heure_depart'];
                    $donneesRetour['heure_arrive'] = $heureArriveeRetourStr;

                } else {
                    $donneesRetour['date_depart'] = Carbon::parse($validated['retour_date_debut_recurrent'])->toDateString();
                    $donneesRetour['date_fin_programmation'] = $validated['date_fin_programmation'];
                    $donneesRetour['heure_depart'] = $validated['retour_heure_depart_recurrent'];
                    $donneesRetour['heure_arrive'] = $this->calculerHeureArrivee($validated['retour_heure_depart_recurrent'], $validated['durer_parcours']);
                    $donneesRetour['jours_recurrence'] = json_encode($validated['jours_retour']);
                }

                $programmeRetour = Programme::create($donneesRetour);
                $programmeAller->update(['programme_retour_id' => $programmeRetour->id]);
            }

            // Succès
            return redirect()->route('programme.index')
                ->with('success', 'Programme créé avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur création programme: ' . $e->getMessage());
            // CORRECTION ICI : le with('error') permet le Pop-up en cas de crash système
            return back()->withInput()->with('error', 'Erreur système : ' . $e->getMessage());
        }
    }
    private function calculerHeureArrivee($heureDepart, $dureeStr)
    {
        try {
            $heures = 0;
            $minutes = 0;
            
            if (preg_match('/(\d+)\s*h/i', $dureeStr, $matches)) {
                $heures = (int)$matches[1];
            }
            if (preg_match('/(\d+)\s*m/i', $dureeStr, $matches)) {
                $minutes = (int)$matches[1];
            }
            
            if ($heures == 0 && $minutes == 0 && strpos($dureeStr, ':') !== false) {
                 list($h, $m) = explode(':', $dureeStr);
                 $heures = (int)$h;
                 $minutes = (int)$m;
            }

            $time = Carbon::createFromFormat('H:i', $heureDepart);
            $time->addHours($heures)->addMinutes($minutes);
            
            return $time->format('H:i');
        } catch (\Exception $e) {
            return $heureDepart;
        }
    }

    // --- CORRECTION DE LA METHODE CHANGER CHAUFFEUR ---
    public function changerChauffeur(Request $request, Programme $programme)
    {
        $request->validate([
            'chauffeur_id' => 'required|exists:personnels,id',
            'raison' => 'nullable|string|max:500'
        ]);

        try {
            $compagnieId = Auth::guard('compagnie')->user()->id;
            $nouveauChauffeur = Personnel::where('id', $request->chauffeur_id)
                ->where('compagnie_id', $compagnieId)
                ->where('type_personnel', 'Chauffeur')
                ->firstOrFail();

            $ancienChauffeur = $programme->chauffeur;

            $programme->load(['vehicule', 'chauffeur', 'convoyeur', 'itineraire']);

            $pourcentage = $programme->vehicule ? round(($programme->nbre_siege_occupe / $programme->vehicule->nombre_place) * 100) : 0;

            $programme->update(['personnel_id' => $request->chauffeur_id]);

            ProgrammeHistorique::create([
                'programme_id' => $programme->id,
                'action' => 'change_chauffeur',
                'vehicule' => $programme->vehicule ? "{$programme->vehicule->marque} {$programme->vehicule->modele} - {$programme->vehicule->immatriculation} ({$programme->vehicule->nombre_place} places)" : 'Non défini',
                'itineraire' => $programme->itineraire ? "{$programme->itineraire->point_depart} → {$programme->itineraire->point_arrive}" : 'Non défini',
                'chauffeur' => $nouveauChauffeur ? "{$nouveauChauffeur->prenom} {$nouveauChauffeur->name} - {$nouveauChauffeur->contact}" : 'Non défini',
                'convoyeur' => $programme->convoyeur ? "{$programme->convoyeur->prenom} {$programme->convoyeur->name} - {$programme->convoyeur->contact}" : 'Aucun',
                'point_depart' => $programme->point_depart,
                'point_arrive' => $programme->point_arrive,
                'duree_parcours' => $programme->durer_parcours,
                // CORRECTION ICI: Utiliser les données du programme existant, pas $validated
                'date_depart' => Carbon::parse($programme->date_depart)->format('Y-m-d'),
                'heure_depart' => $programme->heure_depart,
                'heure_arrivee' => $programme->heure_arrive,
                'sieges_occupes' => "{$programme->nbre_siege_occupe} sièges",
                'statut_places' => $programme->staut_place,
                'pourcentage_occupation' => "{$pourcentage}%",
                'raison' => "Changement de chauffeur: {$ancienChauffeur->prenom} {$ancienChauffeur->name} → {$nouveauChauffeur->prenom} {$nouveauChauffeur->name}. " . ($request->raison ? "Raison: {$request->raison}" : "Aucune raison spécifiée")
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chauffeur modifié avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur changement chauffeur', [
                'programme_id' => $programme->id,
                'chauffeur_id' => $request->chauffeur_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du changement de chauffeur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changerVehicule(Request $request, Programme $programme)
    {
        $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'raison' => 'nullable|string|max:500'
        ]);

        try {
            $compagnieId = Auth::guard('compagnie')->user()->id;
            $nouveauVehicule = Vehicule::where('id', $request->vehicule_id)
                ->where('compagnie_id', $compagnieId)
                ->firstOrFail();

            $ancienVehicule = $programme->vehicule;

            $programme->load(['vehicule', 'chauffeur', 'convoyeur', 'itineraire']);

            $pourcentage = $nouveauVehicule ? round(($programme->nbre_siege_occupe / $nouveauVehicule->nombre_place) * 100) : 0;

            $programme->update(['vehicule_id' => $request->vehicule_id]);

            ProgrammeHistorique::create([
                'programme_id' => $programme->id,
                'action' => 'change_vehicule',
                'vehicule' => $nouveauVehicule ? "{$nouveauVehicule->marque} {$nouveauVehicule->modele} - {$nouveauVehicule->immatriculation} ({$nouveauVehicule->nombre_place} places)" : 'Non défini',
                'itineraire' => $programme->itineraire ? "{$programme->itineraire->point_depart} → {$programme->itineraire->point_arrive}" : 'Non défini',
                'chauffeur' => $programme->chauffeur ? "{$programme->chauffeur->prenom} {$programme->chauffeur->name} - {$programme->chauffeur->contact}" : 'Non défini',
                'convoyeur' => $programme->convoyeur ? "{$programme->convoyeur->prenom} {$programme->convoyeur->name} - {$programme->convoyeur->contact}" : 'Aucun',
                'point_depart' => $programme->point_depart,
                'point_arrive' => $programme->point_arrive,
                'duree_parcours' => $programme->durer_parcours,
                'date_depart' => \Carbon\Carbon::parse($programme->date_depart)->format('d/m/Y'),
                'heure_depart' => $programme->heure_depart,
                'heure_arrivee' => $programme->heure_arrive,
                'sieges_occupes' => "{$programme->nbre_siege_occupe} sièges",
                'statut_places' => $programme->staut_place,
                'pourcentage_occupation' => "{$pourcentage}%",
                'raison' => "Changement de véhicule: {$ancienVehicule->marque} {$ancienVehicule->modele} → {$nouveauVehicule->marque} {$nouveauVehicule->modele}. " . ($request->raison ? "Raison: {$request->raison}" : "Aucune raison spécifiée")
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Véhicule modifié avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur changement véhicule', [
                'programme_id' => $programme->id,
                'vehicule_id' => $request->vehicule_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du changement de véhicule: ' . $e->getMessage()
            ], 500);
        }
    }
}