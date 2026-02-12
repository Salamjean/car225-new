<?php

namespace App\Http\Controllers\Hotesse;

use App\Http\Controllers\Controller;
use App\Models\Hotesse;

use App\Services\HotesseOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\HotesseCreatedMail;

class HotesseAuthController extends Controller
{
    protected $otpService;

    public function __construct(HotesseOtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show OTP verification form
     */
    public function showOtpVerification(Request $request)
    {
        return view('hotesse.auth.verify-otp', ['email' => $request->query('email')]);
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
            // Find the hotesse
            $hotesse = Hotesse::where('email', $email)->first();

            if (!$hotesse) {
                return back()->withErrors(['email' => 'Aucune hôtesse trouvée avec cet email.']);
            }

            // Store email in session for password setup
            session(['hotesse_email_verified' => $email]);

            return redirect()->route('hotesse.auth.setup-password');
        }

        return back()->withErrors(['otp_code' => 'Code OTP invalide ou expiré.']);
    }

    /**
     * Show password setup form
     */
    public function showPasswordSetup()
    {
        if (!session('hotesse_email_verified')) {
            return redirect()->route('hotesse.auth.verify-otp')
                ->withErrors(['error' => 'Veuillez d\'abord vérifier votre email.']);
        }

        return view('hotesse.auth.setup-password');
    }

    /**
     * Setup password for the hotesse
     */
    public function setupPassword(Request $request)
    {
        if (!session('hotesse_email_verified')) {
            return redirect()->route('hotesse.auth.verify-otp')
                ->withErrors(['error' => 'Session expirée. Veuillez recommencer.']);
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = session('hotesse_email_verified');
        $hotesse = Hotesse::where('email', $email)->first();

        if (!$hotesse) {
            return back()->withErrors(['error' => 'Hôtesse introuvable.']);
        }

        // Update password
        $hotesse->password = Hash::make($request->password);
        $hotesse->save();

        // Clear session
        session()->forget('hotesse_email_verified');

        // Auto login
        Auth::guard('hotesse')->login($hotesse);

        return redirect()->route('hotesse.dashboard')
            ->with('success', 'Votre mot de passe a été défini avec succès ! Bienvenue.');
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('hotesse.auth.login');
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

        // Check if hotesse exists
        $hotesse = Hotesse::where('email', $credentials['email'])->first();

        if (!$hotesse) {
            return back()->withErrors(['email' => 'Email non reconnu.'])->withInput();
        }

        // Check if hotesse is archived
        if ($hotesse->isArchived()) {
            return back()->withErrors(['email' => 'Votre compte a été archivé. Contactez votre compagnie.'])->withInput();
        }
        
        // Check if password looks like temporary password
        if (str_starts_with($hotesse->password, Hash::make('temporary_password_')) || 
            Hash::check('temporary_password_' . $hotesse->created_at->timestamp, $hotesse->password)) {
            return redirect()->route('hotesse.auth.verify-otp', ['email' => $hotesse->email])
                ->with('info', 'Veuillez d\'abord configurer votre mot de passe en utilisant le code OTP reçu par email.');
        }

        // Attempt login

        // Attempt login
        if (Auth::guard('hotesse')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('hotesse.dashboard'));
        }

        return back()->withErrors(['password' => 'Mot de passe incorrect.'])->withInput();
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('hotesse')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('hotesse.auth.login')
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

        $hotesse = Hotesse::where('email', $request->email)->first();

        if (!$hotesse) {
            return back()->withErrors(['email' => 'Aucune hôtesse trouvée avec cet email.']);
        }

        // Generate new OTP
        $otpCode = $this->otpService->generateCode();
        $this->otpService->storeOtp($hotesse->email, $otpCode);

        // Send email
        \Mail::to($hotesse->email)->send(new HotesseCreatedMail(
            [
                'prenom' => $hotesse->prenom,
                'name' => $hotesse->name,
                'email' => $hotesse->email,
            ],
            $otpCode,
            $hotesse->compagnie->name ?? 'Votre compagnie'
        ));

        return back()->with('success', 'Un nouveau code OTP a été envoyé à votre adresse email.');
    }
}
