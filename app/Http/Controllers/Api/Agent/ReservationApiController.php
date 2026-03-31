<?php

namespace App\Http\Controllers\Api\Agent;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use App\Models\Reservation;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationApiController extends Controller
{
    /**
     * Liste des réservations + programmes du jour
     */
    public function index(Request $request)
    {
        $agent = $request->user();
        $now = Carbon::now();
        $today = Carbon::today()->toDateString();
        $currentTime = $now->format('H:i');

        // Programmes du jour (même logique que le web)
        $programmesDuJour = Programme::where('compagnie_id', $agent->compagnie_id)
            ->where('gare_depart_id', $agent->gare_id)
            ->where('statut', 'actif')
            ->where(function ($query) use ($today) {
                $query->whereDate('date_depart', $today)
                      ->orWhere(function ($q) use ($today) {
                          $q->where('date_depart', '<=', $today)
                            ->where('date_fin', '>=', $today);
                      });
            })
            ->where('heure_depart', '>', $currentTime)
            ->whereDoesntHave('voyages', function ($q) use ($today) {
                $q->whereDate('date_voyage', $today)
                  ->whereIn('statut', ['en_cours', 'terminé']);
            })
            ->with(['gareDepart', 'gareArrivee', 'voyages' => function($q) use ($today) {
                $q->whereDate('date_voyage', $today)->with('vehicule');
            }])
            ->orderBy('heure_depart')
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'point_depart' => $p->point_depart,
                    'point_arrive' => $p->point_arrive,
                    'heure_depart' => $p->heure_depart,
                    'gare_depart' => $p->gareDepart ? $p->gareDepart->nom_gare : null,
                    'gare_arrivee' => $p->gareArrivee ? $p->gareArrivee->nom_gare : null,
                    'vehicule_id' => $p->vehicule ? $p->vehicule->id : null,
                    'immatriculation' => $p->vehicule->immatriculation ?? 'N/A',
                ];
            });

        // Réservations
        $query = Reservation::with(['programme.gareDepart', 'programme.gareArrivee', 'user'])
            ->whereHas('programme', function($q) use ($agent) {
                $q->where('compagnie_id', $agent->compagnie_id);
            })
            ->orderBy('date_voyage', 'desc');

        // Filtres optionnels
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date')) {
            $query->whereDate('date_voyage', $request->date);
        }

        $reservations = $query->paginate($request->get('per_page', 20));

        // Catégoriser
        $enCours = $reservations->getCollection()->whereNotIn('statut', ['terminee', 'annulee'])->values();
        $terminees = $reservations->getCollection()->where('statut', 'terminee')->values();

        return response()->json([
            'success' => true,
            'programmes_du_jour' => $programmesDuJour,
            'en_cours' => $enCours->map(function($r) {
                return $this->formatReservation($r);
            }),
            'terminees' => $terminees->map(function($r) {
                return $this->formatReservation($r);
            }),
            'reservations' => $reservations->map(function($reservation) {
                return $this->formatReservation($reservation);
            }),
            'pagination' => [
                'current_page' => $reservations->currentPage(),
                'last_page' => $reservations->lastPage(),
                'per_page' => $reservations->perPage(),
                'total' => $reservations->total(),
            ],
        ]);
    }

    /**
     * Rechercher une réservation par référence QR (pour scan)
     */
    public function search(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
            'programme_id' => 'nullable|integer',
        ]);

        $reservation = Reservation::with(['programme.gareDepart', 'programme.gareArrivee', 'user'])
            ->where('reference', $request->reference)
            ->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Réservation non trouvée.',
            ], 404);
        }

        $agent = $request->user();
        if ($reservation->programme->compagnie_id !== $agent->compagnie_id) {
            return response()->json([
                'success' => false,
                'message' => 'Ce billet n\'appartient pas à votre compagnie.',
            ], 403);
        }

        // --- LOGIQUE CIBLE (Aller ou Retour) ---
        $targetScan = null;
        $programScanId = $request->input('programme_id');
        $programmeActuel = null;

        // 1. DÉTECTION VIA LE PROGRAMME SÉLECTIONNÉ
        if ($programScanId) {
            if ($programScanId == $reservation->programme_id) {
                $targetScan = 'aller';
                $programmeActuel = $reservation->programme;
            } elseif ($reservation->programme->programme_retour_id == $programScanId) {
                $targetScan = 'retour';
                $programmeActuel = Programme::with(['gareDepart', 'gareArrivee'])->find($programScanId);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce billet ne correspond pas au trajet sélectionné.',
                ], 400);
            }
        }
        // 2. FALLBACK
        else {
            $programmeActuel = $reservation->programme;
            $targetScan = 'aller';
        }

        // Vérification du statut
        $statutActuel = ($targetScan === 'aller') ? $reservation->statut_aller : $reservation->statut_retour;

        if ($statutActuel === 'terminee') {
            return response()->json([
                'success' => false,
                'message' => "Le trajet " . strtoupper($targetScan) . " a déjà été validé.",
                'already_scanned' => true,
            ], 400);
        }

        // Charger les gares du programme actuel si besoin
        if ($programmeActuel && !$programmeActuel->relationLoaded('gareDepart')) {
            $programmeActuel->load(['gareDepart', 'gareArrivee']);
        }

        $prog = $programmeActuel ?? $reservation->programme;
        $heureDepart = $prog->heure_depart;
        $heureArrivee = $prog->heure_arrive;
        $trajetLabel = $prog->point_depart . ' → ' . $prog->point_arrive;
        $gareDepartNom = optional($prog->gareDepart)->nom_gare ?? '';
        $gareArriveeNom = optional($prog->gareArrivee)->nom_gare ?? '';
        $gareDepartVille = optional($prog->gareDepart)->ville ?? '';
        $gareArriveeVille = optional($prog->gareArrivee)->ville ?? '';

        return response()->json([
            'success' => true,
            'reservation' => [
                'id' => $reservation->id,
                'reference' => $reservation->reference,
                'passager_nom_complet' => $reservation->passager_prenom . ' ' . $reservation->passager_nom,
                'passager_telephone' => $reservation->passager_telephone,
                'passager_email' => $reservation->passager_email ?? '',
                'seat_number' => $reservation->seat_number,
                'date_voyage' => Carbon::parse($prog->date_depart ?? now())->format('d/m/Y'),
                'trajet' => $trajetLabel,
                'heure_depart' => $heureDepart,
                'heure_arrivee' => $heureArrivee,
                'gare_depart' => $gareDepartNom,
                'gare_arrivee' => $gareArriveeNom,
                'gare_depart_ville' => $gareDepartVille,
                'gare_arrivee_ville' => $gareArriveeVille,
                'montant' => number_format($reservation->montant ?? 0, 0, ',', ' ') . ' FCFA',
                'is_aller_retour' => $reservation->is_aller_retour,
                'type_scan' => strtoupper($targetScan),
                'statut' => $statutActuel,
                'vehicule_id' => $prog->vehicule ? $prog->vehicule->id : null,
                'immatriculation' => $prog->vehicule ? $prog->vehicule->immatriculation : 'N/A',
            ],
        ]);
    }

    /**
     * Confirmer l'embarquement
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
            'vehicule_id' => 'required|integer',
            'programme_id' => 'nullable|integer',
        ]);

        $reservation = Reservation::with('programme')->where('reference', $request->reference)->first();
        $agent = $request->user();
        $vehicule = Vehicule::find($request->vehicule_id);

        if (!$reservation || !$vehicule) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides.',
            ], 400);
        }

        // --- DÉTECTION PAR DATE (prioritaire, comme la version web) ---
        $today = Carbon::today();
        $dateAller = Carbon::parse($reservation->date_voyage)->startOfDay();
        $dateRetour = $reservation->date_retour ? Carbon::parse($reservation->date_retour)->startOfDay() : null;

        $isDayAller = $dateAller->equalTo($today);
        $isDayRetour = $dateRetour && $dateRetour->equalTo($today);

        $targetScan = null;

        if (!$isDayAller && !$isDayRetour) {
            return response()->json([
                'success' => false,
                'message' => 'Cette réservation n\'est pas valable pour aujourd\'hui.',
            ], 400);
        }

        if ($isDayAller && $isDayRetour) {
            $targetScan = ($reservation->statut_aller === 'terminee') ? 'retour' : 'aller';
        } elseif ($isDayAller) {
            $targetScan = 'aller';
        } elseif ($isDayRetour) {
            $targetScan = 'retour';
        }

        // Vérifier le programme sélectionné (optionnel mais recommandé)
        if ($request->has('programme_id')) {
            $programScanId = $request->input('programme_id');
            $expectedProgramId = ($targetScan === 'aller')
                ? $reservation->programme_id
                : $reservation->programme->programme_retour_id;

            if ($programScanId != $expectedProgramId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le programme sélectionné ne correspond pas au trajet à scanner.',
                ], 400);
            }
        }

     $updateData = [
            'embarquement_scanned_at' => now(),
            'embarquement_agent_id' => $agent->id,
            'embarquement_vehicule_id' => $vehicule->id,
            'embarquement_status' => 'scanned',
            'statut' => 'terminee', // <-- AJOUTE CETTE LIGNE
        ];

        $message = "";

        if ($targetScan === 'aller') {
            if ($reservation->statut_aller === 'terminee') {
                return response()->json([
                    'success' => false,
                    'message' => 'Trajet Aller déjà scanné.',
                ], 400);
            }
            $updateData['statut_aller'] = 'terminee';
            $message = "Embarquement ALLER validé.";
        } else {
            if ($reservation->statut_retour === 'terminee') {
                return response()->json([
                    'success' => false,
                    'message' => 'Trajet Retour déjà scanné.',
                ], 400);
            }
            $updateData['statut_retour'] = 'terminee';
            $message = "Embarquement RETOUR validé.";
        }

        $reservation->update($updateData);

        // ❌ SUPPRIME COMPLETEMENT CE BLOC :
        // if (!$reservation->is_aller_retour && $reservation->statut_aller === 'terminee') { ... }
        // elseif ($reservation->is_aller_retour && ...) { ... }

        return response()->json([
            'success' => true,
            'message' => $message . ' Passager: ' . $reservation->passager_nom . ' (Siège ' . $reservation->seat_number . ')',
            'reservation' => [
                'id' => $reservation->id,
                'reference' => $reservation->reference,
                'statut' => $reservation->statut, // Affichera désormais bien "terminee"
                'statut_aller' => $reservation->statut_aller,
                'statut_retour' => $reservation->statut_retour,
                'vehicule_id' => $reservation->embarquement_vehicule_id,
            ],
        ]);
    }

    /**
     * Récupérer les véhicules disponibles pour le scan
     */
    public function getVehicles(Request $request)
    {
        $agent = $request->user();

        $vehicules = Vehicule::where('compagnie_id', $agent->compagnie_id)
            ->where('is_active', true)
            ->get()
            ->map(function($vehicule) {
                return [
                    'id' => $vehicule->id,
                    'marque' => $vehicule->marque,
                    'modele' => $vehicule->modele,
                    'immatriculation' => $vehicule->immatriculation,
                    'nombre_place' => $vehicule->nombre_place,
                ];
            });

        return response()->json([
            'success' => true,
            'vehicules' => $vehicules,
        ]);
    }

    /**
     * Récupérer les programmes du jour pour le scan
     * (Aligné avec la logique web : filtrage par gare, statut actif, voyages)
     */
    public function getProgrammesForScan(Request $request)
    {
        $agent = $request->user();
        $today = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->format('H:i');

        $programmes = Programme::where('compagnie_id', $agent->compagnie_id)
            ->where('gare_depart_id', $agent->gare_id)
            ->where('statut', 'actif')
            ->where(function ($query) use ($today) {
                $query->whereDate('date_depart', $today)
                      ->orWhere(function ($q) use ($today) {
                          $q->where('date_depart', '<=', $today)
                            ->where('date_fin', '>=', $today);
                      });
            })
            ->where('heure_depart', '>', $currentTime)
            ->whereDoesntHave('voyages', function ($q) use ($today) {
                $q->whereDate('date_voyage', $today)
                  ->whereIn('statut', ['en_cours', 'terminé']);
            })
            ->with(['gareDepart', 'gareArrivee', 'voyages.vehicule'])
            ->orderBy('heure_depart')
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'point_depart' => $p->point_depart,
                    'point_arrive' => $p->point_arrive,
                    'heure_depart' => $p->heure_depart,
                    'gare_depart' => $p->gareDepart ? $p->gareDepart->nom_gare : null,
                    'gare_arrivee' => $p->gareArrivee ? $p->gareArrivee->nom_gare : null,
                    'vehicule_id' => $p->vehicule ? $p->vehicule->id : null,
                    'immatriculation' => $p->vehicule->immatriculation ?? 'N/A',
                ];
            });

        return response()->json([
            'success' => true,
            'programmes' => $programmes,
        ]);
    }

    /**
     * Historique des scans (nouveau endpoint, aligné avec le web)
     */
    public function historique(Request $request)
    {
        $agent = $request->user();
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $type = $request->get('type');
        $trajetFilter = $request->get('trajet');

        // Requête de base pour les scans
        $query = Reservation::with(['programme.gareDepart', 'programme.gareArrivee', 'embarquementVehicule'])
            ->whereHas('programme', function ($q) use ($agent) {
                $q->where('compagnie_id', $agent->compagnie_id);
            })
            ->whereNotNull('embarquement_scanned_at');

        // Filtre par date
        if ($date) {
            $query->whereDate('embarquement_scanned_at', $date);
        }

        // Filtre par type
        if ($type) {
            if ($type === 'aller_simple') {
                $query->where('is_aller_retour', false);
            } elseif ($type === 'aller') {
                $query->where('is_aller_retour', true)
                      ->where('statut_aller', 'terminee');
            } elseif ($type === 'retour') {
                $query->where('is_aller_retour', true)
                      ->where('statut_retour', 'terminee');
            }
        }

        // Filtre par trajet
        if ($trajetFilter) {
            $query->whereHas('programme', function ($q) use ($trajetFilter) {
                $q->whereRaw("CONCAT(point_depart, ' → ', point_arrive) = ?", [$trajetFilter]);
            });
        }

        $scans = $query->orderBy('embarquement_scanned_at', 'desc')->paginate(20);

        // Statistiques
        $statsQuery = Reservation::whereHas('programme', function ($q) use ($agent) {
            $q->where('compagnie_id', $agent->compagnie_id);
        })->whereNotNull('embarquement_scanned_at');

        if ($date) {
            $statsQuery->whereDate('embarquement_scanned_at', $date);
        }

        $allScans = $statsQuery->get();

        $stats = [
            'total' => $allScans->count(),
            'aller_simple' => $allScans->where('is_aller_retour', false)->count(),
            'aller' => $allScans->where('is_aller_retour', true)->where('statut_aller', 'terminee')->count(),
            'retour' => $allScans->where('is_aller_retour', true)->where('statut_retour', 'terminee')->count(),
        ];

        // Liste des trajets pour le filtre
        $trajets = Programme::where('compagnie_id', $agent->compagnie_id)
            ->selectRaw("CONCAT(point_depart, ' → ', point_arrive) as trajet")
            ->distinct()
            ->pluck('trajet');

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'trajets' => $trajets,
            'scans' => $scans->map(function($scan) {
                return [
                    'id' => $scan->id,
                    'reference' => $scan->reference,
                    'passager_nom' => $scan->passager_prenom . ' ' . $scan->passager_nom,
                    'passager_telephone' => $scan->passager_telephone,
                    'seat_number' => $scan->seat_number,
                    'seat_label' => 'SIEGE #' . $scan->seat_number,
                    'case_label' => 'Case #' . $scan->seat_number,
                    'is_aller_retour' => $scan->is_aller_retour,
                    'statut_aller' => $scan->statut_aller,
                    'statut_retour' => $scan->statut_retour,
                    'embarquement_scanned_at' => $scan->embarquement_scanned_at ? Carbon::parse($scan->embarquement_scanned_at)->format('d/m/Y H:i') : null,
                    'heure_scan' => $scan->embarquement_scanned_at ? Carbon::parse($scan->embarquement_scanned_at)->format('H:i') : null,
                    'vehicule' => $scan->embarquementVehicule ? $scan->embarquementVehicule->immatriculation : 'N/A',
                    'num_car' => $scan->embarquementVehicule ? '#' . $scan->embarquementVehicule->immatriculation : 'N/A',
                    'trajet' => $scan->programme ? $scan->programme->point_depart . ' → ' . $scan->programme->point_arrive : '',
                    'point_depart' => $scan->programme ? $scan->programme->point_depart : '',
                    'point_arrivee' => $scan->programme ? $scan->programme->point_arrive : '',
                    'heure_depart' => $scan->programme ? $scan->programme->heure_depart : '',
                ];
            }),
            'pagination' => [
                'current_page' => $scans->currentPage(),
                'last_page' => $scans->lastPage(),
                'per_page' => $scans->perPage(),
                'total' => $scans->total(),
            ],
        ]);
    }

    /**
     * Historique des scans optimisé pour l'application mobile (inspiré par les maquettes)
     */
    public function scanHistory(Request $request)
    {
        $agent = $request->user();
        $date = $request->get('date');

        // Requête sur les réservations scannées
        $query = Reservation::with(['programme.gareDepart', 'programme.gareArrivee', 'embarquementVehicule'])
            ->whereHas('programme', function ($q) use ($agent) {
                $q->where('compagnie_id', $agent->compagnie_id);
            })
            ->whereNotNull('embarquement_scanned_at');

        // Filtre par date de scan (uniquement si spécifié)
        if ($date) {
            $query->whereDate('embarquement_scanned_at', $date);
        }

        // Filtre de recherche par passager ou référence
        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('passager_nom', 'like', "%{$search}%")
                  ->orWhere('passager_prenom', 'like', "%{$search}%");
            });
        }

        $scans = $query->orderBy('embarquement_scanned_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'total_scans' => $scans->count(), // Pour le badge (ex: "4 SCANS")
            'date_filtre' => $date,
            'scans' => $scans->map(function($scan) {
                $scanAt = Carbon::parse($scan->embarquement_scanned_at);
                return [
                    'id' => $scan->id,
                    'reference' => $scan->reference,
                    'passager_nom' => $scan->passager_prenom . ' ' . $scan->passager_nom,
                    'passager_telephone' => $scan->passager_telephone,
                    'seat_number' => $scan->seat_number,
                    'seat_label' => 'SIEGE #' . $scan->seat_number,
                    'case_label' => 'Case #' . $scan->seat_number,
                    'trajet' => $scan->programme ? $scan->programme->point_depart . ' → ' . $scan->programme->point_arrive : '',
                    'point_depart' => $scan->programme ? $scan->programme->point_depart : '',
                    'point_arrivee' => $scan->programme ? $scan->programme->point_arrive : '',
                    'num_car' => $scan->embarquementVehicule ? '#' . $scan->embarquementVehicule->immatriculation : 'N/A',
                    'heure_scan' => $scanAt->format('H:i'),
                    'date_heure_scan' => $scanAt->translatedFormat('d M Y à H:i'),
                    'statut' => 'VALIDE',
                ];
            }),
        ]);
    }

    /**
     * Rechercher une réservation par référence (recherche manuelle)
     */
    public function searchByReference(Request $request)
    {
        $request->validate(['reference' => 'required|string']);

        $reservation = Reservation::with(['programme.gareDepart', 'programme.gareArrivee', 'user', 'agentEmbarquement', 'embarquementVehicule'])
            ->where('reference', $request->reference)
            ->first();

        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Réservation non trouvée.'], 404);
        }

        $agent = $request->user();
        if ($reservation->programme->compagnie_id !== $agent->compagnie_id) {
            return response()->json(['success' => false, 'message' => 'Ce billet n\'appartient pas à votre compagnie.'], 403);
        }

        $res = [
            'id' => $reservation->id,
            'reference' => $reservation->reference,
            'statut' => $reservation->statut,
            'passager_nom' => $reservation->passager_nom,
            'passager_prenom' => $reservation->passager_prenom,
            'passager_telephone' => $reservation->passager_telephone,
            'passager_email' => $reservation->passager_email,
            'passager_urgence' => $reservation->passager_urgence,
            'seat_number' => $reservation->seat_number,
            'montant' => number_format($reservation->montant ?? 0, 0, ',', ' ') . ' FCFA',
            'trajet' => $reservation->programme ? $reservation->programme->point_depart . ' → ' . $reservation->programme->point_arrive : '',
            'date_voyage' => $reservation->date_voyage ? Carbon::parse($reservation->date_voyage)->format('d/m/Y') : '',
            'heure_depart' => $reservation->programme ? $reservation->programme->heure_depart : '',
            'is_aller_retour' => $reservation->is_aller_retour,
            'statut_aller' => $reservation->statut_aller,
            'statut_retour' => $reservation->statut_retour,
            'gare_depart' => optional($reservation->programme->gareDepart)->nom_gare ?? '',
            'gare_arrivee' => optional($reservation->programme->gareArrivee)->nom_gare ?? '',
            'created_at' => $reservation->created_at ? $reservation->created_at->format('d/m/Y H:i') : '',
        ];

        if ($reservation->embarquement_scanned_at) {
            $res['embarquement'] = [
                'scanned_at' => Carbon::parse($reservation->embarquement_scanned_at)->format('d/m/Y H:i'),
                'agent' => $reservation->agentEmbarquement ? $reservation->agentEmbarquement->nom_complet : 'N/A',
                'vehicule' => $reservation->embarquementVehicule ? $reservation->embarquementVehicule->immatriculation : 'N/A',
            ];
        }

        return response()->json([
            'success' => true,
            'reservation' => $res,
        ]);
    }

    /**
     * Récupérer les réservations d'un programme pour une date donnée
     */
    public function getReservationsForProgramme(Request $request, $programmeId)
    {
        $agent = $request->user();
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        $programme = Programme::where('id', $programmeId)
            ->where('compagnie_id', $agent->compagnie_id)
            ->first();

        if (!$programme) {
            return response()->json([
                'success' => false,
                'message' => 'Programme non trouvé ou non autorisé.',
            ], 404);
        }

        $reservations = Reservation::where('programme_id', $programmeId)
            ->where(function($query) use ($date) {
                $query->whereDate('date_voyage', $date)
                      ->orWhereDate('date_retour', $date);
            })
            ->whereIn('statut', ['confirmee', 'terminee'])
            ->orderBy('seat_number')
            ->get()
            ->map(function($reservation) use ($date) {
                $today = Carbon::parse($date);
                $isRetourDay = $reservation->date_retour &&
                               Carbon::parse($reservation->date_retour)->isSameDay($today) &&
                               !Carbon::parse($reservation->date_voyage)->isSameDay($today);

                return [
                    'id' => $reservation->id,
                    'reference' => $reservation->reference,
                    'passager_nom' => $reservation->passager_nom,
                    'passager_prenom' => $reservation->passager_prenom,
                    'passager_telephone' => $reservation->passager_telephone,
                    'seat_number' => $reservation->seat_number,
                    'is_aller_retour' => $reservation->is_aller_retour,
                    'type_trajet' => $isRetourDay ? 'RETOUR' : 'ALLER',
                    'statut_aller' => $reservation->statut_aller,
                    'statut_retour' => $reservation->statut_retour,
                    'is_scanned' => $isRetourDay
                        ? $reservation->statut_retour === 'terminee'
                        : $reservation->statut_aller === 'terminee',
                ];
            });

        $stats = [
            'total' => $reservations->count(),
            'scanned' => $reservations->where('is_scanned', true)->count(),
            'pending' => $reservations->where('is_scanned', false)->count(),
        ];

        return response()->json([
            'success' => true,
            'programme' => [
                'id' => $programme->id,
                'point_depart' => $programme->point_depart,
                'point_arrive' => $programme->point_arrive,
                'heure_depart' => $programme->heure_depart,
            ],
            'date' => $date,
            'stats' => $stats,
            'reservations' => $reservations,
        ]);
    }

    /**
     * Détails d'une réservation spécifique
     */
    public function showReservation(Request $request, $reservationId)
    {
        $agent = $request->user();

        $reservation = Reservation::with(['programme.gareDepart', 'programme.gareArrivee', 'user'])
            ->whereHas('programme', function($q) use ($agent) {
                $q->where('compagnie_id', $agent->compagnie_id);
            })
            ->find($reservationId);

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Réservation non trouvée.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'reservation' => [
                'id' => $reservation->id,
                'reference' => $reservation->reference,
                'passager_nom' => $reservation->passager_nom,
                'passager_prenom' => $reservation->passager_prenom,
                'passager_email' => $reservation->passager_email,
                'passager_telephone' => $reservation->passager_telephone,
                'seat_number' => $reservation->seat_number,
                'date_voyage' => $reservation->date_voyage,
                'date_retour' => $reservation->date_retour,
                'is_aller_retour' => $reservation->is_aller_retour,
                'montant' => number_format($reservation->montant ?? 0, 0, ',', ' ') . ' FCFA',
                'statut' => $reservation->statut,
                'statut_aller' => $reservation->statut_aller,
                'statut_retour' => $reservation->statut_retour,
                'embarquement_scanned_at' => $reservation->embarquement_scanned_at,
                'programme' => $reservation->programme ? [
                    'id' => $reservation->programme->id,
                    'point_depart' => $reservation->programme->point_depart,
                    'point_arrive' => $reservation->programme->point_arrive,
                    'heure_depart' => $reservation->programme->heure_depart,
                    'gare_depart' => optional($reservation->programme->gareDepart)->nom_gare ?? '',
                    'gare_arrivee' => optional($reservation->programme->gareArrivee)->nom_gare ?? '',
                    'vehicule' => $reservation->programme->getVehiculeForDate($reservation->date_voyage) ? [
                        'marque' => $reservation->programme->getVehiculeForDate($reservation->date_voyage)->marque,
                        'modele' => $reservation->programme->getVehiculeForDate($reservation->date_voyage)->modele,
                        'immatriculation' => $reservation->programme->getVehiculeForDate($reservation->date_voyage)->immatriculation,
                    ] : null,
                ] : null,
            ],
        ]);
    }

    /**
     * Helper: formater une réservation pour la réponse JSON
     */
    private function formatReservation($reservation)
    {
        return [
            'id' => $reservation->id,
            'reference' => $reservation->reference,
            'passager_nom' => $reservation->passager_nom,
            'passager_prenom' => $reservation->passager_prenom,
            'passager_telephone' => $reservation->passager_telephone,
            'seat_number' => $reservation->seat_number,
            'date_voyage' => $reservation->date_voyage,
            'statut' => $reservation->statut,
            'statut_aller' => $reservation->statut_aller,
            'statut_retour' => $reservation->statut_retour,
            'is_aller_retour' => $reservation->is_aller_retour,
            'montant' => number_format($reservation->montant ?? 0, 0, ',', ' ') . ' FCFA',
            'programme' => $reservation->programme ? [
                'id' => $reservation->programme->id,
                'point_depart' => $reservation->programme->point_depart,
                'point_arrive' => $reservation->programme->point_arrive,
                'heure_depart' => $reservation->programme->heure_depart,
                'gare_depart' => optional($reservation->programme->gareDepart)->nom_gare ?? '',
                'gare_arrivee' => optional($reservation->programme->gareArrivee)->nom_gare ?? '',
            ] : null,
        ];
    }
}
