<?php

namespace App\Http\Controllers\Api\Hotesse;

use App\Http\Controllers\Controller;
use App\Models\Hotesse;
use App\Services\HotesseOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\HotesseCreatedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HotesseAuthController extends Controller
{
    protected $otpService;

    public function __construct(HotesseOtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $hotesse = Hotesse::where('email', $request->email)->first();

        if (!$hotesse) {
            return response()->json([
                'success' => false,
                'message' => 'Email non reconnu.'
            ], 404);
        }

        // Check if hotesse is archived
        if ($hotesse->isArchived()) {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte a été archivé. Contactez votre compagnie.'
            ], 403);
        }
        
        // Check if password looks like temporary password
        if (str_starts_with($hotesse->password, Hash::make('temporary_password_')) || 
            Hash::check('temporary_password_' . $hotesse->created_at->timestamp, $hotesse->password)) {
            
            // Suggest OTP Verification Flow path
            return response()->json([
                'success' => false,
                'requires_otp_setup' => true,
                'message' => 'Veuillez d\'abord configurer votre mot de passe en utilisant le code OTP reçu par email.',
                'email' => $hotesse->email
            ], 403);
        }

        if (!Hash::check($request->password, $hotesse->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mot de passe incorrect.'
            ], 401);
        }

        // Authenticate using Sanctum
        $token = $hotesse->createToken('HotesseApiToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'token' => $token,
            'hotesse' => $hotesse
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vous avez été déconnecté avec succès.'
        ]);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|string|size:6',
        ]);

        $email = $request->email;
        $otpCode = $request->otp_code;

        // Verify OTP
        if ($this->otpService->verifyOtp($email, $otpCode)) {
            $hotesse = Hotesse::where('email', $email)->first();

            if (!$hotesse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune hôtesse trouvée avec cet email.'
                ], 404);
            }

            // Return a temporary token or flag to allow password setup
            // For stateless API, we can issue a temporary short-lived token with a specific ability
            $setupToken = $hotesse->createToken('PasswordSetupToken', ['setup-password'])->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'OTP vérifié. Vous pouvez maintenant configurer votre mot de passe.',
                'setup_token' => $setupToken
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Code OTP invalide ou expiré.'
        ], 400);
    }

    /**
     * Setup password for the hotesse after OTP verification
     */
    public function setupPassword(Request $request)
    {
        // The user must be authenticated with the temporary 'setup-password' token
        if (!$request->user()->tokenCan('setup-password')) {
            return response()->json([
                'success' => false,
                'message' => 'Action non autorisée.'
            ], 403);
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $hotesse = $request->user();

        // Update password
        $hotesse->password = Hash::make($request->password);
        $hotesse->save();

        // Revoke the setup token
        $hotesse->currentAccessToken()->delete();

        // Automatically log them in by issuing a real auth token
        $token = $hotesse->createToken('HotesseApiToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Votre mot de passe a été défini avec succès ! Bienvenue.',
            'token' => $token,
            'hotesse' => $hotesse
        ]);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $hotesse = Hotesse::where('email', $request->email)->first();

        if (!$hotesse) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune hôtesse trouvée avec cet email.'
            ], 404);
        }

        // Generate new OTP
        $otpCode = $this->otpService->generateCode();
        $this->otpService->storeOtp($hotesse->email, $otpCode);

        // Send email
        Log::info('Attempting to resend Hotesse OTP from API', [
            'email' => $hotesse->email,
            'compagnie' => $hotesse->compagnie->name ?? 'N/A'
        ]);

        Mail::to($hotesse->email)->send(new HotesseCreatedMail(
            [
                'prenom' => $hotesse->prenom,
                'name' => $hotesse->name,
                'email' => $hotesse->email,
            ],
            $otpCode,
            $hotesse->compagnie->name ?? 'Votre compagnie'
        ));

        return response()->json([
            'success' => true,
            'message' => 'Un nouveau code OTP a été envoyé à votre adresse email.'
        ]);
    }
}
