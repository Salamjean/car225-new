<?php

namespace App\Http\Controllers\Api\Particulier;

use App\Http\Controllers\Controller;
use App\Models\Convoi;
use App\Models\Particulier;
use App\Notifications\ConvoiRefusedNotification;
use App\Notifications\ConvoiValidatedNotification;
use App\Services\FcmService;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConvoiApiController extends Controller
{
    /**
     * Helper pour la mise en forme du convoi côté Particulier
     */
    private function formatConvoi(Convoi $convoi): array
    {
        $convoi->loadMissing(['user', 'passagers']);

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
            'motif_refus'               => $convoi->motif_refus,
            'created_at'                => $convoi->created_at?->toIso8601String(),
            'user' => $convoi->user ? [
                'id'       => $convoi->user->id,
                'name'     => $convoi->user->name,
                'prenom'   => $convoi->user->prenom,
                'contact'  => $convoi->user->contact,
                'email'    => $convoi->user->email,
            ] : [
                'name'     => $convoi->client_nom,
                'prenom'   => $convoi->client_prenom,
                'contact'  => $convoi->client_contact,
                'email'    => $convoi->client_email,
            ],
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

    /**
     * Vérifier les droits d'accès du particulier sur le convoi
     */
    private function checkAccess(Convoi $convoi, $particulier): bool
    {
        if (!$particulier) {
            return false;
        }

        if ($convoi->particulier_id === $particulier->id) {
            return true;
        }

        // Convoi public en attente sans particulier ni compagnie attribué
        if ($convoi->particulier_id === null && $convoi->compagnie_id === null && $convoi->statut === 'en_attente') {
            return true;
        }

        return false;
    }

    /**
     * GET /api/particulier/convois
     * Liste des convois attribués au particulier ou demandes publiques en attente
     */
    public function index(Request $request): JsonResponse
    {
        $particulier = Auth::user();

        $query = Convoi::with(['user'])
            ->withCount('passagers')
            ->where(function ($q) use ($particulier) {
                $q->where('particulier_id', $particulier->id)
                  ->orWhere(function ($sq) {
                      $sq->whereNull('particulier_id')
                         ->whereNull('compagnie_id')
                         ->where('statut', 'en_attente');
                  });
            })
            ->latest();

        if ($request->filled('statut')) {
            $statut = $request->statut;
            if ($statut === 'en_attente') {
                $query->where('statut', 'en_attente');
            } else {
                $query->where('particulier_id', $particulier->id)->where('statut', $statut);
            }
        }

        $convois = $query->paginate($request->get('per_page', 15));

        $enAttenteCount = Convoi::where(function ($q) use ($particulier) {
                $q->where('particulier_id', $particulier->id)
                  ->orWhere(function ($sq) {
                      $sq->whereNull('particulier_id')
                         ->whereNull('compagnie_id')
                         ->where('statut', 'en_attente');
                  });
            })
            ->where('statut', 'en_attente')
            ->count();

        $totalPaye = Convoi::where('particulier_id', $particulier->id)
            ->whereIn('statut', ['paye', 'en_cours', 'termine'])
            ->sum('montant');

        return response()->json([
            'success' => true,
            'stats'   => [
                'en_attente_count' => $enAttenteCount,
                'total_paye'       => $totalPaye,
                'solde_convoie'    => $particulier->solde_convoie,
            ],
            'data'    => $convois->map(fn($c) => $this->formatConvoi($c)),
            'pagination' => [
                'current_page' => $convois->currentPage(),
                'last_page'    => $convois->lastPage(),
                'per_page'     => $convois->perPage(),
                'total'        => $convois->total(),
            ],
        ]);
    }

    /**
     * GET /api/particulier/convois/{id}
     * Détails d'un convoi
     */
    public function show(int $id): JsonResponse
    {
        $particulier = Auth::user();
        $convoi = Convoi::findOrFail($id);

        if (!$this->checkAccess($convoi, $particulier)) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé.'], 403);
        }

        return response()->json([
            'success' => true,
            'convoi'  => $this->formatConvoi($convoi),
        ]);
    }

    /**
     * POST /api/particulier/convois/{id}/valider
     * Valider la demande de convoi et fixer le montant
     */
    public function valider(Request $request, int $id): JsonResponse
    {
        $particulier = Auth::user();
        $convoi = Convoi::findOrFail($id);

        if (!$this->checkAccess($convoi, $particulier)) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé.'], 403);
        }

        if ($convoi->statut !== 'en_attente') {
            return response()->json(['success' => false, 'message' => 'Ce convoi ne peut pas être validé (statut : ' . $convoi->statut . ').'], 422);
        }

        $validated = $request->validate([
            'montant' => 'required|numeric|min:100',
        ], [
            'montant.required' => 'Veuillez saisir le montant à facturer.',
            'montant.min'      => 'Le montant doit être au minimum de 100 FCFA.',
        ]);

        $updates = [
            'statut'  => 'valide',
            'montant' => $validated['montant'],
        ];

        if ($convoi->particulier_id === null) {
            $updates['particulier_id'] = $particulier->id;
        }

        $convoi->update($updates);

        // Notifier le client
        if ($convoi->user) {
            $user = $convoi->user;
            try { $user->notify(new ConvoiValidatedNotification($convoi)); } catch (\Exception $e) { Log::error('Notif valider convoi API: ' . $e->getMessage()); }

            $montantFormate = number_format($convoi->montant, 0, ',', ' ');
            $smsMsg = "Bonjour " . ($user->prenom ?? $user->name) . ",\n"
                    . "Votre convoi CAR225 ref {$convoi->reference} a ete VALIDE par le transporteur particulier.\n"
                    . "Montant propose : {$montantFormate} FCFA.\n"
                    . "Connectez-vous sur l'application pour proceder au paiement.";
            try { app(SmsService::class)->sendSms($user->contact, $smsMsg); } catch (\Exception $e) { Log::error('SMS valider convoi API: ' . $e->getMessage()); }

            if ($user->fcm_token) {
                try {
                    app(FcmService::class)->sendNotification(
                        $user->fcm_token,
                        'Convoi validé par le transporteur ✅',
                        "Ref {$convoi->reference} · Montant : {$montantFormate} FCFA. Payez sur l'application.",
                        ['convoi_id' => (string) $convoi->id, 'type' => 'convoi_valide']
                    );
                } catch (\Exception $e) { Log::error('FCM valider convoi API: ' . $e->getMessage()); }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Demande de convoi validée avec succès.',
            'convoi'  => $this->formatConvoi($convoi->fresh()),
        ]);
    }

    /**
     * POST /api/particulier/convois/{id}/refuser
     * Refuser la demande de convoi
     */
    public function refuser(Request $request, int $id): JsonResponse
    {
        $particulier = Auth::user();
        $convoi = Convoi::findOrFail($id);

        if (!$this->checkAccess($convoi, $particulier)) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé.'], 403);
        }

        if ($convoi->statut !== 'en_attente') {
            return response()->json(['success' => false, 'message' => 'Ce convoi ne peut pas être refusé (statut : ' . $convoi->statut . ').'], 422);
        }

        $validated = $request->validate([
            'motif_refus' => 'required|string|max:500',
        ], [
            'motif_refus.required' => 'Veuillez indiquer le motif du refus.',
        ]);

        $updates = [
            'statut'      => 'refuse',
            'motif_refus' => $validated['motif_refus'],
        ];

        if ($convoi->particulier_id === null) {
            $updates['particulier_id'] = $particulier->id;
        }

        $convoi->update($updates);

        if ($convoi->user) {
            $user = $convoi->user;
            try { $user->notify(new ConvoiRefusedNotification($convoi)); } catch (\Exception $e) { Log::error('Notif refuser convoi API: ' . $e->getMessage()); }

            $smsMsg = "Bonjour " . ($user->prenom ?? $user->name) . ",\n"
                    . "Votre demande de convoi CAR225 ref {$convoi->reference} a ete REFUSEE par le transporteur.\n"
                    . "Motif : {$validated['motif_refus']}\n"
                    . "Vous pouvez formuler une autre demande.";
            try { app(SmsService::class)->sendSms($user->contact, $smsMsg); } catch (\Exception $e) { Log::error('SMS refuser convoi API: ' . $e->getMessage()); }
        }

        return response()->json([
            'success' => true,
            'message' => 'Convoi refusé avec succès.',
            'convoi'  => $this->formatConvoi($convoi->fresh()),
        ]);
    }

    /**
     * POST /api/particulier/convois/{id}/accepter-offre-client
     * Le chauffeur accepte le prix proposé par le client
     */
    public function accepterOffreClient(int $id): JsonResponse
    {
        $particulier = Auth::user();
        $convoi = Convoi::where('particulier_id', $particulier->id)->findOrFail($id);

        if ($convoi->statut !== 'en_attente' || $convoi->dernier_offreur !== 'client' || !$convoi->montant_propose_client) {
            return response()->json(['success' => false, 'message' => 'Aucune offre client en attente pour ce convoi.'], 422);
        }

        $convoi->update([
            'montant'                => $convoi->montant_propose_client,
            'montant_propose_client' => null,
            'dernier_offreur'        => null,
            'statut'                 => 'valide',
        ]);

        if ($convoi->user) {
            $user = $convoi->user;
            $montantF = number_format($convoi->montant, 0, ',', ' ');

            try { $user->notify(new ConvoiValidatedNotification($convoi)); } catch (\Exception $e) { Log::error('Notif accepter offre API: ' . $e->getMessage()); }

            $smsMsg = "Bonjour " . ($user->prenom ?? $user->name) . ",\n"
                    . "Bonne nouvelle ! Le transporteur a ACCEPTE votre offre de {$montantF} FCFA pour le convoi {$convoi->reference}.\n"
                    . "Connectez-vous pour confirmer votre convoi.";
            try { app(SmsService::class)->sendSms($user->contact, $smsMsg); } catch (\Exception $e) { Log::error('SMS accepter offre API: ' . $e->getMessage()); }

            if ($user->fcm_token) {
                try {
                    app(FcmService::class)->sendNotification(
                        $user->fcm_token,
                        'Offre acceptée par le transporteur ✅',
                        "Votre offre de {$montantF} FCFA pour le convoi {$convoi->reference} a été acceptée.",
                        ['type' => 'offre_acceptee_particulier', 'convoi_id' => (string) $convoi->id]
                    );
                } catch (\Exception $e) { Log::error('FCM accepter offre API: ' . $e->getMessage()); }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Offre client acceptée.',
            'convoi'  => $this->formatConvoi($convoi->fresh()),
        ]);
    }

    /**
     * POST /api/particulier/convois/{id}/contre-proposer
     * Le chauffeur fait une contre-proposition au client
     */
    public function contreProposer(Request $request, int $id): JsonResponse
    {
        $particulier = Auth::user();
        $convoi = Convoi::where('particulier_id', $particulier->id)->findOrFail($id);

        if (!in_array($convoi->statut, ['en_attente', 'valide'])) {
            return response()->json(['success' => false, 'message' => 'Ce convoi ne peut pas faire l\'objet d\'une négociation dans son état actuel.'], 422);
        }

        $validated = $request->validate([
            'montant' => 'required|numeric|min:100',
        ], [
            'montant.required' => 'Veuillez saisir le montant à proposer.',
            'montant.min'      => 'Le montant doit être au minimum de 100 FCFA.',
        ]);

        $convoi->update([
            'montant'                => $validated['montant'],
            'montant_propose_client' => null,
            'dernier_offreur'        => 'particulier',
            'statut'                 => 'valide',
        ]);

        if ($convoi->user) {
            $user = $convoi->user;
            $montantF = number_format($validated['montant'], 0, ',', ' ');

            try { $user->notify(new ConvoiValidatedNotification($convoi)); } catch (\Exception $e) { Log::error('Notif contre-proposition API: ' . $e->getMessage()); }

            $smsMsg = "Bonjour " . ($user->prenom ?? $user->name) . ",\n"
                    . "Le transporteur vous propose un nouveau tarif de {$montantF} FCFA pour le convoi ref {$convoi->reference}.\n"
                    . "Connectez-vous pour accepter, négocier ou annuler.";
            try { app(SmsService::class)->sendSms($user->contact, $smsMsg); } catch (\Exception $e) { Log::error('SMS contre-proposition API: ' . $e->getMessage()); }

            if ($user->fcm_token) {
                try {
                    app(FcmService::class)->sendNotification(
                        $user->fcm_token,
                        'Nouveau prix proposé par le transporteur 💰',
                        "Le transporteur propose {$montantF} FCFA pour le convoi {$convoi->reference}.",
                        ['type' => 'contre_proposition_particulier', 'convoi_id' => (string) $convoi->id]
                    );
                } catch (\Exception $e) { Log::error('FCM contre-proposition API: ' . $e->getMessage()); }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Contre-proposition envoyée au client.',
            'convoi'  => $this->formatConvoi($convoi->fresh()),
        ]);
    }

    /**
     * POST /api/particulier/convois/{id}/solder
     * Solder le convoi : confirmer la réception du paiement par le particulier
     */
    public function solder(int $id): JsonResponse
    {
        $particulier = Auth::user();
        $convoi = Convoi::where('particulier_id', $particulier->id)->findOrFail($id);

        if ($convoi->statut !== 'confirme') {
            return response()->json(['success' => false, 'message' => 'Ce convoi ne peut pas être soldé (statut : ' . $convoi->statut . ').'], 422);
        }

        $convoi->update(['statut' => 'paye']);
        $particulier->increment('solde_convoie', $convoi->montant);

        if ($convoi->user) {
            $user = $convoi->user;
            $montantF = number_format($convoi->montant, 0, ',', ' ');
            $smsMsg = "Bonjour " . ($user->prenom ?? $user->name) . ",\n"
                    . "Le transporteur {$particulier->full_name} a CONFIRME la reception de votre paiement de {$montantF} FCFA pour le convoi ref {$convoi->reference}.\n"
                    . "Bon voyage avec CAR225 !";
            try { app(SmsService::class)->sendSms($user->contact, $smsMsg); } catch (\Exception $e) { Log::error('SMS solder particulier API: ' . $e->getMessage()); }

            if ($user->fcm_token) {
                try {
                    app(FcmService::class)->sendNotification(
                        $user->fcm_token,
                        'Paiement validé par le transporteur ✅',
                        "Votre paiement de {$montantF} FCFA pour le convoi {$convoi->reference} a été validé.",
                        ['type' => 'paiement_confirme_particulier', 'convoi_id' => (string) $convoi->id]
                    );
                } catch (\Exception $e) { Log::error('FCM solder particulier API: ' . $e->getMessage()); }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Paiement confirmé avec succès. Le convoi est payé.',
            'convoi'  => $this->formatConvoi($convoi->fresh()),
        ]);
    }

    /**
     * POST /api/particulier/convois/{id}/update-statut
     * Changer le statut du convoi (démarrer / terminer / annuler)
     */
    public function updateStatut(Request $request, int $id): JsonResponse
    {
        $particulier = Auth::user();
        $convoi = Convoi::where('particulier_id', $particulier->id)->findOrFail($id);

        $action = $request->input('action');

        if ($action === 'demarrer') {
            if ($convoi->statut !== 'paye') {
                return response()->json(['success' => false, 'message' => 'Le convoi doit être payé avant de démarrer.'], 422);
            }
            $convoi->update(['statut' => 'en_cours']);
            return response()->json([
                'success' => true,
                'message' => 'Le voyage a démarré.',
                'convoi'  => $this->formatConvoi($convoi->fresh()),
            ]);
        }

        if ($action === 'terminer') {
            if ($convoi->statut !== 'en_cours') {
                return response()->json(['success' => false, 'message' => 'Le convoi doit être en cours pour être terminé.'], 422);
            }
            $convoi->update(['statut' => 'termine']);
            return response()->json([
                'success' => true,
                'message' => 'Le convoi est maintenant terminé.',
                'convoi'  => $this->formatConvoi($convoi->fresh()),
            ]);
        }

        if ($action === 'annuler') {
            if (in_array($convoi->statut, ['annule', 'termine'])) {
                return response()->json(['success' => false, 'message' => 'Ce convoi est déjà terminé ou annulé.'], 422);
            }

            $request->validate([
                'motif_annulation' => 'required|string|min:5|max:500',
            ], [
                'motif_annulation.required' => 'Le motif d\'annulation est obligatoire.',
            ]);

            $convoi->update([
                'statut'      => 'annule',
                'motif_refus' => $request->motif_annulation,
            ]);

            // SMS d'annulation aux passagers
            $convoi->load('passagers');
            $smsService = app(SmsService::class);
            $dateDepart = $convoi->date_depart ? Carbon::parse($convoi->date_depart)->format('d/m/Y') : 'N/A';
            $trajet     = "{$convoi->lieu_depart} -> {$convoi->lieu_retour}";

            foreach ($convoi->passagers as $passager) {
                if (!$passager->contact) continue;
                $prenom = $passager->prenoms ?? $passager->nom ?? 'Passager';
                $msg  = "Bonjour {$prenom},\n"
                      . "Le convoi CAR225 ref {$convoi->reference} ({$trajet}) prevu le {$dateDepart} a ete annule par le transporteur particulier.\n"
                      . "Motif: {$request->motif_annulation}\n"
                      . "Nous nous excusons pour ce désagrément.";
                try { $smsService->sendSms($passager->contact, $msg); } catch (\Exception $e) { Log::error('SMS annuler convoi passager API: ' . $e->getMessage()); }
            }

            if ($convoi->user) {
                try { $convoi->user->notify(new ConvoiRefusedNotification($convoi)); } catch (\Exception $e) { Log::error('Mail annuler convoi demandeur API: ' . $e->getMessage()); }
            }

            return response()->json([
                'success' => true,
                'message' => 'Convoi annulé. L\'utilisateur et les passagers ont été informés.',
                'convoi'  => $this->formatConvoi($convoi->fresh()),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Action non valide.'], 422);
    }

    /**
     * GET /api/particulier/profile
     * Profil du particulier connecté
     */
    public function profile(): JsonResponse
    {
        $particulier = Auth::user();

        return response()->json([
            'success' => true,
            'profile' => [
                'id'                 => $particulier->id,
                'code_id'            => $particulier->code_id,
                'name'               => $particulier->name,
                'prenom'             => $particulier->prenom,
                'full_name'          => $particulier->full_name,
                'email'              => $particulier->email,
                'contact'            => $particulier->contact,
                'immatriculation'    => $particulier->immatriculation,
                'nombre_place_car'   => $particulier->nombre_place_car,
                'photo_proprietaire' => $particulier->photo_proprietaire_url,
                'photo_car'          => $particulier->photo_complete_car_url,
                'solde_convoie'      => $particulier->solde_convoie ?? '0.00',
                'statut'             => $particulier->statut,
            ],
        ]);
    }

    /**
     * POST /api/particulier/fcm-token
     * Mettre à jour le jeton FCM push
     */
    public function updateFcmToken(Request $request): JsonResponse
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $particulier = Auth::user();
        $particulier->update(['fcm_token' => $request->fcm_token]);

        return response()->json(['success' => true, 'message' => 'Token FCM mis à jour.']);
    }
}
