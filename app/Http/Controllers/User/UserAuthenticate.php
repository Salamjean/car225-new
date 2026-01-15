<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserAuthenticate extends Controller
{
    public function login()
    {
        if (auth('web')->check()) {
            return redirect()->route('user.dashboard');
        }
        return view('user.auth.login');
    }

    public function handleLogin(Request $request): RedirectResponse
    {
        if (!Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            return redirect()->route('login')->withErrors([
                'password' => 'Le mot de passe incorrect.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('user.dashboard', absolute: false))->with('success', 'Bienvenue sur votre page!');
    }


    public function register()
    {
        return view('user.auth.register');
    }

    public function handleRegister(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'adresse' => 'required|string|max:255',
                'contact' => 'required|string|max:255',
                'photo_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ],
            [
                // Nom
                'name.required' => 'Le nom est obligatoire.',
                'name.string' => 'Le nom doit être une chaîne de caractères.',
                'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',

                // Prénom
                'prenom.required' => 'Le prénom est obligatoire.',
                'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
                'prenom.max' => 'Le prénom ne doit pas dépasser 255 caractères.',

                // Email
                'email.required' => 'L\'adresse email est obligatoire.',
                'email.email' => 'Veuillez saisir une adresse email valide.',
                'email.unique' => 'Cette adresse email est déjà utilisée.',

                // Mot de passe
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
                'password.confirmed' => 'Les mots de passe ne correspondent pas.',

                // Adresse
                'adresse.required' => 'L\'adresse est obligatoire.',
                'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',
                'adresse.max' => 'L\'adresse ne doit pas dépasser 255 caractères.',

                // Contact
                'contact.required' => 'Le numéro de contact est obligatoire.',
                'contact.string' => 'Le contact doit être une chaîne de caractères.',
                'contact.max' => 'Le contact ne doit pas dépasser 255 caractères.',

                // Photo de profil
                'photo_profile.image' => 'Le fichier doit être une image.',
                'photo_profile.mimes' => 'L\'image doit être au format : jpeg, png, jpg ou gif.',
                'photo_profile.max' => 'L\'image ne doit pas dépasser 2 Mo.',
            ]
        );

        try {
            // Préparation des données utilisateur
            $userData = [
                'name' => $validated['name'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'pays' => 'Cote_Ivoire',
                'contact' => $validated['contact'],
                'adresse' => $validated['adresse'],
                'password' => Hash::make($validated['password']),
            ];

            // Gestion de l'upload de la photo de profil
            if ($request->hasFile('photo_profile')) {
                $photoFile = $request->file('photo_profile');
                $photoName = 'profile_' . time() . '_' . Str::random(10) . '.' . $photoFile->getClientOriginalExtension();
                $photoPath = $photoFile->storeAs('users/profiles', $photoName, 'public');
                $userData['photo_profile_path'] = $photoPath;
            }

            // Création de l'utilisateur
            User::create($userData);

            return redirect()->route('login')
                ->with('success', 'Votre compte a été créé avec succès. Vous pouvez vous connecter.');
        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            Log::error('Erreur lors de la création du compte: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création du compte. Veuillez réessayer.')
                ->withInput();
        }
    }
}
