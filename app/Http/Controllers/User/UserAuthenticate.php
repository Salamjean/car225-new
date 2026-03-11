<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class UserAuthenticate extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function login()
    {
        if (auth('web')->check()) {
            return redirect()->route('reservation.create');
        }
        return view('user.auth.login');
    }

    public function handleLogin(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginValue = $request->input('login');
        $password = $request->input('password');

        // Order of priority for search
        $searchOrder = [
            ['model' => \App\Models\User::class, 'role' => 'user', 'guard' => 'web', 'dashboard' => 'reservation.create'],
            ['model' => \App\Models\Agent::class, 'role' => 'agent', 'guard' => 'agent', 'dashboard' => 'agent.dashboard'],
            ['model' => \App\Models\Hotesse::class, 'role' => 'hotesse', 'guard' => 'hotesse', 'dashboard' => 'hotesse.dashboard'],
            ['model' => \App\Models\Caisse::class, 'role' => 'caisse', 'guard' => 'caisse', 'dashboard' => 'caisse.dashboard'], // Assumé
            ['model' => \App\Models\Personnel::class, 'role' => 'chauffeur', 'guard' => 'chauffeur', 'dashboard' => 'chauffeur.dashboard'],
        ];

        foreach ($searchOrder as $search) {
            $entity = $search['model']::where('contact', $loginValue)
                ->orWhere('code_id', $loginValue)
                ->first();

            if ($entity && Hash::check($password, $entity->password)) {
                // Check if archived for staff roles
                if (in_array($search['role'], ['agent', 'hotesse', 'caisse'])) {
                    if ($entity->archived_at) {
                        return back()->withErrors(['login' => 'Votre compte est archivé.'])->withInput($request->except('password'));
                    }
                }

                // Log in with the appropriate guard
                Auth::guard($search['guard'])->login($entity, $request->filled('remember'));

                // User specific check for phone verification
                if ($search['role'] === 'user') {
                    if (!$entity->phone_verified_at && $entity->contact) {
                        Auth::guard('web')->logout();
                        $this->smsService->sendOtp($entity->contact, $entity->prenom, $entity->name);
                        session([
                            'otp_contact' => $entity->contact,
                            'otp_prenom' => $entity->prenom,
                            'otp_nom' => $entity->name,
                        ]);
                        return redirect()->route('user.verify-otp')
                            ->with('info', 'Veuillez vérifier votre numéro de téléphone.');
                    }
                }

                $request->session()->regenerate();

                // Redirect to the correct dashboard
                return redirect()->route($search['dashboard'])->with('success', 'Bienvenue sur votre espace ' . $search['role'] . '!');
            }
        }

        return redirect()->route('login')->withErrors([
            'login' => 'Les identifiants sont incorrects.',
        ])->withInput($request->except('password'));
    }


    public function register()
    {
        return view('user.auth.register');
    }

    public function handleRegister(Request $request): RedirectResponse
    {
        // Supprimer les comptes non vérifiés avec le même contact (permet la re-inscription)
        if ($request->input('contact')) {
            User::where('contact', $request->input('contact'))
                ->whereNull('phone_verified_at')
                ->whereNull('google_id') // Protéger les comptes Google existants
                ->delete();
        }
        if ($request->input('email')) {
            User::where('email', $request->input('email'))
                ->whereNull('phone_verified_at')
                ->whereNull('google_id') // Protéger les comptes Google existants
                ->delete();
        }

        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'nullable|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'contact' => 'required|string|max:255|unique:users,contact',
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
                'email.email' => 'Veuillez saisir une adresse email valide.',
                'email.unique' => 'Cette adresse email est déjà utilisée.',

                // Mot de passe
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
                'password.confirmed' => 'Les mots de passe ne correspondent pas.',

                // Contact
                'contact.required' => 'Le numéro de contact est obligatoire.',
                'contact.string' => 'Le contact doit être une chaîne de caractères.',
                'contact.max' => 'Le contact ne doit pas dépasser 255 caractères.',
                'contact.unique' => 'Ce numéro de téléphone est déjà utilisé par un compte vérifié.',

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
                'contact' => $validated['contact'],
                'password' => Hash::make($validated['password']),
            ];

            // Gestion de l'upload de la photo de profil
            if ($request->hasFile('photo_profile')) {
                $photoFile = $request->file('photo_profile');
                $photoName = 'profile_' . time() . '_' . Str::random(10) . '.' . $photoFile->getClientOriginalExtension();
                $photoPath = $photoFile->storeAs('users/profiles', $photoName, 'public');
                $userData['photo_profile_path'] = $photoPath;
            }

            // Création de l'utilisateur (phone_verified_at reste null)
            User::create($userData);

            // Envoyer le code OTP par SMS
            $result = $this->smsService->sendOtp($validated['contact'], $validated['prenom'], $validated['name']);

            // Stocker le contact et le nom en session pour la page de vérification
            session([
                'otp_contact' => $validated['contact'],
                'otp_prenom' => $validated['prenom'],
                'otp_nom' => $validated['name'],
            ]);

            if ($result['success']) {
                return redirect()->route('user.verify-otp')
                    ->with('success', 'Votre compte a été créé ! Un code de vérification a été envoyé au ' . $this->maskPhone($validated['contact']) . '.');
            } else {
                return redirect()->route('user.verify-otp')
                    ->with('warning', 'Votre compte a été créé mais l\'envoi du SMS a échoué. Vous pouvez renvoyer le code.');
            }
        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            Log::error('Erreur lors de la création du compte: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création du compte. Veuillez réessayer.')
                ->withInput();
        }
    }

    /**
     * Afficher la page de vérification OTP
     */
    public function showVerifyOtp()
    {
        $contact = session('otp_contact');

        if (!$contact) {
            return redirect()->route('user.register')
                ->with('error', 'Veuillez d\'abord vous inscrire.');
        }

        $maskedPhone = $this->maskPhone($contact);

        return view('user.auth.verify-otp', compact('maskedPhone', 'contact'));
    }

    /**
     * Vérifier le code OTP
     */
    public function handleVerifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Le code de vérification est obligatoire.',
            'otp.size' => 'Le code doit contenir exactement 6 chiffres.',
        ]);

        $contact = session('otp_contact');

        if (!$contact) {
            return redirect()->route('user.register')
                ->with('error', 'Session expirée. Veuillez vous réinscrire.');
        }

        if ($this->smsService->verifyOtp($contact, $request->input('otp'))) {
            // Mettre à jour le champ phone_verified_at
            User::where('contact', $contact)->update([
                'phone_verified_at' => now(),
            ]);

            // Nettoyer la session
            session()->forget('otp_contact');

            // Supprimer les OTP
            $this->smsService->deleteOtp($contact);

            return redirect()->route('login')
                ->with('success', 'Votre numéro a été vérifié avec succès ! Vous pouvez maintenant vous connecter.');
        }

        return redirect()->back()
            ->withErrors(['otp' => 'Code de vérification invalide ou expiré.']);
    }

    /**
     * Renvoyer le code OTP
     */
    public function resendOtp(): RedirectResponse
    {
        $contact = session('otp_contact');

        if (!$contact) {
            return redirect()->route('user.register')
                ->with('error', 'Session expirée. Veuillez vous réinscrire.');
        }

        $prenom = session('otp_prenom', '');
        $nom = session('otp_nom', '');
        $result = $this->smsService->sendOtp($contact, $prenom, $nom);

        if ($result['success']) {
            return redirect()->back()
                ->with('success', 'Un nouveau code a été envoyé au ' . $this->maskPhone($contact) . '.');
        }

        return redirect()->back()
            ->with('error', 'Échec de l\'envoi du SMS. Veuillez réessayer.');
    }

    /**
     * Masquer le numéro de téléphone (ex: 07****88)
     */
    protected function maskPhone(string $phone): string
    {
        $length = strlen($phone);
        if ($length <= 4) return $phone;

        return substr($phone, 0, 2) . str_repeat('*', $length - 4) . substr($phone, -2);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

   public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->user();
        
        // Vérifier si un utilisateur existe déjà avec cet email (même sans google_id)
        $existingUserByEmail = User::where('email', $googleUser->email)->first();
        
        // Vérifier si l'utilisateur a déjà un compte Google connecté
        $existingUserByGoogleId = User::where('google_id', $googleUser->id)->first();
        
        // Si un compte existe avec cet email mais n'a pas de google_id, c'est un compte classique
        if ($existingUserByEmail && !$existingUserByEmail->google_id) {
            return redirect()->route('login')->withErrors([
                'email' => 'Un compte existe déjà avec cette adresse email. Veuillez vous connecter avec votre mot de passe ou un autre contact.'
            ]);
        }
        
        // Si l'utilisateur existe déjà avec Google, on le connecte
        if ($existingUserByGoogleId) {
            Auth::login($existingUserByGoogleId);
            return redirect()->intended(route('reservation.create'))->with('success', 'Bienvenue ' . $existingUserByGoogleId->prenom . ' !');
        }
        
        // Sinon, on crée un nouveau compte
        // Télécharger et sauvegarder l'image de profil Google
        $photoProfilePath = null;
        if ($googleUser->avatar) {
            try {
                $imageContent = file_get_contents($googleUser->avatar);
                $imageName = 'google_profile_' . time() . '_' . Str::random(10) . '.jpg';
                $imagePath = 'users/profiles/' . $imageName;
                Storage::disk('public')->put($imagePath, $imageContent);
                $photoProfilePath = $imagePath;
            } catch (\Exception $e) {
                Log::warning('Impossible de télécharger l\'image de profil Google: ' . $e->getMessage());
            }
        }
        
        // Création d'un nouvel utilisateur
        $contact = !empty($googleUser->user['phone_number']) ? $googleUser->user['phone_number'] : null;
        
        $user = User::create([
            'name' => $googleUser->user['family_name'] ?? $googleUser->name,
            'prenom' => $googleUser->user['given_name'] ?? '',
            'email' => $googleUser->email,
            'google_id' => $googleUser->id,
            'photo_profile_path' => $photoProfilePath,
            'email_verified_at' => now(),
            'phone_verified_at' => now(), // Considérer auto-vérifié via Google
            'password' => Hash::make(Str::random(24)),
            'contact' => $contact,
        ]);

        Auth::login($user);

        return redirect()->intended(route('reservation.create'))->with('success', 'Bienvenue ' . $user->prenom . ' !');

    } catch (\Exception $e) {
        Log::error('Erreur Google Login: ' . $e->getMessage());
        return redirect()->route('login')->with('error', 'Erreur lors de la connexion avec Google. ' . $e->getMessage());
    }
}
}
