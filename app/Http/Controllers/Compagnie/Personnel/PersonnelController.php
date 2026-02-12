<?php

namespace App\Http\Controllers\Compagnie\Personnel;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PersonnelController extends Controller
{
    public function index()
    {
        // Récupérer le personnel de la compagnie connectée
        $personnels = Personnel::where('compagnie_id', Auth::guard('compagnie')->user()->id)
            ->latest()
            ->paginate(10);

        return view('compagnie.personnel.index', compact('personnels'));
    }

    public function create()
    {
        return view('compagnie.personnel.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'type_personnel' => 'required|string|in:Chauffeur,Convoyeur',
            'email' => 'required|email|unique:personnels,email',
            'contact' => 'required|string|max:20',
            'country_code' => 'required|string|max:10',
            'contact_urgence' => 'required|string|max:20',
            'country_code_urgence' => 'required|string|max:10',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'type_personnel.required' => 'Le type de personnel est obligatoire.',
            'type_personnel.in' => 'Le type de personnel doit être Chauffeur ou Convoyeur.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'contact.required' => 'Le contact personnel est obligatoire.',
            'contact_urgence.required' => 'Le contact d\'urgence est obligatoire.',
            'profile_image.image' => 'Le fichier doit être une image.',
            'profile_image.mimes' => 'L\'image doit être au format jpeg, png, jpg ou gif.',
            'profile_image.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ]);

        try {
            // Gestion de l'upload de l'image de profil
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $imageName = 'profile_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                // Stocker l'image dans le dossier storage/app/public/profiles
                $profileImagePath = $image->storeAs('profiles', $imageName, 'public');
            }
            $contact = $validatedData['country_code'] . '' . $validatedData['contact'];
            $contact_urgent = $validatedData['country_code_urgence'] . '' . $validatedData['contact_urgence'];

            // Création du personnel
            $personnel = Personnel::create([
                'name' => $validatedData['name'],
                'prenom' => $validatedData['prenom'],
                'type_personnel' => $validatedData['type_personnel'],
                'email' => $validatedData['email'],
                'contact' => $contact,
                'contact_urgence' => $validatedData['contact_urgence'],
                'profile_image' => $profileImagePath,
                'compagnie_id' => Auth::guard('compagnie')->user()->id ?? null,
            ]);

            // Redirection avec message de succès
            return redirect()
                ->route('personnel.index')
                ->with('success', 'Personnel créé avec succès!');
        } catch (\Exception $e) {
            // En cas d'erreur, supprimer l'image uploadée si elle existe
            if (isset($profileImagePath) && Storage::disk('public')->exists($profileImagePath)) {
                Storage::disk('public')->delete($profileImagePath);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du personnel: ' . $e->getMessage());
        }
    }

    /**
     * API pour récupérer les détails d'un personnel (pour SweetAlert2)
     */
    public function showApi(Personnel $personnel)
    {
        $this->authorize('view', $personnel);

        return response()->json([
            'id' => $personnel->id,
            'name' => $personnel->name,
            'prenom' => $personnel->prenom,
            'email' => $personnel->email,
            'type_personnel' => $personnel->type_personnel,
            'contact' => $personnel->contact,
            'contact_urgence' => $personnel->contact_urgence,
            'statut' => $personnel->statut,
            'profile_image' => $personnel->profile_image ? asset('storage/' . $personnel->profile_image) : null, // URL COMPLÈTE
            'created_at' => $personnel->created_at->format('d/m/Y'),
            'updated_at' => $personnel->updated_at->format('d/m/Y'),
        ]);
    }
}
