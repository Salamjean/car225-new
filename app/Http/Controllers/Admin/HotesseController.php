<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotesse;
use App\Models\Compagnie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


use App\Services\HotesseOtpService;
use App\Mail\HotesseCreatedMail;
use Illuminate\Support\Facades\Mail;

class HotesseController extends Controller
{
    protected $otpService;

    public function __construct(HotesseOtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hotesses = Hotesse::with('compagnie')->latest()->get();
        return view('admin.hotesse.index', compact('hotesses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $compagnies = Compagnie::all();
        return view('admin.hotesse.create', compact('compagnies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:191|unique:hotesses',
            'contact' => 'required|string|max:255',
            'compagnie_id' => 'required|exists:compagnies,id',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('hotesses/profile', 'public');
        }

        // Generate temporary password (will be overwritten by setup process)
        $password = Str::random(16);

        // Format phone numbers to have +225 prefix
        $formatPhone = function($phone) {
            if (!$phone) return null;
            // Remove all non-numeric characters except +
            $phone = preg_replace('/[^0-9+]/', '', $phone);
            if (!str_starts_with($phone, '+')) {
                if (str_starts_with($phone, '225')) {
                    $phone = '+' . $phone;
                } else {
                    $phone = '+225' . $phone;
                }
            }
            return $phone;
        };

        $hotesse = Hotesse::create([
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'contact' => $formatPhone($request->contact),
            'cas_urgence' => $formatPhone($request->cas_urgence),
            'commune' => $request->commune,
            'compagnie_id' => $request->compagnie_id,
            'password' => Hash::make($password),
            'profile_picture' => $profilePicturePath,
            'tickets' => 0,
        ]);

        // Generate and store OTP
        $otpCode = $this->otpService->generateCode();
        $this->otpService->storeOtp($hotesse->email, $otpCode);

        // Get company name
        $compagnie = Compagnie::find($request->compagnie_id);

        // Send email with OTP
        try {
            \Illuminate\Support\Facades\Log::info('STARTING Hotesse Creation Email Process', [
                'hotesse_email' => $hotesse->email,
                'compagnie_id' => $compagnie->id,
                'compagnie_name' => $compagnie->name,
                'otp_code' => $otpCode
            ]);

            Mail::to($hotesse->email)->send(
                new HotesseCreatedMail(
                    [
                        'name' => $hotesse->name,
                        'prenom' => $hotesse->prenom,
                        'email' => $hotesse->email,
                    ],
                    $otpCode,
                    $compagnie->name
                )
            );

            \Illuminate\Support\Facades\Log::info('SUCCESS: Hotesse Creation Email Sent', [
                'email' => $hotesse->email
            ]);

            return redirect()->route('admin.hotesse.index')
                ->with('success', 'Hotesse créée avec succès. Un email avec le code OTP a été envoyé à ' . $hotesse->email);
        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('CRITICAL FAILURE: Hotesse Creation Email Failed', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'hotesse_email' => $hotesse->email
            ]);

            // If email fails, delete the hotesse and show error
            // Should verify if we really want to delete it or just warn user
            // For now, mirroring Caisse/OTP flow, deletion is safer to avoid bad state
            $hotesse->delete();
            return back()->withInput()->with('error', 'Erreur critique lors de l\'envoi de l\'email: ' . $e->getMessage() . '. L\'utilisateur a été supprimé.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Hotesse $hotesse)
    {
        return view('admin.hotesse.show', compact('hotesse'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hotesse $hotesse)
    {
        // Implementation for edit if needed
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hotesse $hotesse)
    {
        // Implementation for update if needed
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hotesse $hotesse)
    {
        if ($hotesse->profile_picture) {
            Storage::disk('public')->delete($hotesse->profile_picture);
        }
        
        $hotesse->delete();

        return redirect()->route('admin.hotesse.index')
            ->with('success', 'Hotesse supprimée avec succès');
    }

    /**
     * Toggle archive status.
     */
    public function toggleArchive(Hotesse $hotesse)
    {
        if ($hotesse->isArchived()) {
            $hotesse->unarchive();
            $message = 'Hotesse réactivée avec succès';
        } else {
            $hotesse->archive();
            $message = 'Hotesse archivée avec succès';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Recharge tickets for the hostess.
     */
    public function recharge(Request $request, Hotesse $hotesse)
    {
        $request->validate([
            'tickets' => 'required|integer|min:1',
        ]);

        $hotesse->addTickets($request->tickets);

        return response()->json([
            'success' => true,
            'message' => 'Tickets rechargés avec succès'
        ]);
    }
}
