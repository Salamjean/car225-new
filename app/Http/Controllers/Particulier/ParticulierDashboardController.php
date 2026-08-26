<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use App\Models\Convoi;
use App\Models\Particulier;
use App\Notifications\ConvoiRefusedNotification;
use App\Notifications\ConvoiValidatedNotification;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ParticulierDashboardController extends Controller
{
    /**
     * Tableau de bord principal du particulier
     */
    public function dashboard(Request $request)
    {
        $particulier = Auth::guard('particulier')->user();
        $statut = $request->query('statut', 'all');

        $query = Convoi::with(['user'])
            ->withCount('passagers')
            ->where(function ($q) use ($particulier) {
                $q->where('particulier_id', $particulier->id)
                  ->orWhere(function ($sq) {
                      $sq->whereNull('particulier_id')
                         ->where('statut', 'en_attente');
                  });
            })
            ->latest();

        if (in_array($statut, ['en_attente', 'valide', 'refuse', 'paye', 'en_cours', 'annule', 'termine'])) {
            if ($statut === 'en_attente') {
                $query->where('statut', 'en_attente');
            } else {
                $query->where('particulier_id', $particulier->id)->where('statut', $statut);
            }
        }

        $convois = $query->paginate(10)->withQueryString();

        $enAttenteCount = Convoi::where(function ($q) use ($particulier) {
                $q->where('particulier_id', $particulier->id)
                  ->orWhere(function ($sq) {
                      $sq->whereNull('particulier_id')
                         ->where('statut', 'en_attente');
                  });
            })
            ->where('statut', 'en_attente')
            ->count();

        $totalPaye = Convoi::where('particulier_id', $particulier->id)
            ->whereIn('statut', ['paye', 'en_cours', 'termine'])
            ->sum('montant');

        $soldeConvoie = $particulier->solde_convoie;

        return view('particulier.dashboard', compact('convois', 'statut', 'enAttenteCount', 'soldeConvoie', 'totalPaye', 'particulier'));
    }

    /**
     * Afficher les détails d'un convoi
     */
    public function showConvoi(Convoi $convoi)
    {
        $particulier = Auth::guard('particulier')->user();

        if ($convoi->particulier_id !== $particulier->id) {
            if (!($convoi->particulier_id === null && $convoi->statut === 'en_attente')) {
                abort(403);
            }
        }

        $convoi->load(['user', 'passagers']);

        return view('particulier.show', compact('convoi'));
    }

    /**
     * Valider la demande de convoi et fixer le montant
     */
    public function validerConvoi(Request $request, Convoi $convoi)
    {
        $particulier = Auth::guard('particulier')->user();

        if ($convoi->particulier_id !== $particulier->id) {
            if ($convoi->particulier_id === null && $convoi->statut === 'en_attente') {
                $convoi->update([
                    'particulier_id' => $particulier->id
                ]);
            } else {
                abort(403);
            }
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

        // Notifier l'utilisateur par SMS / FCM / Email
        if ($convoi->user) {
            $user = $convoi->user;

            // Email
            try { $user->notify(new ConvoiValidatedNotification($convoi)); } catch (\Exception $e) { Log::error('Notif valider convoi particulier: ' . $e->getMessage()); }

            // SMS
            $montantFormate = number_format($convoi->montant, 0, ',', ' ');
            $smsMsg = "Bonjour " . ($user->prenom ?? $user->name) . ",\n"
                    . "Votre convoi CAR225 ref {$convoi->reference} a ete VALIDE par le transporteur particulier.\n"
                    . "Montant propose : {$montantFormate} FCFA.\n"
                    . "Connectez-vous sur l'application pour proceder au paiement.";
            try { app(SmsService::class)->sendSms($user->contact, $smsMsg); } catch (\Exception $e) { Log::error('SMS valider convoi particulier: ' . $e->getMessage()); }

            // FCM
            if ($user->fcm_token) {
                try {
                    app(\App\Services\FcmService::class)->sendNotification(
                        $user->fcm_token,
                        'Convoi validé par le transporteur ✅',
                        "Ref {$convoi->reference} · Montant : {$montantFormate} FCFA. Payez sur l'application.",
                        ['convoi_id' => (string) $convoi->id, 'type' => 'convoi_valide']
                    );
                } catch (\Exception $e) { Log::error('FCM valider convoi particulier: ' . $e->getMessage()); }
            }
        }

        return back()->with('success', 'Demande de convoi validée avec succès. L\'utilisateur a été notifié du montant.');
    }

    /**
     * Refuser la demande de convoi
     */
    public function refuserConvoi(Request $request, Convoi $convoi)
    {
        $particulier = Auth::guard('particulier')->user();

        if ($convoi->particulier_id !== $particulier->id) {
            if ($convoi->particulier_id === null && $convoi->statut === 'en_attente') {
                $convoi->update([
                    'particulier_id' => $particulier->id
                ]);
            } else {
                abort(403);
            }
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

        // Notifier l'utilisateur
        if ($convoi->user) {
            $user = $convoi->user;

            // Email
            try { $user->notify(new ConvoiRefusedNotification($convoi)); } catch (\Exception $e) { Log::error('Notif refuser convoi particulier: ' . $e->getMessage()); }

            // SMS
            $smsMsg = "Bonjour " . ($user->prenom ?? $user->name) . ",\n"
                    . "Votre demande de convoi CAR225 ref {$convoi->reference} a ete REFUSEE par le transporteur.\n"
                    . "Motif : {$validated['motif_refus']}\n"
                    . "Vous pouvez formuler une autre demande.";
            try { app(SmsService::class)->sendSms($user->contact, $smsMsg); } catch (\Exception $e) { Log::error('SMS refuser convoi particulier: ' . $e->getMessage()); }
        }

        return back()->with('success', 'Convoi refusé. L\'utilisateur a été averti.');
    }

    /**
     * Annuler le convoi (Changement de statut en cours/terminé/annulé)
     */
    public function annulerConvoi(Request $request, Convoi $convoi)
    {
        $particulier = Auth::guard('particulier')->user();

        if ($convoi->particulier_id !== $particulier->id) {
            abort(403);
        }

        $action = $request->input('action');

        if ($action === 'demarrer') {
            if ($convoi->statut !== 'paye') {
                return back()->with('error', 'Le convoi doit être payé avant de démarrer.');
            }
            $convoi->update(['statut' => 'en_cours']);
            return back()->with('success', 'Le voyage a démarré.');
        }

        if ($action === 'terminer') {
            if ($convoi->statut !== 'en_cours') {
                return back()->with('error', 'Le convoi doit être en cours pour être terminé.');
            }
            $convoi->update(['statut' => 'termine']);
            return back()->with('success', 'Le convoi est maintenant terminé.');
        }

        if ($action === 'annuler') {
            if (in_array($convoi->statut, ['annule', 'termine'])) {
                return back()->with('error', 'Ce convoi est déjà terminé ou annulé.');
            }

            $request->validate([
                'motif_annulation' => 'required|string|min:5|max:500',
            ]);

            $convoi->update([
                'statut' => 'annule',
                'motif_refus' => $request->motif_annulation,
            ]);

            // SMS d'annulation aux passagers
            $convoi->load('passagers');
            $smsService = app(SmsService::class);
            $dateDepart = $convoi->date_depart ? \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y') : 'N/A';
            $trajet = "{$convoi->lieu_depart} -> {$convoi->lieu_retour}";

            foreach ($convoi->passagers as $passager) {
                if (!$passager->contact) continue;
                $prenom = $passager->prenoms ?? $passager->nom ?? 'Passager';
                $msg  = "Bonjour {$prenom},\n";
                $msg .= "Le convoi CAR225 ref {$convoi->reference} ({$trajet}) prevu le {$dateDepart} a ete annule par le transporteur particulier.\n";
                $msg .= "Motif: {$request->motif_annulation}\n";
                $msg .= "Nous nous excusons pour ce désagrément.";
                try { $smsService->sendSms($passager->contact, $msg); } catch (\Exception $e) { Log::error('SMS annuler convoi passager: ' . $e->getMessage()); }
            }

            // Notifier le demandeur
            if ($convoi->user) {
                try {
                    $convoi->user->notify(new ConvoiRefusedNotification($convoi));
                } catch (\Exception $e) {
                    Log::error('Mail annuler convoi demandeur: ' . $e->getMessage());
                }
            }

            return back()->with('success', 'Convoi annulé. L\'utilisateur et les passagers ont été informés.');
        }

        return back();
    }

    /**
     * AJAX check if convoi is claimed by someone else
     */
    public function checkClaimStatus(Convoi $convoi)
    {
        $particulier = Auth::guard('particulier')->user();

        $claimed = ($convoi->particulier_id !== null);
        $claimedByMe = ($convoi->particulier_id === $particulier->id);

        return response()->json([
            'claimed' => $claimed,
            'claimed_by_me' => $claimedByMe,
            'statut' => $convoi->statut
        ]);
    }

    /**
     * Profil du particulier
     */
    public function profile()
    {
        $particulier = Auth::guard('particulier')->user();
        return view('particulier.profile', compact('particulier'));
    }

    /**
     * Déconnexion du particulier
     */
    public function logout()
    {
        Auth::guard('particulier')->logout();
        return redirect()->route('portail.login')->with('success', 'Vous êtes déconnecté.');
    }
}
