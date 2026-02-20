<?php

namespace App\Http\Controllers\SapeurPompier;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SapeurPompierDashboard extends Controller
{
    public function dashboard()
    {
        $user = Auth::guard('sapeur_pompier')->user();

        // Show signalements assigned to this SP
        $signalements = Signalement::where('sapeur_pompier_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('sapeur_pompier.dashboard', compact('signalements'));
    }

    public function profile()
    {
        $user = Auth::guard('sapeur_pompier')->user();
        return view('sapeur_pompier.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('sapeur_pompier')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'commune' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }
}
