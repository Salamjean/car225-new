<?php

namespace App\Http\Controllers\Hotesse;

use App\Http\Controllers\Controller;
use App\Models\Hotesse;

use App\Services\HotesseOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
            'login' => 'required',
            'password' => 'required|string',
        ], [
            'login.required' => 'L\'identifiant ou l\'email est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $loginValue = $request->input('login');

        // Check if hotesse exists by email or code_id
        $hotesse = Hotesse::where('email', $loginValue)
            ->orWhere('code_id', $loginValue)
            ->first();

        if (!$hotesse) {
            return back()->withErrors(['login' => 'Identifiant non reconnu.'])->withInput($request->except('password'));
        }

        // Check if hotesse is archived
        if ($hotesse->isArchived()) {
            return back()->withErrors(['login' => 'Votre compte a été archivé. Contactez votre compagnie.'])->withInput($request->except('password'));
        }
        
        // Check for temporary password (using a more reliable check)
        // If it was recently created and they are using the default temporary password logic
        if (Hash::check('temporary_password_' . $hotesse->created_at->timestamp, $hotesse->password)) {
            return redirect()->route('hotesse.auth.verify-otp', ['email' => $hotesse->email ?? $hotesse->contact ?? $hotesse->code_id])
                ->with('info', 'Veuillez d\'abord configurer votre mot de passe en utilisant le code OTP reçu.');
        }

        // Determine which field to use for authentication
        $field = 'email';
        if ($hotesse->contact === $loginValue) {
            $field = 'contact';
        } elseif ($hotesse->code_id === $loginValue) {
            $field = 'code_id';
        }

        if (Auth::guard('hotesse')->attempt([$field => $loginValue, 'password' => $request->password], $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Si on a été redirigé vers hotesse/login à cause d'un manque d'accès, on va au dashboard au lieu de 'intended' (qui pourrait être une page user)
            $intended = session()->get('url.intended');
            if ($intended && str_contains($intended, '/hotesse/')) {
                return redirect()->intended(route('hotesse.dashboard'));
            }
            
            return redirect()->route('hotesse.dashboard')->with('success', 'Bienvenue sur votre espace hôtesse!');
        }

        return back()->withErrors(['password' => 'Mot de passe incorrect.'])->withInput($request->except('password'));
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
        \Illuminate\Support\Facades\Log::info('Attempting to resend Hotesse OTP', [
            'email' => $hotesse->email,
            'compagnie' => $hotesse->compagnie->name ?? 'N/A'
        ]);

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
