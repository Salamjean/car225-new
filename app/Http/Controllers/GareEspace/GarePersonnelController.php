<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use App\Models\OtpVerification;
use App\Mail\ChauffeurOtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class GarePersonnelController extends Controller
{
    public function index()
    {
        $gare = Auth::guard('gare')->user();
        $personnels = Personnel::where('gare_id', $gare->id)
            ->orderBy('type_personnel')
            ->orderBy('name')
            ->get();

        return view('gare-espace.personnel.index', compact('personnels'));
    }

    public function create()
    {
        return view('gare-espace.personnel.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'prenom' => 'required|string|min:3|max:255',
            'type_personnel' => 'required|string|in:Chauffeur,Convoyeur',
            'email' => 'required|email|unique:personnels,email',
            'contact' => 'required|string|max:10|unique:personnels,contact',
            'country_code' => 'required|string|max:10',
            'contact_urgence' => 'required|string|max:10',
            'country_code_urgence' => 'required|string|max:10',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'name.min' => 'Le nom doit contenir au moins 3 caractères.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.min' => 'Le prénom doit contenir au moins 3 caractères.',
            'type_personnel.required' => 'Le type de personnel est obligatoire.',
            'type_personnel.in' => 'Le type de personnel doit être Chauffeur ou Convoyeur.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'contact.required' => 'Le contact personnel est obligatoire.',
            'contact.max' => 'Le contact doit contenir au maximum 10 chiffres.',
            'contact.unique' => 'Ce contact est déjà utilisé.',
            'contact_urgence.required' => 'Le contact d\'urgence est obligatoire.',
            'profile_image.image' => 'Le fichier doit être une image.',
            'profile_image.mimes' => 'L\'image doit être au format jpeg, png, jpg ou gif.',
            'profile_image.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ]);

        try {
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $imageName = 'profile_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $profileImagePath = $image->storeAs('profiles', $imageName, 'public');
            }

            $contact = $validatedData['country_code'] . '' . $validatedData['contact'];
            $contact_urgent = $validatedData['country_code_urgence'] . '' . $validatedData['contact_urgence'];

            $gare = Auth::guard('gare')->user();

            $personnel = Personnel::create([
                'name' => $validatedData['name'],
                'prenom' => $validatedData['prenom'],
                'type_personnel' => $validatedData['type_personnel'],
                'email' => $validatedData['email'],
                'contact' => $contact,
                'contact_urgence' => $contact_urgent,
                'profile_image' => $profileImagePath,
                'compagnie_id' => $gare->compagnie_id,
                'gare_id' => $gare->id,
                'password' => Hash::make(Str::random(12)),
                'statut' => 'indisponible',
            ]);

            // Envoi de l'OTP si c'est un chauffeur
            if ($validatedData['type_personnel'] === 'Chauffeur') {
                $otp = OtpVerification::createOtp($personnel->email, 'chauffeur');
                try {
                    Mail::to($personnel->email)->send(new ChauffeurOtpMail($otp->otp, $personnel->name . ' ' . $personnel->prenom, $personnel->email));
                } catch (\Exception $e) {
                    // Log error but continue
                }
            }

            return redirect()
                ->route('gare-espace.personnel.index')
                ->with('success', 'Personnel créé avec succès!');
        } catch (\Exception $e) {
            if (isset($profileImagePath) && Storage::disk('public')->exists($profileImagePath)) {
                Storage::disk('public')->delete($profileImagePath);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du personnel: ' . $e->getMessage());
        }
    }
}
