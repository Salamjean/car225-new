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
            
        $archivedCount = Caisse::where('gare_id', $gare->id)->whereNotNull('archived_at')->count();

        return view('gare-espace.caisse.index', compact('caisses', 'archivedCount'));
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

        $otpCode = $this->otpService->generateCode();
        $this->otpService->storeOtp($caisse->email, $otpCode);

        try {
            Mail::to($caisse->email)->send(new CaisseCreatedMail([
                'name' => $caisse->name,
                'prenom' => $caisse->prenom,
                'email' => $caisse->email,
                'code_id' => $caisse->code_id,
            ], $otpCode, $gare->compagnie->name ?? 'Compagnie'));

            return redirect()->route('gare-espace.caisse.index')->with('success', 'Caissière créée avec succès.');
        } catch (\Exception $e) {
            $caisse->delete();
            return back()->withInput()->with('error', 'Erreur mail: ' . $e->getMessage());
        }
    }

    public function edit(Caisse $caisse)
    {
        return view('gare-espace.caisse.edit', compact('caisse'));
    }

    public function update(Request $request, Caisse $caisse)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'prenom' => 'required|string|max:191',
            'email' => 'required|email|unique:caisses,email,' . $caisse->id,
            'contact' => 'required|string|max:191',
            'cas_urgence' => 'required|string|max:191',
            'nom_urgence' => 'required|string|max:255',
            'lien_parente_urgence' => 'required|string|max:100',
            'commune' => 'nullable|string|max:191',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        try {
            $caisse->update($request->except('profile_picture'));

            if ($request->hasFile('profile_picture')) {
                if ($caisse->profile_picture) Storage::disk('public')->delete($caisse->profile_picture);
                $caisse->profile_picture = $request->file('profile_picture')->store('caisse_profiles', 'public');
                $caisse->save();
            }

            return redirect()->route('gare-espace.caisse.index')->with('success', 'Information caisse mise à jour.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur mise à jour: ' . $e->getMessage());
        }
    }

    public function destroy(Caisse $caisse)
    {
        $caisse->update(['archived_at' => now()]);
        return redirect()->route('gare-espace.caisse.index')->with('success', 'Caisse archivée avec succès.');
    }
}
