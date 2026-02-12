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
        $validated = $request->validate([
            'type' => 'required|string',
            'objet' => 'required|string|max:255',
            'description' => 'required|string',
            'reservation_id' => 'nullable|exists:reservations,id',
        ]);

        \App\Models\SupportRequest::create([
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'objet' => $validated['objet'],
            'description' => $validated['description'],
            'reservation_id' => $validated['reservation_id'],
        ]);
        
        return redirect()->route('user.support.index')->with('success', 'Votre demande a bien été enregistrée. Un administrateur vous répondra prochainement.');
    }
}
