<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use App\Models\Vehicule;
use App\Models\Voyage;
use App\Models\Programme;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class GareDashboardController extends Controller
{
    public function index()
    {
        $gare = Auth::guard('gare')->user();
        $compagnieId = $gare->compagnie_id;

        // Stats for this specific gare
        $totalPersonnel = Personnel::where('gare_id', $gare->id)->count();
        $totalChauffeurs = Personnel::where('gare_id', $gare->id)->where('type_personnel', 'Chauffeur')->count();
        $chauffeursDisponibles = Personnel::where('gare_id', $gare->id)->where('type_personnel', 'Chauffeur')->where('statut', 'disponible')->count();
        $totalVehicules = Vehicule::where('gare_id', $gare->id)->where('is_active', true)->count();
        $vehiculesDisponibles = Vehicule::where('gare_id', $gare->id)->where('is_active', true)->where('statut', 'disponible')->count();

        // Today's data
        $today = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->format('H:i:s');

        // 1. Assigned voyages for today
        $voyagesAujourdhui = Voyage::where('gare_depart_id', $gare->id)
            ->whereDate('date_voyage', $today)
            ->with(['programme.gareArrivee', 'chauffeur', 'vehicule'])
            ->get();

        // 2. Unassigned programs for today
        // We only show programs that are active and haven't been assigned a voyage for today
        $programmesNonAssignes = Programme::where('gare_depart_id', $gare->id)
            ->where('statut', 'actif')
            ->whereDate('date_depart', '<=', $today)
            ->where(function($q) use ($today) {
                $q->whereDate('date_fin', '>=', $today)
                  ->orWhereNull('date_fin');
            })
            ->whereDoesntHave('voyages', function($q) use ($today) {
                $q->whereDate('date_voyage', $today)
                  ->where('statut', '!=', 'annulé');
            })
            ->whereTime('heure_depart', '>', $currentTime) // Only upcoming ones
            ->with(['gareArrivee'])
            ->orderBy('heure_depart')
            ->get();

        $programmesActifs = Programme::where('compagnie_id', $compagnieId)
            ->where('gare_depart_id', $gare->id)
            ->where('statut', 'actif')
            ->whereDate('date_depart', '<=', $today)
            ->where(function($q) use ($today) {
                $q->whereDate('date_fin', '>=', $today)
                  ->orWhereNull('date_fin');
            })
            ->count();

        return view('gare-espace.dashboard', compact(
            'gare', 'totalPersonnel', 'totalChauffeurs', 'chauffeursDisponibles',
            'totalVehicules', 'vehiculesDisponibles', 'voyagesAujourdhui', 
            'programmesActifs', 'programmesNonAssignes'
        ));
    }

    /**
     * Voir le profil de la gare
     */
    public function profile()
    {
        $gare = Auth::guard('gare')->user();
        $gare->load('compagnie');
        return view('gare-espace.profile', compact('gare'));
    }

    /**
     * Mettre à jour le profil de la gare
     */
    public function updateProfile(Request $request)
    {
        $gare = Auth::guard('gare')->user();
        
        $request->validate([
            'nom_gare' => 'required|string|max:255',
            'responsable_nom' => 'required|string|max:255',
            'responsable_prenom' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'contact_urgence' => 'nullable|string|max:20',
            'commune' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['nom_gare', 'responsable_nom', 'responsable_prenom', 'contact', 'contact_urgence', 'commune', 'adresse']);

        if ($request->hasFile('profile_image')) {
            if ($gare->profile_image) {
                Storage::disk('public')->delete($gare->profile_image);
            }
            $path = $request->file('profile_image')->store('gare_profiles', 'public');
            $data['profile_image'] = $path;
        }

        $gare->update($data);

        return back()->with('success', 'Profil de la gare mis à jour avec succès !');
    }

    /**
     * Changer le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $gare = Auth::guard('gare')->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $gare->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel ne correspond pas.']);
        }

        $gare->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Mot de passe mis à jour avec succès !');
    }
}
