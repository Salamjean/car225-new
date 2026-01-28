<?php

namespace App\Http\Controllers\Api\Agent;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AgentController extends Controller
{
    /**
     * Obtenir le profil de l'agent
     */
    public function profile(Request $request)
    {
        $agent = $request->user();
        $agent->load('compagnie');

        return response()->json([
            'success' => true,
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->name,
                'prenom' => $agent->prenom,
                'email' => $agent->email,
                'contact' => $agent->contact,
                'commune' => $agent->commune,
                'cas_urgence' => $agent->cas_urgence,
                'profile_picture' => $agent->profile_picture,
                'profile_picture_url' => $agent->profile_picture 
                    ? asset('storage/' . $agent->profile_picture) 
                    : null,
                'nom_device' => $agent->nom_device,
                'compagnie' => $agent->compagnie ? [
                    'id' => $agent->compagnie->id,
                    'name' => $agent->compagnie->name,
                    'logo' => $agent->compagnie->logo,
                ] : null,
            ],
        ]);
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(Request $request)
    {
        $agent = $request->user();

        $validated = $request->validate([
            'contact' => 'sometimes|string|max:20',
            'cas_urgence' => 'sometimes|string|max:20',
            'commune' => 'sometimes|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Gestion de l'upload de la photo de profil
        if ($request->hasFile('profile_picture')) {
            // Supprimer l'ancienne photo si elle existe
            if ($agent->profile_picture) {
                Storage::disk('public')->delete($agent->profile_picture);
            }

            $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = $imagePath;
        }

        $agent->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->name,
                'prenom' => $agent->prenom,
                'email' => $agent->email,
                'contact' => $agent->contact,
                'commune' => $agent->commune,
                'profile_picture' => $agent->profile_picture,
            ],
        ]);
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $agent = $request->user();

        if (!Hash::check($request->current_password, $agent->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Le mot de passe actuel est incorrect.',
            ], 422);
        }

        $agent->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe modifié avec succès',
        ]);
    }

    /**
     * Mettre à jour le token FCM pour les notifications push
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $agent = $request->user();
        $agent->update([
            'fcm_token' => $request->fcm_token,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token FCM mis à jour avec succès',
        ]);
    }

    /**
     * Tester l'envoi d'une notification push
     */
    public function testNotification(Request $request)
    {
        $agent = $request->user();

        if (!$agent->fcm_token) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun token FCM enregistré. Veuillez d\'abord enregistrer votre token.',
            ], 400);
        }

        $fcmService = app(\App\Services\FcmService::class);
        
        $result = $fcmService->sendNotification(
            $agent->fcm_token,
            '🎫 Test Notification Agent',
            'Votre application agent est bien connectée ! Message de test de Car225.',
            [
                'type' => 'test',
                'timestamp' => now()->toISOString(),
            ]
        );

        return response()->json($result);
    }

    /**
     * Dashboard - Statistiques agent
     */
    public function dashboard(Request $request)
    {
        $agent = $request->user();

        // Statistiques des réservations de la compagnie
        $today = Carbon::today();

        $reservationsToday = Reservation::whereHas('programme', function($query) use ($agent) {
            $query->where('compagnie_id', $agent->compagnie_id);
        })
        ->whereDate('date_voyage', $today)
        ->count();

        $reservationsConfirmees = Reservation::whereHas('programme', function($query) use ($agent) {
            $query->where('compagnie_id', $agent->compagnie_id);
        })
        ->where('statut', 'confirmee')
        ->whereDate('date_voyage', $today)
        ->count();

        $reservationsTerminees = Reservation::whereHas('programme', function($query) use ($agent) {
            $query->where('compagnie_id', $agent->compagnie_id);
        })
        ->where('statut', 'terminee')
        ->whereDate('date_voyage', $today)
        ->count();

        // Programmes du jour
        $programmesDuJour = Programme::where('compagnie_id', $agent->compagnie_id)
            ->where(function($query) use ($today) {
                // Programmes ponctuels
                $query->where(function($q) use ($today) {
                    $q->where('type_programmation', 'ponctuel')
                      ->whereDate('date_depart', $today);
                })
                // Programmes récurrents
                ->orWhere(function($q) use ($today) {
                    $joursFrancais = [
                        'monday' => 'lundi',
                        'tuesday' => 'mardi',
                        'wednesday' => 'mercredi',
                        'thursday' => 'jeudi',
                        'friday' => 'vendredi',
                        'saturday' => 'samedi',
                        'sunday' => 'dimanche'
                    ];
                    $jourSemaine = $joursFrancais[strtolower($today->format('l'))];
                    
                    $q->where('type_programmation', 'recurrent')
                      ->whereDate('date_depart', '<=', $today)
                      ->where(function($dateCheck) use ($today) {
                          $dateCheck->whereNull('date_fin_programmation')
                                   ->orWhereDate('date_fin_programmation', '>=', $today);
                      })
                      ->where(function($dayCheck) use ($jourSemaine) {
                          $dayCheck->whereJsonContains('jours_recurrence', $jourSemaine)
                                   ->orWhere('jours_recurrence', 'like', "%{$jourSemaine}%");
                      });
                });
            })
            ->with('vehicule')
            ->orderBy('heure_depart')
            ->get()
            ->map(function($programme) {
                return [
                    'id' => $programme->id,
                    'point_depart' => $programme->point_depart,
                    'point_arrive' => $programme->point_arrive,
                    'heure_depart' => $programme->heure_depart,
                    'vehicule' => $programme->vehicule ? [
                        'id' => $programme->vehicule->id,
                        'marque' => $programme->vehicule->marque,
                        'modele' => $programme->vehicule->modele,
                        'immatriculation' => $programme->vehicule->immatriculation,
                    ] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'date' => $today->format('Y-m-d'),
            'stats' => [
                'reservations_today' => $reservationsToday,
                'reservations_confirmees' => $reservationsConfirmees,
                'reservations_terminees' => $reservationsTerminees,
                'programmes_today' => $programmesDuJour->count(),
            ],
            'programmes_du_jour' => $programmesDuJour,
        ]);
    }
}
