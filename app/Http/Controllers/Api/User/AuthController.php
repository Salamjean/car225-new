<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Connexion utilisateur
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
            'fcm_token' => 'nullable|string',
            'nom_device' => 'nullable|string|max:255',
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        // Vérifier si le compte est désactivé
        if (!$user->is_active) {
            // Si le compte est désactivé, on vérifie depuis quand
            if ($user->deactivated_at) {
                $deletionDate = $user->deactivated_at->copy()->addDays(30);
                
                if (now()->greaterThanOrEqualTo($deletionDate)) {
                    // Supprimer le compte s'il a dépassé les 30 jours (ou rejeter la connexion)
                    // Ici on rejette, le job de nettoyage s'occupera de la suppression
                    throw ValidationException::withMessages([
                        'email' => ['Votre compte a été supprimé définitivement.'],
                    ]);
                } else {
                    // Réactiver le compte
                    $user->update([
                        'is_active' => true,
                        'deactivated_at' => null,
                    ]);
                }
            } else {
                // Cas bizarre où deactivated_at est null mais is_active false
                $user->update(['is_active' => true]);
            }
        }

        // Mettre à jour le token FCM si fourni
        if ($request->filled('fcm_token')) {
            $user->update(['fcm_token' => $request->fcm_token]);
        }

        // Mettre à jour le nom de l'appareil si fourni (Legacy field)
        if ($request->filled('nom_device')) {
            $user->update(['nom_device' => $request->nom_device]);
        }

        // Enregistrer l'appareil dans l'historique
        if ($request->filled('nom_device')) {
            $user->devices()->min('id'); // Just to load relationship? No.
            
            // On peut limiter le nombre d'appareils stockés si besoin, mais pour l'instant on log tout
            // Ou on update si même nom ?
            // Le mieux est d'ajouter une entrée à chaque login ou update le last_login si existe
            $device = $user->devices()->where('nom_device', $request->nom_device)->first();
            
            if ($device) {
                $device->update([
                    'last_login_at' => now(),
                    'ip_address' => $request->ip(),
                ]);
            } else {
                $user->devices()->create([
                    'nom_device' => $request->nom_device,
                    'last_login_at' => now(),
                    'ip_address' => $request->ip(),
                ]);
            }
        }

        // Créer un nouveau token (on ne révoque plus les anciens pour permettre plusieurs sessions persistantes)
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'contact' => $user->contact,
                'photo_profile_path' => $user->photo_profile_path ? 'storage/' . $user->photo_profile_path : null,
                'nom_device' => $user->nom_device,
                'is_active' => $user->is_active, // Renvoie l'état
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Inscription utilisateur
     */
    public function register(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
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

            $user = User::create($userData);

            // Créer un token pour l'utilisateur
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'contact' => $user->contact,
                    'photo_profile_path' => $user->photo_profile_path ? 'storage/' . $user->photo_profile_path : null,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'inscription: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'inscription.',
            ], 500);
        }
    }

    /**
     * Déconnexion utilisateur
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie',
        ]);
    }

    /**
     * Mot de passe oublié : Envoi OTP
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Aucun compte n\'existe avec cette adresse email.'
        ]);

        $email = $request->email;
        
        // Générer un code OTP à 6 chiffres
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Supprimer les anciens codes OTP pour cet email
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();
        
        // Enregistrer le nouveau code OTP
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($otpCode), // Hashé pour sécurité
            'created_at' => now()
        ]);

        // Envoyer l'email avec le code OTP
        try {
            \Illuminate\Support\Facades\Mail::send('emails.otp', ['otp' => $otpCode], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Code de réinitialisation de mot de passe - Car225');
            });

            return response()->json([
                'success' => true,
                'message' => 'Un code OTP a été envoyé à votre adresse email.',
                'email' => $email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Mot de passe oublié : Vérification OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        $email = $request->email;
        $otp = $request->otp;

        // Récupérer le token
        $resetRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Code OTP invalide ou expiré.'
            ], 422);
        }

        // Vérifier l'expiration (10 minutes)
        $createdAt = \Carbon\Carbon::parse($resetRecord->created_at);
        if ($createdAt->addMinutes(10)->isPast()) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Le code OTP a expiré. Veuillez en demander un nouveau.'
            ], 422);
        }

        // Vérifier le code OTP avec Hash::check
        if (!Hash::check($otp, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Le code OTP est incorrect.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Code OTP vérifié avec succès.'
        ]);
    }

    /**
     * Mot de passe oublié : Réinitialisation
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'password' => 'required|min:8|confirmed'
        ], [
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.'
        ]);

        $email = $request->email;
        $otp = $request->otp;

        // Vérifier à nouveau le code OTP
        $resetRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord || !Hash::check($otp, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Code OTP invalide.'
            ], 422);
        }

        // Vérifier l'expiration
        $createdAt = \Carbon\Carbon::parse($resetRecord->created_at);
        if ($createdAt->addMinutes(10)->isPast()) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Le code OTP a expiré.'
            ], 422);
        }

        // Mettre à jour le mot de passe
        $user = User::where('email', $email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Supprimer le token
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Votre mot de passe a été réinitialisé avec succès.'
        ]);
    }
}
