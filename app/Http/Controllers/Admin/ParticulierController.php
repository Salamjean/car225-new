<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Particulier;
use App\Notifications\ParticulierRegistrationApprovedNotification;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ParticulierController extends Controller
{
    /**
     * Liste des transporteurs particuliers validés ou rejetés
     */
    public function index(Request $request)
    {
        $query = Particulier::where('statut', '!=', 'en_attente');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%")
                  ->orWhere('immatriculation', 'like', "%{$search}%")
                  ->orWhere('code_id', 'like', "%{$search}%");
            });
        }

        $particuliers = $query->latest()->paginate(10)->withQueryString();

        return view('admin.particuliers.index', compact('particuliers'));
    }

    /**
     * Liste des demandes d'inscription en attente
     */
    public function demandes(Request $request)
    {
        $query = Particulier::where('statut', 'en_attente');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%")
                  ->orWhere('immatriculation', 'like', "%{$search}%");
            });
        }

        $demandes = $query->latest()->paginate(10)->withQueryString();

        return view('admin.particuliers.demandes', compact('demandes'));
    }

    /**
     * Afficher les détails d'une demande / d'un particulier
     */
    public function show(Particulier $particulier)
    {
        // Calcul de l'âge du véhicule
        $dateMiseService = Carbon::parse($particulier->date_mise_service);
        $diff = $dateMiseService->diff(Carbon::now());
        
        $ageString = "";
        if ($diff->y > 0) {
            $ageString .= $diff->y . ($diff->y > 1 ? ' ans ' : ' an ');
        }
        if ($diff->m > 0) {
            $ageString .= $diff->m . ' mois';
        }
        if ($ageString === "") {
            $ageString = "Moins d'un mois";
        }

        return view('admin.particuliers.show', compact('particulier', 'ageString'));
    }

    /**
     * Valider la demande d'inscription d'un particulier
     */
    public function valider(Request $request, Particulier $particulier)
    {
        if ($particulier->statut !== 'en_attente') {
            return redirect()->back()->with('error', 'Cette demande a déjà été traitée.');
        }

        try {
            // Génération du mot de passe aléatoire
            $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $rawPassword = substr(str_shuffle(str_repeat($chars, 3)), 0, 10);
            
            // Génération du code_id se fait via bootHasCodeId du HasCodeId trait lors de la sauvegarde s'il est vide.
            // On le génère manuellement ici pour l'envoyer par mail/SMS.
            $codeId = Particulier::generateUniqueCodeId();

            $particulier->update([
                'statut'               => 'valide',
                'code_id'              => $codeId,
                'password'             => Hash::make($rawPassword),
                'must_change_password' => true,
            ]);

            // Envoi de l'email d'approbation
            if (!empty($particulier->email)) {
                try {
                    Notification::route('mail', $particulier->email)
                        ->notify(new ParticulierRegistrationApprovedNotification($codeId, $rawPassword, $particulier->email));
                } catch (\Exception $e) {
                    Log::error('Erreur envoi email validation particulier: ' . $e->getMessage());
                }
            }

            // Envoi du SMS
            try {
                $smsMsg = "CAR225 : Votre demande de transporteur particulier a ete validee. Connectez-vous sur le portail avec le Code: {$codeId} et le Mot de passe: {$rawPassword}. Lien: " . route('portail.login');
                app(SmsService::class)->sendSms($particulier->contact, $smsMsg);
            } catch (\Exception $e) {
                Log::error('Erreur envoi SMS validation particulier: ' . $e->getMessage());
            }

            return redirect()->route('admin.particulier.index')
                ->with('success', 'La demande a été validée avec succès. Les identifiants ont été envoyés par email et par SMS.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la validation du particulier: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la validation : ' . $e->getMessage());
        }
    }

    /**
     * Refuser la demande d'inscription d'un particulier
     */
    public function refuser(Request $request, Particulier $particulier)
    {
        if ($particulier->statut !== 'en_attente') {
            return redirect()->back()->with('error', 'Cette demande a déjà été traitée.');
        }

        try {
            $reasons = $request->input('rejected_items', []);
            $customMotif = trim($request->input('custom_motif', ''));

            if (!empty($customMotif)) {
                $reasons[] = $customMotif;
            }

            $motifRejet = count($reasons) > 0 ? implode(' ; ', $reasons) : 'Dossier non conforme';

            $particulier->update([
                'statut' => 'rejete',
                'motif_rejet' => $motifRejet,
            ]);

            // Envoi d'un SMS de refus
            try {
                $smsMsg = "CAR225 : Votre demande de transporteur particulier a ete refusee par l'administration. Motif: {$motifRejet}.";
                app(SmsService::class)->sendSms($particulier->contact, $smsMsg);
            } catch (\Exception $e) {
                Log::error('Erreur envoi SMS refus particulier: ' . $e->getMessage());
            }

            return redirect()->route('admin.particulier.index')
                ->with('success', 'La demande d\'inscription a été refusée et le motif a été envoyé.');

        } catch (\Exception $e) {
            Log::error('Erreur lors du refus du particulier: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors du traitement : ' . $e->getMessage());
        }
    }
}
