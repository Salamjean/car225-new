<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Caisse;
use App\Services\OtpService;
use App\Mail\CaisseCreatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CaisseController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Display listing of cashiers
     */
    public function index()
    {
        $compagnie = Auth::guard('compagnie')->user();
        $caisses = $compagnie->caisses()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('compagnie.caisse.index', compact('caisses'));
    }

    /**
     * Show the form for creating a new cashier
     */
    public function create()
    {
        return view('compagnie.caisse.create');
    }

    /**
     * Store a newly created cashier
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'prenom' => 'required|string|max:191',
            'email' => 'required|email|unique:caisses,email',
            'contact' => 'required|string|max:191',
            'cas_urgence' => 'nullable|string|max:191',
            'commune' => 'nullable|string|max:191',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $compagnie = Auth::guard('compagnie')->user();

        // Handle profile picture upload
        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('caisse_profiles', 'public');
        }

        // Create caisse with temporary password (will be changed after OTP verification)
        $caisse = Caisse::create([
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'contact' => $request->contact,
            'cas_urgence' => $request->cas_urgence,
            'commune' => $request->commune,
            'password' => Hash::make('temporary_password_' . time()),
            'profile_picture' => $profilePicturePath,
            'compagnie_id' => $compagnie->id,
            'tickets' => 0,
        ]);

        // Generate and store OTP
        $otpCode = $this->otpService->generateCode();
        $this->otpService->storeOtp($caisse->email, $otpCode);

        // Send email with OTP
        try {
            Mail::to($caisse->email)->send(
                new CaisseCreatedMail(
                    [
                        'name' => $caisse->name,
                        'prenom' => $caisse->prenom,
                        'email' => $caisse->email,
                    ],
                    $otpCode,
                    $compagnie->name
                )
            );

            return redirect()->route('compagnie.caisse.index')
                ->with('success', 'Caissière créée avec succès. Un email avec le code OTP a été envoyé à ' . $caisse->email);
        } catch (\Exception $e) {
            // If email fails, delete the caisse and show error
            $caisse->delete();
            return back()->withInput()->with('error', 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
        }
    }

    /**
     * Recharge tickets for a cashier
     */
    public function recharge(Request $request, Caisse $caisse)
    {
        $request->validate([
            'tickets' => 'required|integer|min:1',
        ]);

        $compagnie = Auth::guard('compagnie')->user();

        // Verify caisse belongs to this company
        if ($caisse->compagnie_id !== $compagnie->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Check if company has enough tickets
        if ($compagnie->tickets < $request->tickets) {
            return response()->json([
                'error' => 'Solde de tickets insuffisant. Vous avez ' . $compagnie->tickets . ' tickets disponibles.'
            ], 400);
        }

        // Deduct from company
        $compagnie->deductTickets($request->tickets, 'Rechargement caisse: ' . $caisse->prenom . ' ' . $caisse->name);

        // Add to caisse
        $caisse->addTickets($request->tickets);

        return response()->json([
            'success' => true,
            'message' => $request->tickets . ' tickets rechargés avec succès pour ' . $caisse->prenom . ' ' . $caisse->name,
            'caisse_tickets' => $caisse->fresh()->tickets,
            'compagnie_tickets' => $compagnie->fresh()->tickets,
        ]);
    }

    /**
     * Archive/Unarchive a cashier
     */
    public function toggleArchive(Caisse $caisse)
    {
        $compagnie = Auth::guard('compagnie')->user();

        // Verify caisse belongs to this company
        if ($caisse->compagnie_id !== $compagnie->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        if ($caisse->isArchived()) {
            $caisse->unarchive();
            $message = 'Caissière réactivée avec succès';
        } else {
            $caisse->archive();
            $message = 'Caissière archivée avec succès';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'archived' => $caisse->fresh()->isArchived(),
        ]);
    }

    /**
     * Delete a cashier
     */
    public function destroy(Caisse $caisse)
    {
        $compagnie = Auth::guard('compagnie')->user();

        // Verify caisse belongs to this company
        if ($caisse->compagnie_id !== $compagnie->id) {
            return redirect()->route('compagnie.caisse.index')->with('error', 'Accès non autorisé');
        }

        // Delete profile picture if exists
        if ($caisse->profile_picture) {
            Storage::disk('public')->delete($caisse->profile_picture);
        }

        $caisse->delete();

        return redirect()->route('compagnie.caisse.index')
            ->with('success', 'Caissière supprimée avec succès');
    }
}
