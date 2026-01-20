<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Signalement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // 1. Statistiques Globales
        $totalReservations = Reservation::where('user_id', $user->id)->count();

        $totalSpent = Reservation::where('user_id', $user->id)
            ->where('statut', 'confirmee')
            ->sum('montant');

        $activeReservations = Reservation::where('user_id', $user->id)
            ->where('statut', 'confirmee')
            ->where('date_voyage', '>=', now())
            ->count();

        $totalSignalements = Signalement::where('user_id', $user->id)->count();

        // 2. Activités Récentes
        $recentReservations = Reservation::where('user_id', $user->id)
            ->with(['programme.compagnie', 'programme.vehicule'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $recentSignalements = Signalement::where('user_id', $user->id)
            ->with(['programme.compagnie'])
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();

        return view('user.dashboard', compact(
            'user',
            'totalReservations',
            'totalSpent',
            'activeReservations',
            'totalSignalements',
            'recentReservations',
            'recentSignalements'
        ));
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect()->route('login');
    }
}
