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
     * Demander la mise à jour (envoi OTP si Google, sinon demande de mot de passe)
     */
    public function requestUpdate(Request $request)
    {
        $user = auth()->user();

        // Validation basique des contacts avant l'envoi
        if ($request->contact && strlen($request->contact) !== 10) {
            return response()->json(['success' => false, 'message' => 'Le contact doit comporter exactement 10 chiffres.', 'errors' => ['contact' => ['Le contact doit comporter exactement 10 chiffres.']]], 422);
        }

        if ($user->google_id) {
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            Cache::put('profile_update_otp_' . $user->id, $otp, now()->addMinutes(10));
            Mail::to($user->email)->send(new ProfileUpdateOtpMail($otp, $user->name));
            
            return response()->json([
                'success' => true,
                'type' => 'otp',
                'message' => 'Un code de confirmation a été envoyé à votre adresse email.'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'type' => 'password',
                'message' => 'Veuillez entrer votre mot de passe pour continuer.'
            ]);
        }
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
        if ($user->google_id) {
            $otp = $request->input('otp_code');
            $cachedOtp = Cache::get('profile_update_otp_' . $user->id);
            if (!$otp || $otp !== $cachedOtp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code de vérification invalide ou expiré.',
                    'errors' => ['otp_code' => ['Code de vérification invalide ou expiré.']]
                ], 422);
            }
            Cache::forget('profile_update_otp_' . $user->id);
        } else {
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
