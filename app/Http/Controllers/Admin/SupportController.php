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

        $recipientEmail = $supportRequest->email ?? ($supportRequest->user ? $supportRequest->user->email : null);
        
        if ($recipientEmail) {
            try {
                \Illuminate\Support\Facades\Notification::route('mail', $recipientEmail)
                    ->notify(new \App\Notifications\SupportReplyNotification($supportRequest, $request->reponse));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Erreur email SupportReplyNotification: ' . $e->getMessage());
            }
        }

        // Notification Push FCM (uniquement si l'utilisateur est inscrit et a un token)
        if ($supportRequest->user && $supportRequest->user->fcm_token) {
            try {
                $fcmService = app(\App\Services\FcmService::class);
                $fcmService->sendNotification(
                    $supportRequest->user->fcm_token, 
                    'Réponse du support client', 
                    'Nous avons répondu à votre signalement : ' . \Illuminate\Support\Str::limit($supportRequest->objet, 30)
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('FCM Error (Support reply): ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Réponse envoyée avec succès par email (et notification mobile si inscrit).');
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
