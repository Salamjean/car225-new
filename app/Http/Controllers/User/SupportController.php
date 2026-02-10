<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index()
    {
        return view('user.support.index');
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'autre');
        return view('user.support.create', compact('type'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'objet' => 'required|string|max:255',
            'description' => 'required|string',
            'reservation_id' => 'nullable|exists:reservations,id',
        ]);

        // Pour l'instant, on peut imaginer un modèle Reclamation
        // Ou juste envoyer un mail/notif
        
        return redirect()->route('user.support.index')->with('success', 'Votre demande a bien été enregistrée.');
    }
}
