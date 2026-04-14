<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Convoi;
use App\Models\Gare;
use App\Notifications\ConvoiRefusedNotification;
use App\Notifications\ConvoiValidatedNotification;
use App\Notifications\GareConvoiAssignedNotification;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConvoiController extends Controller
{
    public function index(Request $request)
    {
        $compagnie = Auth::guard('compagnie')->user();
        $statut = $request->query('statut', 'all');

        $query = Convoi::with(['user', 'itineraire', 'gare'])
            ->withCount('passagers')
            ->where('compagnie_id', $compagnie->id)
            ->latest();

        if (in_array($statut, ['en_attente', 'valide', 'refuse', 'paye', 'en_cours', 'annule', 'termine'])) {
            $query->where('statut', $statut);
        }

        $convois = $query->paginate(12)->withQueryString();

        $enAttenteCount = Convoi::where('compagnie_id', $compagnie->id)
            ->where('statut', 'en_attente')
            ->count();

        $totalPaye = Convoi::where('compagnie_id', $compagnie->id)
            ->whereIn('statut', ['paye', 'en_cours', 'termine'])
            ->sum('montant');

        $soldeConvoie = $compagnie->solde_convoie;

        return view('compagnie.convois.index', compact('convois', 'statut', 'enAttenteCount', 'soldeConvoie', 'totalPaye'));
    }

    public function show(Convoi $convoi)
    {
        $compagnie = Auth::guard('compagnie')->user();

        if ($convoi->compagnie_id !== $compagnie->id) {
            abort(403);
        }

        $convoi->load(['user', 'itineraire', 'passagers', 'chauffeur', 'vehicule', 'gare', 'latestLocation']);

        $gares = Gare::where('compagnie_id', $compagnie->id)
            ->orderBy('nom_gare')
            ->get(['id', 'nom_gare']);

        return view('compagnie.convois.show', compact('convoi', 'gares'));
    }

    /** Valider la demande de convoi et fixer le montant */
    public function valider(Request $request, Convoi $convoi)
    {
        $compagnie = Auth::guard('compagnie')->user();

        if ($convoi->compagnie_id !== $compagnie->id) {
            abort(403);
        }

        if ($convoi->statut !== 'en_attente') {
            return back()->with('error', 'Ce convoi ne peut pas être validé dans son état actuel.');
        }

        $validated = $request->validate([
            'montant' => 'required|numeric|min:100',
        ], [
            'montant.required' => 'Veuillez saisir le montant à facturer.',
            'montant.min'      => 'Le montant doit être au minimum de 100 FCFA.',
        ]);

        $convoi->update([
            'statut'  => 'valide',
            'montant' => $validated['montant'],
        ]);

        // Notifier l'utilisateur (email + database + SMS + FCM)
        if ($convoi->user) {
            $user = $convoi->user;

            // Email + in-app (database)
            try { $user->notify(new ConvoiValidatedNotification($convoi)); } catch (\Exception $e) { Log::error('Notif valider convoi: ' . $e->getMessage()); }

            // SMS
            $montantFormate = number_format($convoi->montant, 0, ',', ' ');
            $smsMsg = "Bonjour " . ($user->prenom ?? $user->name) . ",\n"
                    . "Votre convoi CAR225 ref {$convoi->reference} a ete VALIDE.\n"
                    . "Montant a payer : {$montantFormate} FCFA.\n"
                    . "Connectez-vous sur l'application pour proceder au paiement.";
            try { app(\App\Services\SmsService::class)->sendSms($user->contact, $smsMsg); } catch (\Exception $e) { Log::error('SMS valider convoi: ' . $e->getMessage()); }

            // FCM push
            if ($user->fcm_token) {
                try {
                    app(\App\Services\FcmService::class)->sendNotification(
                        $user->fcm_token,
                        'Convoi validé ✅',
                        "Ref {$convoi->reference} — Montant : {$montantFormate} FCFA. Procédez au paiement.",
                        ['convoi_id' => (string) $convoi->id, 'type' => 'convoi_valide']
                    );
                } catch (\Exception $e) { Log::error('FCM valider convoi: ' . $e->getMessage()); }
            }
        }

        return back()->with('success', 'Convoi validé. L\'utilisateur a été notifié par email, SMS et notification push.');
    }

    /** Refuser la demande de convoi */
    public function refuser(Request $request, Convoi $convoi)
    {
        $compagnie = Auth::guard('compagnie')->user();

        if ($convoi->compagnie_id !== $compagnie->id) {
            abort(403);
        }

        if ($convoi->statut !== 'en_attente') {
            return back()->with('error', 'Ce convoi ne peut pas être refusé dans son état actuel.');
        }

        $validated = $request->validate([
            'motif_refus' => 'required|string|max:500',
        ], [
            'motif_refus.required' => 'Veuillez indiquer le motif du refus.',
        ]);

        $convoi->update([
            'statut'      => 'refuse',
            'motif_refus' => $validated['motif_refus'],
        ]);

        // Notifier l'utilisateur (email + database + SMS + FCM)
        if ($convoi->user) {
            $user = $convoi->user;

            // Email + in-app (database)
            try { $user->notify(new ConvoiRefusedNotification($convoi)); } catch (\Exception $e) { Log::error('Notif refuser convoi: ' . $e->getMessage()); }

            // SMS
            $smsMsg = "Bonjour " . ($user->prenom ?? $user->name) . ",\n"
                    . "Votre demande de convoi CAR225 ref {$convoi->reference} a ete REFUSEE.\n"
                    . "Motif : {$validated['motif_refus']}\n"
                    . "Vous pouvez faire une nouvelle demande sur l'application.";
            try { app(\App\Services\SmsService::class)->sendSms($user->contact, $smsMsg); } catch (\Exception $e) { Log::error('SMS refuser convoi: ' . $e->getMessage()); }

            // FCM push
            if ($user->fcm_token) {
                try {
                    app(\App\Services\FcmService::class)->sendNotification(
                        $user->fcm_token,
                        'Convoi refusé ❌',
                        "Ref {$convoi->reference} — Motif : {$validated['motif_refus']}",
                        ['convoi_id' => (string) $convoi->id, 'type' => 'convoi_refuse']
                    );
                } catch (\Exception $e) { Log::error('FCM refuser convoi: ' . $e->getMessage()); }
            }
        }

        return back()->with('success', 'Convoi refusé. L\'utilisateur a été notifié par email, SMS et notification push.');
    }

    /** Assigner une gare au convoi (après paiement de l'utilisateur) */
    public function assignerGare(Request $request, Convoi $convoi)
    {
        $compagnie = Auth::guard('compagnie')->user();

        if ($convoi->compagnie_id !== $compagnie->id) {
            abort(403);
        }

        if ($convoi->statut !== 'paye') {
            return back()->with('error', 'Le convoi doit être payé avant d\'assigner une gare.');
        }

        // Si is_garant = true, l'utilisateur n'est pas tenu de renseigner tous les passagers
        if (!$convoi->is_garant) {
            $passagersCount = $convoi->passagers()->count();
            if ($passagersCount < $convoi->nombre_personnes) {
                $manquants = $convoi->nombre_personnes - $passagersCount;
                return back()->with('error',
                    "Impossible d'assigner une gare : l'utilisateur n'a pas encore renseigné tous ses passagers. " .
                    "Il manque {$manquants} passager(s) sur {$convoi->nombre_personnes} attendus."
                );
            }
        }

        $validated = $request->validate([
            'gare_id' => 'required|exists:gares,id',
        ], [
            'gare_id.required' => 'Veuillez sélectionner une gare.',
        ]);

        // Vérifier que la gare appartient à cette compagnie
        $gare = Gare::where('id', $validated['gare_id'])
            ->where('compagnie_id', $compagnie->id)
            ->firstOrFail();

        $convoi->update([
            'gare_id' => $gare->id,
        ]);

        // Envoyer un SMS à chaque passager enregistré
        $convoi->load(['passagers', 'itineraire']);
        $smsService = app(SmsService::class);

        $dateDepart  = $convoi->date_depart  ? \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y')  : 'N/A';
        $dateRetour  = $convoi->date_retour  ? \Carbon\Carbon::parse($convoi->date_retour)->format('d/m/Y')  : null;
        $heureDepart = $convoi->heure_depart ? substr($convoi->heure_depart, 0, 5) : 'N/A';
        $heureRetour = $convoi->heure_retour ? substr($convoi->heure_retour, 0, 5) : null;
        $trajet      = ($convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? '?'))
                     . ' -> '
                     . ($convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? '?'));
        $lieu        = $convoi->lieu_rassemblement ?? 'A definir';
        $ref         = $convoi->reference;

        foreach ($convoi->passagers as $passager) {
            if (!$passager->contact) continue;

            $prenom = $passager->prenoms ?? $passager->nom ?? 'Passager';
            $msg  = "Bonjour {$prenom},\n";
            $msg .= "Vous faites partie du convoi CAR225.\n";
            $msg .= "Ref: {$ref}\n";
            $msg .= "Trajet: {$trajet}\n";
            $msg .= "Depart: {$dateDepart} a {$heureDepart}\n";
            if ($dateRetour) {
                $msg .= "Retour: {$dateRetour}" . ($heureRetour ? " a {$heureRetour}" : '') . "\n";
            }
            $msg .= "Lieu de rassemblement: {$lieu}\n";
            $msg .= "Bon voyage avec CAR225 !";

            try {
                $smsService->sendSms($passager->contact, $msg);
            } catch (\Exception $e) {
                Log::error('SMS convoi passager erreur: ' . $e->getMessage());
            }
        }

        // Notifier la gare (in-app)
        try { $gare->notify(new GareConvoiAssignedNotification($convoi)); } catch (\Exception $e) { Log::error('Notif gare assignée: ' . $e->getMessage()); }

        return back()->with('success', 'Gare assignée avec succès. Les passagers ont été notifiés par SMS.');
    }

    /** Annuler définitivement un convoi (seule la compagnie peut le faire) */
    public function annuler(Request $request, Convoi $convoi)
    {
        $compagnie = Auth::guard('compagnie')->user();

        if ($convoi->compagnie_id !== $compagnie->id) {
            abort(403);
        }

        if (in_array($convoi->statut, ['annule', 'termine'])) {
            return back()->with('error', 'Ce convoi est déjà terminé ou annulé.');
        }

        $request->validate([
            'motif_annulation' => 'required|string|min:5|max:500',
        ], [
            'motif_annulation.required' => 'Veuillez indiquer le motif d\'annulation.',
        ]);

        // Libérer chauffeur et véhicule si assignés
        if ($convoi->personnel_id) {
            \App\Models\Personnel::where('id', $convoi->personnel_id)->update(['statut' => 'disponible']);
        }
        if ($convoi->vehicule_id) {
            \App\Models\Vehicule::where('id', $convoi->vehicule_id)->update(['statut' => 'disponible']);
        }

        // Supprimer tracking GPS
        \App\Models\DriverLocation::where('convoi_id', $convoi->id)->delete();

        $convoi->update([
            'statut'      => 'annule',
            'motif_refus' => $request->motif_annulation,
            'personnel_id' => null,
            'vehicule_id'  => null,
        ]);

        // Notifier l'utilisateur
        if ($convoi->user) {
            try {
                $convoi->user->notify(new ConvoiRefusedNotification($convoi));
            } catch (\Exception $e) {
                Log::error('Erreur notif annulation compagnie: ' . $e->getMessage());
            }
        }

        // SMS d'annulation à chaque passager enregistré
        $convoi->load(['passagers', 'itineraire']);
        $smsService = app(SmsService::class);
        $dateDepart = $convoi->date_depart ? \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y') : 'N/A';
        $trajet     = ($convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? '?'))
                    . ' -> '
                    . ($convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? '?'));

        foreach ($convoi->passagers as $passager) {
            if (!$passager->contact) continue;
            $prenom = $passager->prenoms ?? $passager->nom ?? 'Passager';
            $msg  = "Bonjour {$prenom},\n";
            $msg .= "Le convoi CAR225 ref {$convoi->reference} ({$trajet}) prevu le {$dateDepart} a ete annule.\n";
            $msg .= "Motif: {$request->motif_annulation}\n";
            $msg .= "Pour toute question, contactez la compagnie. Nous vous prions de nous excuser.";
            try {
                $smsService->sendSms($passager->contact, $msg);
            } catch (\Exception $e) {
                Log::error('SMS annulation convoi passager: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Convoi annulé définitivement. L\'utilisateur et les passagers ont été notifiés.');
    }

    public function location(Convoi $convoi): JsonResponse
    {
        $compagnie = Auth::guard('compagnie')->user();

        if ($convoi->compagnie_id !== $compagnie->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        $convoi->load(['latestLocation', 'chauffeur', 'vehicule', 'itineraire', 'gare']);
        $location = $convoi->latestLocation;

        return response()->json([
            'success'    => true,
            'convoi_id'  => $convoi->id,
            'statut'     => $convoi->statut,
            'latitude'   => $location ? (float) $location->latitude : null,
            'longitude'  => $location ? (float) $location->longitude : null,
            'speed'      => $location ? $location->speed : null,
            'heading'    => $location ? $location->heading : null,
            'last_update'=> $location ? $location->updated_at->diffForHumans() : 'Jamais',
            'chauffeur'  => $convoi->chauffeur ? trim(($convoi->chauffeur->prenom ?? '') . ' ' . ($convoi->chauffeur->name ?? '')) : 'Inconnu',
            'vehicule'   => $convoi->vehicule->immatriculation ?? 'N/A',
            'trajet'     => $convoi->itineraire ? ($convoi->itineraire->point_depart . ' -> ' . $convoi->itineraire->point_arrive) : '-',
            'gare'       => $convoi->gare->nom_gare ?? '-',
        ]);
    }
}
