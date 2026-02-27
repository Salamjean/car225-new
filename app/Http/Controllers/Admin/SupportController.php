<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportRequest;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportRequest::with(['user', 'messages']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('objet', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('telephone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $requests = $query->latest()->paginate(20);
        return view('admin.support.index', compact('requests'));
    }

    public function show(SupportRequest $supportRequest)
    {
        $supportRequest->load(['user', 'reservation.programme.itineraire', 'messages']);
        return view('admin.support.show', compact('supportRequest'));
    }

    public function repondre(Request $request, SupportRequest $supportRequest)
    {
        $request->validate([
            'reponse' => 'required|string',
        ]);

        $supportRequest->messages()->create([
            'sender_type' => 'admin',
            'message' => $request->reponse,
        ]);

        $supportRequest->update([
            'reponse' => $request->reponse, // Keep the last response for backward compatibility
            'statut' => 'en_cours',
        ]);

        // Notification Push FCM (uniquement si l'utilisateur est inscrit et a un token)
        if ($supportRequest->user) {
            // Notification Web / Cloche
            try {
                $supportRequest->user->notify(new \App\Notifications\GeneralNotification(
                    'Nouveau message',
                    "L'équipe support a répondu à votre signalement : " . \Illuminate\Support\Str::limit($supportRequest->objet, 30),
                    'info'
                ));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Erreur notification cloche (Support reply): ' . $e->getMessage());
            }

            // Push FCM
            if ($supportRequest->user->fcm_token) {
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
        }

        return back()->with('success', 'Réponse envoyée avec succès en notification sur mobile et web.');
    }

    public function changeStatut(Request $request, SupportRequest $supportRequest)
    {
        $request->validate([
            'statut' => 'required|in:ouvert,en_cours,ferme',
        ]);

        $supportRequest->update(['statut' => $request->statut]);

        $statutLabel = match($request->statut) {
            'ouvert' => 'Ouvert',
            'en_cours' => 'En cours de traitement',
            'ferme' => 'Fermé / Résolu',
            default => $request->statut
        };

        // Envoi de l'email uniquement lors du changement de statut
        $recipientEmail = $supportRequest->email ?? ($supportRequest->user ? $supportRequest->user->email : null);
        
        if ($recipientEmail) {
            try {
                \Illuminate\Support\Facades\Notification::route('mail', $recipientEmail)
                    ->notify(new \App\Notifications\SupportReplyNotification(
                        $supportRequest, 
                        "Le statut de votre demande a été mis à jour : " . $statutLabel
                    ));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Erreur email SupportReplyNotification (Statut): ' . $e->getMessage());
            }
        }

        // Notifications
        if ($supportRequest->user) {
            // Notification Web / Cloche
            try {
                $supportRequest->user->notify(new \App\Notifications\GeneralNotification(
                    'Statut mis à jour',
                    'Le statut de votre demande est maintenant : ' . $statutLabel,
                    'info'
                ));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Erreur notification cloche (Support status): ' . $e->getMessage());
            }

            // Notification Push FCM
            if ($supportRequest->user->fcm_token) {
                try {
                    $fcmService = app(\App\Services\FcmService::class);
                    $fcmService->sendNotification(
                        $supportRequest->user->fcm_token, 
                        'Mise à jour de votre demande', 
                        'Le statut est maintenant : ' . $statutLabel
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('FCM Error (Support status): ' . $e->getMessage());
                }
            }
        }

        return back()->with('success', 'Statut mis à jour et notifications envoyées.');
    }
}
