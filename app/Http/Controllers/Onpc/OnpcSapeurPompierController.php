<?php

namespace App\Http\Controllers\Onpc;

use App\Http\Controllers\Concerns\HandlesSapeurPompierStations;
use App\Http\Controllers\Controller;
use App\Models\ResetCodePasswordSapeurPompier;
use App\Models\SapeurPompier;
use App\Notifications\SendEmailToSapeurPompierAfterRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

/**
 * Permet à un agent ONPC de créer une caserne de sapeurs-pompiers
 * via un combobox pré-rempli (liste GSPM + OpenStreetMap).
 */
class OnpcSapeurPompierController extends Controller
{
    use HandlesSapeurPompierStations;

    public function create()
    {
        return view('onpc.sapeurs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:sapeur_pompiers,email',
            'commune'   => 'required|string|max:255',
            'adresse'   => 'required|string|max:255',
            'contact'   => 'required|digits:10',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'path_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'email.unique'   => 'Cet email est déjà associé à une caserne.',
            'contact.digits' => 'Le numéro de téléphone doit contenir exactement 10 chiffres.',
        ]);

        // Empêche la création d'une caserne aux mêmes coordonnées (~55 m)
        $duplicate = $this->findDuplicateByCoords(
            $validated['latitude']  ?? null,
            $validated['longitude'] ?? null,
        );
        if ($duplicate) {
            return redirect()->back()->withInput()->with(
                'error',
                'Une caserne existe déjà à ces coordonnées : ' . $duplicate->name
                . ' (' . $duplicate->commune . '). Veuillez choisir une localisation différente.'
            );
        }

        try {
            DB::beginTransaction();

            $logoPath = null;
            if ($request->hasFile('path_logo')) {
                $file = $request->file('path_logo');
                $name = 'logo_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $logoPath = $file->storeAs('sapeur_pompiers/logos', $name, 'public');
            }

            $sp = SapeurPompier::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => Hash::make('default-' . Str::random(8)),
                'commune'   => $validated['commune'],
                'adresse'   => $validated['adresse'],
                'contact'   => $validated['contact'],
                'latitude'  => $validated['latitude']  ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'path_logo' => $logoPath,
            ]);

            ResetCodePasswordSapeurPompier::where('email', $sp->email)->delete();
            $code = rand(1000, 4000) . $sp->id;
            ResetCodePasswordSapeurPompier::create(['code' => $code, 'email' => $sp->email]);

            Notification::route('mail', $sp->email)
                ->notify(new SendEmailToSapeurPompierAfterRegistrationNotification($code, $sp->email));

            DB::commit();

            return redirect()->route('onpc.sapeurs.index')
                ->with('success', 'Caserne créée. Un email a été envoyé pour définir le mot de passe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }
}
