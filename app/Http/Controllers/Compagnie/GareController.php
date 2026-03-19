<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Gare;
use App\Models\OtpVerification;
use App\Mail\GareOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GareController extends Controller
{
    public function index()
    {
        $compagnieId = Auth::guard('compagnie')->id();
        $gares = Gare::where('compagnie_id', $compagnieId)->latest()->get();
        return view('compagnie.gare.index', compact('gares'));
    }

    public function create()
    {
        return view('compagnie.gare.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_gare' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'responsable_nom' => 'required|string|min:2|max:255',
            'responsable_prenom' => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:gares,email',
            'contact' => 'required|string|max:10',
            'country_code' => 'required|string|max:10',
            'contact_urgence' => 'nullable|string|max:10',
            'country_code_urgence' => 'nullable|string|max:10',
            'commune' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nom_gare.required' => 'Le nom de la gare est obligatoire.',
            'responsable_nom.required' => 'Le nom du responsable est obligatoire.',
            'responsable_prenom.required' => 'Le prénom du responsable est obligatoire.',
            'email.required' => "L'email est obligatoire.",
            'email.email' => "L'email doit être une adresse valide.",
            'email.unique' => 'Cet email est déjà utilisé.',
            'contact.required' => 'Le contact principal est obligatoire.',
        ]);

        try {
            // Handle profile image upload
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $imageName = 'gare_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $profileImagePath = $image->storeAs('gares', $imageName, 'public');
            }

            $contact = $validated['country_code'] . '' . $validated['contact'];
            $contactUrgence = null;
            if (!empty($validated['contact_urgence'])) {
                $contactUrgence = ($validated['country_code_urgence'] ?? '+225') . '' . $validated['contact_urgence'];
            }

            // Create the gare
            $gare = Gare::create([
                'nom_gare' => $validated['nom_gare'],
                'ville' => $validated['ville'],
                'adresse' => $validated['adresse'],
                'responsable_nom' => $validated['responsable_nom'],
                'responsable_prenom' => $validated['responsable_prenom'],
                'email' => $validated['email'],
                'contact' => $contact,
                'contact_urgence' => $contactUrgence,
                'commune' => $validated['commune'] ?? null,
                'profile_image' => $profileImagePath,
                'compagnie_id' => Auth::guard('compagnie')->id(),
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'password' => Hash::make(Str::random(12)), // Temporary random password
            ]);

            // Send OTP email
            $otp = OtpVerification::createOtp($gare->email, 'gare');

            \Illuminate\Support\Facades\Log::info('Attempting to send Gare OTP', [
                'email' => $gare->email,
                'mailer' => config('mail.default'),
                'from' => config('mail.from')
            ]);

            Mail::to($gare->email)->send(new GareOtpMail(
                $otp->otp,
                $gare->responsable_nom . ' ' . $gare->responsable_prenom,
                $gare->email,
                $gare->code_id
            ));

            return redirect()
                ->route('gare.index')
                ->with('success', 'Gare créée avec succès ! Un email avec le code OTP a été envoyé au responsable.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gare Creation Error: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());

            if (isset($profileImagePath) && Storage::disk('public')->exists($profileImagePath)) {
                Storage::disk('public')->delete($profileImagePath);
            }
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la gare: ' . $e->getMessage());
        }
    }

    public function edit(Gare $gare)
    {
        if ($gare->compagnie_id !== Auth::guard('compagnie')->id()) {
            abort(403);
        }
        return view('compagnie.gare.edit', compact('gare'));
    }

    public function update(Request $request, Gare $gare)
    {
        if ($gare->compagnie_id !== Auth::guard('compagnie')->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'nom_gare' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'responsable_nom' => 'required|string|min:2|max:255',
            'responsable_prenom' => 'required|string|min:2|max:255',
            'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('gares', 'email')->ignore($gare->id)],
            'contact' => 'required|string|max:10',
            'contact_urgence' => 'nullable|string|max:10',
            'commune' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nom_gare.required' => 'Le nom de la gare est obligatoire.',
            'responsable_nom.required' => 'Le nom du responsable est obligatoire.',
            'responsable_prenom.required' => 'Le prénom du responsable est obligatoire.',
            'email.required' => "L'email est obligatoire.",
            'email.email' => "L'email doit être une adresse valide.",
            'email.unique' => 'Cet email est déjà utilisé.',
            'contact.required' => 'Le contact principal est obligatoire.',
        ]);

        // Handle profile image upload if present
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($gare->profile_image && Storage::disk('public')->exists($gare->profile_image)) {
                Storage::disk('public')->delete($gare->profile_image);
            }
            $image = $request->file('profile_image');
            $imageName = 'gare_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $validated['profile_image'] = $image->storeAs('gares', $imageName, 'public');
        }

        // Check if email has changed
        if ($validated['email'] !== $gare->email) {
            try {
                // Generate OTP for verification
                $otp = OtpVerification::createOtp($validated['email'], 'gare_email_update');

                // Send OTP to NEW email
                \Illuminate\Support\Facades\Log::info('Sending email update OTP to Gare', [
                    'new_email' => $validated['email'],
                    'old_email' => $gare->email
                ]);

                Mail::to($validated['email'])->send(new GareOtpMail(
                    $otp->otp,
                    $validated['responsable_nom'] . ' ' . $validated['responsable_prenom'],
                    $validated['email'],
                    $gare->code_id
                ));

                // Store pending data in session
                session([
                    'gare_update_pending' => [
                        'gare_id' => $gare->id,
                        'data' => $validated
                    ]
                ]);

                return redirect()->route('gare.verify-email-update', $gare->id)
                    ->with('info', "L'adresse email a été modifiée. Un code de vérification a été envoyé à " . $validated['email']);

            } catch (\Exception $e) {
                 \Illuminate\Support\Facades\Log::error('Gare Email Update OTP Error: ' . $e->getMessage());
                return back()->withInput()->with('error', 'Erreur lors de l\'envoi du code de vérification: ' . $e->getMessage());
            }
        }

        // No email change, standard update
        $gare->update($validated);

        return redirect()->route('gare.index')
            ->with('success', 'Gare mise à jour avec succès !');
    }

    public function showEmailVerificationForm(Gare $gare)
    {
        if ($gare->compagnie_id !== Auth::guard('compagnie')->id()) {
            abort(403);
        }

        // Ensure we have pending update data
        if (!session()->has('gare_update_pending') || session('gare_update_pending')['gare_id'] != $gare->id) {
            return redirect()->route('gare.edit', $gare->id)
                ->with('error', 'Aucune modification d\'email en attente.');
        }

        $pendingData = session('gare_update_pending')['data'];
        
        return view('compagnie.gare.verify-email-update', compact('gare', 'pendingData'));
    }

    public function verifyEmailUpdate(Request $request, Gare $gare)
    {
        if ($gare->compagnie_id !== Auth::guard('compagnie')->id()) {
            abort(403);
        }

        $request->validate([
            'otp' => 'required|string|size:6'
        ]);

        if (!session()->has('gare_update_pending') || session('gare_update_pending')['gare_id'] != $gare->id) {
            return redirect()->route('gare.edit', $gare->id)
                ->with('error', 'Session expirée. Veuillez réessayer.');
        }

        $pendingData = session('gare_update_pending')['data'];
        $email = $pendingData['email'];

        // Verify OTP
        if (OtpVerification::verify($email, $request->otp, 'gare_email_update')) {
            // Update Gare with all pending data
            $gare->update($pendingData);

            // Clear session
            session()->forget('gare_update_pending');

            return redirect()->route('gare.index')
                ->with('success', 'Email vérifié et gare mise à jour avec succès !');
        }

        return back()->with('error', 'Code OTP invalide ou expiré.');
    }

    public function destroy(Gare $gare)
    {
        if ($gare->compagnie_id !== Auth::guard('compagnie')->id()) {
            abort(403);
        }

        $gare->delete();

        return redirect()->route('gare.index')
            ->with('success', 'Gare supprimée avec succès !');
    }
}
