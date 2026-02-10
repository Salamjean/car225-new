<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\Caisse;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CaisseAuthController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show OTP verification form
     */
    public function showOtpVerification()
    {
        return view('caisse.auth.verify-otp');
    }

    /**
     * Verify OTP and show password setup form
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
            // Find the caisse
            $caisse = Caisse::where('email', $email)->first();

            if (!$caisse) {
                return back()->withErrors(['email' => 'Aucune caissière trouvée avec cet email.']);
            }

            // Store email in session for password setup
            session(['caisse_email_verified' => $email]);

            return redirect()->route('caisse.auth.setup-password');
        }

        return back()->withErrors(['otp_code' => 'Code OTP invalide ou expiré.']);
    }

    /**
     * Show password setup form
     */
    public function showPasswordSetup()
    {
        if (!session('caisse_email_verified')) {
            return redirect()->route('caisse.auth.verify-otp')
                ->withErrors(['error' => 'Veuillez d\'abord vérifier votre email.']);
        }

        return view('caisse.auth.setup-password');
    }

    /**
     * Setup password for the caisse
     */
    public function setupPassword(Request $request)
    {
        if (!session('caisse_email_verified')) {
            return redirect()->route('caisse.auth.verify-otp')
                ->withErrors(['error' => 'Session expirée. Veuillez recommencer.']);
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = session('caisse_email_verified');
        $caisse = Caisse::where('email', $email)->first();

        if (!$caisse) {
            return back()->withErrors(['error' => 'Caissière introuvable.']);
        }

        // Update password
        $caisse->password = Hash::make($request->password);
        $caisse->save();

        // Clear session
        session()->forget('caisse_email_verified');

        // Auto login

        return redirect()->route('caisse.dashboard')
            ->with('success', 'Votre mot de passe a été défini avec succès ! Bienvenue.');
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('caisse.auth.login');
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

        $credentials = $request->only('email', 'password');

        // Check if caisse exists
        $caisse = Caisse::where('email', $credentials['email'])->first();

        if (!$caisse) {
            return back()->withErrors(['email' => 'Email non reconnu.'])->withInput();
        }

        // Check if caisse is archived
        if ($caisse->isArchived()) {
            return back()->withErrors(['email' => 'Votre compte a été archivé. Contactez votre compagnie.'])->withInput();
        }

        // Check if password looks like temporary password
        if (str_starts_with($caisse->password, Hash::make('temporary_password_')) || 
            Hash::check('temporary_password_' . $caisse->created_at->timestamp, $caisse->password)) {
            return redirect()->route('caisse.auth.verify-otp')
                ->with('info', 'Veuillez d\'abord configurer votre mot de passe en utilisant le code OTP reçu par email.');
        }

        // Attempt login
        if (Auth::guard('caisse')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('caisse.dashboard'));
        }

        return back()->withErrors(['password' => 'Mot de passe incorrect.'])->withInput();
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('caisse')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('caisse.auth.login')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $caisse = Caisse::where('email', $request->email)->first();

        if (!$caisse) {
            return back()->withErrors(['email' => 'Aucune caissière trouvée avec cet email.']);
        }

        // Generate new OTP
        $otpCode = $this->otpService->generateCode();
        $this->otpService->storeOtp($caisse->email, $otpCode);

        // Send email (you would use the CaisseCreatedMail or create a new one)
        Mail::to($caisse->email)->send(new \App\Mail\CaisseCreatedMail(
            [
                'prenom' => $caisse->prenom,
                'name' => $caisse->name,
                'email' => $caisse->email,
            ],
            $otpCode,
            $caisse->compagnie->name ?? 'Votre compagnie'
        ));

        return back()->with('success', 'Un nouveau code OTP a été envoyé à votre adresse email.');
    }
}
