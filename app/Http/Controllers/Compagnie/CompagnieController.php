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
            'name'      => 'required|string|unique:compagnies,name|max:255',
            'email'     => 'required|email|unique:compagnies,email',
            'commune'   => 'required|string|max:255',
            'adresse'   => 'required|string|max:255',
            'contact'   => 'required|string|unique:compagnies,contact|max:255',
            'prefix'    => 'required|string|max:10',
            'sigle'     => 'required|string|max:50|unique:compagnies,sigle',
            'path_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'name.unique' => 'Ce nom de compagnie est déjà utilisé.',
            'email.unique' => 'Cette adresse email est déjà enregistrée.',
            'contact.unique' => 'Ce numéro de contact est déjà utilisé.',
            'sigle.required' => 'Le sigle de la compagnie est obligatoire.',
            'sigle.unique' => 'Ce sigle est déjà utilisé.',
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
                'commune'   => $validated['commune'],
                'adresse'   => $validated['adresse'],
                'contact'   => $validated['contact'],
                'prefix'    => $validated['prefix'],
                'sigle'     => $validated['sigle'],
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
            'name' => 'required|string|max:255|unique:compagnies,name,' . $compagnie->id,
            'email' => 'required|email|unique:compagnies,email,' . $compagnie->id,
            'username' => 'nullable|string|max:255|unique:compagnies,username,' . $compagnie->id,
            'commune' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'contact' => 'required|string|unique:compagnies,contact,' . $compagnie->id,
            'prefix' => 'required|string|max:10',
            'sigle' => 'required|string|max:50|unique:compagnies,sigle,' . $compagnie->id,
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
                $compagnie->path_logo = $logoPath;
            }

            // Gestion du rechargement du solde (Ex-tickets)
            if ($request->filled('add_tickets') && $request->add_tickets > 0) {
                $amountToAdd = $request->input('add_tickets');
                
                $compagnie->tickets = ($compagnie->tickets ?? 0) + $amountToAdd;
                
                // Enregistrer dans l'historique
                $compagnie->historiqueTickets()->create([
                    'quantite' => $amountToAdd, // On garde le nom du champ 'quantite' mais on y stocke le montant
                    'montant' => $amountToAdd,
                    'motif' => 'Recharge administrateur (Solde)'
                ]);
            }

            // Mise à jour de la compagnie avec les champs validés, excluant ceux gérés séparément
            $compagnie->update($request->except(['path_logo', 'password', 'add_tickets', 'montant_paye']));

            // Mise à jour du mot de passe si fourni
            // Mise à jour du mot de passe si fourni
            if ($request->filled('password')) {
                $compagnie->password = $request->password; // Model hashes it
                $compagnie->save();
            }

            return redirect()->route('compagnie.index')
                ->with('success', 'Compagnie mise à jour avec succès (Solde rechargé si renseigné)!');

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
