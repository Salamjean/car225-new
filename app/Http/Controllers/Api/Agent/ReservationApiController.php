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
     * Liste des réservations de la compagnie
     */
    public function index(Request $request)
    {
        $agent = $request->user();

        $query = Reservation::with(['programme', 'user'])
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

        return response()->json([
            'success' => true,
            'reservations' => $reservations->map(function($reservation) {
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
                    'programme' => $reservation->programme ? [
                        'point_depart' => $reservation->programme->point_depart,
                        'point_arrive' => $reservation->programme->point_arrive,
                        'heure_depart' => $reservation->programme->heure_depart,
                    ] : null,
                ];
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
     * Rechercher une réservation par référence QR
     */
    public function search(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
            'vehicule_id' => 'nullable|integer',
            'programme_id' => 'nullable|integer', // Pour detection aller/retour
        ]);

        $reservation = Reservation::with(['programme', 'user', 'embarquementVehicule'])
            ->where('reference', $request->reference)
            ->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation non trouvee.',
            ], 404);
        }

        $agent = $request->user();
        if ($reservation->programme->compagnie_id !== $agent->compagnie_id) {
            return response()->json([
                'success' => false,
                'message' => 'Ce billet n\'appartient pas a votre compagnie.',
            ], 403);
        }

        // --- DETECTION PAR PROGRAMME_ID (prioritaire) ---
        $programScanId = $request->input('programme_id');
        $targetScan = null;
        $programmeActuel = null;

        if ($programScanId) {
            if ($programScanId == $reservation->programme_id) {
                $targetScan = 'aller';
                $programmeActuel = $reservation->programme;
            } elseif ($reservation->programme->programme_retour_id == $programScanId) {
                $targetScan = 'retour';
                $programmeActuel = Programme::find($programScanId);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce billet ne correspond pas au trajet selectionne.',
                ], 400);
            }
        } else {
            // Fallback par date
            $today = Carbon::today();
            $dateAller = Carbon::parse($reservation->date_voyage)->startOfDay();
            $dateRetour = $reservation->date_retour ? Carbon::parse($reservation->date_retour)->startOfDay() : null;

            $isDayAller = $dateAller->equalTo($today);
            $isDayRetour = $reservation->is_aller_retour && $dateRetour && $dateRetour->equalTo($today);

            if ($isDayAller && $isDayRetour) {
                $targetScan = ($reservation->statut_aller === 'terminee') ? 'retour' : 'aller';
            } elseif ($isDayAller) {
                $targetScan = 'aller';
            } elseif ($isDayRetour) {
                $targetScan = 'retour';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Date invalide pour ce billet.',
                ], 400);
            }
            $programmeActuel = $reservation->programme;
        }

        // Trajet label selon le programme actuel
        if ($targetScan === 'aller') {
            $statutActuel = $reservation->statut_aller;
            $trajetLabel = "ALLER : " . $programmeActuel->point_depart . ' -> ' . $programmeActuel->point_arrive;
        } else {
            $statutActuel = $reservation->statut_retour;
            $trajetLabel = "RETOUR : " . ($programmeActuel ? $programmeActuel->point_depart . ' -> ' . $programmeActuel->point_arrive : $reservation->programme->point_arrive . ' -> ' . $reservation->programme->point_depart);
        }

        // Si ce trajet spécifique est déjà terminé
        if ($statutActuel === 'terminee') {
            return response()->json([
                'success' => false,
                'message' => "Le trajet " . strtoupper($targetScan) . " a déjà été validé et consommé.",
                'already_scanned' => true,
            ], 400);
        }

        if ($statutActuel !== 'confirmee') {
            return response()->json([
                'success' => false,
                'message' => "Le statut du trajet $targetScan n'est pas valide ($statutActuel).",
            ], 400);
        }

        return response()->json([
            'success' => true,
            'reservation' => [
                'id' => $reservation->id,
                'reference' => $reservation->reference,
                'passager_nom_complet' => $reservation->passager_prenom . ' ' . $reservation->passager_nom,
                'passager_telephone' => $reservation->passager_telephone,
                'seat_number' => $reservation->seat_number,
                'date_voyage' => $today->format('d/m/Y'),
                'trajet' => $trajetLabel,
                'heure_depart' => $reservation->programme->heure_depart,
                'type_scan' => strtoupper($targetScan),
                'is_aller_retour' => $reservation->is_aller_retour,
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
            'programme_id' => 'nullable|integer', // Pour detection aller/retour
        ]);

        $reservation = Reservation::with('programme')->where('reference', $request->reference)->first();
        $agent = $request->user();
        $vehicule = Vehicule::find($request->vehicule_id);

        if (!$reservation || !$vehicule) {
            return response()->json([
                'success' => false,
                'message' => 'Donnees invalides.',
            ], 400);
        }

        // --- DETECTION PAR PROGRAMME_ID (prioritaire) ---
        $programScanId = $request->input('programme_id');
        $targetScan = null;

        if ($programScanId) {
            if ($programScanId == $reservation->programme_id) {
                $targetScan = 'aller';
            } elseif ($reservation->programme->programme_retour_id == $programScanId) {
                $targetScan = 'retour';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce billet ne correspond pas au trajet selectionne.',
                ], 400);
            }
        } else {
            // Fallback par date
            $today = Carbon::today();
            $dateAller = Carbon::parse($reservation->date_voyage)->startOfDay();
            $dateRetour = $reservation->date_retour ? Carbon::parse($reservation->date_retour)->startOfDay() : null;

            $isDayAller = $dateAller->equalTo($today);
            $isDayRetour = $reservation->is_aller_retour && $dateRetour && $dateRetour->equalTo($today);

            if ($isDayAller && $isDayRetour) {
                $targetScan = ($reservation->statut_aller === 'terminee') ? 'retour' : 'aller';
            } elseif ($isDayAller) {
                $targetScan = 'aller';
            } elseif ($isDayRetour) {
                $targetScan = 'retour';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Date invalide.',
                ], 400);
            }
        }

        $updateData = [
            'embarquement_scanned_at' => now(),
            'embarquement_agent_id' => $agent->id,
            'embarquement_vehicule_id' => $vehicule->id,
            'embarquement_status' => 'scanned'
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

        // Mise à jour du statut global
        if (!$reservation->is_aller_retour && $reservation->statut_aller === 'terminee') {
            $reservation->update(['statut' => 'terminee']);
        } elseif ($reservation->is_aller_retour && $reservation->statut_aller === 'terminee' && $reservation->statut_retour === 'terminee') {
            $reservation->update(['statut' => 'terminee']);
        }

        return response()->json([
            'success' => true,
            'message' => $message . ' Passager: ' . $reservation->passager_nom . ' (Siège ' . $reservation->seat_number . ')',
            'reservation' => [
                'id' => $reservation->id,
                'reference' => $reservation->reference,
                'statut' => $reservation->statut,
                'statut_aller' => $reservation->statut_aller,
                'statut_retour' => $reservation->statut_retour,
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
     */
    public function getProgrammesForScan(Request $request)
    {
        $agent = $request->user();
        $today = Carbon::today();
        $now = Carbon::now();
        $heureMinimum = $now->copy()->subMinutes(30)->format('H:i'); // 30 min de marge

        $programmes = Programme::where('compagnie_id', $agent->compagnie_id)
            ->where('heure_depart', '>=', $heureMinimum) // FILTRE PAR HEURE
            ->where(function($query) use ($today) {
                $query->where(function($q) use ($today) {
                    $q->where('type_programmation', 'ponctuel')
                      ->whereDate('date_depart', $today);
                })
                ->orWhere(function($q) use ($today) {
                    $joursFrancais = [
                        'monday' => 'lundi',
                        'tuesday' => 'mardi',
                        'wednesday' => 'mercredi',
                        'thursday' => 'jeudi',
                        'friday' => 'vendredi',
                        'saturday' => 'samedi',
                        'sunday' => 'dimanche'
                    ];
                    $jourSemaine = $joursFrancais[strtolower($today->format('l'))];
                    
                    $q->where('type_programmation', 'recurrent')
                      ->whereDate('date_depart', '<=', $today)
                      ->where(function($dateCheck) use ($today) {
                          $dateCheck->whereNull('date_fin_programmation')
                                   ->orWhereDate('date_fin_programmation', '>=', $today);
                      })
                      ->where(function($dayCheck) use ($jourSemaine) {
                          $dayCheck->whereJsonContains('jours_recurrence', $jourSemaine)
                                   ->orWhere('jours_recurrence', 'like', "%{$jourSemaine}%");
                      });
                });
            })
            ->with('compagnie')
            ->orderBy('heure_depart')
            ->get()
            ->map(function($programme) {
                return [
                    'id' => $programme->id,
                    'point_depart' => $programme->point_depart,
                    'point_arrive' => $programme->point_arrive,
                    'heure_depart' => $programme->heure_depart,
                    'vehicule' => $programme->getVehiculeForDate($today) ? [
                        'id' => $programme->getVehiculeForDate($today)->id,
                        'marque' => $programme->getVehiculeForDate($today)->marque,
                        'modele' => $programme->getVehiculeForDate($today)->modele,
                        'immatriculation' => $programme->getVehiculeForDate($today)->immatriculation,
                    ] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'programmes' => $programmes,
        ]);
    }

    /**
     * Récupérer les réservations d'un programme pour une date donnée
     * Utile pour voir la liste des passagers à embarquer
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

        $reservation = Reservation::with(['programme', 'user'])
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
                'montant' => $reservation->montant,
                'statut' => $reservation->statut,
                'statut_aller' => $reservation->statut_aller,
                'statut_retour' => $reservation->statut_retour,
                'embarquement_scanned_at' => $reservation->embarquement_scanned_at,
                'programme' => $reservation->programme ? [
                    'id' => $reservation->programme->id,
                    'point_depart' => $reservation->programme->point_depart,
                    'point_arrive' => $reservation->programme->point_arrive,
                    'heure_depart' => $reservation->programme->heure_depart,
                    'vehicule' => $reservation->programme->getVehiculeForDate($reservation->date_voyage) ? [
                        'marque' => $reservation->programme->getVehiculeForDate($reservation->date_voyage)->marque,
                        'modele' => $reservation->programme->getVehiculeForDate($reservation->date_voyage)->modele,
                        'immatriculation' => $reservation->programme->getVehiculeForDate($reservation->date_voyage)->immatriculation,
                    ] : null,
                ] : null,
            ],
        ]);
    }
}
