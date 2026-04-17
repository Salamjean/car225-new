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
            ->whereIn('statut', ['confirme', 'paye', 'en_cours']);

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
            ->whereIn('statut', ['confirme', 'paye', 'en_cours']);

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
            'lieu_rassemblement'       => 'required|string|max:255',
            'lieu_rassemblement_retour'=> 'nullable|string|max:255',
            'motif'                    => 'nullable|string|max:500',
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
            'lieu_rassemblement'        => $validated['lieu_rassemblement'] ?? null,
            'lieu_rassemblement_retour' => $validated['lieu_rassemblement_retour'] ?? null,
            'reference'                 => 'CONV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
            'statut'                    => 'confirme',
            // Champs walk-in
            'client_nom'          => $validated['client_nom'],
            'client_prenom'       => $validated['client_prenom'],
            'client_contact'      => $validated['client_contact'],
            'client_email'        => $validated['client_email'] ?? null,
            'created_by_gare'     => true,
            'is_garant'           => true, // Par défaut, le client est garant pour son groupe
        ]);

        // Envoyer un SMS de création au client
        try {
            $prenom     = $convoi->client_prenom;
            $depart     = $lieuDepart;
            $arrivee    = $lieuRetour;
            $dateDepart = Carbon::parse($convoi->date_depart)->format('d/m/Y');
            $hDepart    = substr($convoi->heure_depart, 0, 5);
            $lieu       = $convoi->lieu_rassemblement ?? 'À définir';

            $smsMsg = "Bonjour {$prenom},\n"
                    . "Votre convoi CAR225 (ref {$convoi->reference}) a ete enregistre.\n"
                    . "Trajet : {$depart} -> {$arrivee}\n"
                    . "Depart : {$dateDepart}" . ($hDepart ? " a {$hDepart}" : '') . "\n"
                    . "Lieu rassemblement : {$lieu}\n"
                    . "Presentez-vous en caisse pour finaliser le paiement.";

            app(\App\Services\SmsService::class)->sendSms($convoi->client_contact, $smsMsg);
        } catch (\Exception $e) {
            Log::error('SMS walk-in convoi: ' . $e->getMessage());
        }

        return redirect()
            ->route('gare-espace.convois.show', $convoi)
            ->with('success', "Convoi {$convoi->reference} créé. Un SMS a été envoyé au client. Cliquez sur « Faire le paiement » pour encaisser et finaliser.");
    }

    /**
     * Encaissement du paiement d'un convoi walk-in créé par la gare.
     * Passe le statut de 'confirme' à 'paye' et crédite la compagnie.
     */
    public function payerWalkin(Request $request, Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id || !$convoi->created_by_gare) {
            abort(403);
        }

        if ($convoi->statut !== 'confirme') {
            return back()->with('error', 'Ce convoi ne peut pas être encaissé (statut : ' . $convoi->statut . ').');
        }

        $convoi->update(['statut' => 'paye']);
        $convoi->compagnie()->increment('solde_convoie', $convoi->montant);

        $convoi->loadMissing(['compagnie', 'gare', 'itineraire', 'chauffeur', 'vehicule']);

        // Envoyer le reçu par email si disponible
        if ($convoi->client_email) {
            try {
                Mail::to($convoi->client_email)->send(new ConvoiWalkinReceiptMail($convoi));
            } catch (\Exception $e) {
                Log::error('Email reçu walk-in paiement: ' . $e->getMessage());
            }
        }

        // Envoyer SMS de confirmation de paiement
        try {
            $prenom     = $convoi->client_prenom;
            $depart     = $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? 'N/A');
            $arrivee    = $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? 'N/A');
            $dateDepart = Carbon::parse($convoi->date_depart)->format('d/m/Y');
            $montantF   = number_format($convoi->montant, 0, ',', ' ');
            $lieu       = $convoi->lieu_rassemblement ?? 'À définir';

            app(\App\Services\SmsService::class)->sendSms(
                $convoi->client_contact,
                "Bonjour {$prenom},\n"
                . "Paiement de {$montantF} FCFA confirme pour votre convoi CAR225 ref {$convoi->reference} !\n"
                . "Trajet : {$depart} -> {$arrivee}\n"
                . "Depart : {$dateDepart}\n"
                . "Lieu rassemblement : {$lieu}\n"
                . "Merci de votre confiance. Bon voyage !"
            );
        } catch (\Exception $e) {
            Log::error('SMS paiement walk-in: ' . $e->getMessage());
        }

        return back()->with('success', "Paiement de {$convoi->reference} encaissé. Le client peut maintenant recevoir son ticket et la liste des passagers peut être complétée.");
    }

    /** Enregistrement / envoi lien passagers pour un convoi walk-in depuis la gare */
    public function storeWalkinPassengers(Request $request, Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id || !$convoi->created_by_gare) {
            abort(403);
        }

        $request->validate([
            'is_garant' => 'nullable|boolean',
        ]);

        $isGarant = (bool) $request->input('is_garant', false);

        $convoi->update(['is_garant' => $isGarant]);

        if ($isGarant) {
            // ── Mode garant : on enregistre directement les infos du client comme passager unique ──
            $convoi->passagers()->delete();
            $convoi->passagers()->create([
                'nom'             => $convoi->client_nom,
                'prenoms'         => $convoi->client_prenom,
                'contact'         => $convoi->client_contact,
                'contact_urgence' => $convoi->client_contact,
            ]);
            $convoi->update(['passagers_soumis' => true]);
            return back()->with('success', 'Le client est enregistré comme garant du groupe. L\'affectation peut être réalisée.');
        }

        // ── Mode liste complète : générer un lien sécurisé et l'envoyer au client ──
        $token = bin2hex(random_bytes(24)); // 48 caractères hex
        $convoi->update([
            'passenger_form_token' => $token,
            'passagers_soumis'     => false,
        ]);

        $lien = route('public.convoi.passagers.form', $token);

        // SMS
        try {
            $prenom = $convoi->client_prenom ?? 'Client';
            $msg = "Bonjour {$prenom},\n"
                 . "CAR225 - Votre convoi ref {$convoi->reference} est enregistre.\n"
                 . "Veuillez renseigner la liste de vos {$convoi->nombre_personnes} passagers via ce lien :\n"
                 . $lien;
            app(\App\Services\SmsService::class)->sendSms($convoi->client_contact, $msg);
        } catch (\Exception $e) {
            Log::error('SMS lien passagers convoi: ' . $e->getMessage());
        }

        // Email si disponible
        if ($convoi->client_email) {
            try {
                \Illuminate\Support\Facades\Mail::to($convoi->client_email)
                    ->send(new \App\Mail\ConvoiPassagerLinkMail($convoi, $lien));
            } catch (\Exception $e) {
                Log::error('Email lien passagers convoi: ' . $e->getMessage());
            }
        }

        return back()->with('success', "Lien envoyé au client ({$convoi->client_contact}) par SMS" . ($convoi->client_email ? " et par email" : '') . ". Il peut maintenant renseigner la liste de ses passagers.");
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

        if (in_array($statut, ['en_attente', 'valide', 'confirme', 'refuse', 'paye', 'en_cours', 'termine', 'annule'])) {
            $query->where('statut', $statut);
        }

        // Montants financiers pour cette gare
        $montantPaye = Convoi::where('gare_id', $gare->id)
            ->whereIn('statut', ['paye', 'en_cours', 'termine'])
            ->sum('montant');

        $montantAPayer = Convoi::where('gare_id', $gare->id)
            ->whereIn('statut', ['valide', 'confirme'])
            ->sum('montant');

        $convois = $query->paginate(10)->withQueryString();

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

        // Nombre de convois en attente pour le badge
        $enAttenteCount = Convoi::where('gare_id', $gare->id)->where('statut', 'en_attente')->count();

        return view('gare-espace.convois.index', compact('convois', 'statut', 'chauffeurs', 'vehicules', 'montantPaye', 'montantAPayer', 'enAttenteCount'));
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

        // Vérifier qu'au moins 1 passager est enregistré ou que le client est garant
        if (!$convoi->is_garant && $convoi->passagers()->count() === 0) {
            return back()->with('error', 'Impossible d\'affecter : au moins 1 passager doit être enregistré, ou le client doit avoir choisi le mode garant.');
        }

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
                     . "Depart : {$dateDepart}" . ($hDepart ? " à {$hDepart}" : '') . "\n"
                     . "Lieu de rassemblement : {$lieu}\n"
                     . "Le chauffeur sera present. Bon voyage !"
                     . "veuillez telecharger l'application car225 sur : " . route('home.download-app') . " pour suivre votre convoi en temps reel.";

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

        // ── Email au client si disponible ─────────────────────────────────
        try {
            $convoi->loadMissing(['chauffeur', 'vehicule', 'passagers', 'itineraire', 'user']);
            $emailTo = $convoi->user ? ($convoi->user->email ?? null) : ($convoi->client_email ?? null);
            if ($emailTo) {
                Mail::to($emailTo)->send(new \App\Mail\ConvoiChauffeurAssigneMail($convoi));
            }
        } catch (\Exception $e) {
            Log::error('Email chauffeur assign: ' . $e->getMessage());
        }

        // ── SMS à chaque passager (hors demandeur principal déjà notifié) ─
        try {
            $convoi->loadMissing(['passagers']);
            $demandeurContact = $convoi->user ? ($convoi->user->contact ?? null) : ($convoi->client_contact ?? null);
            $smsPassager = "Bonjour,\n"
                         . "Vous etes passager d'un convoi CAR225 ref {$convoi->reference}.\n"
                         . "Trajet : {$depart} -> {$arrivee}\n"
                         . "Depart : {$dateDepart}" . ($hDepart ? " à {$hDepart}" : '') . "\n"
                         . "Lieu de rassemblement : {$lieu}\n"
                         . "Un chauffeur a ete affecte a votre convoi. Bon voyage !"
                         . "veuillez telecharger l'application car225 sur : " . route('home.download-app') . " pour suivre votre convoi en temps reel.";
            foreach ($convoi->passagers as $passager) {
                if ($passager->contact && $passager->contact !== $demandeurContact) {
                    app(\App\Services\SmsService::class)->sendSms($passager->contact, $smsPassager);
                }
            }
        } catch (\Exception $e) {
            Log::error('SMS passagers assign convoi: ' . $e->getMessage());
        }

        return back()->with('success', 'Affectation effectuée. Le chauffeur, le demandeur et les passagers ont été notifiés.');
    }

    /** Modifier l'affectation (changer chauffeur / véhicule) */
    public function reassign(Request $request, Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();
        if ($convoi->gare_id !== $gare->id) abort(403);
        if (!in_array($convoi->statut, ['confirme', 'paye'])) {
            return back()->with('error', 'Modification impossible : le convoi n\'est plus en attente d\'affectation.');
        }

        $validated = $request->validate([
            'personnel_id' => 'required|exists:personnels,id',
            'vehicule_id'  => 'required|exists:vehicules,id',
        ]);

        // Vérifier qu'au moins 1 passager est enregistré ou que le client est garant
        if (!$convoi->is_garant && $convoi->passagers()->count() === 0) {
            return back()->with('error', 'Impossible de modifier l\'affectation : au moins 1 passager doit être enregistré, ou le client doit avoir choisi le mode garant.');
        }

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

        // ── FCM au demandeur (utilisateur avec compte) ───────────────────
        try {
            $convoi->loadMissing(['user', 'itineraire']);
            $userR = $convoi->user;
            if ($userR && $userR->fcm_token) {
                app(\App\Services\FcmService::class)->sendNotification(
                    $userR->fcm_token,
                    'Affectation mise à jour 🔄',
                    "Réf. {$convoi->reference} · Nouveau chauffeur/véhicule affecté. Départ le {$rDateDepart}" . ($rHDepart ? " à {$rHDepart}" : '') . '.',
                    ['type' => 'convoi_reassigne', 'convoi_id' => (string) $convoi->id]
                );
            }
        } catch (\Exception $e) {
            Log::error('FCM user reassign convoi: ' . $e->getMessage());
        }

        // ── Email au client si disponible ─────────────────────────────────
        try {
            $convoi->loadMissing(['chauffeur', 'vehicule', 'passagers', 'itineraire', 'user']);
            $emailTo = $convoi->user ? ($convoi->user->email ?? null) : ($convoi->client_email ?? null);
            if ($emailTo) {
                Mail::to($emailTo)->send(new \App\Mail\ConvoiChauffeurAssigneMail($convoi));
            }
        } catch (\Exception $e) {
            Log::error('Email chauffeur reassign: ' . $e->getMessage());
        }

        return back()->with('success', 'Affectation modifiée. Le demandeur a été notifié.');
    }

    /** Annuler l'affectation pour reprogrammer */
    public function unassign(Request $request, Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();
        if ($convoi->gare_id !== $gare->id) abort(403);
        if (!in_array($convoi->statut, ['confirme', 'paye'])) {
            return back()->with('error', 'Impossible d\'annuler l\'affectation : le convoi est déjà en cours ou terminé.');
        }

        $convoi->update([
            'personnel_id' => null,
            'vehicule_id'  => null,
        ]);

        return back()->with('success', 'Affectation annulée. Le convoi peut être réaffecté.');
    }

    /** Valider une demande de convoi en attente — la gare fixe le montant et notifie l'utilisateur */
    public function valider(Request $request, Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id) {
            abort(403);
        }

        if ($convoi->statut !== 'en_attente') {
            return back()->with('error', 'Ce convoi ne peut plus être validé (statut : ' . $convoi->statut . ').');
        }

        $validated = $request->validate([
            'montant' => 'required|numeric|min:100',
        ], [
            'montant.required' => 'Le montant est obligatoire pour valider la demande.',
            'montant.min'      => 'Le montant doit être d\'au moins 100 FCFA.',
        ]);

        $convoi->update([
            'statut'  => 'valide',
            'montant' => $validated['montant'],
        ]);

        $convoi->loadMissing(['user', 'itineraire']);
        $depart     = $convoi->lieu_depart  ?? ($convoi->itineraire->point_depart  ?? 'N/A');
        $arrivee    = $convoi->lieu_retour   ?? ($convoi->itineraire->point_arrive  ?? 'N/A');
        $dateDepart = $convoi->date_depart   ? Carbon::parse($convoi->date_depart)->format('d/m/Y') : 'N/A';
        $montantF   = number_format($validated['montant'], 0, ',', ' ');

        $user = $convoi->user;
        if ($user) {
            // Notifier par DB
            try {
                $user->notify(new \App\Notifications\ConvoiValidatedNotification($convoi));
            } catch (\Exception $e) {
                Log::error('Notif DB valider convoi: ' . $e->getMessage());
            }

            // SMS
            try {
                $prenom = $user->prenom ?? $user->name;
                app(\App\Services\SmsService::class)->sendSms(
                    $user->contact,
                    "Bonjour {$prenom},\n"
                    . "Votre demande de convoi CAR225 (ref {$convoi->reference}) a ete VALIDEE !\n"
                    . "Trajet : {$depart} -> {$arrivee}\n"
                    . "Depart : {$dateDepart}\n"
                    . "Montant a payer : {$montantF} FCFA\n"
                    . "Connectez-vous a l'application pour effectuer le paiement."
                );
            } catch (\Exception $e) {
                Log::error('SMS valider convoi: ' . $e->getMessage());
            }

            // FCM
            try {
                if ($user->fcm_token) {
                    app(\App\Services\FcmService::class)->sendNotification(
                        $user->fcm_token,
                        'Demande de convoi validée ✅',
                        "Ref {$convoi->reference} · {$depart} → {$arrivee} · Montant : {$montantF} FCFA",
                        ['type' => 'convoi_valide', 'convoi_id' => (string) $convoi->id]
                    );
                }
            } catch (\Exception $e) {
                Log::error('FCM valider convoi: ' . $e->getMessage());
            }
        }

        return back()->with('success', "Convoi {$convoi->reference} validé. Le montant de {$montantF} FCFA a été communiqué au demandeur.");
    }

    /** Refuser une demande de convoi en attente */
    public function refuser(Request $request, Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id) {
            abort(403);
        }

        if ($convoi->statut !== 'en_attente') {
            return back()->with('error', 'Ce convoi ne peut plus être refusé (statut : ' . $convoi->statut . ').');
        }

        $validated = $request->validate([
            'motif_refus' => 'required|string|max:500',
        ], [
            'motif_refus.required' => 'Le motif du refus est obligatoire.',
        ]);

        $convoi->update([
            'statut'      => 'refuse',
            'motif_refus' => $validated['motif_refus'],
        ]);

        $convoi->loadMissing(['user', 'itineraire']);
        $user = $convoi->user;

        if ($user) {
            try {
                $prenom     = $user->prenom ?? $user->name;
                $depart     = $convoi->lieu_depart  ?? ($convoi->itineraire->point_depart  ?? 'N/A');
                $arrivee    = $convoi->lieu_retour   ?? ($convoi->itineraire->point_arrive  ?? 'N/A');
                app(\App\Services\SmsService::class)->sendSms(
                    $user->contact,
                    "Bonjour {$prenom},\n"
                    . "Votre demande de convoi CAR225 (ref {$convoi->reference}) a ete refusee.\n"
                    . "Trajet : {$depart} -> {$arrivee}\n"
                    . "Motif : {$validated['motif_refus']}\n"
                    . "Contactez-nous pour plus d'informations."
                );
            } catch (\Exception $e) {
                Log::error('SMS refuser convoi: ' . $e->getMessage());
            }

            try {
                if ($user->fcm_token) {
                    app(\App\Services\FcmService::class)->sendNotification(
                        $user->fcm_token,
                        'Demande de convoi refusée',
                        "Ref {$convoi->reference} · Motif : {$validated['motif_refus']}",
                        ['type' => 'convoi_refuse', 'convoi_id' => (string) $convoi->id]
                    );
                }
            } catch (\Exception $e) {
                Log::error('FCM refuser convoi: ' . $e->getMessage());
            }
        }

        return back()->with('success', "Demande {$convoi->reference} refusée. Le demandeur a été notifié.");
    }

    /**
     * Enregistrement unifié : affectation + garant/passagers en un seul POST.
     * Utilisé pour les convois walk-in (statut=paye) afin d'avoir un seul bouton "Enregistrer".
     */
    public function saveFull(Request $request, Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id) {
            abort(403);
        }

        if ($convoi->statut !== 'paye') {
            return back()->with('error', 'Action impossible : le convoi n\'est pas dans le statut approprié.');
        }

        $request->validate([
            'personnel_id' => 'nullable|exists:personnels,id',
            'vehicule_id'  => 'nullable|exists:vehicules,id',
            'is_garant'    => 'nullable|boolean',
        ]);

        $messages = [];

        // ── 1. Affectation (si les deux champs sont renseignés) ──────────────
        $personnelId = $request->filled('personnel_id') ? $request->input('personnel_id') : null;
        $vehiculeId  = $request->filled('vehicule_id')  ? $request->input('vehicule_id')  : null;

        if ($personnelId && $vehiculeId) {
            // Vérifier chevauchement avec voyages en cours
            $chauffeurOnActiveVoyage = Voyage::where('personnel_id', $personnelId)->where('statut', 'en_cours')->exists();
            if ($chauffeurOnActiveVoyage) {
                return back()->with('error', 'Ce chauffeur est actuellement en course (voyage en cours).');
            }

            // Vérifier chevauchement de dates avec d'autres convois (exclure ce convoi)
            $busyPersonnel = $this->busyPersonnelIdsForDateRange($convoi->date_depart, $convoi->date_retour, $convoi->id);
            if (in_array((int) $personnelId, $busyPersonnel, true)) {
                return back()->with('error', 'Ce chauffeur est déjà assigné à un autre convoi sur cette période.');
            }

            $busyVehicules = $this->busyVehiculeIdsForDateRange($convoi->date_depart, $convoi->date_retour, $convoi->id);
            if (in_array((int) $vehiculeId, $busyVehicules, true)) {
                return back()->with('error', 'Ce véhicule est déjà assigné à un autre convoi sur cette période.');
            }

            // Vérifier chevauchement avec voyages programmés
            $voyageDates = $this->getDateRange($convoi->date_depart, $convoi->date_retour);
            if (!empty($voyageDates)) {
                if (Voyage::where('personnel_id', $personnelId)->whereIn('date_voyage', $voyageDates)->whereNotIn('statut', ['annulé', 'terminé'])->exists()) {
                    return back()->with('error', 'Ce chauffeur a des voyages programmés pendant la période du convoi.');
                }
                if (Voyage::where('vehicule_id', $vehiculeId)->whereIn('date_voyage', $voyageDates)->whereNotIn('statut', ['annulé', 'terminé'])->exists()) {
                    return back()->with('error', 'Ce véhicule a des voyages programmés pendant la période du convoi.');
                }
            }

            // Vérifier capacité véhicule
            $vehicule = Vehicule::findOrFail($vehiculeId);
            if ($vehicule->nombre_place < $convoi->nombre_personnes) {
                return back()->with('error', "Ce véhicule n'a que {$vehicule->nombre_place} place(s) alors que le convoi nécessite {$convoi->nombre_personnes} personne(s).");
            }

            $convoi->update(['personnel_id' => $personnelId, 'vehicule_id' => $vehiculeId]);

            $convoi->loadMissing(['itineraire', 'vehicule', 'user']);
            $depart     = $convoi->lieu_depart  ?? ($convoi->itineraire->point_depart  ?? 'N/A');
            $arrivee    = $convoi->lieu_retour   ?? ($convoi->itineraire->point_arrive  ?? 'N/A');
            $dateDepart = $convoi->date_depart   ? Carbon::parse($convoi->date_depart)->format('d/m/Y') : 'N/A';
            $hDepart    = $convoi->heure_depart  ? substr($convoi->heure_depart, 0, 5) : '';
            $lieu       = $convoi->lieu_rassemblement ?? 'À définir';

            // Notifier le chauffeur
            try {
                $chauffeur = Personnel::find($personnelId);
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
                Log::error('saveFull notif chauffeur: ' . $e->getMessage());
            }

            // Notifier le demandeur
            try {
                $smsBase = "Votre convoi CAR225 ref {$convoi->reference} a ete pris en charge !\n"
                         . "Trajet : {$depart} -> {$arrivee}\n"
                         . "Depart : {$dateDepart}" . ($hDepart ? " à {$hDepart}" : '') . "\n"
                         . "Lieu de rassemblement : {$lieu}\n"
                         . "Le chauffeur sera present. Bon voyage !"
                         . "veuillez telecharger l'application car225 sur : " . route('home.download-app') . " pour suivre votre convoi en temps reel.";

                $user = $convoi->user;
                if ($user) {
                    $prenom = $user->prenom ?? $user->name;
                    $user->notify(new \App\Notifications\ConvoiChauffeurAssignedNotification($convoi));
                    app(\App\Services\SmsService::class)->sendSms($user->contact, "Bonjour {$prenom},\n" . $smsBase);
                    if ($user->fcm_token) {
                        app(\App\Services\FcmService::class)->sendNotification(
                            $user->fcm_token,
                            'Votre convoi est pris en charge 🚌',
                            "Ref {$convoi->reference} · Départ le {$dateDepart} · Lieu : {$lieu}",
                            ['type' => 'convoi_chauffeur_assigne', 'convoi_id' => (string) $convoi->id]
                        );
                    }
                } elseif ($convoi->created_by_gare && $convoi->client_contact) {
                    $prenom = $convoi->client_prenom ?? 'Client';
                    app(\App\Services\SmsService::class)->sendSms($convoi->client_contact, "Bonjour {$prenom},\n" . $smsBase);
                }
            } catch (\Exception $e) {
                Log::error('saveFull notif demandeur: ' . $e->getMessage());
            }

            // Email au client walk-in si disponible
            try {
                $convoi->loadMissing(['chauffeur', 'vehicule', 'passagers', 'itineraire']);
                if ($convoi->client_email) {
                    Mail::to($convoi->client_email)->send(new \App\Mail\ConvoiChauffeurAssigneMail($convoi));
                }
            } catch (\Exception $e) {
                Log::error('Email chauffeur saveFull: ' . $e->getMessage());
            }

            // SMS à chaque passager walk-in déjà enregistré (hors client principal)
            try {
                $convoi->loadMissing(['passagers']);
                $sfDepart  = $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? 'N/A');
                $sfArrivee = $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? 'N/A');
                $sfDate    = $convoi->date_depart ? Carbon::parse($convoi->date_depart)->format('d/m/Y') : 'N/A';
                $sfHeure   = $convoi->heure_depart ? substr($convoi->heure_depart, 0, 5) : '';
                $sfLieu    = $convoi->lieu_rassemblement ?? 'À définir';
                $sfSms = "Bonjour,\n"
                       . "Vous etes passager d'un convoi CAR225 ref {$convoi->reference}.\n"
                       . "Trajet : {$sfDepart} -> {$sfArrivee}\n"
                       . "Depart : {$sfDate}" . ($sfHeure ? " a {$sfHeure}" : '') . "\n"
                       . "Lieu de rassemblement : {$sfLieu}\n"
                       . "Un chauffeur a ete affecte. Bon voyage !"
                       . "veuillez telecharger l'application car225 sur : " . route('home.download-app') . " pour suivre votre convoi en temps reel.";
                foreach ($convoi->passagers as $passager) {
                    if ($passager->contact && $passager->contact !== $convoi->client_contact) {
                        app(\App\Services\SmsService::class)->sendSms($passager->contact, $sfSms);
                    }
                }
            } catch (\Exception $e) {
                Log::error('SMS passagers saveFull: ' . $e->getMessage());
            }

            $messages[] = 'Affectation enregistrée. Le chauffeur, le demandeur et les passagers ont été notifiés.';
        }

        // ── 2. Garant / Passagers (walk-in uniquement, passagers non encore soumis) ──
        if ($convoi->created_by_gare && !$convoi->passagers_soumis) {
            $isGarant = (bool) $request->input('is_garant', false);
            $convoi->update(['is_garant' => $isGarant]);

            if ($isGarant) {
                $convoi->passagers()->delete();
                $convoi->passagers()->create([
                    'nom'             => $convoi->client_nom,
                    'prenoms'         => $convoi->client_prenom,
                    'contact'         => $convoi->client_contact,
                    'contact_urgence' => $convoi->client_contact,
                ]);
                $convoi->update(['passagers_soumis' => true]);
                $messages[] = 'Client enregistré comme garant du groupe.';
            } else {
                // Générer un nouveau token et envoyer le lien
                $token = bin2hex(random_bytes(24));
                $convoi->update(['passenger_form_token' => $token, 'passagers_soumis' => false]);
                $lien = route('public.convoi.passagers.form', $token);

                try {
                    $prenom = $convoi->client_prenom ?? 'Client';
                    $msg = "Bonjour {$prenom},\n"
                         . "CAR225 - Votre convoi ref {$convoi->reference} est enregistre.\n"
                         . "Veuillez renseigner la liste de vos {$convoi->nombre_personnes} passagers via ce lien :\n"
                         . $lien;
                    app(\App\Services\SmsService::class)->sendSms($convoi->client_contact, $msg);
                } catch (\Exception $e) {
                    Log::error('saveFull SMS lien passagers: ' . $e->getMessage());
                }

                if ($convoi->client_email) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($convoi->client_email)
                            ->send(new \App\Mail\ConvoiPassagerLinkMail($convoi, $lien));
                    } catch (\Exception $e) {
                        Log::error('saveFull email lien passagers: ' . $e->getMessage());
                    }
                }

                $messages[] = 'Lien passagers envoyé au client (' . $convoi->client_contact . ') par SMS'
                    . ($convoi->client_email ? ' et par email' : '') . '.';
            }
        }

        $finalMsg = !empty($messages) ? implode(' ', $messages) : 'Enregistrement effectué.';
        return back()->with('success', $finalMsg);
    }

    /** Supprimer un passager d'un convoi (libère une place) */
    public function deletePassager(Request $request, Convoi $convoi, \App\Models\ConvoiPassager $passager)
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id) {
            abort(403);
        }

        if ($passager->convoi_id !== $convoi->id) {
            abort(404);
        }

        // Bloquer si le convoi est déjà en cours ou terminé
        if (in_array($convoi->statut, ['en_cours', 'termine', 'annule'])) {
            return back()->with('error', 'Impossible de supprimer un passager : le convoi est déjà ' . $convoi->statut . '.');
        }

        $passager->delete();

        // Si on passe sous le seuil, remettre passagers_soumis à false pour inciter à compléter
        $remaining = $convoi->passagers()->count();
        if ($remaining < $convoi->nombre_personnes && $convoi->passagers_soumis) {
            $convoi->update(['passagers_soumis' => false]);
        }

        return back()->with('success', 'Passager supprimé. La place est maintenant disponible.');
    }

    /** La gare encaisse le paiement physique → statut paye */
    public function solder(Request $request, Convoi $convoi)
    {
        $gare = Auth::guard('gare')->user();

        if ($convoi->gare_id !== $gare->id) {
            abort(403);
        }

        if ($convoi->statut !== 'confirme') {
            return back()->with('error', 'Ce convoi ne peut pas être soldé (statut : ' . $convoi->statut . ').');
        }

        $convoi->update(['statut' => 'paye']);
        $convoi->compagnie()->increment('solde_convoie', $convoi->montant);

        // Générer un token passagers pour permettre le partage du lien
        if (!$convoi->passenger_form_token) {
            $convoi->update(['passenger_form_token' => bin2hex(random_bytes(24))]);
        }

        $convoi->loadMissing(['user', 'itineraire']);
        $user = $convoi->user;
        if ($user) {
            $prenom     = $user->prenom ?? $user->name;
            $depart     = $convoi->lieu_depart  ?? ($convoi->itineraire->point_depart  ?? 'N/A');
            $arrivee    = $convoi->lieu_retour   ?? ($convoi->itineraire->point_arrive  ?? 'N/A');
            $dateDepart = $convoi->date_depart   ? \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y') : 'N/A';
            $montantF   = number_format($convoi->montant, 0, ',', ' ');

            try {
                $user->notify(new \App\Notifications\ConvoiValidatedNotification($convoi));
            } catch (\Exception $e) {
                Log::error('Notif DB solder convoi: ' . $e->getMessage());
            }

            try {
                app(\App\Services\SmsService::class)->sendSms(
                    $user->contact,
                    "Bonjour {$prenom},\n"
                    . "Votre paiement de {$montantF} FCFA pour le convoi CAR225 ref {$convoi->reference} a bien ete enregistre !\n"
                    . "Trajet : {$depart} -> {$arrivee}\n"
                    . "Depart : {$dateDepart}\n"
                    . "Vous pouvez maintenant telecharger votre ticket depuis l'application ou sur le site web : " . route('home.download-app') . ""
                );
            } catch (\Exception $e) {
                Log::error('SMS solder convoi: ' . $e->getMessage());
            }

            try {
                if ($user->fcm_token) {
                    app(\App\Services\FcmService::class)->sendNotification(
                        $user->fcm_token,
                        'Paiement confirmé ✅',
                        "Ref {$convoi->reference} · {$depart} → {$arrivee} · Montant : {$montantF} FCFA — Votre ticket est disponible.",
                        ['type' => 'convoi_paye', 'convoi_id' => (string) $convoi->id]
                    );
                }
            } catch (\Exception $e) {
                Log::error('FCM solder convoi: ' . $e->getMessage());
            }
        }

        return back()->with('success', "Convoi {$convoi->reference} soldé. Le client a été notifié et peut télécharger son ticket.");
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
