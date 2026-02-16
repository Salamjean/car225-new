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
        $totalReservations = Reservation::where('user_id', $user->id)
            ->where('statut', 'confirmee')
            ->count();

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
            ->where('statut', 'confirmee')
            ->with(['programme.compagnie'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $recentSignalements = Signalement::where('user_id', $user->id)
            ->with(['programme.compagnie'])
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();

        // 3. Données pour le graphique (6 derniers mois)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Reservation::where('user_id', $user->id)
                ->where('statut', 'confirmee')
                ->whereYear('date_voyage', $month->year)
                ->whereMonth('date_voyage', $month->month)
                ->count();
            
            $chartData['labels'][] = $month->translatedFormat('M');
            $chartData['values'][] = $count;
        }

        return view('user.dashboard', compact(
            'user',
            'totalReservations',
            'totalSpent',
            'activeReservations',
            'totalSignalements',
            'recentReservations',
            'recentSignalements',
            'chartData'
        ));
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect()->route('login');
    }

    public function markNotificationRead(Request $request)
    {
        $notification = Auth::user()->notifications()->findOrFail($request->id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}
