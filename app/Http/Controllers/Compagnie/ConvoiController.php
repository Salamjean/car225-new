<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Convoi;
use App\Models\Gare;
use App\Notifications\ConvoiRefusedNotification;
use App\Notifications\ConvoiValidatedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConvoiController extends Controller
{
    public function index(Request $request)
    {
        $compagnie = Auth::guard('compagnie')->user();
        $statut = $request->query('statut', 'all');

        $query = Convoi::with(['user', 'itineraire', 'gare'])
            ->withCount('passagers')
            ->where('compagnie_id', $compagnie->id)
            ->latest();

        if (in_array($statut, ['en_attente', 'valide', 'refuse', 'paye', 'en_cours', 'annule', 'termine'])) {
            $query->where('statut', $statut);
        }

        $convois = $query->paginate(12)->withQueryString();

        $enAttenteCount = Convoi::where('compagnie_id', $compagnie->id)
            ->where('statut', 'en_attente')
            ->count();

        $totalPaye = Convoi::where('compagnie_id', $compagnie->id)
            ->whereIn('statut', ['paye', 'en_cours', 'termine'])
            ->sum('montant');

        $soldeConvoie = $compagnie->solde_convoie;

        return view('compagnie.convois.index', compact('convois', 'statut', 'enAttenteCount', 'soldeConvoie', 'totalPaye'));
    }

    public function show(Convoi $convoi)
    {
        $compagnie = Auth::guard('compagnie')->user();

        if ($convoi->compagnie_id !== $compagnie->id) {
            abort(403);
        }

        $convoi->load(['user', 'itineraire', 'passagers', 'chauffeur', 'vehicule', 'gare', 'latestLocation']);

        $gares = Gare::where('compagnie_id', $compagnie->id)
            ->orderBy('nom_gare')
            ->get(['id', 'nom_gare']);

        return view('compagnie.convois.show', compact('convoi', 'gares'));
    }

    /** Valider la demande de convoi et fixer le montant */
    public function valider(Request $request, Convoi $convoi)
    {
        $compagnie = Auth::guard('compagnie')->user();

        if ($convoi->compagnie_id !== $compagnie->id) {
            abort(403);
        }

        if ($convoi->statut !== 'en_attente') {
            return back()->with('error', 'Ce convoi ne peut pas être validé dans son état actuel.');
        }

        $validated = $request->validate([
            'montant' => 'required|numeric|min:100',
        ], [
            'montant.required' => 'Veuillez saisir le montant à facturer.',
            'montant.min'      => 'Le montant doit être au minimum de 100 FCFA.',
        ]);

        $convoi->update([
            'statut'  => 'valide',
            'montant' => $validated['montant'],
        ]);

        // Notifier l'utilisateur par email
        if ($convoi->user) {
            $convoi->user->notify(new ConvoiValidatedNotification($convoi));
        }

        return back()->with('success', 'Convoi validé. L\'utilisateur a été notifié par email.');
    }

    /** Refuser la demande de convoi */
    public function refuser(Request $request, Convoi $convoi)
    {
        $compagnie = Auth::guard('compagnie')->user();

        if ($convoi->compagnie_id !== $compagnie->id) {
            abort(403);
        }

        if ($convoi->statut !== 'en_attente') {
            return back()->with('error', 'Ce convoi ne peut pas être refusé dans son état actuel.');
        }

        $validated = $request->validate([
            'motif_refus' => 'required|string|max:500',
        ], [
            'motif_refus.required' => 'Veuillez indiquer le motif du refus.',
        ]);

        $convoi->update([
            'statut'      => 'refuse',
            'motif_refus' => $validated['motif_refus'],
        ]);

        // Notifier l'utilisateur par email
        if ($convoi->user) {
            $convoi->user->notify(new ConvoiRefusedNotification($convoi));
        }

        return back()->with('success', 'Convoi refusé. L\'utilisateur a été notifié par email.');
    }

    /** Assigner une gare au convoi (après paiement de l'utilisateur) */
    public function assignerGare(Request $request, Convoi $convoi)
    {
        $compagnie = Auth::guard('compagnie')->user();

        if ($convoi->compagnie_id !== $compagnie->id) {
            abort(403);
        }

        if ($convoi->statut !== 'paye') {
            return back()->with('error', 'Le convoi doit être payé avant d\'assigner une gare.');
        }

        $passagersCount = $convoi->passagers()->count();
        if ($passagersCount < $convoi->nombre_personnes) {
            $manquants = $convoi->nombre_personnes - $passagersCount;
            return back()->with('error',
                "Impossible d'assigner une gare : l'utilisateur n'a pas encore renseigné tous ses passagers. " .
                "Il manque {$manquants} passager(s) sur {$convoi->nombre_personnes} attendus."
            );
        }

        $validated = $request->validate([
            'gare_id' => 'required|exists:gares,id',
        ], [
            'gare_id.required' => 'Veuillez sélectionner une gare.',
        ]);

        // Vérifier que la gare appartient à cette compagnie
        $gare = Gare::where('id', $validated['gare_id'])
            ->where('compagnie_id', $compagnie->id)
            ->firstOrFail();

        $convoi->update([
            'gare_id' => $gare->id,
        ]);

        return back()->with('success', 'Gare assignée avec succès. La gare peut maintenant affecter un chauffeur et un véhicule.');
    }

    public function location(Convoi $convoi): JsonResponse
    {
        $compagnie = Auth::guard('compagnie')->user();

        if ($convoi->compagnie_id !== $compagnie->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        $convoi->load(['latestLocation', 'chauffeur', 'vehicule', 'itineraire', 'gare']);
        $location = $convoi->latestLocation;

        return response()->json([
            'success'    => true,
            'convoi_id'  => $convoi->id,
            'statut'     => $convoi->statut,
            'latitude'   => $location ? (float) $location->latitude : null,
            'longitude'  => $location ? (float) $location->longitude : null,
            'speed'      => $location ? $location->speed : null,
            'heading'    => $location ? $location->heading : null,
            'last_update'=> $location ? $location->updated_at->diffForHumans() : 'Jamais',
            'chauffeur'  => $convoi->chauffeur ? trim(($convoi->chauffeur->prenom ?? '') . ' ' . ($convoi->chauffeur->name ?? '')) : 'Inconnu',
            'vehicule'   => $convoi->vehicule->immatriculation ?? 'N/A',
            'trajet'     => $convoi->itineraire ? ($convoi->itineraire->point_depart . ' -> ' . $convoi->itineraire->point_arrive) : '-',
            'gare'       => $convoi->gare->nom_gare ?? '-',
        ]);
    }
}
