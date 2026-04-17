<?php

namespace App\Http\Controllers;

use App\Models\Convoi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PublicConvoiPassagerController extends Controller
{
    private function cookieName(string $token): string
    {
        return 'car225_pid_' . substr($token, 0, 20);
    }

    /** Afficher le formulaire public — un slot par device */
    public function show(Request $request, string $token)
    {
        $convoi = Convoi::where('passenger_form_token', $token)->firstOrFail();

        if ($convoi->statut !== 'paye' || $convoi->is_garant) {
            abort(403, 'Ce formulaire n\'est plus accessible.');
        }

        $convoi->load(['compagnie', 'gare', 'itineraire', 'passagers']);

        // Récupérer ou générer le device_id via cookie
        $cookieName = $this->cookieName($token);
        $deviceId   = $request->cookie($cookieName);

        // Chercher un passager existant pour ce device
        $existingPassager = null;
        if ($deviceId) {
            $existingPassager = $convoi->passagers()->where('device_id', $deviceId)->first();
        }

        // Nombre de places prises (hors ce device si déjà enregistré)
        $totalRegistered = $convoi->passagers()->count();
        $placesRestantes = $convoi->nombre_personnes - $totalRegistered;

        // Si plus de place ET ce device n'a pas de slot réservé → page complète
        if (!$existingPassager && $placesRestantes <= 0) {
            return view('public.convoi-passagers-complet', compact('convoi'));
        }

        // Générer un device_id si absent
        if (!$deviceId) {
            $deviceId = (string) Str::uuid();
        }

        $placesLabel = $totalRegistered . ' / ' . $convoi->nombre_personnes;

        $response = response()->view(
            'public.convoi-passagers',
            compact('convoi', 'token', 'existingPassager', 'deviceId', 'placesLabel', 'totalRegistered')
        );

        // Mettre à jour / poser le cookie (1 an, non httpOnly pour compatibilité)
        return $response->cookie($cookieName, $deviceId, 60 * 24 * 365, '/', null, false, false);
    }

    /** Enregistrer / mettre à jour le slot du passager */
    public function store(Request $request, string $token)
    {
        $convoi = Convoi::where('passenger_form_token', $token)->firstOrFail();

        if ($convoi->statut !== 'paye' || $convoi->is_garant) {
            abort(403, 'Ce formulaire n\'est plus accessible.');
        }

        $request->validate([
            'nom'             => 'required|string|max:100',
            'prenoms'         => 'required|string|max:150',
            'contact'         => ['required', 'digits:10'],
            'contact_urgence' => ['nullable', 'digits:10'],
            'device_id'       => 'required|string|max:64',
        ], [
            'nom.required'             => 'Votre nom est obligatoire.',
            'prenoms.required'         => 'Vos prénoms sont obligatoires.',
            'contact.required'         => 'Votre numéro de contact est obligatoire.',
            'contact.digits'           => 'Le contact doit contenir exactement 10 chiffres.',
            'contact_urgence.digits'   => 'Le contact d\'urgence doit contenir exactement 10 chiffres.',
        ]);

        $deviceId   = $request->input('device_id');
        $cookieName = $this->cookieName($token);

        $convoi->load(['passagers', 'gare']);

        // Vérifier si ce device a déjà un slot
        $existingPassager = $convoi->passagers()->where('device_id', $deviceId)->first();

        // Vérifier doublon de contact (un autre passager a déjà ce numéro)
        $contactPris = $convoi->passagers()
            ->where('contact', $request->contact)
            ->when($existingPassager, fn($q) => $q->where('id', '!=', $existingPassager->id))
            ->exists();
        if ($contactPris) {
            return back()->withErrors(['contact' => 'Ce numéro de contact est déjà utilisé par un autre passager de ce convoi.'])->withInput();
        }

        if ($existingPassager) {
            // Mise à jour du slot existant
            $existingPassager->update([
                'nom'             => $request->nom,
                'prenoms'         => $request->prenoms,
                'contact'         => $request->contact,
                'contact_urgence' => $request->contact_urgence,
            ]);
        } else {
            // Vérifier la capacité avant création
            $totalRegistered = $convoi->passagers()->count();
            if ($totalRegistered >= $convoi->nombre_personnes) {
                return back()->withErrors(['general' => 'Désolé, toutes les places sont déjà prises pour ce convoi.']);
            }

            $convoi->passagers()->create([
                'nom'             => $request->nom,
                'prenoms'         => $request->prenoms,
                'contact'         => $request->contact,
                'contact_urgence' => $request->contact_urgence,
                'device_id'       => $deviceId,
            ]);
        }

        // Vérifier si toutes les places sont maintenant remplies
        $newTotal = $convoi->passagers()->count();
        if ($newTotal >= $convoi->nombre_personnes && !$convoi->passagers_soumis) {
            $convoi->update(['passagers_soumis' => true]);
            // Notifier la gare que le convoi est complet
            try {
                $gare = $convoi->gare;
                if ($gare && $gare->contact) {
                    app(\App\Services\SmsService::class)->sendSms(
                        $gare->contact,
                        "CAR225 - Toutes les {$convoi->nombre_personnes} places du convoi {$convoi->reference} ont ete renseignees. Vous pouvez proceder a l'affectation chauffeur/vehicule."
                    );
                }
            } catch (\Exception $e) {
                Log::error('SMS gare passagers complet: ' . $e->getMessage());
            }
        }

        $response = redirect()->route('public.convoi.passagers.confirmation', $token);
        return $response->cookie($cookieName, $deviceId, 60 * 24 * 365, '/', null, false, false);
    }

    /** Page de confirmation après soumission */
    public function confirmation(string $token)
    {
        $convoi = Convoi::where('passenger_form_token', $token)->firstOrFail();
        $convoi->load(['compagnie', 'gare', 'itineraire']);

        // Récupérer le device_id pour afficher les infos du passager
        $cookieName = $this->cookieName($token);
        $deviceId   = request()->cookie($cookieName);
        $myPassager = $deviceId ? $convoi->passagers()->where('device_id', $deviceId)->first() : null;

        $totalRegistered = $convoi->passagers()->count();

        return view('public.convoi-passagers-confirmation', compact('convoi', 'myPassager', 'totalRegistered', 'token'));
    }
}
