<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Convoi;
use App\Models\Programme;
use App\Models\Vehicule;
use App\Models\Voyage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ChauffeurController extends Controller
{
    public function dashboard()
    {
        $chauffeur = Auth::guard('chauffeur')->user();
        
        // Un voyage interrompu reste visible 1h, puis disparaît du dashboard
        $interruptionCutoff = Carbon::now()->subHour();

        $todayVoyages = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('date_voyage', Carbon::today())
            ->where('statut', '!=', 'terminé')
            ->where('statut', '!=', 'annulé')
            ->where(function ($q) use ($interruptionCutoff) {
                $q->where('statut', '!=', 'interrompu')
                  ->orWhere('updated_at', '>=', $interruptionCutoff);
            })
            ->with(['programme', 'vehicule', 'gareDepart', 'gareArrivee'])
            ->get();

        $upcomingVoyages = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('date_voyage', '>', Carbon::today())
            ->where('statut', '!=', 'terminé')
            ->where('statut', '!=', 'annulé')
            ->with(['programme', 'vehicule', 'gareDepart', 'gareArrivee'])
            ->orderBy('date_voyage', 'asc')
            ->get();

        $activeConvois = Convoi::where('personnel_id', $chauffeur->id)
            ->whereIn('statut', ['paye', 'en_cours'])
            ->where(function ($q) {
                // Afficher : convois en_cours OU convois paye dont le départ est aujourd'hui ou passé
                $q->where('statut', 'en_cours')
                  ->orWhere(function ($q2) {
                      $q2->where('statut', 'paye')
                         ->whereDate('date_depart', '<=', Carbon::today());
                  });
            })
            ->with(['itineraire', 'gare', 'vehicule'])
            ->latest()
            ->get();

        // Convois à venir (paye, départ dans le futur)
        $upcomingConvois = Convoi::where('personnel_id', $chauffeur->id)
            ->where('statut', 'paye')
            ->whereDate('date_depart', '>', Carbon::today())
            ->with(['itineraire', 'vehicule'])
            ->orderBy('date_depart', 'asc')
            ->get();

        $completedVoyagesToday = Voyage::where('personnel_id', $chauffeur->id)
            ->whereDate('updated_at', Carbon::today())
            ->whereIn('statut', ['terminé', 'succès'])
            ->count();

        $completedConvoisToday = Convoi::where('personnel_id', $chauffeur->id)
            ->whereDate('updated_at', Carbon::today())
            ->where('statut', 'termine')
            ->count();

        $todayMissionsCount = $todayVoyages->count() + $activeConvois->count();
        $completedMissionsTodayCount = $completedVoyagesToday + $completedConvoisToday;

        return view('chauffeur.dashboard', compact(
            'todayVoyages',
            'upcomingVoyages',
            'activeConvois',
            'upcomingConvois',
            'todayMissionsCount',
            'completedMissionsTodayCount'
        ));
    }

    public function myVoyages()
    {
        $chauffeur = Auth::guard('chauffeur')->user();
        $voyages = Voyage::where('personnel_id', $chauffeur->id)
            ->with(['programme', 'vehicule', 'gareDepart', 'gareArrivee'])
            ->orderBy('date_voyage', 'desc')
            ->paginate(10);

        return view('chauffeur.voyages.history', compact('voyages'));
    }

    /**
     * Voir le profil du chauffeur
     */
    public function profile()
    {
        $chauffeur = Auth::guard('chauffeur')->user();
        $chauffeur->load(['compagnie', 'gare']);
        return view('chauffeur.profile', compact('chauffeur'));
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(Request $request)
    {
        $chauffeur = Auth::guard('chauffeur')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'contact_urgence' => 'nullable|string|max:20',
            'commune' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'prenom', 'contact', 'contact_urgence', 'commune']);

        if ($request->hasFile('profile_image')) {
            if ($chauffeur->profile_image) {
                Storage::disk('public')->delete($chauffeur->profile_image);
            }
            $path = $request->file('profile_image')->store('chauffeur_profiles', 'public');
            $data['profile_image'] = $path;
        }

        $chauffeur->update($data);

        return back()->with('success', 'Profil mis à jour avec succès !');
    }

    /**
     * Changer le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $chauffeur = Auth::guard('chauffeur')->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $chauffeur->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel ne correspond pas.']);
        }

        $chauffeur->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Mot de passe mis à jour avec succès !');
    }
}
