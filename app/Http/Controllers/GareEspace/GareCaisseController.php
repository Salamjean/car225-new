<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Models\Caisse;
use App\Services\OtpService;
use App\Mail\CaisseCreatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GareCaisseController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function index()
    {
        $gare = Auth::guard('gare')->user();
        $caisses = Caisse::where('gare_id', $gare->id)
            ->whereNull('archived_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('gare-espace.caisse.index', compact('caisses'));
    }

    public function create()
    {
        return view('gare-espace.caisse.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'prenom' => 'required|string|max:191',
            'email' => 'required|email|unique:caisses,email',
            'contact' => 'required|string|max:191',
            'cas_urgence' => 'required|string|max:191',
            'nom_urgence' => 'required|string|max:255',
            'lien_parente_urgence' => 'required|string|max:100',
            'commune' => 'nullable|string|max:191',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $gare = Auth::guard('gare')->user();

        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('caisse_profiles', 'public');
        }

        $caisse = Caisse::create([
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'contact' => $request->contact,
            'cas_urgence' => $request->cas_urgence,
            'nom_urgence' => $request->nom_urgence,
            'lien_parente_urgence' => $request->lien_parente_urgence,
            'commune' => $request->commune,
            'password' => Hash::make('temporary_password_' . time()),
            'profile_picture' => $profilePicturePath,
            'compagnie_id' => $gare->compagnie_id,
            'gare_id' => $gare->id,
        ]);

        // Generate and store OTP
        $otpCode = $this->otpService->generateCode();
        $this->otpService->storeOtp($caisse->email, $otpCode);

        // Send email with OTP
        try {
            $compagnie = $gare->compagnie;
            Mail::to($caisse->email)->send(
                new CaisseCreatedMail(
                    [
                        'name' => $caisse->name,
                        'prenom' => $caisse->prenom,
                        'email' => $caisse->email,
                        'code_id' => $caisse->code_id,
                    ],
                    $otpCode,
                    $compagnie->name ?? 'Compagnie'
                )
            );

            return redirect()->route('gare-espace.caisse.index')
                ->with('success', 'Caissière créée avec succès. Un email avec le code OTP a été envoyé à ' . $caisse->email);
        } catch (\Exception $e) {
            $caisse->delete();
            return back()->withInput()->with('error', 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
        }
    }
}
