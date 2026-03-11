<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Services\FcmService;
use Carbon\Carbon;

class AuthController extends Controller
{
    protected SmsService $smsService;
    protected FcmService $fcmService;

    public function __construct(SmsService $smsService, FcmService $fcmService)
    {
        $this->smsService = $smsService;
        $this->fcmService = $fcmService;
    }
    /**
     * Connexion utilisateur
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required|min:8',
            'fcm_token' => 'nullable|string',
            'nom_device' => 'nullable|string|max:255',
        ], [
            'login.required' => 'L\'identifiant (Code ID ou contact) est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $loginValue = $request->login;
        $user = User::where('contact', $loginValue)
            ->orWhere('code_id', $loginValue)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Les identifiants fournis sont incorrects.',
                'errors' => [
                    'login' => ['Les identifiants fournis sont incorrects.']
                ]
            ], 422);
        }

        // Vérifier si le numéro de téléphone est vérifié
        if (!$user->phone_verified_at && $user->contact) {
            // Envoyer un nouveau code OTP
            $result = $this->smsService->sendOtp($user->contact, $user->prenom, $user->name);

            // Envoi de la notification push OTP
            $fcmToken = $request->fcm_token ?? $user->fcm_token;
            if ($fcmToken && isset($result['code'])) {
                try {
                    $this->fcmService->sendNotification(
                        $fcmToken,
                        'Code de vérification Car225',
                        "Votre code de vérification est : {$result['code']}. Ce code expire dans 10 minutes.",
                        ['type' => 'otp']
                    );
                } catch (\Exception $e) {
                    Log::error('Erreur lors de l\'envoi de la notification push OTP au login: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'requires_otp' => true,
                'message' => 'Votre numéro de téléphone n\'est pas encore vérifié. Un code OTP a été envoyé.',
                'contact' => $user->contact,
            ], 200);
        }

        // Vérifier si le compte est désactivé
        if (!$user->is_active) {
            // Si le compte est désactivé, on vérifie depuis quand
            if ($user->deactivated_at) {
                $deletionDate = $user->deactivated_at->copy()->addDays(30);
                
                if (now()->greaterThanOrEqualTo($deletionDate)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Votre compte a été supprimé définitivement.',
                        'errors' => [
                            'login' => ['Votre compte a été supprimé définitivement.']
                        ]
                    ], 403);
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
                'code_id' => $user->code_id,
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
                'fcm_token' => 'nullable|string',
            ],
            [
                'name.required' => 'Le nom est obligatoire.',
                'name.string' => 'Le nom doit être une chaîne de caractères.',
                'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
                'prenom.required' => 'Le prénom est obligatoire.',
                'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
                'prenom.max' => 'Le prénom ne doit pas dépasser 255 caractères.',
                'email.email' => 'Veuillez saisir une adresse email valide.',
                'email.unique' => 'Cette adresse email est déjà utilisée.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
                'password.confirmed' => 'Les mots de passe ne correspondent pas.',
                'contact.required' => 'Le numéro de contact est obligatoire.',
                'contact.string' => 'Le contact doit être une chaîne de caractères.',
                'contact.max' => 'Le contact ne doit pas dépasser 255 caractères.',
                'contact.unique' => 'Ce numéro de téléphone est déjà utilisé par un compte vérifié.',
                'photo_profile.image' => 'Le fichier doit être une image.',
                'photo_profile.mimes' => 'L\'image doit être au format : jpeg, png, jpg ou gif.',
                'photo_profile.max' => 'L\'image ne doit pas dépasser 2 Mo.',
                'photo_profile.uploaded' => 'L\'image est trop lourde pour le serveur (elle dépasse très probablement la limite de 2 Mo) ou le fichier est invalide. Veuillez compresser l\'image avant de l\'envoyer.',
            ]
        );

        try {
            $userData = [
                'name' => $validated['name'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'] ?? null,
                'contact' => $validated['contact'],
                'password' => Hash::make($validated['password']),
                'fcm_token' => $request->input('fcm_token'),
            ];

            // Gestion de l'upload de la photo de profil
            if ($request->hasFile('photo_profile')) {
                $photoFile = $request->file('photo_profile');
                $photoName = 'profile_' . time() . '_' . Str::random(10) . '.' . $photoFile->getClientOriginalExtension();
                $photoPath = $photoFile->storeAs('users/profiles', $photoName, 'public');
                $userData['photo_profile_path'] = $photoPath;
            }

            // Créer l'utilisateur (phone_verified_at reste null)
            $user = User::create($userData);

            // Envoyer le code OTP par SMS
            $result = $this->smsService->sendOtp($validated['contact'], $validated['prenom'], $validated['name']);

            // Mettre à jour le fcm_token si fourni (déjà fait à la création, mais on garde fcmToken sous la main)
            $fcmToken = $request->input('fcm_token');
            if ($fcmToken && isset($result['code'])) {
                try {
                    $this->fcmService->sendNotification(
                        $fcmToken,
                        'Code de vérification Car225',
                        "Votre code de vérification est : {$result['code']}. Ce code expire dans 10 minutes.",
                        ['type' => 'otp']
                    );
                } catch (\Exception $e) {
                    Log::error('Erreur lors de l\'envoi de la notification push OTP: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'requires_otp' => true,
                'message' => $result['success']
                    ? 'Inscription réussie ! Un code de vérification a été envoyé à votre numéro.'
                    : 'Inscription réussie mais l\'envoi du SMS a échoué. Vous pouvez renvoyer le code.',
                'sms_sent' => $result['success'],
                'contact' => $validated['contact'],
                'user' => [
                    'id' => $user->id,
                    'code_id' => $user->code_id,
                    'name' => $user->name,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'contact' => $user->contact,
                    'photo_profile_path' => $user->photo_profile_path ? 'storage/' . $user->photo_profile_path : null,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'inscription API: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'inscription.',
            ], 500);
        }
    }

    /**
     * Vérifier le code OTP du téléphone (inscription/login)
     */
    public function verifyPhoneOtp(Request $request)
    {
        $request->validate([
            'contact' => 'required|string',
            'otp' => 'required|string|size:6',
        ], [
            'contact.required' => 'Le numéro de contact est obligatoire.',
            'otp.required' => 'Le code OTP est obligatoire.',
            'otp.size' => 'Le code OTP doit contenir exactement 6 chiffres.',
        ]);

        // Vérifier le code OTP
        $otpRecord = DB::table('user_otp_codes')
            ->where('contact', $request->contact)
            ->where('verified', false)
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun code OTP trouvé pour ce numéro. Veuillez en demander un nouveau.',
            ], 422);
        }

        // Vérifier l'expiration
        if (Carbon::parse($otpRecord->expires_at)->isPast()) {
            DB::table('user_otp_codes')->where('id', $otpRecord->id)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Le code OTP a expiré. Veuillez en demander un nouveau.',
            ], 422);
        }

        // Vérifier le code
        if ($otpRecord->code !== $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Le code OTP est incorrect.',
            ], 422);
        }

        // Marquer le code comme vérifié
        DB::table('user_otp_codes')->where('id', $otpRecord->id)->update(['verified' => true]);

        // Marquer le téléphone comme vérifié
        $user = User::where('contact', $request->contact)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur introuvable.',
            ], 404);
        }

        $user->phone_verified_at = now();
        $user->save();

        // Créer le token d'authentification
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Numéro de téléphone vérifié avec succès !',
            'user' => [
                'id' => $user->id,
                'code_id' => $user->code_id,
                'name' => $user->name,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'contact' => $user->contact,
                'photo_profile_path' => $user->photo_profile_path ? 'storage/' . $user->photo_profile_path : null,
                'phone_verified_at' => $user->phone_verified_at,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Renvoyer le code OTP
     */
    public function resendPhoneOtp(Request $request)
    {
        $request->validate([
            'contact' => 'required|string',
            'fcm_token' => 'nullable|string',
        ], [
            'contact.required' => 'Le numéro de contact est obligatoire.',
        ]);

        $user = User::where('contact', $request->contact)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun compte trouvé avec ce numéro.',
            ], 404);
        }

        if ($user->phone_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Ce numéro est déjà vérifié.',
            ], 422);
        }

        $result = $this->smsService->sendOtp($request->contact, $user->prenom, $user->name);

        $fcmToken = $request->fcm_token ?? $user->fcm_token;
        if ($fcmToken && isset($result['code'])) {
            try {
                $this->fcmService->sendNotification(
                    $fcmToken,
                    'Code de vérification Car225',
                    "Votre code de vérification est : {$result['code']}. Ce code expire dans 10 minutes.",
                    ['type' => 'otp']
                );
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'envoi de la notification push OTP au renvoi: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => $result['success'],
            'message' => $result['success']
                ? 'Un nouveau code OTP a été envoyé à votre numéro.'
                : 'Échec de l\'envoi du SMS. Veuillez réessayer.',
        ]);
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
            'identity' => 'required'
        ]);

        $identity = $request->identity;
        
        // Trouver l'utilisateur par email, contact ou code_id
        $user = User::where('email', $identity)
            ->orWhere('contact', $identity)
            ->orWhere('code_id', $identity)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun compte n\'existe avec ces informations.'
            ], 404);
        }

        // Utiliser l'email comme identifiant principal pour le token, ou le contact/code_id si absent
        $identifier = $user->email ?? $user->contact ?? $user->code_id;
        
        // Générer un code OTP à 6 chiffres
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Supprimer les anciens codes OTP pour cet identifiant
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $identifier)
            ->delete();
        
        // Enregistrer le nouveau code OTP
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->insert([
            'email' => $identifier,
            'token' => Hash::make($otpCode), // Hashé pour sécurité
            'created_at' => now()
        ]);

        $emailIdentifier = $user->email ?? $user->contact ?? $user->code_id;
        $successMail = false;
        $successSms = false;

        // Envoyer l'email si disponible
        if ($user->email) {
            try {
                \Illuminate\Support\Facades\Mail::send('emails.otp', ['otp' => $otpCode], function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Code de réinitialisation de mot de passe - Car225');
                });
                $successMail = true;
            } catch (\Exception $e) {
                Log::error('Erreur API envoi Email reset password: ' . $e->getMessage());
            }
        }

        // Envoyer le SMS si disponible
        if ($user->contact) {
            try {
                $messageSms = "Votre code de reinitialisation Car225 est : {$otpCode}. Il expire dans 10 minutes.";
                $successSms = $this->smsService->sendSms($user->contact, $messageSms);
            } catch (\Exception $e) {
                Log::error('Erreur API envoi SMS reset password: ' . $e->getMessage());
            }
        }

        if ($successMail || $successSms) {
            $msg = 'Un code OTP a été envoyé';
            if ($successMail && $successSms) $msg .= ' par email et SMS.';
            elseif ($successMail) $msg .= ' par email.';
            else $msg .= ' par SMS.';

            return response()->json([
                'success' => true,
                'message' => $msg,
                'email' => $emailIdentifier
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'envoi du code. Aucun moyen d\'envoi disponible.'
        ], 500);
    }

    /**
     * Mot de passe oublié : Vérification OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required',
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
            'email' => 'required',
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
        $user = User::where('email', $email)
            ->orWhere('contact', $email)
            ->orWhere('code_id', $email)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur introuvable.'
            ], 404);
        }

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

    /**
     * Connexion / Inscription via Google (API)
     */
    public function googleAuth(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'google_id' => 'required|string',
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|string',
            'google_token' => 'nullable|string',
            'fcm_token' => 'nullable|string',
            'nom_device' => 'nullable|string|max:255',
        ]);

        try {
            // Rechercher l'utilisateur par google_id ou par email
            $user = User::where('google_id', $request->google_id)
                        ->orWhere('email', $request->email)
                        ->first();

            $userData = [
                'google_id' => $request->google_id,
                'google_token' => $request->google_token,
                'name' => $request->name,
                'prenom' => $request->prenom ?? ($user ? $user->prenom : ''),
                'email' => $request->email,
            ];

            // Ne mettre à jour le contact que s'il est fourni (pour ne pas écraser par null)
            if ($request->filled('contact')) {
                $userData['contact'] = $request->contact;
            }

            // Ne mettre à jour l'avatar que s'il est fourni
            if ($request->filled('avatar_url')) {
                $userData['photo_profile_path'] = $request->avatar_url;
            }

            if ($user) {
                // Mise à jour de l'utilisateur existant
                $user->update($userData);
            } else {
                // Création d'un nouvel utilisateur
                $userData['password'] = null; // Pas de mot de passe car authentification Google
                $userData['is_active'] = true;
                $user = User::create($userData);
            }

            // Gérer le token FCM et le device
            if ($request->filled('fcm_token')) {
                $user->update(['fcm_token' => $request->fcm_token]);
            }

            if ($request->filled('nom_device')) {
                $user->update(['nom_device' => $request->nom_device]);
                
                $device = $user->devices()->where('nom_device', $request->nom_device)->first();
                if ($device) {
                    $device->update(['last_login_at' => now(), 'ip_address' => $request->ip()]);
                } else {
                    $user->devices()->create([
                        'nom_device' => $request->nom_device,
                        'last_login_at' => now(),
                        'ip_address' => $request->ip(),
                    ]);
                }
            }

            // Générer le token Sanctum
            $token = $user->createToken('mobile-app')->plainTextToken;

            // Préparer l'URL de la photo de profil
            $photoUrl = $user->photo_profile_path;
            if ($photoUrl && !str_starts_with($photoUrl, 'http')) {
                $photoUrl = asset('storage/' . $photoUrl);
            }

            return response()->json([
                'success' => true,
                'message' => 'Authentification Google réussie',
                'requires_contact' => empty($user->contact),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'contact' => $user->contact,
                    'photo_profile_path' => $photoUrl,
                    'google_id' => $user->google_id,
                    'fcm_token' => $user->fcm_token,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur API Google Auth: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'authentification Google.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour le contact (obligatoire pour Google users)
     */
    public function updateContact(Request $request)
    {
        $request->validate([
            'contact' => 'required|string|regex:/^[0-9]+$/|min:10|unique:users,contact,' . $request->user()->id,
        ], [
            'contact.required' => 'Le numéro de téléphone est obligatoire.',
            'contact.regex' => 'Le format du numéro de téléphone est invalide.',
            'contact.min' => 'Le numéro de téléphone doit contenir au moins 10 chiffres.',
            'contact.unique' => 'Ce numéro de téléphone est déjà utilisé.',
        ]);

        $user = $request->user();
        $user->update([
            'contact' => $request->contact,
            'phone_verified_at' => now(), // Auto-vérifié pour Google, ou on peut forcer la verif OTP si besoin
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Numéro de téléphone mis à jour avec succès.',
            'user' => [
                'id' => $user->id,
                'contact' => $user->contact,
                'name' => $user->name,
            ]
        ]);
    }
}
