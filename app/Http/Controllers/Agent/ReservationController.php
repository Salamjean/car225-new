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
        
        // Récupérer les réservations de la compagnie de l'agent
        $reservations = Reservation::with(['programme', 'user'])
            ->whereHas('programme', function($query) use ($agent) {
                $query->where('compagnie_id', $agent->compagnie_id);
            })
            ->orderBy('date_voyage', 'desc')
            ->get();

        $enCours = $reservations->where('statut', 'confirmee');
        $terminees = $reservations->where('statut', 'terminee');

        // Programmes du jour pour la sélection avant scan
        $programmesDuJour = Programme::where('compagnie_id', $agent->compagnie_id)
            ->whereDate('date_depart', Carbon::today())
            ->with('vehicule')
            ->orderBy('heure_depart')
            ->get();

        return view('agent.reservations.reservation', compact('enCours', 'terminees', 'programmesDuJour'));
    }

    /**
     * Récupérer les programmes du jour pour le scan (AJAX)
     */
    public function getProgrammesForScan()
    {
        $agent = Auth::guard('agent')->user();
        
        $programmes = Programme::where('compagnie_id', $agent->compagnie_id)
            ->whereDate('date_depart', Carbon::today())
            ->with('vehicule')
            ->orderBy('heure_depart')
            ->get()
            ->map(function($prog) {
                return [
                    'id' => $prog->id,
                    'trajet' => $prog->point_depart . ' → ' . $prog->point_arrive,
                    'heure' => $prog->heure_depart,
                    'vehicule_id' => $prog->vehicule_id,
                    'vehicule' => $prog->vehicule ? [
                        'id' => $prog->vehicule->id,
                        'immatriculation' => $prog->vehicule->immatriculation,
                        'marque' => $prog->vehicule->marque,
                        'modele' => $prog->vehicule->modele,
                    ] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'programmes' => $programmes
        ]);
    }

    /**
     * Afficher la page de recherche de passager
     */
    public function recherchePage()
    {
        return view('agent.reservations.recherche');
    }

    /**
     * Rechercher une réservation par référence (page recherche)
     */
    public function searchByReference(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
        ]);

        $agent = Auth::guard('agent')->user();

        $reservation = Reservation::with(['programme.vehicule', 'programme.compagnie', 'user', 'embarquementVehicule', 'agentEmbarquement'])
            ->where('reference', 'like', '%' . $request->reference . '%')
            ->whereHas('programme', function($q) use ($agent) {
                $q->where('compagnie_id', $agent->compagnie_id);
            })
            ->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune réservation trouvée avec cette référence.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'reservation' => [
                'id' => $reservation->id,
                'reference' => $reservation->reference,
                'statut' => $reservation->statut,
                'passager_nom' => $reservation->passager_nom,
                'passager_prenom' => $reservation->passager_prenom,
                'passager_email' => $reservation->passager_email,
                'passager_telephone' => $reservation->passager_telephone,
                'passager_urgence' => $reservation->passager_urgence,
                'seat_number' => $reservation->seat_number,
                'date_voyage' => $reservation->date_voyage->format('d/m/Y'),
                'montant' => number_format($reservation->montant, 0, ',', ' ') . ' FCFA',
                'is_aller_retour' => $reservation->is_aller_retour,
                'created_at' => $reservation->created_at->format('d/m/Y H:i'),
                
                // Programme
                'trajet' => $reservation->programme->point_depart . ' → ' . $reservation->programme->point_arrive,
                'heure_depart' => $reservation->programme->heure_depart,
                'heure_arrivee' => $reservation->programme->heure_arrive,
                
                // Véhicule du programme
                'vehicule_programme' => $reservation->programme->vehicule ? [
                    'immatriculation' => $reservation->programme->vehicule->immatriculation,
                    'marque' => $reservation->programme->vehicule->marque,
                    'modele' => $reservation->programme->vehicule->modele,
                ] : null,
                
                // Embarquement (si scanné)
                'embarquement' => $reservation->embarquement_scanned_at ? [
                    'scanned_at' => Carbon::parse($reservation->embarquement_scanned_at)->format('d/m/Y H:i'),
                    'agent' => $reservation->agentEmbarquement ? $reservation->agentEmbarquement->name : null,
                    'vehicule' => $reservation->embarquementVehicule ? $reservation->embarquementVehicule->immatriculation : null,
                ] : null,
            ]
        ]);
    }

    /**
     * Rechercher une réservation par référence (pour afficher les infos avant confirmation)
     * Appelé en AJAX
     */
    public function search(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
            'vehicule_id' => 'nullable|integer',
            'programme_id' => 'nullable|integer',
        ]);

        $reservation = Reservation::with(['programme.vehicule', 'user', 'embarquementVehicule'])
            ->where('reference', $request->reference)
            ->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Réservation non trouvée avec cette référence.'
            ], 404);
        }

        // Vérifier si la réservation appartient à la compagnie de l'agent
        $agent = Auth::guard('agent')->user();
        if ($reservation->programme->compagnie_id !== $agent->compagnie_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cette réservation n\'appartient pas à votre compagnie.'
            ], 403);
        }

        // Vérifier si la réservation correspond au programme sélectionné
        if ($request->programme_id && $reservation->programme_id != $request->programme_id) {
            return response()->json([
                'success' => false,
                'message' => 'Ce ticket n\'est pas valide pour ce programme. Ce ticket est pour le trajet ' . 
                    $reservation->programme->point_depart . ' → ' . $reservation->programme->point_arrive . 
                    ' du ' . $reservation->date_voyage->format('d/m/Y') . ' à ' . $reservation->programme->heure_depart
            ], 400);
        }

        if ($reservation->statut === 'terminee') {
            // Message détaillé avec infos du véhicule
            $vehiculeInfo = '';
            if ($reservation->embarquementVehicule) {
                $vehiculeInfo = ' dans le véhicule ' . $reservation->embarquementVehicule->immatriculation;
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Ce ticket a déjà été scanné pour le programme ' . 
                    $reservation->programme->point_depart . ' → ' . $reservation->programme->point_arrive . 
                    $vehiculeInfo . ' le ' . $reservation->embarquement_scanned_at?->format('d/m/Y à H:i'),
                'already_scanned' => true,
                'scanned_at' => $reservation->embarquement_scanned_at?->format('d/m/Y H:i'),
                'vehicule' => $reservation->embarquementVehicule ? $reservation->embarquementVehicule->immatriculation : null,
            ], 400);
        }

        if ($reservation->statut !== 'confirmee') {
            return response()->json([
                'success' => false,
                'message' => 'Statut de réservation invalide: ' . $reservation->statut
            ], 400);
        }

        // Retourner les infos du passager pour confirmation
        return response()->json([
            'success' => true,
            'reservation' => [
                'id' => $reservation->id,
                'reference' => $reservation->reference,
                'passager_nom' => $reservation->passager_nom,
                'passager_prenom' => $reservation->passager_prenom,
                'passager_nom_complet' => $reservation->passager_prenom . ' ' . $reservation->passager_nom,
                'passager_email' => $reservation->passager_email,
                'passager_telephone' => $reservation->passager_telephone,
                'seat_number' => $reservation->seat_number,
                'date_voyage' => $reservation->date_voyage->format('d/m/Y'),
                'trajet' => $reservation->programme->point_depart . ' → ' . $reservation->programme->point_arrive,
                'heure_depart' => $reservation->programme->heure_depart,
                'montant' => number_format($reservation->montant, 0, ',', ' ') . ' FCFA',
                'programme_vehicule' => $reservation->programme->vehicule ? $reservation->programme->vehicule->immatriculation : null,
            ]
        ]);
    }

    /**
     * Confirmer l'embarquement (après vérification visuelle par l'agent)
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'reference' => 'required|string|exists:reservations,reference',
            'vehicule_id' => 'required|integer|exists:vehicules,id',
        ]);

        $reservation = Reservation::with(['programme', 'embarquementVehicule'])->where('reference', $request->reference)->first();
        $agent = Auth::guard('agent')->user();

        // Vérifier si la réservation appartient à la compagnie de l'agent
        if ($reservation->programme->compagnie_id !== $agent->compagnie_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cette réservation n\'appartient pas à votre compagnie.'
            ], 403);
        }

        // Vérifier que le véhicule appartient à la compagnie
        $vehicule = Vehicule::find($request->vehicule_id);
        if (!$vehicule || $vehicule->compagnie_id !== $agent->compagnie_id) {
            return response()->json([
                'success' => false,
                'message' => 'Ce véhicule n\'appartient pas à votre compagnie.'
            ], 403);
        }

        if ($reservation->statut === 'terminee') {
            $vehiculeInfo = '';
            if ($reservation->embarquementVehicule) {
                $vehiculeInfo = ' dans le véhicule ' . $reservation->embarquementVehicule->immatriculation;
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Ce ticket a déjà été scanné' . $vehiculeInfo . ' le ' . 
                    $reservation->embarquement_scanned_at?->format('d/m/Y à H:i')
            ], 400);
        }

        if ($reservation->statut !== 'confirmee') {
            return response()->json([
                'success' => false,
                'message' => 'Statut de réservation invalide pour le scan.'
            ], 400);
        }

        // Mettre à jour le statut avec le véhicule
        $reservation->update([
            'statut' => 'terminee',
            'embarquement_scanned_at' => now(),
            'embarquement_agent_id' => $agent->id,
            'embarquement_vehicule_id' => $request->vehicule_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Embarquement confirmé pour ' . $reservation->passager_prenom . ' ' . $reservation->passager_nom . 
                ' (Place ' . $reservation->seat_number . ') dans le véhicule ' . $vehicule->immatriculation
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
