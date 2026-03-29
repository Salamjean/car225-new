<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Mail\GareLocationApprovedMail;
use App\Models\CompanyMessage;
use App\Models\GareLocationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class GareLocationRequestController extends Controller
{
    /**
     * Liste des demandes de localisation en attente pour cette compagnie
     */
    public function index()
    {
        $compagnie = Auth::guard('compagnie')->user();

        $pendingRequests = GareLocationRequest::where('compagnie_id', $compagnie->id)
            ->where('statut', 'pending')
            ->with('gare')
            ->orderByDesc('created_at')
            ->get();

        $historyRequests = GareLocationRequest::where('compagnie_id', $compagnie->id)
            ->whereIn('statut', ['approved', 'rejected'])
            ->with('gare')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        $allGares = \App\Models\Gare::where('compagnie_id', $compagnie->id)->orderBy('nom_gare')->get();

        return view('compagnie.gare-location-requests.index', compact('pendingRequests', 'historyRequests', 'allGares'));
    }

    /**
     * Approuver une demande : met à jour les coordonnées de la gare et envoie un mail
     */
    public function approve(GareLocationRequest $gareLocationRequest)
    {
        $compagnie = Auth::guard('compagnie')->user();

        if ($gareLocationRequest->compagnie_id !== $compagnie->id) {
            abort(403);
        }

        if ($gareLocationRequest->statut !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Cette demande a déjà été traitée.'], 422);
        }

        $gare = $gareLocationRequest->gare;

        // Mettre à jour les coordonnées de la gare
        $gare->update([
            'latitude'  => $gareLocationRequest->latitude,
            'longitude' => $gareLocationRequest->longitude,
        ]);

        // Marquer la demande comme approuvée (popup pas encore affiché)
        $gareLocationRequest->update([
            'statut'       => 'approved',
            'approved_at'  => now(),
            'gare_notified' => false,
        ]);

        // Message dans la boîte de réception de la gare
        CompanyMessage::create([
            'compagnie_id'   => $compagnie->id,
            'recipient_type' => 'App\Models\Gare',
            'recipient_id'   => $gare->id,
            'subject'        => '✅ Votre localisation GPS a été approuvée',
            'message'        => "Bonne nouvelle ! Votre demande de mise à jour de position GPS a été approuvée.\n\nNouvelles coordonnées actives :\n• Latitude : {$gareLocationRequest->latitude}\n• Longitude : {$gareLocationRequest->longitude}\n\nVotre gare est maintenant visible avec la bonne position sur la carte de suivi en temps réel.",
            'is_read'        => false,
        ]);

        // Email après la réponse (évite de bloquer)
        $gareSnap = $gare;
        $lat = (float) $gareLocationRequest->latitude;
        $lng = (float) $gareLocationRequest->longitude;
        app()->terminating(function () use ($gareSnap, $lat, $lng) {
            try {
                Mail::to($gareSnap->email)->send(new GareLocationApprovedMail($gareSnap, $lat, $lng, 'approved'));
            } catch (\Exception $e) {}
        });

        return response()->json([
            'success' => true,
            'message' => 'Localisation approuvée et mise à jour pour ' . $gare->nom_gare . '.',
        ]);
    }

    /**
     * Mise à jour manuelle des coordonnées d'une gare par la compagnie
     */
    public function updateGareLocation(Request $request, \App\Models\Gare $gare)
    {
        $compagnie = Auth::guard('compagnie')->user();

        if ($gare->compagnie_id !== $compagnie->id) {
            abort(403);
        }

        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $gare->update([
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // Message dans la boîte de réception de la gare
        CompanyMessage::create([
            'compagnie_id'   => $compagnie->id,
            'recipient_type' => 'App\Models\Gare',
            'recipient_id'   => $gare->id,
            'subject'        => '📍 Votre localisation GPS a été mise à jour',
            'message'        => "Votre compagnie a mis à jour manuellement la position GPS de votre gare.\n\nNouvelles coordonnées actives :\n• Latitude : {$request->latitude}\n• Longitude : {$request->longitude}\n\nVotre gare est maintenant visible avec la bonne position sur la carte de suivi en temps réel.",
            'is_read'        => false,
        ]);

        // Email après la réponse
        $gareSnap = $gare;
        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;
        app()->terminating(function () use ($gareSnap, $lat, $lng) {
            try {
                Mail::to($gareSnap->email)->send(new GareLocationApprovedMail($gareSnap, $lat, $lng, 'manual'));
            } catch (\Exception $e) {}
        });

        return response()->json([
            'success' => true,
            'message' => 'Coordonnées de ' . $gare->nom_gare . ' mises à jour.',
        ]);
    }

    /**
     * Rejeter une demande
     */
    public function reject(Request $request, GareLocationRequest $gareLocationRequest)
    {
        $compagnie = Auth::guard('compagnie')->user();

        if ($gareLocationRequest->compagnie_id !== $compagnie->id) {
            abort(403);
        }

        if ($gareLocationRequest->statut !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Cette demande a déjà été traitée.'], 422);
        }

        $gareLocationRequest->update([
            'statut'          => 'rejected',
            'rejected_reason' => $request->input('reason', 'Demande rejetée par la compagnie.'),
            'gare_notified'   => true, // pas de popup pour un rejet
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Demande rejetée.',
        ]);
    }
}
