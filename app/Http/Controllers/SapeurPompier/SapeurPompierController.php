<?php

namespace App\Http\Controllers\SapeurPompier;

use App\Http\Controllers\Controller;
use App\Models\SapeurPompier;
use App\Models\ResetCodePasswordSapeurPompier;
use App\Notifications\SendEmailToSapeurPompierAfterRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SapeurPompierController extends Controller
{
    /**
     * Afficher la liste des sapeurs pompiers
     */
    public function index(Request $request)
    {
        $query = SapeurPompier::query();

        // Recherche
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('commune', 'like', "%{$search}%")
                    ->orWhere('contact', 'like', "%{$search}%");
            });
        }

        $sapeurPompiers = $query->latest()->paginate(10);

        return view('admin.sapeur_pompier.index', compact('sapeurPompiers'));
    }

    /**
     * creer une sapeur pompier
     */
    public function create()
    {
        return view('admin.sapeur_pompier.create');
    }

    /**
     * Stocker un nouveau sapeur pompier
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:sapeur_pompiers,email',
            'commune' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'path_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();
            $logoPath = null;

            if ($request->hasFile('path_logo')) {
                $logoFile = $request->file('path_logo');
                $logoName = 'logo_' . time() . '_' . Str::random(10) . '.' . $logoFile->getClientOriginalExtension();
                $logoPath = $logoFile->storeAs('sapeur_pompiers/logos', $logoName, 'public');
            }
            $sapeurPompier = SapeurPompier::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make('default'),
                'commune' => $validated['commune'],
                'adresse' => $validated['adresse'],
                'contact' => $validated['contact'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'path_logo' => $logoPath,
            ]);

            ResetCodePasswordSapeurPompier::where('email', $sapeurPompier->email)->delete();

            $codeSimple = rand(1000, 4000);
            $code = $codeSimple . '' . $sapeurPompier->id;

            ResetCodePasswordSapeurPompier::create([
                'code' => $code,
                'email' => $sapeurPompier->email,
            ]);

            Notification::route('mail', $sapeurPompier->email)
                ->notify(new SendEmailToSapeurPompierAfterRegistrationNotification($code, $sapeurPompier->email));

            DB::commit();

            return redirect()->route('sapeur-pompier.index')
                ->with('success', 'Sapeur Pompier créé avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Afficher les détails
     */
    public function show(SapeurPompier $sapeurPompier)
    {
        return view('admin.sapeur_pompier.show', compact('sapeurPompier'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(SapeurPompier $sapeurPompier)
    {
        return view('admin.sapeur_pompier.edit', compact('sapeurPompier'));
    }

    /**
     * Mettre à jour
     */
    public function update(Request $request, SapeurPompier $sapeurPompier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:sapeur_pompiers,email,' . $sapeurPompier->id,
            'commune' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        try {
            if ($request->hasFile('path_logo')) {
                if ($sapeurPompier->path_logo && Storage::disk('public')->exists($sapeurPompier->path_logo)) {
                    Storage::disk('public')->delete($sapeurPompier->path_logo);
                }

                $logoFile = $request->file('path_logo');
                $logoName = 'logo_' . time() . '_' . Str::random(10) . '.' . $logoFile->getClientOriginalExtension();
                $logoPath = $logoFile->storeAs('sapeur_pompiers/logos', $logoName, 'public');
                $validated['path_logo'] = $logoPath;
            }

            if ($request->filled('password')) {
                $validated['password'] = $request->password;
            } else {
                unset($validated['password']);
            }

            $sapeurPompier->update($validated);

            return redirect()->route('sapeur-pompier.index')
                ->with('success', 'Sapeur Pompier mis à jour avec succès!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprimer
     */
    public function destroy(SapeurPompier $sapeurPompier)
    {
        try {
            if ($sapeurPompier->path_logo && Storage::disk('public')->exists($sapeurPompier->path_logo)) {
                Storage::disk('public')->delete($sapeurPompier->path_logo);
            }

            $sapeurPompier->delete();

            return redirect()->route('sapeur-pompier.index')
                ->with('success', 'Sapeur Pompier supprimé avec succès!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
}
