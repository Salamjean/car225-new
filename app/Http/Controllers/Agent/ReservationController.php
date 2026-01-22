<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Programme;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        $today = Carbon::today();
        $todayDayName = strtolower($today->locale('fr')->dayName); // ex: 'mardi'
        
        // Récupérer tous les programmes de la compagnie
        $allProgrammes = Programme::where('compagnie_id', $agent->compagnie_id)
            ->with('vehicule')
            ->orderBy('heure_depart')
            ->get();
        
        // Filtrer: programmes ponctuels du jour OU programmes récurrents actifs aujourd'hui
        $programmes = $allProgrammes->filter(function($prog) use ($today, $todayDayName) {
            if ($prog->type_programmation === 'ponctuel') {
                // Programme ponctuel: doit être aujourd'hui
                return Carbon::parse($prog->date_depart)->isSameDay($today);
            } else {
                // Programme récurrent: vérifier si aujourd'hui est dans les jours de récurrence
                $joursRecurrence = $prog->jours_recurrence;
                if (is_string($joursRecurrence)) {
                    $joursRecurrence = json_decode($joursRecurrence, true) ?? [];
                }
                $joursRecurrence = array_map('strtolower', $joursRecurrence ?? []);
                
                // Vérifier si le programme est dans sa période de validité
                $dateDebut = Carbon::parse($prog->date_debut_programmation ?? $prog->date_depart);
                $dateFin = $prog->date_fin_programmation ? Carbon::parse($prog->date_fin_programmation) : null;
                
                $inPeriod = $today->gte($dateDebut) && ($dateFin === null || $today->lte($dateFin));
                
                return $inPeriod && in_array($todayDayName, $joursRecurrence);
            }
        })->map(function($prog) {
            return [
                'id' => $prog->id,
                'trajet' => $prog->point_depart . ' → ' . $prog->point_arrive,
                'heure' => $prog->heure_depart,
                'type' => $prog->type_programmation,
                'vehicule_id' => $prog->vehicule_id,
                'vehicule' => $prog->vehicule ? [
                    'id' => $prog->vehicule->id,
                    'immatriculation' => $prog->vehicule->immatriculation,
                    'marque' => $prog->vehicule->marque,
                    'modele' => $prog->vehicule->modele,
                ] : null,
            ];
        })->values();

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
        $agent = Auth::guard('agent')->user();
        
        $terminees = Reservation::with(['programme', 'user', 'embarquementVehicule'])
            ->whereHas('programme', function($query) use ($agent) {
                $query->where('compagnie_id', $agent->compagnie_id);
            })
            ->where('statut', 'terminee')
            ->where('embarquement_agent_id', $agent->id) // Optionnel: voir seulement ceux scannés par cet agent ? Ou tous pour la compagnie ? Le user a dit "voir les ticket scanner", supposons par la compagnie ou l'agent.
            // Si on veut tous ceux de la compagnie, on enlève embarquement_agent_id. 
            // Mais généralement "mes scans" c'est mieux.
            // Cependant, la vue précédente affichait $terminees qui venait de $reservations filtré par 'terminee' dans index(), 
            // et index() prenait toutes les résa de la compagnie. Gardons la logique précédente.
            // Correction: index() filtrait par compagnie seulement. Donc on garde ça.
            ->orderBy('embarquement_scanned_at', 'desc')
            ->paginate(5);

        return view('agent.reservations.recherche', compact('terminees'));
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

        $reference = $request->reference;
        $isRetourTicket = str_contains($reference, '-R');
        $baseReference = str_replace('-R', '', $reference);

        $reservation = Reservation::with(['programme.vehicule', 'programmeRetour.vehicule', 'user', 'embarquementVehicule'])
            ->where('reference', $baseReference)
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
        if ($request->programme_id) {
            $programmeValide = false;
            
            if ($isRetourTicket) {
                // Pour un ticket RETOUR, il doit correspondre au programme_retour_id
                $programmeValide = ($reservation->programme_retour_id == $request->programme_id);
            } else {
                // Pour un ticket ALLER, il doit correspondre au programme_id
                $programmeValide = ($reservation->programme_id == $request->programme_id);
            }
            
            if (!$programmeValide) {
                $ticketType = $isRetourTicket ? 'RETOUR' : 'ALLER';
                $programmeInfo = $isRetourTicket && $reservation->programmeRetour 
                    ? $reservation->programmeRetour 
                    : $reservation->programme;
                    
                return response()->json([
                    'success' => false,
                    'message' => 'Ce ticket ' . $ticketType . ' n\'est pas valide pour ce programme. Ce ticket est pour le trajet ' . 
                        $programmeInfo->point_depart . ' → ' . $programmeInfo->point_arrive . 
                        ' du ' . ($isRetourTicket ? $reservation->date_retour?->format('d/m/Y') : $reservation->date_voyage->format('d/m/Y')) . 
                        ' à ' . $programmeInfo->heure_depart
                ], 400);
            }
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
            'reference' => 'required|string',
            'vehicule_id' => 'required|integer|exists:vehicules,id',
            'qr_type' => 'nullable|string|in:aller,retour', // Type de QR code (aller ou retour)
        ]);

        // Extraire la référence de base (sans le -R pour retour)
        $reference = $request->reference;
        $baseReference = str_replace('-R', '', $reference);
        
        $reservation = Reservation::with(['programme', 'embarquementVehicule'])->where('reference', $baseReference)->first();
        
        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Réservation non trouvée.'
            ], 404);
        }
        
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

        // Déterminer le type de QR code (aller ou retour)
        $qrType = $request->qr_type ?? 'aller';
        
        // Si la référence contient -R, c'est un retour
        if (str_contains($reference, '-R')) {
            $qrType = 'retour';
        }

        // Vérifier si c'est un voyage aller-retour
        $isAllerRetour = $reservation->is_aller_retour;

        // Pour les tickets retour, vérifier que l'agent scanne sur le bon programme (programme retour)
        if ($isAllerRetour && $qrType === 'retour') {
            // Charger le programme retour lié à cette réservation
            $reservation->load('programmeRetour');
            
            if ($reservation->programme_retour_id) {
                // Vérifier si le véhicule sélectionné appartient au programme retour
                $programmeRetour = $reservation->programmeRetour;
                if ($programmeRetour && $programmeRetour->vehicule_id != $request->vehicule_id) {
                    // L'agent est sur le mauvais véhicule/programme pour le retour
                    // On permet quand même le scan mais on log l'info
                    Log::info('Scan retour sur véhicule différent du programme retour', [
                        'reservation_id' => $reservation->id,
                        'programme_retour_vehicule' => $programmeRetour->vehicule_id,
                        'vehicule_scanne' => $request->vehicule_id
                    ]);
                }
            }
        }

        if ($isAllerRetour) {
            // Logique pour aller-retour
            if ($qrType === 'retour') {
                // Vérifier si le retour n'est pas déjà terminé
                if ($reservation->statut_retour === 'terminee') {
                    $vehiculeInfo = $reservation->embarquementVehicule 
                        ? ' dans le véhicule ' . $reservation->embarquementVehicule->immatriculation 
                        : '';
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Le ticket RETOUR a déjà été scanné' . $vehiculeInfo
                    ], 400);
                }
                
                // Mettre à jour le statut retour
                $reservation->update([
                    'statut_retour' => 'terminee',
                ]);
                
                // Si les deux sont terminés, mettre le statut global à terminee
                if ($reservation->statut_aller === 'terminee') {
                    $reservation->update([
                        'statut' => 'terminee',
                        'embarquement_scanned_at' => now(),
                        'embarquement_agent_id' => $agent->id,
                        'embarquement_vehicule_id' => $request->vehicule_id,
                    ]);
                }
                
                $tripTypeLabel = 'RETOUR';
            } else {
                // Vérifier si l'aller n'est pas déjà terminé
                if ($reservation->statut_aller === 'terminee') {
                    $vehiculeInfo = $reservation->embarquementVehicule 
                        ? ' dans le véhicule ' . $reservation->embarquementVehicule->immatriculation 
                        : '';
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Le ticket ALLER a déjà été scanné' . $vehiculeInfo
                    ], 400);
                }
                
                // Mettre à jour le statut aller
                $reservation->update([
                    'statut_aller' => 'terminee',
                    'embarquement_scanned_at' => now(),
                    'embarquement_agent_id' => $agent->id,
                    'embarquement_vehicule_id' => $request->vehicule_id,
                ]);
                
                $tripTypeLabel = 'ALLER';
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Embarquement ' . $tripTypeLabel . ' confirmé pour ' . $reservation->passager_prenom . ' ' . $reservation->passager_nom . 
                    ' (Place ' . $reservation->seat_number . ') dans le véhicule ' . $vehicule->immatriculation,
                'trip_type' => $tripTypeLabel,
                'is_aller_retour' => true
            ]);
        } else {
            // Logique pour voyage simple (aller uniquement)
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
                'statut_aller' => 'terminee',
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
