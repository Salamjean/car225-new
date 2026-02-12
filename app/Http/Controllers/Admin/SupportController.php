<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportRequest;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $requests = SupportRequest::with('user')->latest()->paginate(20);
        return view('admin.support.index', compact('requests'));
    }

    public function show(SupportRequest $supportRequest)
    {
        $supportRequest->load(['user', 'reservation.programme.itineraire']);
        return view('admin.support.show', compact('supportRequest'));
    }

    public function repondre(Request $request, SupportRequest $supportRequest)
    {
        $request->validate([
            'reponse' => 'required|string',
        ]);

        $supportRequest->update([
            'reponse' => $request->reponse,
            'statut' => 'en_cours',
        ]);

        return back()->with('success', 'Réponse envoyée avec succès.');
    }

    public function changeStatut(Request $request, SupportRequest $supportRequest)
    {
        $request->validate([
            'statut' => 'required|in:ouvert,en_cours,ferme',
        ]);

        $supportRequest->update(['statut' => $request->statut]);

        return back()->with('success', 'Statut mis à jour.');
    }
}
