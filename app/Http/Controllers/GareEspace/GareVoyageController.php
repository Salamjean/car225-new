<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use App\Models\Personnel;
use App\Models\Vehicule;
use App\Models\Voyage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GareVoyageController extends Controller
{
    /**
     * Display voyage assignment page
     */
    public function index(Request $request)
    {
        $gare = Auth::guard('gare')->user();
        $compagnieId = $gare->compagnie_id;
        $date = $request->input('date', Carbon::today()->toDateString());

        // Get all active programmes for this specific gare
        $allProgrammes = Programme::where('compagnie_id', '=', $compagnieId)
            ->where('gare_depart_id', '=', $gare->id)
            ->where('statut', '=', 'actif')
            ->whereDate('date_depart', '<=', $date)
            ->whereDate('date_fin', '>=', $date)
            ->whereDoesntHave('voyages', function ($query) use ($date) {
                $query->whereDate('date_voyage', $date)
                      ->where('statut', 'terminé');
            });

        // Hide past programs if today
        if (Carbon::parse($date)->isToday()) {
            $currentTime = Carbon::now()->format('H:i:s');
            $allProgrammes->whereTime('heure_depart', '>', $currentTime);
        }

        $allProgrammes = $allProgrammes->with(['gareDepart', 'gareArrivee', 'voyages' => function ($query) use ($date) {
                $query->whereDate('date_voyage', $date)
                      ->where('statut', '!=', 'annulé');
            }])
            ->orderBy('heure_depart')
            ->get();

        $totalProgrammesCount = $allProgrammes->count();

        // Separate assigned from unassigned
        $assignedVoyages = $allProgrammes->filter(function($p) {
            return $p->voyages->count() > 0;
        });
        
        // Allow multiple assignments per program (doubler les bus)
        $availableProgrammes = $allProgrammes;

        // Enrichment: Get reservations count for each programme for THIS specific date
        $availableProgrammes->each(function($p) use ($date) {
            $p->reservations_count = $p->getPlacesReserveesForDate($date);
            $p->total_seats = $p->getTotalSeats($date);
        });

        // Unique destinations for the filter with full details
        $itineraries = $availableProgrammes->map(function($p) {
            return [
                'id' => $p->gare_arrivee_id,
                'name' => $p->gareArrivee?->nom_gare ?? 'Inconnu',
                'route' => ($p->point_depart ?: $p->gareDepart?->nom_gare) . ' → ' . ($p->point_arrive ?: $p->gareArrivee?->nom_gare),
                'capacity' => $p->capacity ?: $p->getTotalSeats()
            ];
        })->unique('id')->values();

        // Available drivers (station-specific)
        $chauffeurs = Personnel::where('compagnie_id', '=', $compagnieId)
            ->where('gare_id', '=', $gare->id)
            ->where('type_personnel', '=', 'Chauffeur')
            ->where('statut', '=', 'disponible')
            ->orderBy('name')
            ->get();

        // Available vehicles (station-specific)
        $vehicules = Vehicule::where('compagnie_id', '=', $compagnieId)
            ->where('gare_id', '=', $gare->id)
            ->where('is_active', '=', true)
            ->where('statut', '=', 'disponible')
            ->orderBy('immatriculation')
            ->get();

        return view('gare-espace.voyages.index', compact(
            'availableProgrammes', 
            'assignedVoyages', 
            'itineraries',
            'chauffeurs', 
            'vehicules', 
            'date', 
            'totalProgrammesCount'
        ));
    }

    /**
     * Display finished voyages history
     */
    public function history(Request $request)
    {
        $gare = Auth::guard('gare')->user();
        $compagnieId = $gare->compagnie_id;
        
        $voyages = Voyage::whereHas('programme', function($query) use ($compagnieId) {
                $query->where('compagnie_id', $compagnieId);
            })
            ->where('gare_depart_id', $gare->id)
            ->where('statut', 'terminé')
            ->with(['programme.gareDepart', 'programme.gareArrivee', 'chauffeur', 'vehicule'])
            ->orderBy('date_voyage', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('gare-espace.voyages.history', compact('voyages'));
    }

    /**
     * Store a new voyage assignment
     */
    public function store(Request $request)
    {
        $gare = Auth::guard('gare')->user();
        $compagnieId = $gare->compagnie_id;

        $validated = $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'personnel_id' => 'required|exists:personnels,id',
            'vehicule_id' => 'required|exists:vehicules,id',
            'date_voyage' => 'required|date|after_or_equal:today',
        ]);

        // Verify programme belongs to gare's company
        $programme = Programme::findOrFail($validated['programme_id']);
        if ($programme->compagnie_id !== $compagnieId) {
            return back()->with('error', 'Ce programme n\'appartient pas à votre compagnie.');
        }

        // Verify driver belongs to gare's company and is available
        $chauffeur = Personnel::findOrFail($validated['personnel_id']);
        if ($chauffeur->compagnie_id !== $compagnieId) {
            return back()->with('error', 'Ce chauffeur n\'appartient pas à votre compagnie.');
        }
        if ($chauffeur->statut !== 'disponible') {
            return back()->with('error', 'Ce chauffeur n\'est pas disponible.');
        }

        // Verify vehicle belongs to gare's company and is available
        $vehicule = Vehicule::findOrFail($validated['vehicule_id']);
        if ($vehicule->compagnie_id !== $compagnieId) {
            return back()->with('error', 'Ce véhicule n\'appartient pas à votre compagnie.');
        }
        if ($vehicule->statut !== 'disponible') {
            return back()->with('error', 'Ce véhicule n\'est pas disponible.');
        }

        // Vérifier la capacité du véhicule
        $requiredCapacity = $programme->getTotalSeats();
        $actualReservationsCount = $programme->getPlacesReserveesForDate($validated['date_voyage']);
        
        // 1. Ne doit pas être inférieur aux réservations déjà faites
        if ((int)$vehicule->nombre_place < $actualReservationsCount) {
            return back()->with('error', "Le véhicule sélectionné ({$vehicule->nombre_place} places) possède une capacité insuffisante pour les {$actualReservationsCount} réservations déjà effectuées à cette date.");
        }

        // 2. Ne doit pas excéder la capacité du programme (selon demande utilisateur)
        if ((int)$vehicule->nombre_place > (int)$requiredCapacity) {
             return back()->with('error', "Le véhicule sélectionné ({$vehicule->nombre_place} places) excède la capacité maximale autorisée pour ce programme ({$requiredCapacity} places).");
        }

        // Check if active voyage already exists
        $exists = Voyage::where('programme_id', '=', $programme->id)
            ->whereDate('date_voyage', $validated['date_voyage'])
            ->whereNotIn('statut', ['annulé']) // Allow new assignment if previous was cancelled
            ->exists();

        if ($exists) {
            return back()->with('error', 'Un voyage actif est déjà assigné pour ce programme à cette date.');
        }

        // Check driver availability (ignoring cancelled trips)
        $chauffeurBusy = Voyage::where('personnel_id', '=', $chauffeur->id)
            ->whereDate('date_voyage', $validated['date_voyage'])
            ->whereNotIn('statut', ['annulé', 'terminé'])
            ->exists();

        if ($chauffeurBusy) {
            return back()->with('error', 'Ce chauffeur est déjà occupé par un autre voyage actif pour cette date.');
        }

        // Check vehicle availability (ignoring cancelled trips)
        $vehiculeBusy = Voyage::where('vehicule_id', '=', $vehicule->id)
            ->whereDate('date_voyage', $validated['date_voyage'])
            ->whereNotIn('statut', ['annulé', 'terminé'])
            ->exists();

        if ($vehiculeBusy) {
            return back()->with('error', 'Ce véhicule est déjà utilisé pour un autre voyage actif à cette date.');
        }

        // Create voyage
        $voyage = Voyage::create([
            'programme_id' => $programme->id,
            'date_voyage' => $validated['date_voyage'],
            'vehicule_id' => $vehicule->id,
            'personnel_id' => $chauffeur->id,
            'gare_depart_id' => $programme->gare_depart_id,
            'gare_arrivee_id' => $programme->gare_arrivee_id,
            'statut' => 'en_attente',
        ]);

        // Update driver and vehicle status
        $chauffeur->update(['statut' => 'indisponible']);
        $vehicule->update(['statut' => 'indisponible']);

        // Send Notifications
        try {
            $chauffeur->notify(new \App\Notifications\VoyageAssignedNotification($voyage));

            if ($chauffeur->fcm_token) {
                $fcmService = app(\App\Services\FcmService::class);
                $heureDepart = Carbon::parse($programme->heure_depart)->format('H:i');
                $title = "Nouveau Voyage Assigné 🚍";
                $body = "Trajet : {$programme->point_depart} ➝ {$programme->point_arrive}\n" .
                        "Départ : {$heureDepart}\n" .
                        "Date : " . Carbon::parse($validated['date_voyage'])->format('d/m/Y') . "\n" .
                        "Véhicule : {$vehicule->immatriculation} ({$vehicule->marque})";
                
                $fcmService->sendNotification(
                    $chauffeur->fcm_token, $title, $body,
                    ['type' => 'voyage_assigned', 'voyage_id' => $voyage->id, 'programme_id' => $programme->id]
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur notification voyage (GareEspace/VoyageController): " . $e->getMessage());
        }

        return back()->with('success', 'Le voyage a été assigné avec succès au chauffeur ' . $chauffeur->prenom . ' ' . $chauffeur->name . '.');
    }

    /**
     * Cancel a voyage assignment
     */
    public function destroy(Voyage $voyage)
    {
        $gare = Auth::guard('gare')->user();

        if ($voyage->programme->compagnie_id !== $gare->compagnie_id) {
            return back()->with('error', 'Ce voyage n\'appartient pas à votre compagnie.');
        }

        if ($voyage->statut === 'terminé') {
            return back()->with('error', 'Impossible d\'annuler un voyage déjà terminé.');
        }

        if ($voyage->chauffeur) {
            $voyage->chauffeur->update(['statut' => 'disponible']);
        }
        if ($voyage->vehicule) {
            $voyage->vehicule->update(['statut' => 'disponible']);
        }

        $voyage->delete();

        return back()->with('success', 'Le voyage a été annulé avec succès.');
    }
}
