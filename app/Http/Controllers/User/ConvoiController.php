<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Compagnie;
use App\Models\Convoi;
use App\Models\Gare;
use App\Models\Itineraire;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ConvoiController extends Controller
{
    public function index()
    {
        $convois = Convoi::with(['compagnie', 'itineraire'])
            ->withCount('passagers')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.convoi.index', compact('convois'));
    }

    public function create()
    {
        $compagnies = Compagnie::where('statut', 'actif')
            ->orderBy('name')
            ->get(['id', 'name', 'sigle', 'path_logo']);

        $particuliers = \App\Models\Particulier::where('statut', 'valide')
            ->orderBy('name')
            ->get();

        return view('user.convoi.create', compact('compagnies', 'particuliers'));
    }

    /** AJAX : gares d'une compagnie (pour le formulaire de demande de convoi) */
    public function garesByCompagnie($compagnieId)
    {
        $gares = Gare::where('compagnie_id', $compagnieId)
            ->orderBy('nom_gare')
            ->get(['id', 'nom_gare', 'ville', 'adresse']);

        return response()->json(['gares' => $gares]);
    }

    /** AJAX : gares d'une compagnie filtrées par itinéraire */
    public function garesByItineraire($itineraireId)
    {
        $itineraire = Itineraire::findOrFail($itineraireId);
        $gares = Gare::where('compagnie_id', $itineraire->compagnie_id)
            ->orderBy('nom_gare')
            ->get(['id', 'nom_gare', 'ville', 'adresse']);
        return response()->json(['gares' => $gares]);
    }

    /** AJAX : itinéraires d'une compagnie avec point_depart et point_arrive */
    public function itinerairesByCompagnie($compagnieId)
    {
        $itineraires = Itineraire::where('compagnie_id', $compagnieId)
            ->orderBy('point_depart')
            ->get(['id', 'point_depart', 'point_arrive', 'durer_parcours']);

        return response()->json(['itineraires' => $itineraires]);
    }

    /** Création du convoi en une seule étape — envoi direct à la gare choisie ou au particulier */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_transporteur' => 'required|in:compagnie,particulier',
            'compagnie_id'      => 'required_if:type_transporteur,compagnie|nullable|exists:compagnies,id',
            'gare_id'           => 'required_if:type_transporteur,compagnie|nullable|exists:gares,id',
            'particulier_id'    => 'nullable|exists:particuliers,id',
            'itineraire_id'     => 'nullable|exists:itineraires,id',
            'lieu_depart'       => 'required_without:itineraire_id|string|max:255',
            'lieu_retour'       => 'required_without:itineraire_id|string|max:255',
            'nombre_personnes'  => 'required|integer|min:10',
            'date_depart'       => 'required|date|after_or_equal:today',
            'heure_depart'      => 'required|date_format:H:i',
            'date_retour'       => 'nullable|date|after_or_equal:date_depart',
            'heure_retour'      => 'nullable|date_format:H:i|required_with:date_retour',
        ], [
            'compagnie_id.required_if'       => 'Veuillez choisir la compagnie.',
            'gare_id.required_if'            => 'Veuillez choisir votre gare la plus proche.',
            'lieu_depart.required_without'   => 'Le lieu de départ est obligatoire si aucun itinéraire n\'est sélectionné.',
            'lieu_retour.required_without'   => 'Le lieu d\'arrivée est obligatoire si aucun itinéraire n\'est sélectionné.',
            'nombre_personnes.min'           => 'Le minimum est de 10 personnes pour un convoi.',
            'date_depart.after_or_equal'     => 'La date de départ ne peut pas être dans le passé.',
            'date_retour.after_or_equal'     => 'La date de retour doit être égale ou après la date de départ.',
            'heure_retour.required_with'     => 'L\'heure de retour est obligatoire si vous indiquez une date de retour.',
        ]);

        $isParticulier = ($validated['type_transporteur'] === 'particulier');

        // Résoudre lieu_depart / lieu_retour
        if (!$isParticulier && !empty($validated['itineraire_id'])) {
            $itineraire = Itineraire::findOrFail($validated['itineraire_id']);
            $lieuDepart = $itineraire->point_depart;
            $lieuRetour = $itineraire->point_arrive;
        } else {
            $itineraire = null;
            $lieuDepart = $validated['lieu_depart'];
            $lieuRetour = $validated['lieu_retour'];
        }

        $convoi = Convoi::create([
            'user_id'          => Auth::id(),
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
                // Notifier un particulier spécifique par email + push + SMS
                $particulier = \App\Models\Particulier::findOrFail($validated['particulier_id']);
                
                // Email Notification
                try {
                    $particulier->notify(new \App\Notifications\NewConvoiRequestForParticulierNotification($convoi));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Mail new convoi particulier: ' . $e->getMessage());
                }

                // Push Notification (FCM)
                try {
                    if ($particulier->fcm_token) {
                        app(\App\Services\FcmService::class)->sendNotification(
                            $particulier->fcm_token,
                            'Nouvelle demande de convoi disponible 📋',
                            "Réf. {$convoi->reference} · {$lieuDepart} → {$lieuRetour} · {$convoi->nombre_personnes} personnes.",
                            ['type' => 'nouveau_convoi_particulier', 'convoi_id' => (string) $convoi->id]
                        );
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('FCM new convoi particulier: ' . $e->getMessage());
                }

                // SMS Notification (only when a specific carrier is selected)
                try {
                    $smsMsg = "CAR225 : Vous avez recu une nouvelle demande de convoi ref {$convoi->reference} ({$lieuDepart} -> {$lieuRetour}) pour {$convoi->nombre_personnes} personnes. Connectez-vous sur votre espace.";
                    app(\App\Services\SmsService::class)->sendSms($particulier->contact, $smsMsg);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('SMS new convoi particulier: ' . $e->getMessage());
                }
            } else {
                // Notifier tous les particuliers par email + push (pas de SMS !)
                $particuliers = \App\Models\Particulier::where('statut', 'valide')->get();

                foreach ($particuliers as $particulier) {
                    // Email Notification
                    try {
                        $particulier->notify(new \App\Notifications\NewConvoiRequestForParticulierNotification($convoi));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Mail new convoi particulier (all): ' . $e->getMessage());
                    }

                    // Push Notification (FCM)
                    try {
                        if ($particulier->fcm_token) {
                            app(\App\Services\FcmService::class)->sendNotification(
                                $particulier->fcm_token,
                                'Nouvelle demande de convoi disponible 📋',
                                "Réf. {$convoi->reference} · {$lieuDepart} → {$lieuRetour} · {$convoi->nombre_personnes} personnes.",
                                ['type' => 'nouveau_convoi_particulier', 'convoi_id' => (string) $convoi->id]
                            );
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('FCM new convoi particulier (all): ' . $e->getMessage());
                    }
                }
            }
        } else {
            // ── Notification push de confirmation au demandeur ────────────────
            try {
                $user = Auth::user();
                if ($user && $user->fcm_token) {
                    $dateDepart = \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y');
                    app(\App\Services\FcmService::class)->sendNotification(
                        $user->fcm_token,
                        'Demande de convoi envoyée 📋',
                        "Réf. {$convoi->reference} · {$lieuDepart} → {$lieuRetour} · Départ le {$dateDepart}. La gare examine votre demande.",
                        ['type' => 'convoi_en_attente', 'convoi_id' => (string) $convoi->id]
                    );
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('FCM store convoi: ' . $e->getMessage());
            }
        }

        return redirect()->route('user.convoi.show', $convoi)
            ->with('success', 'Votre demande de convoi a été envoyée. Vous recevrez une notification dès que le prestataire l\'aura validée.');
    }

    /** Télécharger / imprimer le reçu PDF du convoi (disponible après paiement) */
    public function downloadRecu(Convoi $convoi)
    {
        abort_if($convoi->user_id !== Auth::id(), 403);
        abort_if(!in_array($convoi->statut, ['paye', 'en_cours', 'termine']), 403);

        $convoi->load(['compagnie', 'gare', 'itineraire', 'chauffeur', 'vehicule', 'user']);

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

    public function show(Convoi $convoi)
    {
        abort_if($convoi->user_id !== Auth::id(), 403);

        if (!$convoi->passenger_form_token && in_array($convoi->statut, ['confirme', 'paye', 'en_cours'])) {
            $convoi->update(['passenger_form_token' => bin2hex(random_bytes(24))]);
        }

        $convoi->load(['compagnie', 'particulier', 'itineraire', 'passagers', 'gare', 'chauffeur', 'vehicule', 'user']);
        $authUser = Auth::user();
        return view('user.convoi.show', compact('convoi', 'authUser'));
    }

    /** Utilisateur accepte le montant fixé par la gare → confirme */
    public function accepterMontant(Request $request, Convoi $convoi)
    {
        abort_if($convoi->user_id !== Auth::id(), 403);
        abort_if($convoi->statut !== 'valide', 403);

        $request->validate([
            'reglement_accepte' => 'required|accepted',
        ], [
            'reglement_accepte.required' => 'Vous devez accepter le règlement des convois avant de confirmer.',
            'reglement_accepte.accepted' => 'Vous devez cocher la case pour accepter le règlement.',
        ]);

        $updates = ['statut' => 'confirme'];
        if (!$convoi->passenger_form_token) {
            $updates['passenger_form_token'] = bin2hex(random_bytes(24));
        }
        $convoi->update($updates);

        // ── Notification push de confirmation à l'utilisateur ─────────────
        try {
            $user = Auth::user();
            if ($user && $user->fcm_token) {
                $montantF = number_format($convoi->montant, 0, ',', ' ');
                app(\App\Services\FcmService::class)->sendNotification(
                    $user->fcm_token,
                    'Convoi confirmé ✅',
                    "Réf. {$convoi->reference} · Montant accepté : {$montantF} FCFA. Rendez-vous à la gare pour régler le paiement.",
                    ['type' => 'convoi_confirme', 'convoi_id' => (string) $convoi->id]
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('FCM accepterMontant convoi: ' . $e->getMessage());
        }

        // Notifier la gare (SMS)
        try {
            $gare = $convoi->gare;
            if ($gare) {
                $depart     = $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? 'N/A');
                $arrivee    = $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? 'N/A');
                $montantF   = number_format($convoi->montant, 0, ',', ' ');
                app(\App\Services\SmsService::class)->sendSms(
                    $gare->contact ?? '',
                    "CAR225 : Le client {$convoi->demandeur_nom} a ACCEPTE le montant de {$montantF} FCFA pour le convoi ref {$convoi->reference} ({$depart} → {$arrivee}). Solde en attente."
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SMS accepter convoi: ' . $e->getMessage());
        }

        $successMsg = $convoi->particulier_id
            ? 'Vous avez confirmé votre convoi. Veuillez contacter le transporteur particulier pour effectuer le paiement.'
            : 'Vous avez confirmé votre convoi. Présentez-vous à la gare pour effectuer le paiement avant votre départ.';

        return redirect()->route('user.convoi.show', $convoi)
            ->with('success', $successMsg);
    }

    /** Utilisateur refuse le montant → annule */
    public function refuserMontant(Request $request, Convoi $convoi)
    {
        abort_if($convoi->user_id !== Auth::id(), 403);
        abort_if($convoi->statut !== 'valide', 403);

        $convoi->update([
            'statut'      => 'annule',
            'motif_refus' => 'Convoi annulé par le client.',
        ]);

        return redirect()->route('user.convoi.show', $convoi)
            ->with('success', 'Le convoi a été annulé avec succès.');
    }

    /** Le client propose son propre prix au chauffeur particulier */
    public function proposerMontant(Request $request, Convoi $convoi)
    {
        abort_if($convoi->user_id !== Auth::id(), 403);
        abort_if($convoi->statut !== 'valide', 403);
        abort_if(!$convoi->particulier_id, 403);

        $request->validate([
            'montant_propose' => 'required|numeric|min:100',
        ], [
            'montant_propose.required' => 'Veuillez renseigner le montant proposé.',
            'montant_propose.min' => 'Le montant doit être de 100 FCFA minimum.',
        ]);

        $convoi->update([
            'montant_propose_client' => $request->montant_propose,
            'dernier_offreur' => 'client',
            'statut' => 'en_attente',
        ]);

        $particulier = $convoi->particulier;
        if ($particulier) {
            $depart = $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? 'N/A');
            $arrivee = $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? 'N/A');
            $montantF = number_format($request->montant_propose, 0, ',', ' ');

            // SMS
            try {
                $smsMsg = "CAR225 : Le client {$convoi->demandeur_nom} vous propose {$montantF} FCFA pour le convoi {$convoi->reference} ({$depart} -> {$arrivee}). Connectez-vous pour accepter ou faire une contre-offre.";
                app(\App\Services\SmsService::class)->sendSms($particulier->contact, $smsMsg);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('SMS negociation client convoi: ' . $e->getMessage());
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
                \Illuminate\Support\Facades\Log::error('FCM negociation client convoi: ' . $e->getMessage());
            }
        }

        return redirect()->route('user.convoi.show', $convoi)
            ->with('success', 'Votre proposition de montant a bien été transmise au transporteur particulier.');
    }

    /** Paiement : l'utilisateur accepte le règlement et paye le montant fixé par la compagnie */
    public function pay(Request $request, Convoi $convoi)
    {
        abort_if($convoi->user_id !== Auth::id(), 403);
        abort_if($convoi->statut !== 'valide', 403);

        $request->validate([
            'reglement_accepte' => 'required|accepted',
        ], [
            'reglement_accepte.required' => 'Vous devez accepter le règlement des convois avant de payer.',
            'reglement_accepte.accepted'  => 'Vous devez cocher la case pour accepter le règlement.',
        ]);

        $convoi->update(['statut' => 'paye']);
        if ($convoi->compagnie_id) {
            $convoi->compagnie()->increment('solde_convoie', $convoi->montant);
        } elseif ($convoi->particulier_id) {
            $convoi->particulier()->increment('solde_convoie', $convoi->montant);
        }

        return redirect()->route('user.convoi.show', $convoi)
            ->with('success', 'Paiement confirmé ! Vous pouvez maintenant renseigner les informations de vos passagers.');
    }

    /** Enregistrement lieu de rassemblement + toggle garant */
    public function storeLieuRassemblement(Request $request, Convoi $convoi)
    {
        abort_if($convoi->user_id !== Auth::id(), 403);
        abort_if(!in_array($convoi->statut, ['confirme', 'paye', 'en_cours']), 403);

        $request->validate([
            'lieu_rassemblement' => 'required|string|max:255',
            'is_garant'          => 'nullable|boolean',
        ]);

        $convoi->update([
            'lieu_rassemblement' => $request->lieu_rassemblement ?: null,
            'is_garant'          => (bool) $request->is_garant,
        ]);

        return back()->with('success', 'Lieu de rassemblement enregistré.');
    }

    /** Enregistrement des passagers après paiement (gère aussi lieu_rassemblement + is_garant) */
    public function storePassengers(Request $request, Convoi $convoi)
    {
        abort_if($convoi->user_id !== Auth::id(), 403);
        abort_if(!in_array($convoi->statut, ['confirme', 'paye', 'en_cours']), 403);

        // Bloquer la modification si le départ est dans moins d'1 heure
        if ($convoi->date_depart && $convoi->heure_depart) {
            $departureAt = \Carbon\Carbon::parse($convoi->date_depart . ' ' . $convoi->heure_depart);
            if ($departureAt->diffInMinutes(now(), false) > -60) {
                return back()->with('error', 'Impossible de modifier les passagers moins d\'1 heure avant le départ.');
            }
        }

        $retourObligatoire = !empty($convoi->date_retour);

        $request->validate([
            'lieu_rassemblement'          => 'required|string|max:255',
            'lieu_rassemblement_retour'   => $retourObligatoire ? 'required|string|max:255' : 'nullable|string|max:255',
            'passagers'                   => 'nullable|array',
            'passagers.*.nom'             => 'nullable|string|max:100',
            'passagers.*.prenoms'         => 'nullable|string|max:150',
            'passagers.*.contact'         => ['nullable', 'digits:10'],
            'passagers.*.contact_urgence' => ['nullable', 'digits:10'],
        ], [
            'lieu_rassemblement.required'         => 'Le lieu de rassemblement (aller) est obligatoire.',
            'lieu_rassemblement_retour.required'  => 'Le lieu de rassemblement pour le retour est obligatoire car un retour est prévu.',
            'passagers.*.contact.digits'          => 'Le contact doit contenir exactement 10 chiffres.',
            'passagers.*.contact_urgence.digits'  => 'Le contact d\'urgence doit contenir exactement 10 chiffres.',
        ]);

        $isGarant = (bool) $request->input('is_garant', false);

        // Vérifier les doublons exacts (même nom + prénoms + contact)
        if (!$isGarant) {
            $signatures = collect($request->input('passagers', []))
                ->filter(fn($p) => !empty(trim($p['nom'] ?? '')) || !empty(trim($p['contact'] ?? '')))
                ->map(fn($p) => strtolower(trim($p['nom'] ?? '')) . '|' . strtolower(trim($p['prenoms'] ?? '')) . '|' . trim($p['contact'] ?? ''));

            if ($signatures->count() !== $signatures->unique()->count()) {
                return back()
                    ->withInput()
                    ->with('error', 'Deux passagers ont exactement les mêmes informations (nom, prénoms et contact identiques). Veuillez corriger les doublons.');
            }
        }

        $previouslySubmitted = (bool) $convoi->passagers_soumis;

        // Sauvegarder lieu + garant + marquer passagers soumis
        $convoi->update([
            'lieu_rassemblement'        => $request->lieu_rassemblement,
            'lieu_rassemblement_retour' => $request->lieu_rassemblement_retour,
            'is_garant'                 => $isGarant,
            'passagers_soumis'          => true,
        ]);

        // Ne sauvegarder que les lignes qui ont au moins un champ renseigné
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

        // ── Envoi SMS aux passagers (UNIQUEMENT lors de la première soumission) ──
        if (!$previouslySubmitted && !empty($passengersData)) {
            try {
                $depart     = $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? 'N/A');
                $arrivee    = $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? 'N/A');
                $dateDepart = $convoi->date_depart ? \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y') : 'N/A';
                $hDepart    = $convoi->heure_depart ? substr($convoi->heure_depart, 0, 5) : '';
                $lieu       = $convoi->lieu_rassemblement;

                $smsBody = "CAR225 : Vous etes passager d'un convoi ref {$convoi->reference}.\n"
                         . "Trajet : {$depart} -> {$arrivee}\n"
                         . "Depart : {$dateDepart}" . ($hDepart ? " à {$hDepart}" : '') . "\n"
                         . "Lieu : {$lieu}";

                if ($convoi->date_retour) {
                    $dateRetour = \Carbon\Carbon::parse($convoi->date_retour)->format('d/m/Y');
                    $hRetour    = $convoi->heure_retour ? substr($convoi->heure_retour, 0, 5) : '';
                    $lieuRet    = $convoi->lieu_rassemblement_retour;
                    $smsBody .= "\nRetour : {$dateRetour}" . ($hRetour ? " à {$hRetour}" : '') . "\n"
                              . "Lieu retour : " . ($lieuRet ?? 'À definir');
                }

                $smsBody .= "\nSuivez le convoi : " . route('home.download-app');

                $smsService = app(\App\Services\SmsService::class);
                foreach (array_unique($passengersData) as $phone) {
                    $smsService->sendSms($phone, $smsBody);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('SMS storePassengers: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Les informations des passagers ont été enregistrées avec succès.');
    }
}
