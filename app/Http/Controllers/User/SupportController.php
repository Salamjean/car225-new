<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    /**
     * Types qui nécessitent une réservation liée
     */
    private const TYPES_REQUIRING_RESERVATION = ['bagage_perdu', 'objet_oublie', 'remboursement', 'qualite'];

    public function index()
    {
        return view('user.support.index');
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'autre');
        $reservations = collect();
        $needsReservation = in_array($type, self::TYPES_REQUIRING_RESERVATION);

        if ($needsReservation) {
            $query = \App\Models\Reservation::where('user_id', Auth::id())
                ->with('programme')
                ->orderBy('date_voyage', 'desc')
                ->take(20);

            switch ($type) {
                case 'bagage_perdu':
                case 'objet_oublie':
                    // Voyages déjà effectués
                    $query->where('statut', 'terminee');
                    break;

                case 'remboursement':
                    // Réservations annulées
                    $query->where('statut', 'annulee');
                    break;

                case 'qualite':
                    // Voyages confirmés ou terminés
                    $query->whereIn('statut', ['confirmee', 'terminee']);
                    break;
            }

            $reservations = $query->get();
        }

        return view('user.support.create', compact('type', 'reservations', 'needsReservation'));
    }

    public function mesDeclarations()
    {
        $declarations = \App\Models\SupportRequest::where('user_id', Auth::id())
            ->with('reservation')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('type');

        $typesLabels = [
            'bagage_perdu'  => ['label' => 'Bagage Perdu',        'icon' => 'fa-suitcase-rolling', 'color' => 'orange'],
            'objet_oublie'  => ['label' => 'Objet Oublié',         'icon' => 'fa-glasses',           'color' => 'blue'],
            'remboursement' => ['label' => 'Remboursement',        'icon' => 'fa-hand-holding-usd',  'color' => 'green'],
            'qualite'       => ['label' => 'Qualité de Service',   'icon' => 'fa-star',              'color' => 'purple'],
            'compte'        => ['label' => 'Mon Compte',           'icon' => 'fa-user-cog',          'color' => 'gray'],
            'autre'         => ['label' => 'Autre demande',        'icon' => 'fa-question',          'color' => 'red'],
        ];

        return view('user.support.mes-declarations', compact('declarations', 'typesLabels'));
    }

    public function store(Request $request)
    {
        $type = $request->input('type', 'autre');
        $needsReservation = in_array($type, self::TYPES_REQUIRING_RESERVATION);

        $rules = [
            'type' => 'required|string',
            'objet' => 'required|string|max:255',
            'description' => 'required|string',
        ];

        if ($needsReservation) {
            $rules['reservation_id'] = 'required|exists:reservations,id';
        } else {
            $rules['reservation_id'] = 'nullable|exists:reservations,id';
        }

        $validated = $request->validate($rules);

        \App\Models\SupportRequest::create([
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'objet' => $validated['objet'],
            'description' => $validated['description'],
            'reservation_id' => $validated['reservation_id'] ?? null,
        ]);
        
        return redirect()->route('user.support.index')->with('success', 'Votre demande a bien été enregistrée. Un administrateur vous répondra prochainement.');
    }
}
