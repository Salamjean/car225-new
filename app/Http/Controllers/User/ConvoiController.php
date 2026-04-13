<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Compagnie;
use App\Models\Convoi;
use App\Models\Itineraire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ConvoiController extends Controller
{
    public function index()
    {
        $convois = Convoi::with(['compagnie', 'itineraire'])
            ->withCount('passagers')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.convoi.index', compact('convois'));
    }

    public function create()
    {
        $compagnies = Compagnie::where('statut', 'actif')
            ->orderBy('name')
            ->get(['id', 'name', 'sigle']);

        return view('user.convoi.create', compact('compagnies'));
    }

    /** AJAX : itinéraires d'une compagnie avec point_depart et point_arrive */
    public function itinerairesByCompagnie($compagnieId)
    {
        $itineraires = Itineraire::where('compagnie_id', $compagnieId)
            ->orderBy('point_depart')
            ->get(['id', 'point_depart', 'point_arrive', 'durer_parcours']);

        return response()->json(['itineraires' => $itineraires]);
    }

    /** Création du convoi en une seule étape — envoi direct à la compagnie */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'compagnie_id'     => 'required|exists:compagnies,id',
            'itineraire_id'    => 'nullable|exists:itineraires,id',
            'lieu_depart'      => 'required_without:itineraire_id|string|max:255',
            'lieu_retour'      => 'required_without:itineraire_id|string|max:255',
            'nombre_personnes' => 'required|integer|min:10',
            'date_depart'      => 'required|date|after_or_equal:today',
            'heure_depart'     => 'required|date_format:H:i',
            'date_retour'      => 'nullable|date|after_or_equal:date_depart',
            'heure_retour'     => 'nullable|date_format:H:i|required_with:date_retour',
        ], [
            'lieu_depart.required_without'   => 'Le lieu de départ est obligatoire si aucun itinéraire n\'est sélectionné.',
            'lieu_retour.required_without'   => 'Le lieu d\'arrivée est obligatoire si aucun itinéraire n\'est sélectionné.',
            'nombre_personnes.min'           => 'Le minimum est de 10 personnes pour un convoi.',
            'date_depart.after_or_equal'     => 'La date de départ ne peut pas être dans le passé.',
            'date_retour.after_or_equal'     => 'La date de retour doit être égale ou après la date de départ.',
            'heure_retour.required_with'     => 'L\'heure de retour est obligatoire si vous indiquez une date de retour.',
        ]);

        // Résoudre lieu_depart / lieu_retour
        if (!empty($validated['itineraire_id'])) {
            $itineraire = Itineraire::findOrFail($validated['itineraire_id']);
            $lieuDepart = $itineraire->point_depart;
            $lieuRetour = $itineraire->point_arrive;
        } else {
            $itineraire = null;
            $lieuDepart = $validated['lieu_depart'];
            $lieuRetour = $validated['lieu_retour'];
        }

        $convoi = Convoi::create([
            'user_id'          => Auth::id(),
            'compagnie_id'     => $validated['compagnie_id'],
            'itineraire_id'    => $validated['itineraire_id'] ?? null,
            'lieu_depart'      => $lieuDepart,
            'lieu_retour'      => $lieuRetour,
            'nombre_personnes' => $validated['nombre_personnes'],
            'date_depart'      => $validated['date_depart'],
            'heure_depart'     => $validated['heure_depart'],
            'date_retour'      => $validated['date_retour'] ?? null,
            'heure_retour'     => $validated['heure_retour'] ?? null,
            'reference'        => 'CONV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
            'statut'           => 'en_attente',
        ]);

        return redirect()->route('user.convoi.show', $convoi)
            ->with('success', 'Votre demande de convoi a été envoyée. La compagnie reviendra vers vous rapidement.');
    }

    public function show(Convoi $convoi)
    {
        abort_if($convoi->user_id !== Auth::id(), 403);
        $convoi->load(['compagnie', 'itineraire', 'passagers', 'gare', 'chauffeur', 'vehicule']);
        return view('user.convoi.show', compact('convoi'));
    }

    /** Paiement : l'utilisateur accepte le règlement et paye le montant fixé par la compagnie */
    public function pay(Request $request, Convoi $convoi)
    {
        abort_if($convoi->user_id !== Auth::id(), 403);
        abort_if($convoi->statut !== 'valide', 403);

        $request->validate([
            'reglement_accepte' => 'required|accepted',
        ], [
            'reglement_accepte.required' => 'Vous devez accepter le règlement des convois avant de payer.',
            'reglement_accepte.accepted'  => 'Vous devez cocher la case pour accepter le règlement.',
        ]);

        $convoi->update(['statut' => 'paye']);
        $convoi->compagnie()->increment('solde_convoie', $convoi->montant);

        return redirect()->route('user.convoi.show', $convoi)
            ->with('success', 'Paiement confirmé ! Vous pouvez maintenant renseigner les informations de vos passagers.');
    }

    /** Enregistrement des passagers après paiement */
    public function storePassengers(Request $request, Convoi $convoi)
    {
        abort_if($convoi->user_id !== Auth::id(), 403);
        abort_if($convoi->statut !== 'paye', 403);

        $validated = $request->validate([
            'passagers'                       => 'required|array|size:' . $convoi->nombre_personnes,
            'passagers.*.nom'                 => 'required|string|max:100',
            'passagers.*.prenoms'             => 'required|string|max:150',
            'passagers.*.contact'             => ['required', 'digits:10'],
            'passagers.*.contact_urgence'     => ['required', 'digits:10'],
        ], [
            'passagers.size'                          => 'Le nombre de passagers doit correspondre exactement à ' . $convoi->nombre_personnes . ' personnes.',
            'passagers.*.contact.digits'              => 'Le contact doit contenir exactement 10 chiffres.',
            'passagers.*.contact_urgence.required'    => 'Le contact d\'urgence est obligatoire.',
            'passagers.*.contact_urgence.digits'      => 'Le contact d\'urgence doit contenir exactement 10 chiffres.',
        ]);

        $convoi->passagers()->delete();
        foreach ($validated['passagers'] as $p) {
            $convoi->passagers()->create($p);
        }

        return back()->with('success', 'Les informations des passagers ont été enregistrées avec succès.');
    }
}
