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
}
