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

        // On charge les relations ET le comptage des réservations
        $programmes = Programme::with(['vehicule', 'chauffeur', 'convoyeur', 'itineraire'])
            ->where('compagnie_id', $compagnieId)
            // On compte les réservations où ce programme est l'aller
            ->withCount(['reservationsAller' => function ($query) {
                $query->where('statut', '!=', 'annulee');
            }])
            // On compte les réservations où ce programme est le retour
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
            ->orderBy('date_depart', 'asc')
            ->paginate(10);

        // Calcul du total des sièges occupés (Aller + Retour) pour l'affichage
        // On injecte l'attribut calculé directement dans la collection
        $programmes->getCollection()->transform(function ($programme) {
            $programme->total_reserves = $programme->reservations_aller_count + $programme->reservations_retour_count;
            
            // Mise à jour du statut en temps réel (optionnel mais visuel)
            $totalPlaces = $programme->vehicule ? $programme->vehicule->nombre_place : 0;
            if ($totalPlaces > 0) {
                if ($programme->total_reserves >= $totalPlaces) {
                    $programme->staut_place = 'rempli';
                } elseif ($programme->total_reserves >= ($totalPlaces * 0.8)) {
                    $programme->staut_place = 'presque_complet';
                } else {
                    $programme->staut_place = 'vide'; // ou disponible
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

        // 1. Historique des actions (Logs)
        $logs = ProgrammeHistorique::orderBy('created_at', 'desc')->paginate(10, ['*'], 'logs_page');

        // 2. Programmes TERMINÉS (Expirés)
        $programmesExpires = Programme::with(['vehicule', 'itineraire'])
            ->where('compagnie_id', $compagnieId)
            ->where(function($query) use ($today) {
                // Ponctuel passé
                $query->where(function($q) use ($today) {
                    $q->where('type_programmation', 'ponctuel')
                      ->where('date_depart', '<', $today);
                })
                // Récurrent dont la date de fin est passée
                ->orWhere(function($q) use ($today) {
                    $q->where('type_programmation', 'recurrent')
                      ->where('date_fin_programmation', '<', $today);
                });
            })
            ->orderBy('date_depart', 'desc')
            ->paginate(10, ['*'], 'prog_page');

        return view('compagnie.programme.historique', compact('logs', 'programmesExpires'));
    }

       // AJOUT DE LA MÉTHODE DESTROY
    public function destroy($id)
    {
        try {
            $compagnieId = Auth::guard('compagnie')->user()->id;
            $programme = Programme::where('id', $id)->where('compagnie_id', $compagnieId)->firstOrFail();

            // VÉRIFICATION DES RÉSERVATIONS
            // Assurez-vous que la relation 'reservations' existe dans votre modèle Programme
            if ($programme->reservations()->count() > 0) {
                return back()->with('error', 'Impossible de supprimer ce programme car il contient des réservations actives ou passées.');
            }

            // Si pas de réservation, on supprime
            $programme->delete();

            return back()->with('success', 'Programme supprimé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur suppression programme : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }
       // --- CORRECTION DE L'ERREUR : AJOUT DE LA MÉTHODE EDIT ---
    public function edit($id)
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;
        
        // Récupérer le programme
        $programme = Programme::where('id', $id)->where('compagnie_id', $compagnieId)->firstOrFail();
        
        // Récupérer les données nécessaires pour les listes déroulantes
        $itineraires = Itineraire::where('compagnie_id', $compagnieId)->get();
        // On inclut le véhicule actuel même s'il est inactif
        $vehicules = Vehicule::where('compagnie_id', $compagnieId)
            ->where(function($q) use ($programme) {
                $q->where('is_active', true)->orWhere('id', $programme->vehicule_id);
            })->get();
            
        $chauffeurs = Personnel::where('compagnie_id', $compagnieId)
            ->where('type_personnel', 'Chauffeur')
            ->where('statut', 'disponible')
            ->orWhere('id', $programme->personnel_id) // Inclure le chauffeur actuel
            ->get();
            
        $convoyeurs = Personnel::where('compagnie_id', $compagnieId)
            ->where('type_personnel', 'Convoyeur')
            ->get();

        return view('compagnie.programme.edit', compact('programme', 'itineraires', 'vehicules', 'chauffeurs', 'convoyeurs'));
    }

  // --- AJOUT DE LA MÉTHODE UPDATE ---
    public function update(Request $request, $id)
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;
        $programme = Programme::where('id', $id)->where('compagnie_id', $compagnieId)->firstOrFail();

        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'personnel_id' => 'required|exists:personnels,id', // Chauffeur
            'convoyeur_id' => 'nullable|exists:personnels,id',
            'montant_billet' => 'required|numeric|min:0',
            'heure_depart' => 'required',
            // On évite de modifier l'itinéraire ou la date pour ne pas casser les réservations existantes
        ]);

        // Calcul automatique de l'heure d'arrivée si l'heure de départ change
        $heureArrivee = $programme->heure_arrive;
        if($request->heure_depart != $programme->heure_depart) {
            // Logique simple d'ajout de durée (à adapter selon votre format de duree_parcours)
            // Ici on garde l'ancienne logique ou on ne la modifie pas pour l'instant
            // Idéalement, recalculez l'heure d'arrivée ici
        }

        $programme->update([
            'vehicule_id' => $validated['vehicule_id'],
            'personnel_id' => $validated['personnel_id'],
            'convoyeur_id' => $validated['convoyeur_id'],
            'montant_billet' => $validated['montant_billet'],
            'heure_depart' => $validated['heure_depart'],
        ]);

        // Log historique
        ProgrammeHistorique::create([
            'programme_id' => $programme->id,
            'action' => 'modification_programme',
            'raison' => 'Mise à jour via formulaire d\'édition',
            'vehicule' => $programme->vehicule->immatriculation,
            'chauffeur' => $programme->chauffeur->name,
            'itineraire' => $programme->point_depart . ' - ' . $programme->point_arrive,
             // ... autres champs requis par votre table historique
             // Assurez-vous de remplir tous les champs non-nullables de votre table historique
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

            // 1. Charger les relations ET compter les réservations
            $programme->load(['vehicule', 'chauffeur', 'convoyeur']);
            $programme->loadCount(['reservationsAller', 'reservationsRetour']); // <-- AJOUT IMPORTANT

            if (!$programme->vehicule || !$programme->chauffeur) {
                return response()->json(['error' => 'Données incomplètes (Véhicule ou Chauffeur manquant)'], 404);
            }

            // 2. Calculer le nombre réel de places occupées
            // On vérifie que les compteurs ne sont pas null
            $totalOccupied = ($programme->reservations_aller_count ?? 0) + ($programme->reservations_retour_count ?? 0);
            
            // 3. Recalculer le statut dynamiquement pour le popup
            $totalPlaces = $programme->vehicule->nombre_place;
            $statut = 'vide';
            if ($totalPlaces > 0) {
                if ($totalOccupied >= $totalPlaces) {
                    $statut = 'rempli';
                } elseif ($totalOccupied >= ($totalPlaces * 0.8)) {
                    $statut = 'presque_complet';
                }
            }

            // Gestion des jours de récurrence
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
                'date_depart_formatee' => \Carbon\Carbon::parse($programme->date_depart)->format('d/m/Y'), // Formatage propre
                'heure_depart' => $programme->heure_depart,
                'heure_arrive' => $programme->heure_arrive,
                
                // --- CORRECTION ICI : On envoie le total calculé ---
                'nbre_siege_occupe' => $totalOccupied, 
                
                // --- CORRECTION ICI : On envoie le statut recalculé ---
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

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Erreur API programme', [
                'programme_id' => $programme->id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Méthode pour récupérer les programmes disponibles à une date donnée
     */
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
            $jourSemaine = strtolower(date('l', strtotime($dateRecherche))); // Récupère le jour en anglais

            // Construction de la requête
            $query = Programme::with(['vehicule', 'chauffeur', 'itineraire'])
                ->where('compagnie_id', $compagnieId)
                ->where(function ($q) use ($dateRecherche, $jourSemaine) {
                    // Programmes ponctuels à la date exacte
                    $q->where(function ($sub) use ($dateRecherche) {
                        $sub->where('type_programmation', 'ponctuel')
                            ->where('date_depart', $dateRecherche);
                    });

                    // OU programmes récurrents
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

            // Filtres optionnels
            if ($request->filled('point_depart')) {
                $query->where('point_depart', 'like', '%' . $request->point_depart . '%');
            }

            if ($request->filled('point_arrive')) {
                $query->where('point_arrive', 'like', '%' . $request->point_arrive . '%');
            }

            // Trier par heure de départ
            $query->orderBy('heure_depart', 'asc');

            $programmes = $query->get();

            // Formater les données pour la réponse
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
                'jour_semaine' => $jourSemaine,
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

        // Validation conditionnelle pour le type Récurrent
        if ($request->input('type_programmation') === 'recurrent') {
            $rules['date_fin_programmation'] = 'required|date|after_or_equal:date_depart';
            $rules['jours_recurrence'] = 'required|array';
        }

        // Validation conditionnelle pour l'Aller-Retour
        if ($request->has('is_aller_retour')) {
            if ($request->input('type_programmation') === 'ponctuel') {
                $rules['retour_heure_depart'] = 'required|string';
                $rules['retour_heure_arrive'] = 'required|string';
                // Pour ponctuel, la date retour est la même que l'aller, pas besoin de validation
            } else {
                // Récurrent
                $rules['jours_retour'] = 'required|array';
                $rules['retour_date_debut_recurrent'] = 'required|date'; // Calculé par JS ou saisi
                $rules['retour_heure_depart_recurrent'] = 'required|string';
                $rules['retour_heure_arrive_recurrent'] = 'required|string';
            }
        }

        $validated = $request->validate($rules);
        Log::info('Données validées avec succès', ['validated_data' => $validated]);

        // ✅ VALIDATION PERSONNALISÉE : Vérifier que la date_depart correspond aux jours de récurrence
        if ($request->input('type_programmation') === 'recurrent') {
            Log::info('Début validation personnalisée pour type récurrent');
            $dateDepart = $request->input('date_depart');
            $joursRecurrence = $request->input('jours_recurrence', []);
            
            // Convertir la date en jour de la semaine en français
            $joursFrancais = [
                'monday' => 'lundi',
                'tuesday' => 'mardi',
                'wednesday' => 'mercredi',
                'thursday' => 'jeudi',
                'friday' => 'vendredi',
                'saturday' => 'samedi',
                'sunday' => 'dimanche'
            ];
            
            $jourAnglais = strtolower(date('l', strtotime($dateDepart)));
            $jourFrancais = $joursFrancais[$jourAnglais] ?? $jourAnglais;
            
            // Vérifier que le jour de la date_depart est dans jours_recurrence
            if (!in_array($jourFrancais, $joursRecurrence)) {
                $joursChoisis = implode(', ', $joursRecurrence);
                return back()
                    ->withInput()
                    ->withErrors([
                        'date_depart' => "La date de départ ({$dateDepart}) tombe un {$jourFrancais}, mais ce jour n'est pas dans vos jours de récurrence sélectionnés ({$joursChoisis}). Choisissez une date qui correspond à un de vos jours de récurrence."
                    ]);
            }
            
            // Validation similaire pour le retour récurrent (si applicable)
            if ($request->has('is_aller_retour') && $request->input('type_programmation') === 'recurrent') {
                $dateRetour = $request->input('retour_date_debut_recurrent');
                $joursRetour = $request->input('jours_retour', []);
                
                $jourRetourAnglais = strtolower(date('l', strtotime($dateRetour)));
                $jourRetourFrancais = $joursFrancais[$jourRetourAnglais] ?? $jourRetourAnglais;
                
                if (!in_array($jourRetourFrancais, $joursRetour)) {
                    $joursRetourChoisis = implode(', ', $joursRetour);
                    return back()
                        ->withInput()
                        ->withErrors([
                            'retour_date_debut_recurrent' => "La date de retour ({$dateRetour}) tombe un {$jourRetourFrancais}, mais ce jour n'est pas dans vos jours de récurrence retour sélectionnés ({$joursRetourChoisis}). Choisissez une date qui correspond à un de vos jours de récurrence retour."
                        ]);
                }
            }
        }

        Log::info('Validation personnalisée terminée avec succès');

        try {
            $compagnieId = Auth::guard('compagnie')->user()->id;
            $itineraire = Itineraire::find($validated['itineraire_id']);

            Log::info('Tentative de création du programme', [
                'compagnie_id' => $compagnieId,
                'itineraire_id' => $validated['itineraire_id'],
                'vehicule_id' => $validated['vehicule_id'],
                'personnel_id' => $validated['personnel_id'],
                'convoyeur_id' => $validated['convoyeur_id'] ?? null,
                'point_depart' => $itineraire->point_depart,
                'point_arrive' => $itineraire->point_arrive,
                'durer_parcours' => $validated['durer_parcours'],
                'date_depart' => $validated['date_depart'],
                'heure_depart' => $validated['heure_depart'],
                'heure_arrive' => $validated['heure_arrive'],
                'montant_billet' => $validated['montant_billet'],
                'nbre_siege_occupe' => 0,
                'staut_place' => 'vide',
                'type_programmation' => $validated['type_programmation'],
                'is_aller_retour' => $request->has('is_aller_retour'),
                'date_fin_programmation' => $validated['date_fin_programmation'] ?? null,
                'jours_recurrence' => isset($validated['jours_recurrence']) ? json_encode($validated['jours_recurrence']) : null,
            ]);

            // 2. Création du Programme ALLER
            $programmeAller = Programme::create([
                'compagnie_id' => $compagnieId,
                'itineraire_id' => $validated['itineraire_id'],
                'vehicule_id' => $validated['vehicule_id'],
                'personnel_id' => $validated['personnel_id'],
                'convoyeur_id' => $validated['convoyeur_id'] ?? null,
                'point_depart' => $itineraire->point_depart,
                'point_arrive' => $itineraire->point_arrive,
                'durer_parcours' => $validated['durer_parcours'],
                'montant_billet' => $validated['montant_billet'],
                'date_depart' => $validated['date_depart'],
                'heure_depart' => $validated['heure_depart'],
                'heure_arrive' => $validated['heure_arrive'],
                'type_programmation' => $validated['type_programmation'],
                'nbre_siege_occupe' => 0,
                'staut_place' => 'vide',
                'is_aller_retour' => $request->has('is_aller_retour'),
                'date_fin_programmation' => $validated['date_fin_programmation'] ?? null,
                'jours_recurrence' => isset($validated['jours_recurrence']) ? json_encode($validated['jours_recurrence']) : null,
            ]);

            Log::info('Programme créé avec succès', ['programme_id' => $programmeAller->id]);

            // 3. Gestion du Programme RETOUR (si activé)
            if ($request->has('is_aller_retour')) {
                Log::info('Début création programme retour', ['programme_aller_id' => $programmeAller->id]);
                
                // A. Trouver ou créer l'itinéraire inverse
                $itineraireRetour = Itineraire::firstOrCreate(
                    [
                        'compagnie_id' => $compagnieId,
                        'point_depart' => $itineraire->point_arrive,
                        'point_arrive' => $itineraire->point_depart,
                    ],
                    [
                        'durer_parcours' => $itineraire->durer_parcours
                    ]
                );

                $donneesRetour = [
                    'compagnie_id' => $compagnieId,
                    'itineraire_id' => $itineraireRetour->id,
                    'vehicule_id' => $validated['vehicule_id'],
                    'personnel_id' => $validated['personnel_id'],
                    'convoyeur_id' => $validated['convoyeur_id'] ?? null,
                    'point_depart' => $itineraireRetour->point_depart,
                    'point_arrive' => $itineraireRetour->point_arrive,
                    'durer_parcours' => $validated['durer_parcours'],
                    'montant_billet' => $validated['montant_billet'],
                    'nbre_siege_occupe' => 0,
                    'staut_place' => 'vide',
                    'type_programmation' => $validated['type_programmation'],
                    'is_aller_retour' => false, // Le retour n'est pas un aller-retour en soi
                    'programme_retour_id' => $programmeAller->id, // Liaison avec l'aller
                ];

                // B. Configuration spécifique selon le type
                if ($validated['type_programmation'] === 'ponctuel') {
                    // Pour ponctuel, même date que l'aller, mais heures différentes
                    $donneesRetour['date_depart'] = $validated['date_depart'];
                    $donneesRetour['heure_depart'] = $validated['retour_heure_depart'];
                    $donneesRetour['heure_arrive'] = $validated['retour_heure_arrive'];
                } else {
                    // Pour récurrent
                    $donneesRetour['date_depart'] = $validated['retour_date_debut_recurrent'];
                    $donneesRetour['date_fin_programmation'] = $validated['date_fin_programmation'];
                    $donneesRetour['heure_depart'] = $validated['retour_heure_depart_recurrent'];
                    $donneesRetour['heure_arrive'] = $validated['retour_heure_arrive_recurrent'];
                    $donneesRetour['jours_recurrence'] = json_encode($validated['jours_retour']);
                }

                $programmeRetour = Programme::create($donneesRetour);

                Log::info('Programme retour créé avec succès', ['programme_retour_id' => $programmeRetour->id]);

                // C. Mettre à jour l'aller avec l'ID du retour
                $programmeAller->update(['programme_retour_id' => $programmeRetour->id]);
                
                Log::info('Liaison aller-retour établie', [
                    'programme_aller_id' => $programmeAller->id,
                    'programme_retour_id' => $programmeRetour->id
                ]);
            }

            Log::info('Redirection vers la liste des programmes');

            return redirect()->route('programme.index')
                ->with('success', 'Programme ' . ($request->has('is_aller_retour') ? 'Aller-Retour' : '') . ' créé avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur création programme: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }


    // Méthode pour récupérer les chauffeurs disponibles
    public function chauffeursDisponibles(Programme $programme)
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;

        // Récupérer les chauffeurs disponibles (statut disponible et type chauffeur)
        $chauffeurs = Personnel::where('compagnie_id', $compagnieId)
            ->where('type_personnel', 'Chauffeur')
            ->where('statut', 'disponible')
            ->orWhere('id', $programme->chauffeur_id) // Inclure le chauffeur actuel même s'il n'est pas disponible
            ->get()
            ->map(function ($chauffeur) {
                return [
                    'id' => $chauffeur->id,
                    'nom_complet' => $chauffeur->prenom . ' ' . $chauffeur->name,
                    'contact' => $chauffeur->contact,
                    'statut' => $chauffeur->statut
                ];
            });

        return response()->json($chauffeurs);
    }

    // Méthode pour récupérer les véhicules disponibles
    public function vehiculesDisponibles(Programme $programme)
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;

        // Récupérer les véhicules disponibles (actifs)
        $vehicules = Vehicule::where('compagnie_id', $compagnieId)
            ->where('is_active', true)
            ->orWhere('id', $programme->vehicule_id) // Inclure le véhicule actuel même s'il n'est pas actif
            ->get()
            ->map(function ($vehicule) {
                return [
                    'id' => $vehicule->id,
                    'nom_complet' => $vehicule->marque . ' ' . $vehicule->modele . ' - ' . $vehicule->immatriculation,
                    'places' => $vehicule->nombre_place,
                    'statut' => $vehicule->is_active ? 'Actif' : 'Inactif'
                ];
            });

        return response()->json($vehicules);
    }

    // Méthode pour changer le chauffeur
    public function changerChauffeur(Request $request, Programme $programme)
    {
        $request->validate([
            'chauffeur_id' => 'required|exists:personnels,id',
            'raison' => 'nullable|string|max:500'
        ]);

        try {
            // Vérifier que le nouveau chauffeur appartient à la compagnie
            $compagnieId = Auth::guard('compagnie')->user()->id;
            $nouveauChauffeur = Personnel::where('id', $request->chauffeur_id)
                ->where('compagnie_id', $compagnieId)
                ->where('type_personnel', 'Chauffeur')
                ->firstOrFail();

            $ancienChauffeur = $programme->chauffeur;

            // Charger les relations pour l'historique
            $programme->load(['vehicule', 'chauffeur', 'convoyeur', 'itineraire']);

            // Calculer le pourcentage d'occupation
            $pourcentage = $programme->vehicule ? round(($programme->nbre_siege_occupe / $programme->vehicule->nombre_place) * 100) : 0;

            // Mettre à jour le programme
            $programme->update(['personnel_id' => $request->chauffeur_id]);

            // Enregistrer dans l'historique avec TOUTES les informations
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
                'date_depart' => \Carbon\Carbon::parse($programme->date_depart)->format('d/m/Y'),
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

    // Méthode pour changer le véhicule
    public function changerVehicule(Request $request, Programme $programme)
    {
        $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'raison' => 'nullable|string|max:500'
        ]);

        try {
            // Vérifier que le nouveau véhicule appartient à la compagnie
            $compagnieId = Auth::guard('compagnie')->user()->id;
            $nouveauVehicule = Vehicule::where('id', $request->vehicule_id)
                ->where('compagnie_id', $compagnieId)
                ->firstOrFail();

            $ancienVehicule = $programme->vehicule;

            // Charger les relations pour l'historique
            $programme->load(['vehicule', 'chauffeur', 'convoyeur', 'itineraire']);

            // Calculer le pourcentage d'occupation avec le NOUVEAU véhicule
            $pourcentage = $nouveauVehicule ? round(($programme->nbre_siege_occupe / $nouveauVehicule->nombre_place) * 100) : 0;

            // Mettre à jour le programme
            $programme->update(['vehicule_id' => $request->vehicule_id]);

            // Enregistrer dans l'historique avec TOUTES les informations
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
