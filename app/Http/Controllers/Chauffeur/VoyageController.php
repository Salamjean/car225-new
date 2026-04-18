<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Convoi;
use App\Models\Voyage;
use App\Models\Reservation;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VoyageController extends Controller
{
    /**
     * Display driver's assigned voyages
     */
    public function index(Request $request)
    {
        $chauffeur = Auth::guard('chauffeur')->user();
        $tab = $request->input('tab', 'active'); // 'active' (default), 'non_effectues', 'effectues'
        $date = $request->input('date', Carbon::today()->toDateString());
        
        $query = Voyage::where('personnel_id', '=', $chauffeur->id)
            ->with(['programme.gareDepart', 'programme.gareArrivee', 'vehicule'])
            ->orderBy('date_voyage', 'desc')
            ->orderBy('created_at', 'desc');

        if ($tab === 'effectues') {
            // Completed
            $query->whereIn('statut', ['terminé', 'succès']);
        } elseif ($tab === 'non_effectues') {
            // Failed
            $query->whereIn('statut', ['annulé', 'interrompu', 'arrêté', 'non_effectué']);
            if ($request->filled('date')) {
                $query->whereDate('date_voyage', $request->date);
            }
        } else {
            // Active / Planned (Default)
            $query->whereIn('statut', ['en_attente', 'confirmé', 'en_cours'])
                  ->whereDate('date_voyage', $date);
        }

        $voyages = $query->paginate(10);

        $convoiQuery = Convoi::where('personnel_id', '=', $chauffeur->id)
            ->with(['itineraire', 'gare', 'vehicule'])
            ->orderBy('created_at', 'desc');

        if ($tab === 'effectues') {
            $convoiQuery->where('statut', 'termine');
        } elseif ($tab === 'non_effectues') {
            $convoiQuery->where('statut', 'annule');
        } else {
            // Active convoy missions for chauffeur (aller ou retour actifs pour la date sélectionnée)
            $convoiQuery->whereIn('statut', ['paye', 'en_cours'])
                ->where(function ($q) use ($date) {
                    $q->where('statut', 'en_cours')
                      ->orWhere(function ($q2) use ($date) {
                          // Aller : paye + aller_done = false + date_depart == date filtre
                          $q2->where('statut', 'paye')
                             ->where('aller_done', false)
                             ->whereDate('date_depart', $date);
                      })
                      ->orWhere(function ($q3) use ($date) {
                          // Retour : paye + aller_done = true + date_retour == date filtre
                          $q3->where('statut', 'paye')
                             ->where('aller_done', true)
                             ->whereDate('date_retour', $date);
                      })
                      ->orWhere(function ($q4) use ($date) {
                          // Aller passé non démarré (en retard) : paye + aller_done = false + date_depart < date filtre
                          $q4->where('statut', 'paye')
                             ->where('aller_done', false)
                             ->whereDate('date_depart', '<', $date);
                      })
                      ->orWhere(function ($q5) use ($date) {
                          // Retour passé non démarré (en retard)
                          $q5->where('statut', 'paye')
                             ->where('aller_done', true)
                             ->whereDate('date_retour', '<', $date);
                      });
                });
        }

        $convois = $convoiQuery->get();

        return view('chauffeur.programmes.index', compact('voyages', 'tab', 'date', 'convois'));
    }

    public function startConvoi(Convoi $convoi)
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        if ((int) $convoi->personnel_id !== (int) $chauffeur->id) {
            return back()->with('error', 'Ce convoi ne vous appartient pas.');
        }

        if ($convoi->statut !== 'paye') {
            return back()->with('error', 'Ce convoi ne peut pas être démarré (statut : ' . $convoi->statut . ').');
        }

        // Pour le retour (aller_done = true), vérifier la date_retour
        // Pour l'aller (aller_done = false), vérifier la date_depart
        if ($convoi->aller_done) {
            // Démarrage du retour
            if ($convoi->date_retour && Carbon::parse($convoi->date_retour)->isFuture() && !Carbon::parse($convoi->date_retour)->isToday()) {
                return back()->with('error', 'Le retour ne peut être démarré qu\'à partir du ' . Carbon::parse($convoi->date_retour)->format('d/m/Y') . '.');
            }
        } else {
            // Démarrage de l'aller
            if ($convoi->date_depart && Carbon::parse($convoi->date_depart)->isFuture() && !Carbon::parse($convoi->date_depart)->isToday()) {
                return back()->with('error', 'Le convoi ne peut être démarré qu\'à partir du ' . Carbon::parse($convoi->date_depart)->format('d/m/Y') . '.');
            }
        }

        // Marquer le chauffeur et le véhicule indisponibles maintenant qu'il démarre vraiment
        $chauffeur->update(['statut' => 'indisponible']);
        if ($convoi->vehicule_id) {
            \App\Models\Vehicule::where('id', $convoi->vehicule_id)->update(['statut' => 'indisponible']);
        }

        $convoi->update(['statut' => 'en_cours']);

        $msg = $convoi->aller_done ? 'Retour démarré avec succès. Bon voyage !' : 'Convoi démarré avec succès.';
        return back()->with('success', $msg);
    }

    public function completeConvoi(Convoi $convoi)
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        if ((int) $convoi->personnel_id !== (int) $chauffeur->id) {
            return back()->with('error', 'Ce convoi ne vous appartient pas.');
        }

        if ($convoi->statut !== 'en_cours') {
            return back()->with('error', 'Ce convoi n\'est pas en cours.');
        }

        // Supprimer le GPS de ce trajet
        \App\Models\DriverLocation::where('convoi_id', $convoi->id)->delete();

        // Convoi avec retour ET aller pas encore marqué terminé → terminer l'aller seulement
        if ($convoi->date_retour && !$convoi->aller_done) {
            $convoi->update([
                'statut'     => 'paye',      // retour en attente (réutilise statut "assigné")
                'aller_done' => true,
            ]);
            // Libérer le chauffeur et le véhicule entre les deux trajets
            $chauffeur->update(['statut' => 'disponible']);
            if ($convoi->vehicule_id) {
                Vehicule::where('id', $convoi->vehicule_id)->update(['statut' => 'disponible']);
            }

            $dateRetour = Carbon::parse($convoi->date_retour)->translatedFormat('d F Y');
            $hRetour    = $convoi->heure_retour ? ' à ' . substr($convoi->heure_retour, 0, 5) : '';
            return back()->with('success', "Trajet aller terminé ✅ Le retour est prévu le {$dateRetour}{$hRetour}. Il apparaîtra sur votre tableau de bord à cette date.");
        }

        // Pas de retour (ou c'est le retour qui se termine) → terminaison définitive
        $convoi->update(['statut' => 'termine']);
        $chauffeur->update(['statut' => 'disponible']);

        if ($convoi->vehicule_id) {
            Vehicule::where('id', $convoi->vehicule_id)->update(['statut' => 'disponible']);
        }

        $msg = $convoi->aller_done ? 'Convoi retour terminé avec succès. Bienvenue !' : 'Convoi terminé avec succès.';
        return back()->with('success', $msg);
    }

    /**
     * Page de suivi GPS en temps réel pour un convoi en cours
     */
    public function trackingConvoi(Convoi $convoi)
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        if ((int) $convoi->personnel_id !== (int) $chauffeur->id) {
            abort(403, 'Ce convoi ne vous appartient pas.');
        }

        if ($convoi->statut !== 'en_cours') {
            return redirect()->route('chauffeur.voyages.index')
                ->with('error', 'Le suivi en temps réel est uniquement disponible pour un convoi en cours.');
        }

        $convoi->load(['itineraire', 'vehicule', 'latestLocation']);

        // Position initiale : dernière position connue → sinon centre CI
        $initialLat = $convoi->latestLocation?->latitude  ?? 6.8276;
        $initialLng = $convoi->latestLocation?->longitude ?? -5.2893;

        // Déterminer le trajet à afficher (aller ou retour)
        $isRetour  = (bool) $convoi->aller_done;
        $depart    = $convoi->lieu_depart  ?? ($convoi->itineraire?->point_depart  ?? 'Départ');
        $arrivee   = $convoi->lieu_retour  ?? ($convoi->itineraire?->point_arrive  ?? 'Arrivée');
        $dateLabel = $isRetour
            ? Carbon::parse($convoi->date_retour)->format('d/m/Y')
            : Carbon::parse($convoi->date_depart)->format('d/m/Y');
        $heureLabel = $isRetour
            ? substr($convoi->heure_retour ?? '', 0, 5)
            : substr($convoi->heure_depart ?? '', 0, 5);

        if ($isRetour) {
            // Pour le retour, on inverse le sens
            [$depart, $arrivee] = [$arrivee, $depart];
        }

        return view('chauffeur.voyages.tracking-convoi', compact(
            'convoi', 'chauffeur', 'initialLat', 'initialLng',
            'depart', 'arrivee', 'dateLabel', 'heureLabel', 'isRetour'
        ));
    }

    public function annulerConvoi(Request $request, Convoi $convoi)
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        if ((int) $convoi->personnel_id !== (int) $chauffeur->id) {
            return back()->with('error', 'Ce convoi ne vous appartient pas.');
        }

        if (!in_array($convoi->statut, ['paye', 'en_cours'], true)) {
            return back()->with('error', 'Ce convoi ne peut plus être annulé.');
        }

        $request->validate([
            'motif_annulation' => 'required|string|min:10|max:500',
        ], [
            'motif_annulation.required' => 'Veuillez indiquer le motif d\'annulation.',
            'motif_annulation.min' => 'Le motif doit contenir au moins 10 caractères.',
        ]);

        // Le convoi repasse à "paye" sans chauffeur/véhicule → la gare peut réaffecter
        // Seule la compagnie peut définitivement annuler
        $convoi->update([
            'statut'                     => 'paye',
            'personnel_id'               => null,
            'vehicule_id'                => null,
            'motif_annulation_chauffeur' => $request->motif_annulation,
        ]);

        // Libérer le chauffeur et le véhicule
        $chauffeur->update(['statut' => 'disponible']);
        if ($convoi->vehicule_id) {
            Vehicule::where('id', $convoi->vehicule_id)->update(['statut' => 'disponible']);
        }

        \App\Models\DriverLocation::where('convoi_id', $convoi->id)->delete();

        // Notifier la gare via message
        if ($convoi->gare_id) {
            \App\Models\GareMessage::create([
                'gare_id'        => $convoi->gare_id,
                'sender_type'    => 'App\Models\Personnel',
                'sender_id'      => $chauffeur->id,
                'recipient_type' => 'App\Models\Gare',
                'recipient_id'   => $convoi->gare_id,
                'subject'        => 'Désistement convoi ' . ($convoi->reference ?? '#' . $convoi->id),
                'message'        => "Le chauffeur {$chauffeur->prenom} {$chauffeur->name} s'est désisté du convoi {$convoi->reference}.\n\nMotif : " . $request->motif_annulation . "\n\nVeuillez réaffecter un autre chauffeur et véhicule.",
                'is_read'        => false,
            ]);
        }

        return back()->with('success', 'Désistement enregistré. La gare a été notifiée et pourra réaffecter le convoi.');
    }

    /**
     * Confirm a voyage (en_attente -> confirmé)
     */
    public function confirm(Voyage $voyage)
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        // Verify voyage belongs to this driver
        if ($voyage->personnel_id !== $chauffeur->id) {
            return back()->with('error', 'Ce voyage ne vous appartient pas.');
        }

        // Only allow confirmation if voyage is pending
        if ($voyage->statut !== 'en_attente') {
            return back()->with('error', 'Ce voyage ne peut plus être confirmé.');
        }

        $voyage->update(['statut' => 'confirmé']);

        return back()->with('success', 'Voyage confirmé avec succès.');
    }

    /**
     * Start a voyage (confirmé -> en_cours)
     */
    public function start(Voyage $voyage)
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        // Verify voyage belongs to this driver
        if ($voyage->personnel_id !== $chauffeur->id) {
            return back()->with('error', 'Ce voyage ne vous appartient pas.');
        }

        // Only allow starting if voyage is confirmed or pending
        if (!in_array($voyage->statut, ['en_attente', 'confirmé'])) {
            return back()->with('error', 'Ce voyage ne peut pas être démarré.');
        }

        if ($voyage->occupancy < 1) {
            return back()->with('error', 'Impossible de démarrer le voyage : au moins 1 passager doit être présent dans le car.');
        }

        $voyage->update(['statut' => 'en_cours']);

        // Seed an initial tracking point at departure station so company/gare can see the trip immediately.
        $voyage->loadMissing(['gareDepart', 'programme.gareDepart']);
        $departGare = $voyage->gareDepart ?: $voyage->programme?->gareDepart;
        $seedLat = ($departGare && $departGare->latitude) ? (float) $departGare->latitude : 6.8276;
        $seedLng = ($departGare && $departGare->longitude) ? (float) $departGare->longitude : -5.2893;

        \App\Models\DriverLocation::updateOrCreate(
            ['voyage_id' => $voyage->id, 'personnel_id' => $chauffeur->id],
            [
                'convoi_id' => null,
                'latitude' => $seedLat,
                'longitude' => $seedLng,
                'speed' => 0,
                'heading' => null,
            ]
        );

        return back()->with('success', 'Bon voyage ! Le voyage a été démarré.');
    }

    /**
     * Complete a voyage (en_cours -> terminé) and update driver status to disponible
     */
    public function complete(Voyage $voyage)
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        // Verify voyage belongs to this driver
        if ($voyage->personnel_id !== $chauffeur->id) {
            return back()->with('error', 'Ce voyage ne vous appartient pas.');
        }

        // Only allow completion if voyage is in progress
        if ($voyage->statut !== 'en_cours') {
            return back()->with('error', 'Ce voyage n\'est pas en cours.');
        }

        // Update voyage status
        $voyage->update(['statut' => 'terminé']);

        // Update driver status to disponible
        $chauffeur->update(['statut' => 'disponible']);

        // Update vehicle status to disponible
        if ($voyage->vehicule_id) {
            \App\Models\Vehicule::where('id', $voyage->vehicule_id)->update(['statut' => 'disponible']);
        }

        return redirect()->route('chauffeur.voyages.index', ['tab' => 'effectues'])
            ->with('success', 'Voyage terminé avec succès. Vous êtes maintenant disponible.');
    }

    /**
     * Update driver GPS location for a voyage in progress
     */
    public function updateLocation(Request $request, Voyage $voyage)
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        if ($voyage->personnel_id !== $chauffeur->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        if ($voyage->statut !== 'en_cours') {
            return response()->json(['success' => false, 'message' => 'Le voyage n\'est pas en cours'], 422);
        }

        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed'     => 'nullable|numeric|min:0',
            'heading'   => 'nullable|numeric|between:0,360',
        ]);

        $latestLoc = $voyage->latestLocation;
        if ($latestLoc) {
            $elapsedSeconds = now()->diffInSeconds($latestLoc->updated_at);
            
            // Si la vitesse est quasi nulle (< 5 km/h) et que la dernière maj est récente (< 10 min)
            // On considère que le véhicule est à l'arrêt et on accumule du retard
            if (($request->speed ?? 0) < 5 && $elapsedSeconds < 600) {
                $voyage->updateEstimatedArrival($elapsedSeconds);
            }
        }

        \App\Models\DriverLocation::updateOrCreate(
            ['voyage_id' => $voyage->id, 'personnel_id' => $chauffeur->id],
            [
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'speed'     => $request->speed,
                'heading'   => $request->heading,
            ]
        );

        return response()->json([
            'success' => true,
            'estimated_arrival' => $voyage->estimated_arrival_at ? $voyage->estimated_arrival_at->toIso8601String() : null,
            'temps_restant' => $voyage->temps_restant
        ]);
    }

    public function updateConvoiLocation(Request $request, Convoi $convoi)
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        if ((int) $convoi->personnel_id !== (int) $chauffeur->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        if ($convoi->statut !== 'en_cours') {
            return response()->json(['success' => false, 'message' => 'Le convoi n\'est pas en cours'], 422);
        }

        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed'     => 'nullable|numeric|min:0',
            'heading'   => 'nullable|numeric|between:0,360',
        ]);

        \App\Models\DriverLocation::updateOrCreate(
            ['convoi_id' => $convoi->id, 'personnel_id' => $chauffeur->id],
            [
                'voyage_id' => null,
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'speed'     => $request->speed,
                'heading'   => $request->heading,
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Annuler un voyage (confirmé ou en_attente -> annulé)
     */
    public function annuler(Request $request, Voyage $voyage)
    {
        $request->validate([
            'reason' => 'required|string|min:5|max:1000',
        ]);

        $chauffeur = Auth::guard('chauffeur')->user();

        if ($voyage->personnel_id !== $chauffeur->id) {
            return back()->with('error', 'Ce voyage ne vous appartient pas.');
        }

        if (!in_array($voyage->statut, ['en_attente', 'confirmé'])) {
            return back()->with('error', 'Ce voyage ne peut plus être annulé.');
        }

        $voyage->update(['statut' => 'annulé']);

        // Libérer les réservations liées : remettre voyage_id à null
        // 1. Réservations directement liées via voyage_id
        \App\Models\Reservation::where('voyage_id', $voyage->id)
            ->update(['voyage_id' => null]);

        // 2. Réservations liées par programme + date (n'ayant pas encore été nullifiées)
        \App\Models\Reservation::where('programme_id', $voyage->programme_id)
            ->whereDate('date_voyage', $voyage->date_voyage)
            ->where('voyage_id', $voyage->id)
            ->update(['voyage_id' => null]);

        \Illuminate\Support\Facades\Log::info("Annulation voyage #{$voyage->id} (web): réservations libérées");

        // Envoyer le motif à la gare
        $gareId = $voyage->gare_depart_id ?? ($voyage->programme ? $voyage->programme->gare_depart_id : null);

        if ($gareId) {
            \App\Models\GareMessage::create([
                'gare_id' => $gareId,
                'sender_type' => 'App\Models\Personnel',
                'sender_id' => $chauffeur->id,
                'recipient_type' => 'App\Models\Gare',
                'recipient_id' => $gareId,
                'subject' => 'Annulation de voyage #' . $voyage->id,
                'message' => "Le chauffeur {$chauffeur->prenom} {$chauffeur->name} a annulé le voyage #{$voyage->id}. \n\nMotif : " . $request->reason,
                'is_read' => false,
            ]);
        }

        // Libérer le chauffeur
        $chauffeur->update(['statut' => 'disponible']);

        // Libérer le véhicule
        if ($voyage->vehicule_id) {
            \App\Models\Vehicule::where('id', $voyage->vehicule_id)->update(['statut' => 'disponible']);
        }

        return redirect()->route('chauffeur.voyages.index', ['tab' => 'non_effectues'])
            ->with('success', 'Le voyage a été annulé et le motif a été transmis à la gare.');
    }

    /**
     * Page de suivi en temps réel pour le chauffeur
     * Position initiale = gare de départ assignée
     */
    public function tracking(Voyage $voyage)
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        if ($voyage->personnel_id !== $chauffeur->id) {
            abort(403, 'Ce voyage ne vous appartient pas.');
        }

        if ($voyage->statut !== 'en_cours') {
            return redirect()->route('chauffeur.voyages.index')
                ->with('error', 'Le suivi en temps réel est uniquement disponible pour les voyages en cours.');
        }

        $voyage->load(['programme.gareDepart', 'programme.gareArrivee', 'vehicule', 'latestLocation']);

        $gareDepart  = $voyage->gareDepart ?? $voyage->programme->gareDepart;
        $gareArrivee = $voyage->gareArrivee ?? $voyage->programme->gareArrivee;

        // Position initiale : dernière loc connue → sinon gare de départ → sinon centre CI
        $initialLat = $voyage->latestLocation?->latitude
            ?? $gareDepart?->latitude
            ?? 6.8276;
        $initialLng = $voyage->latestLocation?->longitude
            ?? $gareDepart?->longitude
            ?? -5.2893;

        return view('chauffeur.voyages.tracking', compact(
            'voyage', 'chauffeur', 'gareDepart', 'gareArrivee', 'initialLat', 'initialLng'
        ));
    }
}
