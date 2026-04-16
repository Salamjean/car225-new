<?php

namespace App\Http\Controllers;

use App\Models\Convoi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PublicConvoiPassagerController extends Controller
{
    /** Afficher le formulaire public de saisie des passagers */
    public function show(string $token)
    {
        $convoi = Convoi::where('passenger_form_token', $token)->firstOrFail();

        // Le formulaire n'est accessible que si le convoi est payé et non garant
        if ($convoi->statut !== 'paye' || $convoi->is_garant) {
            abort(403, 'Ce formulaire n\'est plus accessible.');
        }

        $convoi->load(['compagnie', 'gare', 'itineraire', 'passagers']);

        return view('public.convoi-passagers', compact('convoi', 'token'));
    }

    /** Enregistrer les passagers soumis par le client via le lien public */
    public function store(Request $request, string $token)
    {
        $convoi = Convoi::where('passenger_form_token', $token)->firstOrFail();

        if ($convoi->statut !== 'paye' || $convoi->is_garant) {
            abort(403, 'Ce formulaire n\'est plus accessible.');
        }

        $request->validate([
            'passagers'                   => 'required|array|min:1',
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

        // Remplacer les passagers existants
        $convoi->passagers()->delete();

        foreach ($request->input('passagers') as $p) {
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

        $convoi->update(['passagers_soumis' => true]);

        // Notifier la gare par SMS (optionnel)
        try {
            $gare = $convoi->gare;
            if ($gare && $gare->contact) {
                $prenom = $convoi->demandeur_nom;
                app(\App\Services\SmsService::class)->sendSms(
                    $gare->contact,
                    "CAR225 - Le client {$prenom} (ref {$convoi->reference}) vient de soumettre la liste de ses {$convoi->passagers()->count()} passager(s). Vous pouvez consulter la liste dans votre espace gare."
                );
            }
        } catch (\Exception $e) {
            Log::error('SMS gare passagers soumis: ' . $e->getMessage());
        }

        return redirect()->route('public.convoi.passagers.confirmation', $token);
    }

    /** Page de confirmation après soumission */
    public function confirmation(string $token)
    {
        $convoi = Convoi::where('passenger_form_token', $token)->firstOrFail();
        $convoi->load(['compagnie', 'gare', 'itineraire']);
        return view('public.convoi-passagers-confirmation', compact('convoi'));
    }
}
