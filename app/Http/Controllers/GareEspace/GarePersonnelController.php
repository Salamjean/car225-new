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
            ->whereNull('archived_at')
            ->orderBy('type_personnel')
            ->orderBy('name')
            ->get();
            
        $archivedCount = Personnel::where('gare_id', $gare->id)->whereNotNull('archived_at')->count();

        return view('gare-espace.personnel.index', compact('personnels', 'archivedCount'));
    }

    public function create()
    {
        return view('gare-espace.personnel.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'prenom' => 'required|string|min:2|max:255',
            'type_personnel' => 'required|string|in:Chauffeur,Convoyeur',
            'email' => 'required|email|unique:personnels,email',
            'contact' => 'required|string|max:15',
            'country_code' => 'required|string|max:10',
            'contact_urgence' => 'required|string|max:15',
            'nom_urgence' => 'required|string|max:255',
            'lien_parente_urgence' => 'required|string|max:100',
            'country_code_urgence' => 'required|string|max:10',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

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
                'nom_urgence' => $validatedData['nom_urgence'],
                'lien_parente_urgence' => $validatedData['lien_parente_urgence'],
                'profile_image' => $profileImagePath,
                'compagnie_id' => $gare->compagnie_id,
                'gare_id' => $gare->id,
                'password' => Hash::make(Str::random(12)),
                'statut' => 'indisponible',
            ]);

            if ($validatedData['type_personnel'] === 'Chauffeur') {
                $otp = OtpVerification::createOtp($personnel->email, 'chauffeur');
                Mail::to($personnel->email)->send(new ChauffeurOtpMail($otp->otp, $personnel->name . ' ' . $personnel->prenom, $personnel->email, $personnel->code_id));
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('gare-espace.personnel.index')->with('success', 'Personnel créé avec succès!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            Log::error('Erreur creation personnel: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur creation: ' . $e->getMessage());
        }
    }

    public function edit(Personnel $personnel)
    {
        return view('gare-espace.personnel.edit', compact('personnel'));
    }

    public function update(Request $request, Personnel $personnel)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'prenom' => 'required|string|min:3|max:255',
            'type_personnel' => 'required|string|in:Chauffeur,Convoyeur',
            'email' => 'required|email|unique:personnels,email,' . $personnel->id,
            'contact' => 'required|string|max:15',
            'contact_urgence' => 'required|string|max:15',
            'nom_urgence' => 'required|string|max:255',
            'lien_parente_urgence' => 'required|string|max:100',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $personnel->name = $validatedData['name'];
            $personnel->prenom = $validatedData['prenom'];
            $personnel->type_personnel = $validatedData['type_personnel'];
            $personnel->email = $validatedData['email'];
            $personnel->contact = $validatedData['contact'];
            $personnel->contact_urgence = $validatedData['contact_urgence'];
            $personnel->nom_urgence = $validatedData['nom_urgence'];
            $personnel->lien_parente_urgence = $validatedData['lien_parente_urgence'];

            if ($request->hasFile('profile_image')) {
                if ($personnel->profile_image) Storage::disk('public')->delete($personnel->profile_image);
                $personnel->profile_image = $request->file('profile_image')->store('profiles', 'public');
            }

            $personnel->save();
            return redirect()->route('gare-espace.personnel.index')->with('success', 'Personnel mis à jour avec succès!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur mise à jour: ' . $e->getMessage());
        }
    }

    public function destroy(Personnel $personnel)
    {
        $personnel->update(['archived_at' => now()]);
        return redirect()->route('gare-espace.personnel.index')->with('success', 'Personnel archivé avec succès!');
    }
}
