<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProfileUpdateOtpMail;

class ProfileController extends Controller
{
    /**
     * Afficher la page de profil
     */
    public function index()
    {
        $user = auth()->user();
        return view('user.profile.profile', compact('user'));
    }

    /**
     * Demander la mise à jour (envoi OTP par SMS si le contact change, ou OTP Email si Google, sinon demande de mot de passe)
     */
    public function requestUpdate(Request $request)
    {
        $user = auth()->user();

        // Validation basique des contacts avant l'envoi
        if ($request->contact && strlen($request->contact) !== 10) {
            return response()->json(['success' => false, 'message' => 'Le contact doit comporter exactement 10 chiffres.', 'errors' => ['contact' => ['Le contact doit comporter exactement 10 chiffres.']]], 422);
        }

        $smsService = app(\App\Services\SmsService::class);

        // CAS 1 : Le numéro de téléphone a changé
        if ($request->filled('contact') && $request->contact !== $user->contact) {
            $res = $smsService->sendOtp($request->contact, $user->prenom, $user->name);
            
            if ($res['success']) {
                return response()->json([
                    'success' => true,
                    'type' => 'otp',
                    'message' => 'Un code de confirmation a été envoyé par SMS au nouveau numéro ' . $request->contact
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi du SMS de confirmation.'
                ], 500);
            }
        }

        // CAS 2 : Utilisateur Google (OTP par Email)
        if ($user->google_id) {
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            Cache::put('profile_update_otp_' . $user->id, $otp, now()->addMinutes(10));
            Mail::to($user->email)->send(new ProfileUpdateOtpMail($otp, $user->name));
            
            return response()->json([
                'success' => true,
                'type' => 'otp',
                'message' => 'Un code de confirmation a été envoyé à votre adresse email.'
            ]);
        } 
        
        // CAS 3 : Utilisateur classique sans changement de numéro (Mot de passe)
        return response()->json([
            'success' => true,
            'type' => 'password',
            'message' => 'Veuillez entrer votre mot de passe pour continuer.'
        ]);
    }

    /**
     * Mettre à jour les informations du profil
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'contact' => 'nullable|string|digits:10',
            'nom_urgence' => 'nullable|string|max:255',
            'lien_parente_urgence' => 'nullable|string|max:255',
            'contact_urgence' => 'nullable|string|digits:10|different:contact',
        ], [
            'contact.digits' => 'Le contact doit comporter exactement 10 chiffres.',
            'contact_urgence.digits' => 'Le contact d\'urgence doit comporter exactement 10 chiffres.',
            'contact_urgence.different' => 'Le contact d\'urgence doit être différent de votre contact principal.',
        ]);

        // Vérification de sécurité
        $smsService = app(\App\Services\SmsService::class);

        // Si le contact a été modifié, on DOIT vérifier l'OTP SMS
        if ($request->filled('contact') && $request->contact !== $user->contact) {
            $otp = $request->input('otp_code');
            if (!$otp || !$smsService->verifyOtp($request->contact, $otp)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code de confirmation SMS invalide ou expiré.',
                    'errors' => ['otp_code' => ['Code de confirmation SMS invalide ou expiré.']]
                ], 422);
            }
            // Nettoyage après vérification réussie
            $smsService->deleteOtp($request->contact);

        } elseif ($user->google_id) {
            // Utilisateur Google sans changement de numéro -> OTP Email
            $otp = $request->input('otp_code');
            $cachedOtp = Cache::get('profile_update_otp_' . $user->id);
            if (!$otp || $otp !== $cachedOtp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code de vérification email invalide ou expiré.',
                    'errors' => ['otp_code' => ['Code de vérification email invalide ou expiré.']]
                ], 422);
            }
            Cache::forget('profile_update_otp_' . $user->id);

        } else {
            // Utilisateur classique sans changement de numéro -> Mot de passe
            $password = $request->input('confirm_password');
            if (!$password || !Hash::check($password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mot de passe incorrect.',
                    'errors' => ['confirm_password' => ['Mot de passe incorrect.']]
                ], 422);
            }
        }

        try {
            // Empêcher la modification de l'email pour les utilisateurs Google
            if ($user->google_id) {
                unset($validated['email']);
            }

            // Since we merged nom and prenom into nom_urgence, we force prenom_urgence to null
            $validated['prenom_urgence'] = null;
            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du profil'
            ], 500);
        }
    }

    /**
     * Mettre à jour les informations d'urgence (Contact, Nom urgence, Contact urgence)
     * Utilisé lors de la réservation sans demande de mot de passe/OTP
     */
    public function updateEmergencyContact(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'contact' => 'nullable|string|digits:10',
            'nom_urgence' => 'nullable|string|max:255',
            'lien_parente_urgence' => 'nullable|string|max:255',
            'contact_urgence' => 'nullable|string|digits:10|different:contact',
        ], [
            'contact.digits' => 'Le contact doit comporter exactement 10 chiffres.',
            'contact_urgence.digits' => 'Le contact d\'urgence doit comporter exactement 10 chiffres.',
            'contact_urgence.different' => 'Le contact d\'urgence doit être différent de votre contact principal.',
        ]);

        try {
            // Uniquement mettre à jour les champs fournis s'ils sont vides actuellement
            // ou si l'utilisateur les a modifiés dans le pop-up
            $updateData = [];
            if ($request->has('contact')) $updateData['contact'] = $validated['contact'];
            if ($request->has('nom_urgence')) $updateData['nom_urgence'] = $validated['nom_urgence'];
            if ($request->has('lien_parente_urgence')) $updateData['lien_parente_urgence'] = $validated['lien_parente_urgence'];
            if ($request->has('contact_urgence')) $updateData['contact_urgence'] = $validated['contact_urgence'];

            if (!empty($updateData)) {
                $user->update($updateData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du profil'
            ], 500);
        }
    }

    /**
     * Changer le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();

        // Vérifier l'ancien mot de passe
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Le mot de passe actuel est incorrect'
            ], 422);
        }

        try {
            $user->update([
                'password' => Hash::make($validated['new_password'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe modifié avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du changement de mot de passe'
            ], 500);
        }
    }

    /**
     * Mettre à jour la photo de profil
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = auth()->user();

        try {
            // Supprimer l'ancienne photo si elle existe
            if ($user->photo_profile_path && Storage::disk('public')->exists($user->photo_profile_path)) {
                Storage::disk('public')->delete($user->photo_profile_path);
            }

            // Sauvegarder la nouvelle photo
            $path = $request->file('photo')->store('profile-photos', 'public');
            
            $user->update(['photo_profile_path' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil mise à jour avec succès',
                'photo_url' => Storage::url($path)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement de la photo'
            ], 500);
        }
    }
}
