<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    /**
     * Afficher la liste des réservations de la compagnie connecté
     */
    public function index(Request $request)
    {
        $compagnie = Auth::guard('compagnie')->user();

        // Récupérer les réservations liées aux programmes de cette compagnie
        $query = Reservation::whereHas('programme', function ($q) use ($compagnie) {
            $q->where('compagnie_id', $compagnie->id);
        })->with(['programme', 'user', 'programme.itineraire']);

        // Séparer "En cours" et "Terminées"
        // En cours: 'en_attente', 'confirmee' ET date_voyage >= aujourd'hui
        // Terminées: 'terminee', 'annulee', OU date_voyage passée

        $reservationsEnCours = (clone $query)
            ->where('statut', 'confirmee')
            ->whereDate('date_voyage', '>=', now())
            ->orderBy('date_voyage', 'asc')
            ->paginate(10, ['*'], 'page_cours');

        // Terminées = statut terminee OU statut annulee OU date passée (peu importe le statut)
        $reservationsTerminees = (clone $query)
            ->where(function ($q) {
                $q->whereIn('statut', ['terminee', 'annulee'])
                    ->orWhereDate('date_voyage', '<', now());
            })
            ->orderBy('date_voyage', 'desc')
            ->paginate(10, ['*'], 'page_terminees');

        return view('compagnie.reservations.index', compact('reservationsEnCours', 'reservationsTerminees'));
    }
}
