<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Compagnie;
use App\Models\Convoi;
use App\Models\ConvoiPassager;
use App\Models\Gare;
use App\Models\Itineraire;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConvoiApiController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    //  HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    private function formatConvoi(Convoi $convoi): array
    {
        $convoi->loadMissing(['compagnie', 'particulier', 'gare', 'itineraire', 'chauffeur', 'vehicule', 'passagers']);

        $passagerFormUrl = $convoi->passenger_form_token
            ? route('public.convoi.passagers.form', $convoi->passenger_form_token)
            : null;

        return [
            'id'                        => $convoi->id,
            'reference'                 => $convoi->reference,
            'statut'                    => $convoi->statut,
            'montant'                   => $convoi->montant,
            'montant_propose_client'    => $convoi->montant_propose_client,
            'dernier_offreur'           => $convoi->dernier_offreur,
            'nombre_personnes'          => $convoi->nombre_personnes,
            'lieu_depart'               => $convoi->lieu_depart,
            'lieu_retour'               => $convoi->lieu_retour,
            'lieu_rassemblement'        => $convoi->lieu_rassemblement,
            'lieu_rassemblement_retour' => $convoi->lieu_rassemblement_retour,
            'date_depart'               => $convoi->date_depart,
            'heure_depart'              => $convoi->heure_depart ? substr($convoi->heure_depart, 0, 5) : null,
            'date_retour'               => $convoi->date_retour,
            'heure_retour'              => $convoi->heure_retour ? substr($convoi->heure_retour, 0, 5) : null,
            'is_garant'                 => (bool) $convoi->is_garant,
            'passagers_soumis'          => (bool) $convoi->passagers_soumis,
            'passenger_form_url'        => $passagerFormUrl,
            'motif_refus'               => $convoi->motif_refus,
            'created_at'                => $convoi->created_at?->toIso8601String(),
            'type_transporteur'         => $convoi->particulier_id ? 'particulier' : ($convoi->compagnie_id ? 'compagnie' : ($convoi->particulier ? 'particulier' : 'compagnie')),
            'compagnie' => $convoi->compagnie ? [
                'id'    => $convoi->compagnie->id,
                'name'  => $convoi->compagnie->name,
                'sigle' => $convoi->compagnie->sigle,
                'logo'  => $convoi->compagnie->path_logo ? asset('storage/' . $convoi->compagnie->path_logo) : null,
            ] : null,
            'particulier' => $convoi->particulier ? [
                'id'                 => $convoi->particulier->id,
                'full_name'          => $convoi->particulier->full_name,
                'contact'            => $convoi->particulier->contact,
                'photo'              => $convoi->particulier->photo_proprietaire_url,
                'immatriculation'    => $convoi->particulier->immatriculation,
                'nombre_place_car'   => $convoi->particulier->nombre_place_car,
            ] : null,
            'gare' => $convoi->gare ? [
                'id'       => $convoi->gare->id,
                'nom_gare' => $convoi->gare->nom_gare,
                'ville'    => $convoi->gare->ville,
                'adresse'  => $convoi->gare->adresse,
                'contact'  => $convoi->gare->contact,
            ] : null,
            'itineraire' => $convoi->itineraire ? [
                'id'            => $convoi->itineraire->id,
                'point_depart'  => $convoi->itineraire->point_depart,
                'point_arrive'  => $convoi->itineraire->point_arrive,
            ] : null,
            'chauffeur' => $convoi->chauffeur ? [
                'id'     => $convoi->chauffeur->id,
                'nom'    => trim(($convoi->chauffeur->prenom ?? '') . ' ' . ($convoi->chauffeur->name ?? '')),
                'photo'  => $convoi->chauffeur->photo ? asset('storage/' . $convoi->chauffeur->photo) : null,
                'contact'=> $convoi->chauffeur->contact ?? null,
            ] : null,
            'vehicule' => $convoi->vehicule ? [
                'id'             => $convoi->vehicule->id,
                'immatriculation'=> $convoi->vehicule->immatriculation,
                'modele'         => $convoi->vehicule->modele,
                'nombre_place'   => $convoi->vehicule->nombre_place,
            ] : null,
            'passagers_count' => $convoi->passagers->count(),
            'passagers' => $convoi->passagers->map(fn($p) => [
                'id'              => $p->id,
                'nom'             => $p->nom,
                'prenoms'         => $p->prenoms,
                'contact'         => $p->contact,
                'contact_urgence' => $p->contact_urgence,
            ])->values(),
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  RÈGLEMENT (statique)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * GET /api/user/convois-form/reglement
     * Retourne le texte du règlement des convois CAR225
     */
    public function reglement(): JsonResponse
    {
        return response()->json([
            'success'   => true,
            'reglement' => [
                [
                    'numero' => 1,
                    'titre'  => 'Réservation',
                    'texte'  => 'Toute demande de convoi est soumise à la validation par le prestataire. Le montant fixé est définitif.',
                ],
                [
                    'numero' => 2,
                    'titre'  => 'Paiement',
                    'texte'  => 'Le paiement doit être effectué en totalité avant la mise à disposition du véhicule et du chauffeur.',
                ],
                [
                    'numero' => 3,
                    'titre'  => 'Passagers',
                    'texte'  => 'La liste des passagers doit être complète avant la date de départ.',
                ],
                [
                    'numero' => 4,
                    'titre'  => 'Annulation',
                    'texte'  => 'Toute annulation doit être notifiée au prestataire au moins 48h avant la date de départ.',
                ],
                [
                    'numero' => 5,
                    'titre'  => 'Responsabilité',
                    'texte'  => 'CAR225 et le prestataire ne sauraient être tenus responsables de tout incident imputable au non-respect de ce règlement par le demandeur.',
                ],
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  DONNÉES DE FORMULAIRE (publiques)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * GET /api/user/convois/compagnies
     * Liste des compagnies actives pour le formulaire de création
     */
    public function compagnies(): JsonResponse
    {
        $compagnies = Compagnie::where('statut', 'actif')
            ->orderBy('name')
            ->get(['id', 'name', 'sigle', 'path_logo'])
            ->map(fn($c) => [
                'id'    => $c->id,
                'name'  => $c->name,
                'sigle' => $c->sigle,
                'logo'  => $c->path_logo ? asset('storage/' . $c->path_logo) : null,
            ]);

        return response()->json(['success' => true, 'compagnies' => $compagnies]);
    }

    /**
     * GET /api/user/convois-form/particuliers
     * Liste des particuliers validés pour le formulaire de création de convoi
     */
    public function particuliers(): JsonResponse
    {
        $particuliers = \App\Models\Particulier::where('statut', 'valide')
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id'                 => $p->id,
                'name'               => $p->name,
                'prenom'             => $p->prenom,
                'full_name'          => $p->full_name,
                'contact'            => $p->contact,
                'email'              => $p->email,
                'immatriculation'    => $p->immatriculation,
                'nombre_place_car'   => $p->nombre_place_car,
                'photo_proprietaire' => $p->photo_proprietaire_url,
                'photo_car'          => $p->photo_complete_car_url,
            ]);

        return response()->json(['success' => true, 'particuliers' => $particuliers]);
    }

    /**
     * GET /api/user/convois/compagnies/{id}/gares
     * Gares d'une compagnie
     */
    public function garesByCompagnie(int $compagnieId): JsonResponse
    {
        $gares = Gare::where('compagnie_id', $compagnieId)
            ->orderBy('nom_gare')
            ->get(['id', 'nom_gare', 'ville', 'adresse', 'contact']);

        return response()->json(['success' => true, 'gares' => $gares]);
    }

    /**
     * GET /api/user/convois/compagnies/{id}/itineraires
     * Itinéraires d'une compagnie
     */
    public function itinerairesByCompagnie(int $compagnieId): JsonResponse
    {
        $itineraires = Itineraire::where('compagnie_id', $compagnieId)
            ->orderBy('point_depart')
            ->get(['id', 'point_depart', 'point_arrive', 'durer_parcours']);

        return response()->json(['success' => true, 'itineraires' => $itineraires]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  CONVOIS (authentifiés)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * GET /api/user/convois
     * Liste des convois de l'utilisateur connecté
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $query = Convoi::with(['compagnie', 'particulier', 'itineraire', 'gare'])
            ->withCount('passagers')
            ->where('user_id', $user->id)
            ->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $convois = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $convois->map(fn($c) => [
                'id'                     => $c->id,
                'reference'              => $c->reference,
                'statut'                 => $c->statut,
                'montant'                => $c->montant,
                'montant_propose_client' => $c->montant_propose_client,
                'dernier_offreur'        => $c->dernier_offreur,
                'nombre_personnes'       => $c->nombre_personnes,
                'passagers_count'        => $c->passagers_count,
                'lieu_depart'            => $c->lieu_depart,
                'lieu_retour'            => $c->lieu_retour,
                'date_depart'            => $c->date_depart,
                'heure_depart'           => $c->heure_depart ? substr($c->heure_depart, 0, 5) : null,
                'created_at'             => $c->created_at?->toIso8601String(),
                'type_transporteur'      => $c->particulier_id ? 'particulier' : ($c->compagnie_id ? 'compagnie' : ($c->particulier ? 'particulier' : 'compagnie')),
                'compagnie'              => $c->compagnie ? ['id' => $c->compagnie->id, 'name' => $c->compagnie->name] : null,
                'particulier'            => $c->particulier ? ['id' => $c->particulier->id, 'full_name' => $c->particulier->full_name, 'contact' => $c->particulier->contact] : null,
                'gare'                   => $c->gare ? ['id' => $c->gare->id, 'nom_gare' => $c->gare->nom_gare] : null,
                'itineraire'             => $c->itineraire ? [
                    'point_depart' => $c->itineraire->point_depart,
                    'point_arrive' => $c->itineraire->point_arrive,
                ] : null,
            ]),
            'pagination' => [
                'current_page' => $convois->currentPage(),
                'last_page'    => $convois->lastPage(),
                'per_page'     => $convois->perPage(),
                'total'        => $convois->total(),
            ],
        ]);
    }

    /**
     * POST /api/user/convois
     * Créer une nouvelle demande de convoi (Compagnie ou Particulier)
     */
    public function store(Request $request): JsonResponse
    {
        $typeTransporteur = $request->input('type_transporteur', $request->has('compagnie_id') ? 'compagnie' : 'particulier');

        $validated = $request->validate([
            'type_transporteur' => 'nullable|in:compagnie,particulier',
            'compagnie_id'     => 'required_if:type_transporteur,compagnie|nullable|exists:compagnies,id',
            'gare_id'          => 'required_if:type_transporteur,compagnie|nullable|exists:gares,id',
            'particulier_id'    => 'nullable|exists:particuliers,id',
            'itineraire_id'    => 'nullable|exists:itineraires,id',
            'lieu_depart'      => 'required_without:itineraire_id|nullable|string|max:255',
            'lieu_retour'      => 'required_without:itineraire_id|nullable|string|max:255',
            'nombre_personnes' => 'required|integer|min:10',
            'date_depart'      => 'required|date|after_or_equal:today',
            'heure_depart'     => 'required|date_format:H:i',
            'date_retour'      => 'nullable|date|after_or_equal:date_depart',
            'heure_retour'     => 'nullable|date_format:H:i|required_with:date_retour',
        ], [
            'compagnie_id.required_if'     => 'Veuillez choisir la compagnie.',
            'gare_id.required_if'          => 'Veuillez choisir votre gare la plus proche.',
            'lieu_depart.required_without' => 'Le lieu de départ est obligatoire si aucun itinéraire n\'est sélectionné.',
            'lieu_retour.required_without' => 'Le lieu d\'arrivée est obligatoire si aucun itinéraire n\'est sélectionné.',
            'nombre_personnes.min'         => 'Le minimum est de 10 personnes pour un convoi.',
            'date_depart.after_or_equal'   => 'La date de départ ne peut pas être dans le passé.',
            'heure_retour.required_with'   => 'L\'heure de retour est obligatoire si vous indiquez une date de retour.',
        ]);

        $isParticulier = ($typeTransporteur === 'particulier');

        // Résoudre les lieux via itinéraire si fourni
        if (!$isParticulier && !empty($validated['itineraire_id'])) {
            $itineraire = Itineraire::findOrFail($validated['itineraire_id']);
            $lieuDepart = $itineraire->point_depart;
            $lieuRetour = $itineraire->point_arrive;
        } else {
            $lieuDepart = $validated['lieu_depart'];
            $lieuRetour = $validated['lieu_retour'];
        }

        $user   = Auth::user();
        $convoi = Convoi::create([
            'user_id'          => $user->id,
            'compagnie_id'     => !$isParticulier ? $validated['compagnie_id'] : null,
            'gare_id'          => !$isParticulier ? $validated['gare_id'] : null,
            'particulier_id'   => ($isParticulier && !empty($validated['particulier_id'])) ? $validated['particulier_id'] : null,
            'itineraire_id'    => !$isParticulier ? ($validated['itineraire_id'] ?? null) : null,
            'lieu_depart'      => $lieuDepart,
            'lieu_retour'      => $lieuRetour,
            'nombre_personnes' => $validated['nombre_personnes'],
            'date_depart'      => $validated['date_depart'],
            'heure_depart'     => $validated['heure_depart'],
            'date_retour'      => $validated['date_retour'] ?? null,
            'heure_retour'     => $validated['heure_retour'] ?? null,
            'reference'        => 'CONV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
            'statut'           => 'en_attente',
        ]);

        if ($isParticulier) {
            if (!empty($validated['particulier_id'])) {
                $particulier = \App\Models\Particulier::find($validated['particulier_id']);
                if ($particulier) {
                    try {
                        $particulier->notify(new \App\Notifications\NewConvoiRequestForParticulierNotification($convoi));
                    } catch (\Exception $e) { Log::error('Mail new convoi particulier: ' . $e->getMessage()); }

                    try {
                        if ($particulier->fcm_token) {
                            app(\App\Services\FcmService::class)->sendNotification(
                                $particulier->fcm_token,
                                'Nouvelle demande de convoi disponible 📋',
                                "Réf. {$convoi->reference} · {$lieuDepart} → {$lieuRetour} · {$convoi->nombre_personnes} personnes.",
                                ['type' => 'nouveau_convoi_particulier', 'convoi_id' => (string) $convoi->id]
                            );
                        }
                    } catch (\Exception $e) { Log::error('FCM new convoi particulier: ' . $e->getMessage()); }

                    try {
                        $smsMsg = "CAR225 : Vous avez recu une nouvelle demande de convoi ref {$convoi->reference} ({$lieuDepart} -> {$lieuRetour}) pour {$convoi->nombre_personnes} personnes. Connectez-vous sur votre espace.";
                        app(\App\Services\SmsService::class)->sendSms($particulier->contact, $smsMsg);
                    } catch (\Exception $e) { Log::error('SMS new convoi particulier: ' . $e->getMessage()); }
                }
            } else {
                $particuliers = \App\Models\Particulier::where('statut', 'valide')->get();
                foreach ($particuliers as $p) {
                    try {
                        $p->notify(new \App\Notifications\NewConvoiRequestForParticulierNotification($convoi));
                    } catch (\Exception $e) { Log::error('Mail new convoi particulier (all): ' . $e->getMessage()); }

                    try {
                        if ($p->fcm_token) {
                            app(\App\Services\FcmService::class)->sendNotification(
                                $p->fcm_token,
                                'Nouvelle demande de convoi disponible 📋',
                                "Réf. {$convoi->reference} · {$lieuDepart} → {$lieuRetour} · {$convoi->nombre_personnes} personnes.",
                                ['type' => 'nouveau_convoi_particulier', 'convoi_id' => (string) $convoi->id]
                            );
                        }
                    } catch (\Exception $e) { Log::error('FCM new convoi particulier (all): ' . $e->getMessage()); }
                }
            }
        } else {
            try {
                if ($user->fcm_token) {
                    $dateDepart = Carbon::parse($convoi->date_depart)->format('d/m/Y');
                    app(\App\Services\FcmService::class)->sendNotification(
                        $user->fcm_token,
                        'Demande de convoi envoyée 📋',
                        "Réf. {$convoi->reference} · {$lieuDepart} → {$lieuRetour} · Départ le {$dateDepart}. La gare examine votre demande.",
                        ['type' => 'convoi_en_attente', 'convoi_id' => (string) $convoi->id]
                    );
                }
            } catch (\Exception $e) {
                Log::error('FCM API store convoi: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Votre demande de convoi a été transmise avec succès.',
            'convoi'  => $this->formatConvoi($convoi),
        ], 201);
    }

    /**
     * GET /api/user/convois/{id}
     * Détail d'un convoi
     */
    public function show(int $id): JsonResponse
    {
        $convoi = Convoi::where('user_id', Auth::id())->findOrFail($id);

        return response()->json([
            'success' => true,
            'convoi'  => $this->formatConvoi($convoi),
        ]);
    }

    /**
     * POST /api/user/convois/{id}/accepter-montant
     * Accepter le montant proposé par la gare (valide → confirme)
     */
    public function accepterMontant(Request $request, int $id): JsonResponse
    {
        $convoi = Convoi::where('user_id', Auth::id())->findOrFail($id);

        if ($convoi->statut !== 'valide') {
            return response()->json(['success' => false, 'message' => 'Ce convoi ne peut pas être confirmé (statut actuel : ' . $convoi->statut . ').'], 422);
        }

        $request->validate([
            'reglement_accepte' => 'required|accepted',
        ], [
            'reglement_accepte.required' => 'Vous devez accepter le règlement des convois.',
            'reglement_accepte.accepted' => 'Vous devez cocher la case pour accepter le règlement.',
        ]);

        $convoi->update(['statut' => 'confirme']);

        // FCM
        try {
            $user = Auth::user();
            if ($user->fcm_token) {
                $montantF = number_format($convoi->montant, 0, ',', ' ');
                app(\App\Services\FcmService::class)->sendNotification(
                    $user->fcm_token,
                    'Convoi confirmé ✅',
                    "Réf. {$convoi->reference} · {$montantF} FCFA acceptés. Rendez-vous à la gare pour régler.",
                    ['type' => 'convoi_confirme', 'convoi_id' => (string) $convoi->id]
                );
            }
        } catch (\Exception $e) {
            Log::error('FCM API accepterMontant: ' . $e->getMessage());
        }

        // SMS gare (même format que le web)
        try {
            $convoi->loadMissing(['gare', 'itineraire']);
            $gare = $convoi->gare;
            if ($gare) {
                $depart   = $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? 'N/A');
                $arrivee  = $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? 'N/A');
                $montantF = number_format($convoi->montant, 0, ',', ' ');
                app(\App\Services\SmsService::class)->sendSms(
                    $gare->contact ?? '',
                    "CAR225 : Le client {$convoi->demandeur_nom} a ACCEPTE le montant de {$montantF} FCFA pour le convoi ref {$convoi->reference} ({$depart} → {$arrivee}). Solde en attente."
                );
            }
        } catch (\Exception $e) {
            Log::error('SMS API accepterMontant: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Convoi confirmé. Présentez-vous à la gare pour effectuer le paiement.',
            'convoi'  => $this->formatConvoi($convoi->fresh()),
        ]);
    }

    /**
     * POST /api/user/convois/{id}/refuser-montant
     * Refuser le montant proposé (valide → annule)
     */
    public function refuserMontant(int $id): JsonResponse
    {
        $convoi = Convoi::where('user_id', Auth::id())->findOrFail($id);

        if ($convoi->statut !== 'valide') {
            return response()->json(['success' => false, 'message' => 'Impossible de refuser (statut : ' . $convoi->statut . ').'], 422);
        }

        $convoi->update([
            'statut'      => 'annule',
            'motif_refus' => 'Montant refusé par le client.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Montant refusé. Le convoi a été annulé. Vous pouvez faire une nouvelle demande.',
            'convoi'  => $this->formatConvoi($convoi->fresh()),
        ]);
    }

    /**
     * POST /api/user/convois/{id}/proposer-montant
     * Le client propose son propre prix au chauffeur particulier
     */
    public function proposerMontant(Request $request, int $id): JsonResponse
    {
        $convoi = Convoi::where('user_id', Auth::id())->findOrFail($id);

        if ($convoi->statut !== 'valide') {
            return response()->json(['success' => false, 'message' => 'Ce convoi ne peut pas faire l\'objet d\'une négociation (statut actuel : ' . $convoi->statut . ').'], 422);
        }

        if (!$convoi->particulier_id) {
            return response()->json(['success' => false, 'message' => 'Cette fonctionnalité est réservée aux convois avec transporteur particulier.'], 422);
        }

        $request->validate([
            'montant_propose' => 'required|numeric|min:100',
        ], [
            'montant_propose.required' => 'Veuillez renseigner le montant proposé.',
            'montant_propose.min'      => 'Le montant doit être de 100 FCFA minimum.',
        ]);

        $convoi->update([
            'montant_propose_client' => $request->montant_propose,
            'dernier_offreur'        => 'client',
            'statut'                 => 'en_attente',
        ]);

        $particulier = $convoi->particulier;
        if ($particulier) {
            $depart   = $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? 'N/A');
            $arrivee  = $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? 'N/A');
            $montantF = number_format($request->montant_propose, 0, ',', ' ');

            // SMS
            try {
                $smsMsg = "CAR225 : Le client {$convoi->demandeur_nom} vous propose {$montantF} FCFA pour le convoi {$convoi->reference} ({$depart} -> {$arrivee}). Connectez-vous pour accepter ou faire une contre-offre.";
                app(\App\Services\SmsService::class)->sendSms($particulier->contact, $smsMsg);
            } catch (\Exception $e) {
                Log::error('SMS negociation client convoi API: ' . $e->getMessage());
            }

            // FCM Push
            try {
                if ($particulier->fcm_token) {
                    app(\App\Services\FcmService::class)->sendNotification(
                        $particulier->fcm_token,
                        'Nouvelle offre de prix reçue 💰',
                        "Le client propose {$montantF} FCFA pour le convoi {$convoi->reference}.",
                        ['type' => 'offre_negociation_client', 'convoi_id' => (string) $convoi->id]
                    );
                }
            } catch (\Exception $e) {
                Log::error('FCM negociation client convoi API: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Votre proposition de montant a bien été transmise au transporteur particulier.',
            'convoi'  => $this->formatConvoi($convoi->fresh()),
        ]);
    }

    /**
     * POST /api/user/convois/{id}/passagers
     * Enregistrer le lieu de rassemblement + la liste des passagers
     * Body : { lieu_rassemblement, lieu_rassemblement_retour?, is_garant, passagers: [{nom,prenoms,contact,contact_urgence}] }
     */
    public function storePassagers(Request $request, int $id): JsonResponse
    {
        $convoi = Convoi::where('user_id', Auth::id())->findOrFail($id);

        if ($convoi->statut !== 'paye') {
            return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas encore renseigner les passagers (paiement requis).'], 403);
        }

        // Vérifier la deadline d'1h avant départ
        if ($convoi->date_depart && $convoi->heure_depart) {
            $departureAt = Carbon::parse($convoi->date_depart . ' ' . $convoi->heure_depart);
            if ($departureAt->diffInMinutes(now(), false) > -60) {
                return response()->json(['success' => false, 'message' => 'Impossible de modifier les passagers moins d\'1 heure avant le départ.'], 422);
            }
        }

        $retourObligatoire = !empty($convoi->date_retour);

        $request->validate([
            'lieu_rassemblement'          => 'required|string|max:255',
            'lieu_rassemblement_retour'   => $retourObligatoire ? 'required|string|max:255' : 'nullable|string|max:255',
            'is_garant'                   => 'nullable|boolean',
            'passagers'                   => 'nullable|array',
            'passagers.*.nom'             => 'nullable|string|max:100',
            'passagers.*.prenoms'         => 'nullable|string|max:150',
            'passagers.*.contact'         => ['nullable', 'digits:10'],
            'passagers.*.contact_urgence' => ['nullable', 'digits:10'],
        ], [
            'lieu_rassemblement.required'        => 'Le lieu de rassemblement (aller) est obligatoire.',
            'lieu_rassemblement_retour.required' => 'Le lieu de rassemblement pour le retour est obligatoire car un retour est prévu.',
        ]);

        $isGarant = (bool) $request->input('is_garant', false);

        // Anti-doublon exact (nom + prénoms + contact identiques)
        if (!$isGarant) {
            $signatures = collect($request->input('passagers', []))
                ->filter(fn($p) => !empty(trim($p['nom'] ?? '')) || !empty(trim($p['contact'] ?? '')))
                ->map(fn($p) => strtolower(trim($p['nom'] ?? '')) . '|' . strtolower(trim($p['prenoms'] ?? '')) . '|' . trim($p['contact'] ?? ''));

            if ($signatures->count() !== $signatures->unique()->count()) {
                return response()->json(['success' => false, 'message' => 'Deux passagers ont exactement les mêmes informations. Veuillez corriger les doublons.'], 422);
            }
        }

        $previouslySubmitted = (bool) $convoi->passagers_soumis;

        $convoi->update([
            'lieu_rassemblement'        => $request->lieu_rassemblement,
            'lieu_rassemblement_retour' => $request->lieu_rassemblement_retour,
            'is_garant'                 => $isGarant,
            'passagers_soumis'          => true,
        ]);

        $convoi->passagers()->delete();
        $passengersData = [];
        foreach (($request->input('passagers') ?? []) as $p) {
            $nom     = trim($p['nom'] ?? '');
            $prenoms = trim($p['prenoms'] ?? '');
            $contact = trim($p['contact'] ?? '');
            if ($nom || $prenoms || $contact) {
                $convoi->passagers()->create([
                    'nom'             => $nom ?: null,
                    'prenoms'         => $prenoms ?: null,
                    'contact'         => $contact ?: null,
                    'contact_urgence' => trim($p['contact_urgence'] ?? '') ?: null,
                ]);
                if ($contact) {
                    $passengersData[] = $contact;
                }
            }
        }

        // ── SMS aux passagers — uniquement à la première soumission (identique au web) ──
        if (!$previouslySubmitted && !empty($passengersData)) {
            try {
                $convoi->loadMissing('itineraire');
                $depart     = $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? 'N/A');
                $arrivee    = $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? 'N/A');
                $dateDepart = $convoi->date_depart ? Carbon::parse($convoi->date_depart)->format('d/m/Y') : 'N/A';
                $hDepart    = $convoi->heure_depart ? substr($convoi->heure_depart, 0, 5) : '';
                $lieu       = $convoi->lieu_rassemblement;

                $smsBody = "CAR225 : Vous etes passager d'un convoi ref {$convoi->reference}.\n"
                         . "Trajet : {$depart} -> {$arrivee}\n"
                         . "Depart : {$dateDepart}" . ($hDepart ? " a {$hDepart}" : '') . "\n"
                         . "Lieu : {$lieu}";

                if ($convoi->date_retour) {
                    $dateRetour = Carbon::parse($convoi->date_retour)->format('d/m/Y');
                    $hRetour    = $convoi->heure_retour ? substr($convoi->heure_retour, 0, 5) : '';
                    $lieuRet    = $convoi->lieu_rassemblement_retour;
                    $smsBody .= "\nRetour : {$dateRetour}" . ($hRetour ? " a {$hRetour}" : '') . "\n"
                              . "Lieu retour : " . ($lieuRet ?? 'A definir');
                }

                $smsBody .= "\nSuivez le convoi : " . route('home.download-app');

                $smsService = app(\App\Services\SmsService::class);
                foreach (array_unique($passengersData) as $phone) {
                    $smsService->sendSms($phone, $smsBody);
                }
            } catch (\Exception $e) {
                Log::error('SMS API storePassagers: ' . $e->getMessage());
            }
        }

        $convoi->load('passagers');

        return response()->json([
            'success'         => true,
            'message'         => 'Passagers et lieu de rassemblement enregistrés.',
            'passagers_count' => $convoi->passagers->count(),
            'convoi'          => $this->formatConvoi($convoi->fresh()),
        ]);
    }

    /**
     * DELETE /api/user/convois/{id}/passagers/{passagerId}
     * Supprimer un passager (libère une place)
     */
    public function deletePassager(int $id, int $passagerId): JsonResponse
    {
        $convoi   = Convoi::where('user_id', Auth::id())->findOrFail($id);
        $passager = ConvoiPassager::where('convoi_id', $convoi->id)->findOrFail($passagerId);

        if (in_array($convoi->statut, ['en_cours', 'termine', 'annule'])) {
            return response()->json(['success' => false, 'message' => 'Impossible de supprimer un passager sur un convoi ' . $convoi->statut . '.'], 422);
        }

        // Vérifier la deadline
        if ($convoi->date_depart && $convoi->heure_depart) {
            $departureAt = Carbon::parse($convoi->date_depart . ' ' . $convoi->heure_depart);
            if ($departureAt->diffInMinutes(now(), false) > -60) {
                return response()->json(['success' => false, 'message' => 'Impossible de modifier les passagers moins d\'1 heure avant le départ.'], 422);
            }
        }

        $passager->delete();

        $remaining = $convoi->passagers()->count();
        if ($remaining < $convoi->nombre_personnes && $convoi->passagers_soumis) {
            $convoi->update(['passagers_soumis' => false]);
        }

        return response()->json([
            'success'         => true,
            'message'         => 'Passager supprimé.',
            'passagers_count' => $remaining,
        ]);
    }
}
