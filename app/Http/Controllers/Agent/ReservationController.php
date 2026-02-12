<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Programme;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index()
    {
        $agent = Auth::guard('agent')->user();

        $now = Carbon::now();
        $heureMinimum = $now->copy()->subMinutes(30)->format('H:i');
        $today = Carbon::today()->toDateString();

        // Récupérer les programmes du jour (FlixBus Model: Simple date check)
        // On cherche les programmes dont la date de départ est aujourd'hui
        // OU les programmes "continus" qui incluent aujourd'hui (si date_fin existe)
        $programmesDuJour = Programme::where('compagnie_id', $agent->compagnie_id)
            ->where('statut', 'actif')
            ->where(function ($query) use ($today) {
                // Cas standard : Voyage à date unique
                $query->whereDate('date_depart', $today)
                      // Cas étendu : Période de validité (si utilisé)
                      ->orWhere(function ($q) use ($today) {
                          $q->where('date_depart', '<=', $today)
                            ->where('date_fin', '>=', $today);
                      });
            })
            ->where('heure_depart', '>=', $heureMinimum)
            ->with('vehicule')
            ->orderBy('heure_depart')
            ->get();

        // Récupération des réservations
        $reservations = Reservation::with(['programme', 'user'])
            ->whereHas('programme', function ($query) use ($agent) {
                $query->where('compagnie_id', $agent->compagnie_id);
            })
            ->orderBy('date_voyage', 'desc')
            ->limit(50)
            ->get();

        $enCours = $reservations->whereNotIn('statut', ['terminee', 'annulee']);
        $terminees = $reservations->where('statut', 'terminee');

        return view('agent.reservations.reservation', compact('enCours', 'terminees', 'programmesDuJour'));
    }

    /**
     * Afficher la page de recherche de réservations
     */
    public function recherchePage()
    {
        $agent = Auth::guard('agent')->user();

        // Récupérer les programmes du jour pour la recherche
        $now = Carbon::now();
        $heureMinimum = $now->copy()->subMinutes(30)->format('H:i');
        $today = Carbon::today()->toDateString();

        $programmesDuJour = Programme::where('compagnie_id', $agent->compagnie_id)
            ->where('statut', 'actif')
            ->where(function ($query) use ($today) {
                $query->whereDate('date_depart', $today)
                      ->orWhere(function ($q) use ($today) {
                          $q->where('date_depart', '<=', $today)
                            ->where('date_fin', '>=', $today);
                      });
            })
            ->where('heure_depart', '>=', $heureMinimum)
            ->with('vehicule')
            ->orderBy('heure_depart')
            ->get();

        // Récupérer les derniers scans d'aujourd'hui pour cet agent
        $derniersScans = Reservation::with('programme')
            ->whereHas('programme', function ($q) use ($agent) {
                $q->where('compagnie_id', $agent->compagnie_id);
            })
            ->where('embarquement_agent_id', $agent->id) // Filtrer par l'agent actuel
            ->whereDate('embarquement_scanned_at', Carbon::today())
            ->orderBy('embarquement_scanned_at', 'desc')
            ->limit(10)
            ->get();

        return view('agent.reservations.recherche', compact('programmesDuJour', 'derniersScans'));
    }

    /**
     * Afficher l'historique des scans
     */
    public function historique(Request $request)
    {
        $agent = Auth::guard('agent')->user();
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $type = $request->get('type');
        $trajetFilter = $request->get('trajet');

        // Requête de base pour les scans
        $query = Reservation::with(['programme', 'embarquementVehicule'])
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

        // Récupérer les scans paginés
        $scans = $query->orderBy('embarquement_scanned_at', 'desc')->paginate(20);

        // Statistiques pour la date sélectionnée
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

        return view('agent.reservations.historique', compact('scans', 'stats', 'trajets'));
    }

    /**
     * Rechercher une réservation pour scan (AJAX)
     */
    public function search(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
            'programme_id' => 'nullable|integer', // L'ID du programme sélectionné dans le modal
        ]);

        // On charge la réservation avec son programme ALLER par défaut
        $reservation = Reservation::with(['programme.vehicule', 'user'])
            ->where('reference', $request->reference)
            ->first();

        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Réservation non trouvée.'], 404);
        }

        $agent = Auth::guard('agent')->user();
        if ($reservation->programme->compagnie_id !== $agent->compagnie_id) {
            return response()->json(['success' => false, 'message' => 'Ce billet n\'appartient pas à votre compagnie.'], 403);
        }

        // --- LOGIQUE CIBLE (Aller ou Retour) ---
        $targetScan = null;
        $programScanId = $request->input('programme_id');

        // Variables pour l'affichage correct (Heure et Trajet du programme ACTUELLEMENT scanné)
        $programmeActuel = null;

        // 1. DÉTECTION VIA LE PROGRAMME SÉLECTIONNÉ (Cas Robuste)
        if ($programScanId) {
            // Cas A : L'agent a sélectionné le programme qui correspond à l'ALLER du billet
            if ($programScanId == $reservation->programme_id) {
                $targetScan = 'aller';
                $programmeActuel = $reservation->programme;
            }
            // Cas B : L'agent a sélectionné le programme qui correspond au RETOUR du billet
            elseif ($reservation->programme->programme_retour_id == $programScanId) {
                $targetScan = 'retour';
                // IMPORTANT : On doit récupérer les infos du programme retour pour l'affichage (heure, etc.)
                $programmeActuel = Programme::find($programScanId);
            }
            // Cas C : Le billet n'a rien à voir avec le bus sélectionné
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce billet ne correspond pas au trajet sélectionné.'
                ], 400);
            }
        }
        // 2. FALLBACK (Si pas de programme sélectionné, déprécié mais géré)
        else {
            // Logique par date (inchangée, mais moins fiable)
            // ... (ton code existant pour la date) ...
            // Pour simplifier ici, on assume que programmeActuel = reservation->programme
            $programmeActuel = $reservation->programme;
            $targetScan = 'aller'; // Par défaut
        }

        // --- Vérification du statut ---
        $statutActuel = ($targetScan === 'aller') ? $reservation->statut_aller : $reservation->statut_retour;

        if ($statutActuel === 'terminee') {
            return response()->json([
                'success' => false,
                'message' => "Le trajet " . strtoupper($targetScan) . " a déjà été validé.",
                'already_scanned' => true
            ], 400);
        }

        // --- PRÉPARATION DES DONNÉES D'AFFICHAGE ---
        // Ici on utilise $programmeActuel pour avoir la BONNE heure et le BON trajet (Aller ou Retour)

        $heureDepart = $programmeActuel ? $programmeActuel->heure_depart : $reservation->programme->heure_depart;
        $trajetLabel = $programmeActuel
            ? ($programmeActuel->point_depart . ' → ' . $programmeActuel->point_arrive)
            : ($reservation->programme->point_depart . ' → ' . $reservation->programme->point_arrive);

        return response()->json([
            'success' => true,
            'reservation' => [
                'id' => $reservation->id,
                'reference' => $reservation->reference,
                'passager_nom_complet' => $reservation->passager_prenom . ' ' . $reservation->passager_nom,
                'passager_telephone' => $reservation->passager_telephone,
                'seat_number' => $reservation->seat_number,
                // On affiche la date du jour pour le scan, ou la date prévue
                'date_voyage' => Carbon::parse($programmeActuel->date_depart ?? now())->format('d/m/Y'),
                'trajet' => $trajetLabel,
                'heure_depart' => $heureDepart, // <--- C'est ici que ça change (10:00 ou 20:00 selon le scan)
                'type_scan' => strtoupper($targetScan)
            ]
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
        $agent = Auth::guard('agent')->user();
        $vehicule = Vehicule::find($request->vehicule_id);

        if (!$reservation || !$vehicule) {
            return response()->json(['success' => false, 'message' => 'Donnees invalides.'], 400);
        }

        // --- NOUVELLE LOGIQUE : DÉTECTION PAR DATE ---
        $today = Carbon::today();
        $dateAller = Carbon::parse($reservation->date_voyage)->startOfDay();
        $dateRetour = $reservation->date_retour ? Carbon::parse($reservation->date_retour)->startOfDay() : null;

        $isDayAller = $dateAller->equalTo($today);
        $isDayRetour = $dateRetour && $dateRetour->equalTo($today);

        $targetScan = null;

        // Vérification que la date correspond à un trajet
        if (!$isDayAller && !$isDayRetour) {
            return response()->json([
                'success' => false,
                'message' => 'Cette réservation n\'est pas valable pour aujourd\'hui.'
            ], 400);
        }

        // Détection du trajet à scanner
        if ($isDayAller && $isDayRetour) {
            // Les deux dates sont aujourd'hui
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
                    'message' => 'Le programme sélectionné ne correspond pas au trajet à scanner.'
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
                return response()->json(['success' => false, 'message' => 'Trajet Aller déjà scanné.'], 400);
            }
            $updateData['statut_aller'] = 'terminee';
            $message = "Embarquement ALLER validé.";
        } else {
            if ($reservation->statut_retour === 'terminee') {
                return response()->json(['success' => false, 'message' => 'Trajet Retour déjà scanné.'], 400);
            }
            $updateData['statut_retour'] = 'terminee';
            $message = "Embarquement RETOUR validé.";
        }

        // Mise à jour
        $reservation->update($updateData);

        // Mise à jour du statut global
        if (!$reservation->is_aller_retour && $reservation->statut_aller === 'terminee') {
            $reservation->update(['statut' => 'terminee']);
        } elseif ($reservation->is_aller_retour && $reservation->statut_aller === 'terminee' && $reservation->statut_retour === 'terminee') {
            $reservation->update(['statut' => 'terminee']);
        }

        return response()->json([
            'success' => true,
            'message' => $message . ' Passager: ' . $reservation->passager_nom . ' (Siège ' . $reservation->seat_number . ')'
        ]);
    }


    /**
     * Ancienne méthode scan (conservée pour compatibilité)
     */
    public function scan(Request $request)
    {
        $request->validate([
            'reference' => 'required|string|exists:reservations,reference',
        ]);

        $reservation = Reservation::where('reference', $request->reference)->first();
        $agent = Auth::guard('agent')->user();

        // Vérifier si la réservation appartient à la compagnie de l'agent
        if ($reservation->programme->compagnie_id !== $agent->compagnie_id) {
            return back()->with('error', 'Cette réservation n\'appartient pas à votre compagnie.');
        }

        if ($reservation->statut === 'terminee') {
            return back()->with('warning', 'Cette réservation a déjà été scannée et terminée.');
        }

        if ($reservation->statut !== 'confirmee') {
            return back()->with('error', 'Statut de réservation invalide pour le scan.');
        }

        // Mettre à jour le statut
        $reservation->update([
            'statut' => 'terminee',
            'embarquement_scanned_at' => now(),
            'embarquement_agent_id' => $agent->id,
        ]);

        return back()->with('success', 'Réservation scannée et terminée avec succès.');
    }
}
