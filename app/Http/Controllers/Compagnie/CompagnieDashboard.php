<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Signalement;
use App\Models\Vehicule;
use App\Models\Programme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CompagnieDashboard extends Controller
{
    public function dashboard()
    {
        $compagnieId = Auth::guard('compagnie')->id();

        // 1. Statistiques Globales
        $totalRevenue = Reservation::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->where('statut', 'confirmee')->sum('montant');

        $totalReservations = Reservation::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->where('statut', 'confirmee')->count();

        $totalVehicles = Vehicule::where('compagnie_id', $compagnieId)->count();

        $totalSignalements = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->count();

        // 2. Activités Récentes
        $recentReservations = Reservation::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->where('statut', 'confirmee')
            ->with(['user', 'programme'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentSignalements = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->with(['user', 'programme'])
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        // 3. Données pour le graphique (7 derniers jours)
        $days = [];
        $revenuePerDay = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('d M');

            $revenue = Reservation::whereHas('programme', function ($q) use ($compagnieId) {
                $q->where('compagnie_id', $compagnieId);
            })
                ->where('statut', 'confirmee')
                ->whereDate('created_at', $date)
                ->sum('montant');

            $revenuePerDay[] = (float) $revenue;
        }

        $liveVoyagesCount = \App\Models\Voyage::where('statut', 'en_cours')
            ->whereHas('programme', function ($q) use ($compagnieId) {
                $q->where('compagnie_id', $compagnieId);
            })->count();

        return view('compagnie.dashboard', compact(
            'totalRevenue',
            'totalReservations',
            'totalVehicles',
            'totalSignalements',
            'recentReservations',
            'recentSignalements',
            'days',
            'revenuePerDay',
            'compagnieId',
            'liveVoyagesCount'
        ));
    }

    public function addTickets(Request $request)
    {
        $request->validate([
            'quantite' => 'required|integer|min:1',
        ]);

        $compagnie = Auth::guard('compagnie')->user();
        
        DB::transaction(function () use ($compagnie, $request) {
            $compagnie->tickets += $request->quantite;
            $compagnie->save();

            $compagnie->historiqueTickets()->create([
                'quantite' => $request->quantite,
                'motif' => 'Ajout manuel depuis le tableau de bord',
            ]);
        });

        return back()->with('success', 'Tickets ajoutés avec succès !');
    }

    public function logout()
    {
        Auth::guard('compagnie')->logout();
        return redirect()->route('portail.login');
    }

    public function profile()
    {
        $compagnie = Auth::guard('compagnie')->user();
        return view('compagnie.profile.index', compact('compagnie'));
    }

    public function updateProfile(Request $request)
    {
        $compagnie = Auth::guard('compagnie')->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:compagnies,email,' . $compagnie->id,
            'contact' => 'required|string|max:255',
            'commune' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'sigle' => 'nullable|string|max:20',
            'slogan' => 'nullable|string|max:255',
            'path_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('path_logo')) {
            // Supprimer l'ancien logo si nécessaire
            if ($compagnie->path_logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($compagnie->path_logo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($compagnie->path_logo);
            }
            $validated['path_logo'] = $request->file('path_logo')->store('logos', 'public');
        }

        /** @var \App\Models\Compagnie $compagnie */
        $compagnie->update($validated);

        return back()->with('success', 'Profil mis à jour avec succès !');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        /** @var \App\Models\Compagnie $compagnie */
        $compagnie = Auth::guard('compagnie')->user();
        $compagnie->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return back()->with('success', 'Mot de passe mis à jour avec succès !');
    }
}
