<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignalementController extends Controller
{
    /**
     * Affiche la liste des signalements pour la compagnie connectée.
     */
    public function index()
    {
        $compagnieId = Auth::guard('compagnie')->id();

        // On récupère les signalements liés aux programmes de cette compagnie
        // ATTENTION : La relation Signalement -> Programme -> Compagnie est la voie classique
        // Il faut donc des signalements avec un programme_id qui appartient à la compagnie
        $signalements = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })
            ->with(['programme.vehicule', 'user', 'programme'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('compagnie.signalements.index', compact('signalements'));
    }

    /**
     * Affiche le détail d'un signalement.
     */
    public function show($id)
    {
        $compagnieId = Auth::guard('compagnie')->id();

        $signalement = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })
            ->with(['programme.vehicule', 'user', 'programme'])
            ->findOrFail($id);

        return view('compagnie.signalements.show', compact('signalement'));
    }
}
