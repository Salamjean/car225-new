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
                'password' => Hash::make(Str::random(12)), // Temporary random password
            ]);

            // Send OTP email
            $otp = OtpVerification::createOtp($gare->email, 'gare');
            try {
                Mail::to($gare->email)->send(new GareOtpMail(
                    $otp->otp,
                    $gare->responsable_nom . ' ' . $gare->responsable_prenom,
                    $gare->email
                ));
            } catch (\Exception $e) {
                // Log error but continue
                \Illuminate\Support\Facades\Log::error('Failed to send OTP email to gare: ' . $e->getMessage());
            }

            return redirect()
                ->route('gare.index')
                ->with('success', 'Gare créée avec succès ! Un email avec le code OTP a été envoyé au responsable.');
        } catch (\Exception $e) {
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
        ]);

        $gare->update($validated);

        return redirect()->route('gare.index')
            ->with('success', 'Gare mise à jour avec succès !');
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
