<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Compagnie;
use App\Models\ResetCodePasswordCompagnie;
use App\Notifications\SendEmailToCompagnieAfterRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompagnieController extends Controller
{
    /**
     * Afficher la liste des compagnies
     */
    public function index(Request $request)
    {
        $query = Compagnie::query();
        
        // Recherche
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('sigle', 'like', "%{$search}%")
                ->orWhere('commune', 'like', "%{$search}%")
                ->orWhere('contact', 'like', "%{$search}%");
            });
        }
        
        $compagnies = $query->latest()->paginate(10);
        
        return view('admin.compagnie.index', compact('compagnies'));
    }
    /**
     * creer une compagnies
     */
    public function create(){
        return view('admin.compagnie.create');
    }

     /**
     * Stocker une nouvelle compagnie
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:compagnies,email',
            'username'  => 'nullable|string|max:255|unique:compagnies,username',
            'commune'   => 'required|string|max:255',
            'adresse'   => 'required|string|max:255',
            'contact'   => 'required|string|max:255',
            'prefix'    => 'required|string|max:10',
            'sigle'     => 'nullable|string|max:50',
            'slogan'    => 'nullable|string|max:255',
            'path_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();
            $logoPath = null;

            if ($request->hasFile('path_logo')) {
                $logoFile = $request->file('path_logo');
                $logoName = 'logo_' . time() . '_' . Str::random(10) . '.' . $logoFile->getClientOriginalExtension();
                $logoPath = $logoFile->storeAs('compagnies/logos', $logoName, 'public');
            }
            $compagnie = Compagnie::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => Hash::make('default'),
                'username'  => $validated['username'],
                'commune'   => $validated['commune'],
                'adresse'   => $validated['adresse'],
                'contact'   => $validated['contact'],
                'prefix'    => $validated['prefix'],
                'sigle'     => $validated['sigle'],
                'slogan'    => $validated['slogan'],
                'path_logo' => $logoPath,
            ]);

            ResetCodePasswordCompagnie::where('email', $compagnie->email)->delete();

            $codeSimple = rand(1000, 4000);
            $code = $codeSimple . '' . $compagnie->id;

            ResetCodePasswordCompagnie::create([
                'code'  => $code,
                'email' => $compagnie->email,
            ]);
            
            Notification::route('mail', $compagnie->email)
                ->notify(new SendEmailToCompagnieAfterRegistrationNotification($code, $compagnie->email));

            DB::commit();

            return redirect()->route('compagnie.index')
                ->with('success', 'Compagnie créée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Afficher les détails d'une compagnie
     */
    public function show(Compagnie $compagnie)
    {
        return view('admin.compagnie.show', compact('compagnie'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Compagnie $compagnie)
    {
        return view('admin.compagnie.edit', compact('compagnie'));
    }

    /**
     * Mettre à jour une compagnie
     */
    public function update(Request $request, Compagnie $compagnie)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:compagnies,email,' . $compagnie->id,
            'username' => 'nullable|string|max:255|unique:compagnies,username,' . $compagnie->id,
            'commune' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'prefix' => 'required|string|max:10',
            'sigle' => 'nullable|string|max:50',
            'slogan' => 'nullable|string|max:255',
        ]);

        try {
            // Gestion de l'upload du logo
            if ($request->hasFile('path_logo')) {
                // Supprimer l'ancien logo s'il existe
                if ($compagnie->path_logo && Storage::disk('public')->exists($compagnie->path_logo)) {
                    Storage::disk('public')->delete($compagnie->path_logo);
                }

                $logoFile = $request->file('path_logo');
                $logoName = 'logo_' . time() . '_' . Str::random(10) . '.' . $logoFile->getClientOriginalExtension();
                $logoPath = $logoFile->storeAs('compagnies/logos', $logoName, 'public');
                $validated['path_logo'] = $logoPath;
            }

            // Mise à jour du mot de passe si fourni
            if ($request->filled('password')) {
                $validated['password'] = $request->password; // Le modèle s'occupe du hash
            } else {
                unset($validated['password']);
            }

            // Mise à jour de la compagnie
            $compagnie->update($validated);

            return redirect()->route('compagnie.index')
                ->with('success', 'Compagnie mise à jour avec succès!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprimer une compagnie
     */
    public function destroy(Compagnie $compagnie)
    {
        try {
            // Supprimer le logo s'il existe
            if ($compagnie->path_logo && Storage::disk('public')->exists($compagnie->path_logo)) {
                Storage::disk('public')->delete($compagnie->path_logo);
            }

            $compagnie->delete();

            return redirect()->route('compagnie.index')
                ->with('success', 'Compagnie supprimée avec succès!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
}
