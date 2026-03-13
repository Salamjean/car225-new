<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Programme;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AgentDashboard extends Controller
{
    public function dashboard(Request $request)
    {
        $agent = Auth::guard('agent')->user();
        $compagnieId = $agent->compagnie_id;
        $today = Carbon::today();

        // Statistiques des scans de l'agent
        $scansToday = Reservation::where('embarquement_agent_id', $agent->id)
            ->whereDate('embarquement_scanned_at', $today)
            ->count();

        $totalScans = Reservation::where('embarquement_agent_id', $agent->id)->count();


        // Programmes du jour de la GARE
        $programmesQuery = Programme::where('compagnie_id', $compagnieId)
            ->where('gare_depart_id', $agent->gare_id)
            ->whereDate('date_depart', $today);

        $programmesToday = $programmesQuery->count();

        $programmesDuJour = $programmesQuery->with(['voyages.vehicule', 'compagnie.vehicules'])
            ->orderBy('heure_depart')
            ->get();

        // Passagers embarqués du jour (Scans effectués par n'importe quel agent à CETTE GARE aujourd'hui)
        $passagersEmbarquesToday = Reservation::whereHas('programme', function($q) use ($agent) {
                $q->where('gare_depart_id', $agent->gare_id);
            })
            ->whereIn('statut', ['confirmee', 'terminee'])
            ->whereNotNull('embarquement_scanned_at')
            ->whereDate('embarquement_scanned_at', $today)
            ->count();

        // Réservations terminées (Historique pour cette gare)
        $reservationsTerminees = Reservation::whereHas('programme', function ($q) use ($agent) {
            $q->where('gare_depart_id', $agent->gare_id);
        })->where('statut', 'terminee')->count();

        // Revenus des réservations scannées par l'agent (Optionnel, on le garde)
        $revenueAgent = Reservation::where('embarquement_agent_id', $agent->id)
            ->sum('montant');

        // 5 dernières réservations scannées par l'agent
        $recentScans = Reservation::where('embarquement_agent_id', $agent->id)
            ->with(['programme', 'user'])
            ->orderBy('embarquement_scanned_at', 'desc')
            ->take(5)
            ->get();

        return view('agent.dashboard', compact(
            'agent',
            'scansToday',
            'totalScans',
            'passagersEmbarquesToday',
            'reservationsTerminees',
            'programmesToday',
            'programmesDuJour',
            'revenueAgent',
            'recentScans'
        ));
    }

    public function logout()
    {
        Auth::guard('agent')->logout();
        return redirect()->route('agent.login');
    }

    /**
     * Voir le profil de l'agent
     */
    public function profile()
    {
        $agent = Auth::guard('agent')->user();
        $agent->load(['compagnie', 'gare']);
        return view('agent.profile', compact('agent'));
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(Request $request)
    {
        $agent = Auth::guard('agent')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'cas_urgence' => 'nullable|string|max:20',
            'commune' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'prenom', 'contact', 'cas_urgence', 'commune']);

        if ($request->hasFile('profile_picture')) {
            if ($agent->profile_picture) {
                Storage::disk('public')->delete($agent->profile_picture);
            }
            $path = $request->file('profile_picture')->store('agent_profiles', 'public');
            $data['profile_picture'] = $path;
        }

        $agent->update($data);

        return back()->with('success', 'Profil mis à jour avec succès !');
    }

    /**
     * Changer le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $agent = Auth::guard('agent')->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $agent->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel ne correspond pas.']);
        }

        $agent->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Mot de passe mis à jour avec succès !');
    }
}
