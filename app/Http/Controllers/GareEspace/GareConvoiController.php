<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Mail\ConvoiWalkinReceiptMail;
use App\Models\Convoi;
use App\Models\Itineraire;
use App\Models\Personnel;
use App\Models\Vehicule;
use App\Models\Voyage;
use App\Notifications\ConvoiAssignedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class GareConvoiController extends Controller
{
    /**
     * Récupérer les IDs de personnel occupés par un convoi qui chevauche la plage de dates donnée.
     */
    private function busyPersonnelIdsForDateRange(?string $dateDepart, ?string $dateRetour, ?int $excludeConvoiId = null): array
    {
        $query = Convoi::whereNotNull('personnel_id')
            ->whereIn('statut', ['paye', 'en_cours']);

        if ($excludeConvoiId) {
            $query->where('id', '!=', $excludeConvoiId);
        }

        if ($dateDepart && $dateRetour) {
            $query->where(function ($q) use ($dateDepart, $dateRetour) {
                $q->where('date_depart', '<=', $dateRetour)
                  ->where('date_retour', '>=', $dateDepart);
            });
        }

        return $query->pluck('personnel_id')->toArray();
    }

    /**
     * Récupérer les IDs de véhicules occupés par un convoi qui chevauche la plage de dates donnée.
     */
    private function busyVehiculeIdsForDateRange(?string $dateDepart, ?string $dateRetour, ?int $excludeConvoiId = null): array
    {
        $query = Convoi::whereNotNull('vehicule_id')
            ->whereIn('statut', ['paye', 'en_cours']);

        if ($excludeConvoiId) {
            $query->where('id', '!=', $excludeConvoiId);
        }

        if ($dateDepart && $dateRetour) {
            $query->where(function ($q) use ($dateDepart, $dateRetour) {
                $q->where('date_depart', '<=', $dateRetour)
                  ->where('date_retour', '>=', $dateDepart);
            });
        }

        return $query->pluck('vehicule_id')->toArray();
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Walk-in : création d'un convoi pour un client sur place
    // ─────────────────────────────────────────────────────────────────────────

    public function createWalkin()
    {
        $gare = Auth::guard('gare')->user();

        $itineraires = Itineraire::where('compagnie_id', $gare->compagnie_id)
            ->orderBy('point_depart')
            ->get(['id', 'point_depart', 'point_arrive']);

        return view('gare-espace.convois.create', compact('gare', 'itineraires'));
    }

    public function storeWalkin(Request $request)
    {
        $gare = Auth::guard('gare')->user();

        $validated = $request->validate([
            'client_nom'        => 'required|string|max:100',
            'client_prenom'     => 'required|string|max:100',
            'client_contact'    => 'required|string|max:20',
            'client_email'      => 'nullable|email|max:191',
            'itineraire_id'     => 'nullable|exists:itineraires,id',
            'lieu_depart'       => 'required_without:itineraire_id|nullable|string|max:191',
            'lieu_retour'       => 'required_without:itineraire_id|nullable|string|max:191',
            'nombre_personnes'  => 'required|integer|min:10',
            'date_depart'       => 'required|date|after_or_equal:today',
            'heure_depart'      => 'required|date_format:H:i',
            'date_retour'       => 'nullable|date|after_or_equal:date_depart',
            'heure_retour'      => 'nullable|required_with:date_retour|date_format:H:i',
            'montant'           => 'required|numeric|min:0',
            'lieu_rassemblement'=> 'required|string|max:255',
            'motif'             => 'nullable|string|max:500',
        ], [
            'client_nom.required'       => 'Le nom du client est obligatoire.',
            'client_prenom.required'    => 'Le prénom du client est obligatoire.',
            'client_contact.required'   => 'Le contact du client est obligatoire.',
            'lieu_depart.required_without' => 'Le lieu de départ est obligatoire si aucun itinéraire n\'est sélectionné.',
            'lieu_retour.required_without' => 'Le lieu d\'arrivée est obligatoire si aucun itinéraire n\'est sélectionné.',
            'nombre_personnes.min'      => 'Le minimum est de 10 personnes pour un convoi.',
            'date_depart.after_or_equal'=> 'La date de départ ne peut pas être dans le passé.',
            'montant.min'               => 'Le montant doit être positif.',
            'lieu_rassemblement.required' => 'Le lieu de rassemblement est obligatoire.',
        ]);

        // Résoudre les lieux via l'itinéraire si fourni
        if (!empty($validated['itineraire_id'])) {
            $itineraire = Itineraire::findOrFail($validated['itineraire_id']);
            $lieuDepart = $itineraire->point_depart;
            $lieuRetour = $itineraire->point_arrive;
        } else {
            $itineraire = null;
            $lieuDepart = $validated['lieu_depart'];
            $lieuRetour = $validated['lieu_retour'];
        }

        $convoi = Convoi::create([
            'user_id'             => null,
            'compagnie_id'        => $gare->compagnie_id,
            'gare_id'             => $gare->id,
            'itineraire_id'       => $validated['itineraire_id'] ?? null,
            'lieu_depart'         => $lieuDepart,
            'lieu_retour'         => $lieuRetour,
            'nombre_personnes'    => $validated['nombre_personnes'],
            'date_depart'         => $validated['date_depart'],
            'heure_depart'        => $validated['heure_depart'],
            'date_retour'         => $validated['date_retour'] ?? null,
            'heure_retour'        => $validated['heure_retour'] ?? null,
            'montant'             => $validated['montant'],
            'lieu_rassemblement'  => $validated['lieu_rassemblement'] ?? null,
            'reference'           => 'CONV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
            'statut'              => 'paye',
            // Champs walk-in
            'client_nom'          => $validated['client_nom'],
            'client_prenom'       => $validated['client_prenom'],
            'client_contact'      => $validated['client_contact'],
            'client_email'        => $validated['client_email'] ?? null,
            'created_by_gare'     => true,
            'is_garant'           => true, // Par défaut, le client est garant pour son groupe
        ]);

        // Créditer le compte convoi de la compagnie
        $convoi->compagnie()->increment('solde_convoie', $convoi->montant);

        // Envoyer un SMS de confirmation au client
        try {
            $prenom     = $convoi->client_prenom;
            $depart     = $lieuDepart;
            $arrivee    = $lieuRetour;
            $dateDepart = Carbon::parse($convoi->date_depart)->format('d/m/Y');
            $hDepart    = substr($convoi->heure_depart, 0, 5);
            $lieu       = $convoi->lieu_rassemblement ?? 'À définir';

            $smsMsg = "Bonjour {$prenom},\n"
                    . "Votre convoi CAR225 (ref {$convoi->reference}) a ete enregistre avec succes !\n"
                    . "Trajet : {$depart} -> {$arrivee}\n"
                    . "Depart : {$dateDepart}" . ($hDepart ? " a {$hDepart}" : '') . "\n"
                    . "Lieu rassemblement : {$lieu}\n"
                    . "Montant : " . number_format($convoi->montant, 0, ',', ' ') . " FCFA. Merci !";

            app(\App\Services\SmsService::class)->sendSms($convoi->client_contact, $smsMsg);
        } catch (\Exception $e) {
            Log::error('SMS walk-in convoi: ' . $e->getMessage());
        }

        // Envoyer l'email avec le reçu PDF si le client a fourni un mail
        if ($convoi->client_email) {
            try {
                $convoi->loadMissing(['compagnie', 'gare', 'itineraire', 'chauffeur', 'vehicule']);
                Mail::to($convoi->client_email)->send(new ConvoiWalkinReceiptMail($convoi));
            } catch (\Exception $e) {
                Log::error('Email reçu walk-in convoi: ' . $e->getMessage());
            }
        }

        return redirect()
            ->route('gare-espace.convois.show', $convoi)
            ->with('success', "Convoi {$convoi->reference} créé avec succès. Un SMS de confirmation a été envoyé au client." . ($convoi->client_email ? " Le reçu PDF a également été envoyé par e-mail." : ''));
    }

    /** Enregistrement des passagers pour un convoi walk-in depuis la gare */
    public function storeWalkinPassengers(Request $request, Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id || !$convoi->created_by_gare) {
            abort(403);
        }

        $request->validate([
            'is_garant'                   => 'nullable|boolean',
            'passagers'                   => 'nullable|array',
            'passagers.*.nom'             => 'required|string|max:100',
            'passagers.*.prenoms'         => 'required|string|max:150',
            'passagers.*.contact'         => 'required|string|max:20',
            'passagers.*.contact_urgence' => 'required|string|max:20',
        ], [
            'passagers.*.nom.required'             => 'Le nom de chaque passager est obligatoire.',
            'passagers.*.prenoms.required'         => 'Les prénoms de chaque passager sont obligatoires.',
            'passagers.*.contact.required'         => 'Le contact de chaque passager est obligatoire.',
            'passagers.*.contact_urgence.required' => 'Le contact d\'urgence de chaque passager est obligatoire.',
        ]);

        $isGarant = (bool) $request->input('is_garant', false);

        $convoi->update(['is_garant' => $isGarant]);

        // Remplace les passagers existants
        $convoi->passagers()->delete();

        foreach (($request->input('passagers') ?? []) as $p) {
            $nom     = trim($p['nom'] ?? '');
            $prenoms = trim($p['prenoms'] ?? '');
            $contact = trim($p['contact'] ?? '');
            if ($nom || $prenoms || $contact) {
                $convoi->passagers()->create([
                    'nom'             => $nom,
                    'prenoms'         => $prenoms,
                    'contact'         => $contact,
                    'contact_urgence' => trim($p['contact_urgence'] ?? ''),
                ]);
            }
        }

        return back()->with('success', 'Liste des passagers enregistrée avec succès.');
    }

    /** Télécharger / imprimer le reçu PDF d'un convoi walk-in */
    public function downloadRecu(Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id) {
            abort(403);
        }

        $convoi->loadMissing(['compagnie', 'gare', 'itineraire', 'chauffeur', 'vehicule']);

        $pdf = Pdf::loadView('pdf.convoi_recu', compact('convoi'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'dpi'                  => 120,
            ]);

        return $pdf->stream("recu-convoi-{$convoi->reference}.pdf");
    }

    public function index(Request $request)
    {
        $gare = Auth::guard('gare')->user();
        $statut = $request->query('statut', 'all');

        $query = Convoi::with(['user', 'compagnie', 'itineraire', 'chauffeur', 'vehicule'])
            ->withCount('passagers')
            ->where('gare_id', $gare->id)
            ->latest();

        if (in_array($statut, ['paye', 'en_cours', 'termine', 'annule'])) {
            $query->where('statut', $statut);
        }

        $convois = $query->paginate(12)->withQueryString();

        // Pour le dropdown d'affectation, on récupère les chauffeurs/vehicules disponibles
        // On ne peut pas filtrer par date ici car chaque convoi a ses propres dates
        // Le vrai check se fait dans assign()
        $busyPersonnelIds = Voyage::where('statut', 'en_cours')
            ->whereNotNull('personnel_id')
            ->pluck('personnel_id');

        $busyVehiculeIds = Voyage::where('statut', 'en_cours')
            ->whereNotNull('vehicule_id')
            ->pluck('vehicule_id');

        $chauffeurs = Personnel::where('gare_id', $gare->id)
            ->where('type_personnel', 'chauffeur')
            ->where('statut', 'disponible')
            ->whereNull('archived_at')
            ->whereNotIn('id', $busyPersonnelIds)
            ->orderBy('prenom')
            ->orderBy('name')
            ->get(['id', 'name', 'prenom']);

        $vehicules = Vehicule::where('gare_id', $gare->id)
            ->where('is_active', true)
            ->where('statut', 'disponible')
            ->whereNotIn('id', $busyVehiculeIds)
            ->orderBy('immatriculation')
            ->get(['id', 'immatriculation', 'modele', 'nombre_place']);

        return view('gare-espace.convois.index', compact('convois', 'statut', 'chauffeurs', 'vehicules'));
    }

    public function show(Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id) {
            abort(403);
        }

        $convoi->load(['user', 'compagnie', 'itineraire', 'passagers', 'chauffeur', 'vehicule', 'latestLocation']);

        return view('gare-espace.convois.show', compact('convoi'));
    }

    public function location(Convoi $convoi): JsonResponse
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        $convoi->load(['latestLocation', 'chauffeur', 'vehicule', 'itineraire', 'gare']);
        $location = $convoi->latestLocation;

        return response()->json([
            'success' => true,
            'convoi_id' => $convoi->id,
            'statut' => $convoi->statut,
            'latitude' => $location ? (float) $location->latitude : null,
            'longitude' => $location ? (float) $location->longitude : null,
            'speed' => $location ? $location->speed : null,
            'heading' => $location ? $location->heading : null,
            'last_update' => $location ? $location->updated_at->diffForHumans() : 'Jamais',
            'chauffeur' => $convoi->chauffeur ? trim(($convoi->chauffeur->prenom ?? '') . ' ' . ($convoi->chauffeur->name ?? '')) : 'Inconnu',
            'vehicule' => $convoi->vehicule->immatriculation ?? 'N/A',
            'trajet' => $convoi->itineraire ? ($convoi->itineraire->point_depart . ' -> ' . $convoi->itineraire->point_arrive) : '-',
            'gare' => $convoi->gare->nom_gare ?? '-',
        ]);
    }

    public function assign(Request $request, Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id) {
            abort(403);
        }

        $validated = $request->validate([
            'personnel_id' => 'required|exists:personnels,id',
            'vehicule_id' => 'required|exists:vehicules,id',
        ]);

        // Vérifier que le chauffeur n'a pas un voyage en_cours maintenant
        $chauffeurOnActiveVoyage = Voyage::where('personnel_id', $validated['personnel_id'])
            ->where('statut', 'en_cours')
            ->exists();
        if ($chauffeurOnActiveVoyage) {
            return back()->with('error', 'Ce chauffeur est actuellement en course (voyage en cours).');
        }

        // Vérifier chevauchement de dates avec d'autres convois
        $busyPersonnel = $this->busyPersonnelIdsForDateRange($convoi->date_depart, $convoi->date_retour);
        if (in_array((int) $validated['personnel_id'], $busyPersonnel, true)) {
            return back()->with('error', 'Ce chauffeur est déjà assigné à un autre convoi sur cette période.');
        }

        $busyVehicules = $this->busyVehiculeIdsForDateRange($convoi->date_depart, $convoi->date_retour);
        if (in_array((int) $validated['vehicule_id'], $busyVehicules, true)) {
            return back()->with('error', 'Ce véhicule est déjà assigné à un autre convoi sur cette période.');
        }

        // Vérifier chevauchement avec voyages programmés sur les mêmes dates
        $voyageDates = $this->getDateRange($convoi->date_depart, $convoi->date_retour);
        $chauffeurBusyVoyage = Voyage::where('personnel_id', $validated['personnel_id'])
            ->whereIn('date_voyage', $voyageDates)
            ->whereNotIn('statut', ['annulé', 'terminé'])
            ->exists();
        if ($chauffeurBusyVoyage) {
            return back()->with('error', 'Ce chauffeur a des voyages programmés pendant la période du convoi.');
        }

        $vehiculeBusyVoyage = Voyage::where('vehicule_id', $validated['vehicule_id'])
            ->whereIn('date_voyage', $voyageDates)
            ->whereNotIn('statut', ['annulé', 'terminé'])
            ->exists();
        if ($vehiculeBusyVoyage) {
            return back()->with('error', 'Ce véhicule a des voyages programmés pendant la période du convoi.');
        }

        // Vérifier que le véhicule a assez de places
        $vehicule = Vehicule::findOrFail($validated['vehicule_id']);
        if ($vehicule->nombre_place < $convoi->nombre_personnes) {
            return back()->with('error',
                "Ce véhicule n'a que {$vehicule->nombre_place} place(s) alors que le convoi nécessite {$convoi->nombre_personnes} personne(s). Veuillez choisir un véhicule adapté."
            );
        }

        $convoi->update([
            'personnel_id' => $validated['personnel_id'],
            'vehicule_id'  => $validated['vehicule_id'],
        ]);

        $convoi->loadMissing(['itineraire', 'vehicule', 'user']);
        $depart     = $convoi->lieu_depart  ?? ($convoi->itineraire->point_depart  ?? 'N/A');
        $arrivee    = $convoi->lieu_retour  ?? ($convoi->itineraire->point_arrive  ?? 'N/A');
        $dateDepart = $convoi->date_depart  ? Carbon::parse($convoi->date_depart)->format('d/m/Y') : 'N/A';
        $hDepart    = $convoi->heure_depart ? substr($convoi->heure_depart, 0, 5) : '';
        $lieu       = $convoi->lieu_rassemblement ?? 'À définir';

        // ── Notifier le chauffeur ──────────────────────────────────────────
        try {
            $chauffeur = Personnel::find($validated['personnel_id']);
            if ($chauffeur) {
                $chauffeur->notify(new ConvoiAssignedNotification($convoi));
                if ($chauffeur->fcm_token) {
                    app(\App\Services\FcmService::class)->sendNotification(
                        $chauffeur->fcm_token,
                        'Nouveau Convoi Assigné',
                        "Ref {$convoi->reference} · {$depart} → {$arrivee} · {$dateDepart}",
                        ['type' => 'convoi_assigned', 'convoi_id' => (string) $convoi->id]
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Notif chauffeur assign: ' . $e->getMessage());
        }

        // ── Notifier le demandeur (SMS + database si compte) ─────────────
        try {
            $smsBase = "Votre convoi CAR225 ref {$convoi->reference} a ete pris en charge !\n"
                     . "Trajet : {$depart} -> {$arrivee}\n"
                     . "Depart : {$dateDepart}" . ($hDepart ? " a {$hDepart}" : '') . "\n"
                     . "Lieu de rassemblement : {$lieu}\n"
                     . "Le chauffeur sera present. Bon voyage !";

            $user = $convoi->user;
            if ($user) {
                // Utilisateur avec compte → notification DB + SMS + FCM
                $prenom = $user->prenom ?? $user->name;
                $user->notify(new \App\Notifications\ConvoiChauffeurAssignedNotification($convoi));
                app(\App\Services\SmsService::class)->sendSms(
                    $user->contact,
                    "Bonjour {$prenom},\n" . $smsBase
                );
                if ($user->fcm_token) {
                    app(\App\Services\FcmService::class)->sendNotification(
                        $user->fcm_token,
                        'Votre convoi est pris en charge 🚌',
                        "Ref {$convoi->reference} · Départ le {$dateDepart} · Lieu : {$lieu}",
                        ['type' => 'convoi_chauffeur_assigne', 'convoi_id' => (string) $convoi->id]
                    );
                }
            } elseif ($convoi->created_by_gare && $convoi->client_contact) {
                // Client walk-in (sans compte) → SMS uniquement
                $prenom = $convoi->client_prenom ?? 'Client';
                app(\App\Services\SmsService::class)->sendSms(
                    $convoi->client_contact,
                    "Bonjour {$prenom},\n" . $smsBase
                );
            }
        } catch (\Exception $e) {
            Log::error('Notif demandeur assign convoi: ' . $e->getMessage());
        }

        return back()->with('success', 'Affectation effectuée. Le chauffeur et le demandeur ont été notifiés.');
    }

    /** Modifier l'affectation (changer chauffeur / véhicule) */
    public function reassign(Request $request, Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();
        if ($convoi->gare_id !== $gare->id) abort(403);
        if ($convoi->statut !== 'paye') {
            return back()->with('error', 'Modification impossible : le convoi n\'est plus en attente d\'affectation.');
        }

        $validated = $request->validate([
            'personnel_id' => 'required|exists:personnels,id',
            'vehicule_id'  => 'required|exists:vehicules,id',
        ]);

        // Vérifier chevauchement de dates (exclure le convoi courant)
        $busyPersonnel = $this->busyPersonnelIdsForDateRange($convoi->date_depart, $convoi->date_retour, $convoi->id);
        if (in_array((int) $validated['personnel_id'], $busyPersonnel, true)) {
            return back()->with('error', 'Ce chauffeur est déjà assigné à un autre convoi sur cette période.');
        }

        $busyVehicules = $this->busyVehiculeIdsForDateRange($convoi->date_depart, $convoi->date_retour, $convoi->id);
        if (in_array((int) $validated['vehicule_id'], $busyVehicules, true)) {
            return back()->with('error', 'Ce véhicule est déjà assigné à un autre convoi sur cette période.');
        }

        // Vérifier chevauchement avec voyages programmés
        $voyageDates = $this->getDateRange($convoi->date_depart, $convoi->date_retour);
        if (!empty($voyageDates)) {
            $chauffeurBusyVoyage = Voyage::where('personnel_id', $validated['personnel_id'])
                ->whereIn('date_voyage', $voyageDates)
                ->whereNotIn('statut', ['annulé', 'terminé'])
                ->exists();
            if ($chauffeurBusyVoyage) {
                return back()->with('error', 'Ce chauffeur a des voyages programmés pendant la période du convoi.');
            }

            $vehiculeBusyVoyage = Voyage::where('vehicule_id', $validated['vehicule_id'])
                ->whereIn('date_voyage', $voyageDates)
                ->whereNotIn('statut', ['annulé', 'terminé'])
                ->exists();
            if ($vehiculeBusyVoyage) {
                return back()->with('error', 'Ce véhicule a des voyages programmés pendant la période du convoi.');
            }
        }

        // Vérifier capacité véhicule
        $vehiculeR = Vehicule::findOrFail($validated['vehicule_id']);
        if ($vehiculeR->nombre_place < $convoi->nombre_personnes) {
            return back()->with('error',
                "Ce véhicule n'a que {$vehiculeR->nombre_place} place(s) alors que le convoi nécessite {$convoi->nombre_personnes} personne(s)."
            );
        }

        $convoi->update([
            'personnel_id' => $validated['personnel_id'],
            'vehicule_id'  => $validated['vehicule_id'],
        ]);

        $convoi->loadMissing(['itineraire', 'vehicule', 'user']);
        $rDepart     = $convoi->lieu_depart  ?? ($convoi->itineraire->point_depart  ?? 'N/A');
        $rArrivee    = $convoi->lieu_retour   ?? ($convoi->itineraire->point_arrive  ?? 'N/A');
        $rDateDepart = $convoi->date_depart   ? Carbon::parse($convoi->date_depart)->format('d/m/Y') : 'N/A';
        $rHDepart    = $convoi->heure_depart  ? substr($convoi->heure_depart, 0, 5) : '';
        $rLieu       = $convoi->lieu_rassemblement ?? 'À définir';

        // Notifier le nouveau chauffeur
        try {
            $chauffeur = Personnel::find($validated['personnel_id']);
            if ($chauffeur) {
                $chauffeur->notify(new ConvoiAssignedNotification($convoi));
                if ($chauffeur->fcm_token) {
                    app(\App\Services\FcmService::class)->sendNotification(
                        $chauffeur->fcm_token,
                        'Convoi Réassigné',
                        "Référence : {$convoi->reference}\nTrajet : {$rDepart} → {$rArrivee}\nPassagers : {$convoi->nombre_personnes}",
                        ['type' => 'convoi_assigned', 'convoi_id' => (string) $convoi->id]
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur notification chauffeur (reassign): ' . $e->getMessage());
        }

        // Notifier le demandeur (SMS) — utilisateur ou client walk-in
        try {
            $smsBase = "Votre convoi CAR225 ref {$convoi->reference} a ete mis a jour !\n"
                     . "Nouveau chauffeur/vehicule affecte.\n"
                     . "Trajet : {$rDepart} -> {$rArrivee}\n"
                     . "Depart : {$rDateDepart}" . ($rHDepart ? " a {$rHDepart}" : '') . "\n"
                     . "Lieu de rassemblement : {$rLieu}";

            $user = $convoi->user;
            if ($user) {
                $prenom = $user->prenom ?? $user->name;
                app(\App\Services\SmsService::class)->sendSms(
                    $user->contact,
                    "Bonjour {$prenom},\n" . $smsBase
                );
            } elseif ($convoi->created_by_gare && $convoi->client_contact) {
                $prenom = $convoi->client_prenom ?? 'Client';
                app(\App\Services\SmsService::class)->sendSms(
                    $convoi->client_contact,
                    "Bonjour {$prenom},\n" . $smsBase
                );
            }
        } catch (\Exception $e) {
            Log::error('Erreur notification demandeur (reassign): ' . $e->getMessage());
        }

        return back()->with('success', 'Affectation modifiée avec succès. Le demandeur a été notifié par SMS.');
    }

    /** Annuler l'affectation pour reprogrammer */
    public function unassign(Request $request, Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();
        if ($convoi->gare_id !== $gare->id) abort(403);
        if ($convoi->statut !== 'paye') {
            return back()->with('error', 'Impossible d\'annuler l\'affectation : le convoi est déjà en cours ou terminé.');
        }

        $convoi->update([
            'personnel_id' => null,
            'vehicule_id'  => null,
        ]);

        return back()->with('success', 'Affectation annulée. Le convoi peut être réaffecté.');
    }

    /**
     * Générer un tableau de dates entre date_depart et date_retour (inclus).
     */
    private function getDateRange(?string $dateDepart, ?string $dateRetour): array
    {
        if (!$dateDepart) return [];

        $start = Carbon::parse($dateDepart);
        $end   = $dateRetour ? Carbon::parse($dateRetour) : $start->copy();
        $dates = [];

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $dates[] = $d->format('Y-m-d');
        }

        return $dates;
    }
}
