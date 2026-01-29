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
        
        // --- CORRECTION 1 : Filtrage des programmes ---
        // On récupère les programmes du jour.
        // Si tu as un champ 'ville' sur l'agent, ajoute : ->where('point_depart', $agent->ville)
        $programmesDuJour = Programme::where('compagnie_id', $agent->compagnie_id)
            ->whereDate('date_depart', Carbon::today())
            // Optionnel : Ne pas afficher les bus partis il y a plus de 4 heures pour alléger la liste
            // ->where('heure_depart', '>=', Carbon::now()->subHours(4)->format('H:i')) 
            ->with('vehicule')
            ->orderBy('heure_depart')
            ->get();

        // Récupération des réservations (inchangé)
        $reservations = Reservation::with(['programme', 'user'])
            ->whereHas('programme', function($query) use ($agent) {
                $query->where('compagnie_id', $agent->compagnie_id);
            })
            ->orderBy('date_voyage', 'desc')
            ->limit(50) // Optimisation : ne pas charger 1000 historiques inutilement
            ->get();

        $enCours = $reservations->whereNotIn('statut', ['terminee', 'annulee']);
        $terminees = $reservations->where('statut', 'terminee');

        return view('agent.reservations.reservation', compact('enCours', 'terminees', 'programmesDuJour'));
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
        ]);

        $reservation = Reservation::where('reference', $request->reference)->first();
        $agent = Auth::guard('agent')->user();
        $vehicule = Vehicule::find($request->vehicule_id);

        if (!$reservation || !$vehicule) {
            return response()->json(['success' => false, 'message' => 'Données invalides.'], 400);
        }

        // --- MÊME LOGIQUE DE DÉTECTION QUE SEARCH ---
        $today = Carbon::today();
        $dateAller = Carbon::parse($reservation->date_voyage)->startOfDay();
        $dateRetour = $reservation->date_retour ? Carbon::parse($reservation->date_retour)->startOfDay() : null;

        $isDayAller = $dateAller->equalTo($today);
        $isDayRetour = $reservation->is_aller_retour && $dateRetour && $dateRetour->equalTo($today);

        $targetScan = null;

        if ($isDayAller && $isDayRetour) {
            // Priorité au retour si aller déjà fait
            $targetScan = ($reservation->statut_aller === 'terminee') ? 'retour' : 'aller';
        } elseif ($isDayAller) {
            $targetScan = 'aller';
        } elseif ($isDayRetour) {
            $targetScan = 'retour';
        } else {
            return response()->json(['success' => false, 'message' => 'Date invalide.'], 400);
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
        }
        elseif ($reservation->is_aller_retour && $reservation->statut_aller === 'terminee' && $reservation->statut_retour === 'terminee') {
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
