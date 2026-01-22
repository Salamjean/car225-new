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

class ProgrammeController extends Controller
{
    public function index()
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;

        $programmes = Programme::with(['vehicule', 'chauffeur', 'convoyeur', 'itineraire'])
            ->where('compagnie_id', $compagnieId)
            ->orderBy('date_depart', 'desc')
            ->orderBy('heure_depart', 'desc')
            ->paginate(10);

        return view('compagnie.programme.index', compact('programmes'));
    }
    public function history()
    {
        $compagnieId = Auth::guard('compagnie')->user()->id;

        $programmes = ProgrammeHistorique::paginate(10);

        return view('compagnie.programme.historique', compact('programmes'));
    }

    public function showApi(Programme $programme)
    {
        Log::info('Appel API programme', ['programme_id' => $programme->id, 'user_id' => Auth::guard('compagnie')->user()->id]);

        try {
            // Vérifier que le programme appartient à la compagnie connectée
            $compagnieId = Auth::guard('compagnie')->user()->id;

            if ($programme->compagnie_id != $compagnieId) {
                Log::warning('Tentative d\'accès non autorisé', [
                    'programme_compagnie' => $programme->compagnie_id,
                    'user_compagnie' => $compagnieId
                ]);
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            // Charger les relations avec vérification
            $programme->load(['vehicule', 'chauffeur', 'convoyeur']);

            // Vérifier que les relations sont bien chargées
            if (!$programme->vehicule) {
                Log::error('Véhicule non trouvé pour le programme', ['programme_id' => $programme->id]);
                return response()->json(['error' => 'Véhicule non trouvé'], 404);
            }

            if (!$programme->chauffeur) {
                Log::error('Chauffeur non trouvé pour le programme', ['programme_id' => $programme->id]);
                return response()->json(['error' => 'Chauffeur non trouvé'], 404);
            }

            // Décode les jours de récurrence si c'est un JSON
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
                'date_depart_formatee' => $programme->date_depart,
                'heure_depart' => $programme->heure_depart,
                'heure_arrive' => $programme->heure_arrive,
                'nbre_siege_occupe' => $programme->nbre_siege_occupe,
                'staut_place' => $programme->staut_place,
                'montant_billet' => $programme->montant_billet,
                'type_programmation' => $programme->type_programmation,
                'is_aller_retour' => (bool) $programme->is_aller_retour,
                'date_fin_programmation' => $programme->date_fin_programmation ?
                    $programme->date_fin_programmation : null,
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

            // Ajouter le convoyeur seulement s'il existe
            if ($programme->convoyeur) {
                $data['convoyeur'] = [
                    'prenom' => $programme->convoyeur->prenom,
                    'name' => $programme->convoyeur->name,
                    'contact' => $programme->convoyeur->contact,
                ];
            } else {
                $data['convoyeur'] = null;
            }

            Log::info('Données API envoyées avec succès', ['programme_id' => $programme->id]);

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Erreur API programme', [
                'programme_id' => $programme->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
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

        // Définir la date de départ par défaut (aujourd'hui)
        $dateDepartDefault = now()->format('Y-m-d');
        // Définir une date de fin par défaut (30 jours plus tard)
        $dateFinDefault = now()->addDays(30)->format('Y-m-d');

        return view('compagnie.programme.create', compact(
            'itineraires',
            'vehicules',
            'chauffeurs',
            'convoyeurs',
            'dateDepartDefault',
            'dateFinDefault'
        ));
    }

    public function store(Request $request)
    {
        Log::info('Début de la création du programme', ['request_data' => $request->all()]);

        // Règles de validation de base
        $rules = [
            'itineraire_id' => 'required|exists:itineraires,id',
            'vehicule_id' => 'required|exists:vehicules,id',
            'personnel_id' => 'required|exists:personnels,id',
            'convoyeur_id' => 'nullable|exists:personnels,id',
            'point_depart' => 'required|string|max:255',
            'point_arrive' => 'required|string|max:255',
            'montant_billet' => 'required|numeric|min:0',
            'durer_parcours' => 'required|string|max:50',
            'date_depart' => 'required|date',
            'heure_depart' => 'required|string',
            'heure_arrive' => 'required|string',
            'type_programmation' => 'required|in:ponctuel,recurrent',
            'date_fin_programmation' => 'nullable|required_if:type_programmation,recurrent|date|after_or_equal:date_depart',
            'jours_recurrence' => 'nullable|array|required_if:type_programmation,recurrent',
            'jours_recurrence.*' => 'string|in:lundi,mardi,mercredi,jeudi,vendredi,samedi,dimanche',
            'is_aller_retour' => 'boolean',
        ];

        // Ajouter les règles de validation pour aller-retour si activé
        if ($request->has('is_aller_retour')) {
            if ($request->input('type_programmation') === 'ponctuel') {
                // Ponctuel: date et heures spécifiques pour le retour
                $rules['retour_date'] = 'required|date|after_or_equal:date_depart';
                $rules['retour_heure_depart'] = 'required|string';
                $rules['retour_heure_arrive'] = 'required|string';
            } else {
                // Récurrent: jours et heure unique pour tous les retours
                // ICI: On valide que jours_retour est un tableau valide
                $rules['jours_retour'] = 'required|array|min:1';
                $rules['jours_retour.*'] = 'string|in:lundi,mardi,mercredi,jeudi,vendredi,samedi,dimanche';
               // NOUVEAU : Validation de la date de début retour
                $rules['retour_date_debut_recurrent'] = 'required|date|after_or_equal:date_depart';
                $rules['retour_heure_depart_recurrent'] = 'required|string';
                $rules['retour_heure_arrive_recurrent'] = 'required|string';
            }
        }

        $validatedData = $request->validate($rules);


        Log::info('Données validées avec succès', ['validated_data' => $validatedData]);

        try {
            // Le statut des places sera "vide" par défaut au début
            $staut_place = 'vide';
            $isAllerRetour = $request->has('is_aller_retour') ? true : false;
            $compagnieId = Auth::guard('compagnie')->user()->id;

            // Préparer les données pour la création du programme ALLER
            $programmeData = [
                'compagnie_id' => $compagnieId,
                'itineraire_id' => $validatedData['itineraire_id'],
                'vehicule_id' => $validatedData['vehicule_id'],
                'personnel_id' => $validatedData['personnel_id'],
                'convoyeur_id' => $validatedData['convoyeur_id'] ?? null,
                'point_depart' => $validatedData['point_depart'],
                'point_arrive' => $validatedData['point_arrive'],
                'durer_parcours' => $validatedData['durer_parcours'],
                'date_depart' => $validatedData['date_depart'],
                'heure_depart' => $validatedData['heure_depart'],
                'heure_arrive' => $validatedData['heure_arrive'],
                'montant_billet' => $validatedData['montant_billet'],
                'nbre_siege_occupe' => 0,
                'staut_place' => $staut_place,
                'type_programmation' => $validatedData['type_programmation'],
                'is_aller_retour' => $isAllerRetour,
            ];

            // Ajouter les champs spécifiques aux programmations récurrentes
            if ($validatedData['type_programmation'] === 'recurrent') {
                $programmeData['date_fin_programmation'] = $validatedData['date_fin_programmation'];
                $programmeData['jours_recurrence'] = json_encode($validatedData['jours_recurrence']);
            }

            Log::info('Tentative de création du programme ALLER', $programmeData);

            // Créer le programme ALLER
            $programmeAller = Programme::create($programmeData);

            Log::info('Programme ALLER créé avec succès', ['programme_id' => $programmeAller->id]);

            // Si aller-retour, créer automatiquement le programme RETOUR avec configuration utilisateur
            if ($isAllerRetour) {
                // Trouver ou créer l'itinéraire inverse
                $itineraireAller = Itineraire::find($validatedData['itineraire_id']);
                
                // Chercher l'itinéraire inverse (départ <-> arrivée)
                $itineraireRetour = Itineraire::where('compagnie_id', $compagnieId)
                    ->where('point_depart', $itineraireAller->point_arrive)
                    ->where('point_arrive', $itineraireAller->point_depart)
                    ->first();

                // Si l'itinéraire inverse n'existe pas, le créer automatiquement
                if (!$itineraireRetour) {
                    $itineraireRetour = Itineraire::create([
                        'compagnie_id' => $compagnieId,
                        'point_depart' => $itineraireAller->point_arrive,
                        'point_arrive' => $itineraireAller->point_depart,
                        'durer_parcours' => $itineraireAller->durer_parcours,
                    ]);
                    Log::info('Itinéraire retour créé automatiquement', ['itineraire_retour_id' => $itineraireRetour->id]);
                }

                // Préparer les données du programme retour
                $programmeRetourData = [
                    'compagnie_id' => $compagnieId,
                    'itineraire_id' => $itineraireRetour->id,
                    'vehicule_id' => $validatedData['vehicule_id'],
                    'personnel_id' => $validatedData['personnel_id'],
                    'convoyeur_id' => $validatedData['convoyeur_id'] ?? null,
                    'point_depart' => $validatedData['point_arrive'],
                    'point_arrive' => $validatedData['point_depart'],
                    'durer_parcours' => $validatedData['durer_parcours'],
                    'montant_billet' => $validatedData['montant_billet'],
                    'nbre_siege_occupe' => 0,
                    'staut_place' => $staut_place,
                    'type_programmation' => $validatedData['type_programmation'],
                    'is_aller_retour' => false,
                    'programme_retour_id' => $programmeAller->id,
                ];

                // Configurer les dates et heures selon le type de programmation
                if ($validatedData['type_programmation'] === 'ponctuel') {
                    // CAS PONCTUEL : On utilise explicitement la date de retour choisie par l'utilisateur
                    $programmeRetourData['date_depart'] = $validatedData['retour_date'];
                    $programmeRetourData['heure_depart'] = $validatedData['retour_heure_depart'];
                    $programmeRetourData['heure_arrive'] = $validatedData['retour_heure_arrive'];
                } else {
                     // CAS RÉCURRENT :
                    
                    // CORRECTION ICI : On utilise la date spécifique fournie par l'utilisateur
                    $programmeRetourData['date_depart'] = $validatedData['retour_date_debut_recurrent']; 
                    
                    $programmeRetourData['date_fin_programmation'] = $validatedData['date_fin_programmation'];
                    $programmeRetourData['jours_recurrence'] = json_encode($validatedData['jours_retour']);
                    $programmeRetourData['heure_depart'] = $validatedData['retour_heure_depart_recurrent'];
                    $programmeRetourData['heure_arrive'] = $validatedData['retour_heure_arrive_recurrent'];
                }
                Log::info('Tentative de création du programme RETOUR', $programmeRetourData);

                // Créer le programme RETOUR
                $programmeRetour = Programme::create($programmeRetourData);

                // Mettre à jour le programme ALLER pour le lier au RETOUR
                $programmeAller->update(['programme_retour_id' => $programmeRetour->id]);

                Log::info('Programme RETOUR créé et lié', [
                    'programme_aller_id' => $programmeAller->id,
                    'programme_retour_id' => $programmeRetour->id,
                    'itineraire_retour_id' => $itineraireRetour->id
                ]);
            }

            return redirect()->route('programme.index')
                ->with('success', $isAllerRetour 
                    ? 'Programme aller-retour créé avec succès! (2 programmes créés avec itinéraire inversé)' 
                    : 'Programme créé avec succès!');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du programme', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du programme: ' . $e->getMessage());
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
