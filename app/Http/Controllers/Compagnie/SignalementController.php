<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use App\Models\CompanyMessage;
use App\Models\Reservation;
use App\Models\SapeurPompier;
use App\Notifications\NewSignalementNotification;
use App\Notifications\NewInternalMessageNotification;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SignalementController extends Controller
{
    /**
     * Affiche la liste des signalements pour la compagnie connectée.
     */
    public function index(Request $request)
    {
        $compagnieId = Auth::guard('compagnie')->id();

        $query = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->with(['user', 'personnel', 'programme', 'vehicule', 'voyage']);

        // Filtre par source (chauffeur ou utilisateur)
        if ($request->filled('source')) {
            if ($request->source === 'chauffeur') {
                $query->whereNotNull('personnel_id')->whereNull('user_id');
            } elseif ($request->source === 'utilisateur') {
                $query->whereNotNull('user_id');
            }
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $signalements = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Stats
        $baseQuery = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        });

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'nouveaux' => (clone $baseQuery)->where('statut', 'nouveau')->count(),
            'traites' => (clone $baseQuery)->where('statut', 'traite')->count(),
            'from_chauffeurs' => (clone $baseQuery)->whereNotNull('personnel_id')->whereNull('user_id')->count(),
            'from_users' => (clone $baseQuery)->whereNotNull('user_id')->count(),
        ];

        return view('compagnie.signalements.index', compact('signalements', 'stats'));
    }

    /**
     * Affiche le détail d'un signalement.
     */
    public function show($id)
    {
        $compagnieId = Auth::guard('compagnie')->id();

        $signalement = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })
            ->with(['user', 'personnel', 'programme.gareDepart', 'programme.gareArrivee', 'vehicule', 'voyage', 'sapeurPompier'])
            ->findOrFail($id);

        if (!$signalement->is_read_by_company) {
            $signalement->is_read_by_company = true;
            $signalement->save();
        }

        // Récupérer la gare de départ liée au programme
        $gareDepart = $signalement->programme?->gareDepart;

        // Récupérer le sapeur pompier assigné ou le plus proche
        $sapeurPompier = $signalement->sapeurPompier;
        if (!$sapeurPompier && $signalement->latitude && $signalement->longitude) {
            $sapeurPompier = $this->findNearestSapeurPompier($signalement->latitude, $signalement->longitude);
        }
        // Récupérer les véhicules disponibles pour un éventuel transbordement
        // On filtre par la gare de départ ET le même nombre de places/type
        $availableVehicles = collect();
        if ($signalement->programme && $signalement->programme->gare_depart_id) {
            $currentVehicule = $signalement->vehicule ?? $signalement->programme->vehicule;
            
            $query = \App\Models\Vehicule::where('gare_id', $signalement->programme->gare_depart_id)
                ->where('statut', 'disponible')
                ->where('is_active', true);

            // On s'assure que le nouveau car a au moins les mêmes caractéristiques
            if ($currentVehicule) {
                $query->where('nombre_place', $currentVehicule->nombre_place)
                      ->where('type_range', $currentVehicule->type_range);
            }

            $availableVehicles = $query->get();
        }

        return view('compagnie.signalements.show', compact('signalement', 'gareDepart', 'sapeurPompier', 'availableVehicles'));
    }

    /**
     * Envoyer une alerte à la gare de départ concernant un signalement.
     */
    public function alertGare(Request $request, $id)
    {
        $compagnieId = Auth::guard('compagnie')->id();
        $compagnie = Auth::guard('compagnie')->user();

        $signalement = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->with(['programme.gareDepart', 'vehicule'])->findOrFail($id);

        $gare = $signalement->programme?->gareDepart;

        if (!$gare) {
            return back()->with('error', 'Aucune gare de départ trouvée pour ce trajet.');
        }

        $customMessage = $request->input('message', '');

        // Construire le sujet et le message
        $typeLabels = [
            'accident' => '🚨 ACCIDENT',
            'panne' => '🔧 PANNE',
            'retard' => '⏰ RETARD',
            'comportement' => '⚠️ COMPORTEMENT',
            'autre' => '📋 SIGNALEMENT',
        ];

        $subject = ($typeLabels[$signalement->type] ?? 'SIGNALEMENT') . ' - ' .
                   ($signalement->programme?->point_depart ?? '?') . ' → ' .
                   ($signalement->programme?->point_arrive ?? '?');

        $vehicleInfo = $signalement->vehicule?->immatriculation ?? $signalement->programme?->vehicule?->immatriculation ?? 'Non assigné';

        $messageBody = "⚠️ ALERTE SIGNALEMENT - {$subject}\n\n";
        $messageBody .= "Type: " . ucfirst($signalement->type) . "\n";
        $messageBody .= "Véhicule: {$vehicleInfo}\n";
        $messageBody .= "Description: {$signalement->description}\n";
        $messageBody .= "Date du signalement: {$signalement->created_at->format('d/m/Y à H:i')}\n";

        if ($signalement->latitude && $signalement->longitude) {
            $messageBody .= "Localisation GPS: {$signalement->latitude}, {$signalement->longitude}\n";
            $messageBody .= "Google Maps: https://www.google.com/maps/search/?api=1&query={$signalement->latitude},{$signalement->longitude}\n";
        }

        if ($customMessage) {
            $messageBody .= "\n--- Message de la compagnie ---\n{$customMessage}\n";
        }

        $messageBody .= "\n--- Envoyé automatiquement par la compagnie {$compagnie->name} via CAR225 ---";

        // Créer le CompanyMessage pour la gare
        $message = new CompanyMessage([
            'compagnie_id' => $compagnie->id,
            'subject' => $subject,
            'message' => $messageBody,
            'is_read' => false,
        ]);
        $message->recipient()->associate($gare);
        $message->save();

        // Notification in-app
        try {
            $gare->notify(new NewInternalMessageNotification($message));
        } catch (\Exception $e) {
            Log::error("Erreur notification gare signalement: " . $e->getMessage());
        }

        // Envoi email à la gare
        if ($gare->email) {
            try {
                Mail::raw($messageBody, function ($mail) use ($gare, $subject, $compagnie) {
                    $mail->to($gare->email)
                         ->subject("[CAR225] {$subject}")
                         ->from(config('mail.from.address'), $compagnie->name);
                });
            } catch (\Exception $e) {
                Log::error("Erreur email gare signalement: " . $e->getMessage());
            }
        }

        return back()->with('success', "Alerte envoyée avec succès à la gare \"{$gare->nom_gare}\" (message + email).");
    }

    /**
     * Contacter le sapeur pompier le plus proche pour un accident.
     */
    public function alertPompier(Request $request, $id)
    {
        $compagnieId = Auth::guard('compagnie')->id();

        $signalement = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->findOrFail($id);

        // Trouver ou utiliser le pompier déjà assigné
        $pompier = null;
        if ($signalement->sapeur_pompier_id) {
            $pompier = SapeurPompier::find($signalement->sapeur_pompier_id);
        }

        if (!$pompier && $signalement->latitude && $signalement->longitude) {
            $pompier = $this->findNearestSapeurPompier($signalement->latitude, $signalement->longitude);
        }

        if (!$pompier) {
            return back()->with('error', 'Aucun sapeur pompier disponible trouvé à proximité. Vérifiez que les coordonnées GPS sont renseignées.');
        }

        // Assigner le pompier au signalement s'il ne l'est pas déjà
        if (!$signalement->sapeur_pompier_id) {
            $signalement->sapeur_pompier_id = $pompier->id;
            $signalement->save();
        }

        // Envoyer la notification par email + base de données
        try {
            Notification::send($pompier, new NewSignalementNotification($signalement));
        } catch (\Exception $e) {
            Log::error('Erreur notification pompier depuis compagnie: ' . $e->getMessage());
        }

        $customMessage = $request->input('message', '');

        // Envoi email supplémentaire si message personnalisé
        if ($pompier->email && $customMessage) {
            try {
                $compagnie = Auth::guard('compagnie')->user();
                $emailBody = "🚨 ALERTE ACCIDENT - Via {$compagnie->name}\n\n";
                $emailBody .= "Description: {$signalement->description}\n";
                if ($signalement->latitude && $signalement->longitude) {
                    $emailBody .= "GPS: {$signalement->latitude}, {$signalement->longitude}\n";
                    $emailBody .= "Google Maps: https://www.google.com/maps/search/?api=1&query={$signalement->latitude},{$signalement->longitude}\n";
                }
                $emailBody .= "\nMessage de la compagnie:\n{$customMessage}\n";

                Mail::raw($emailBody, function ($mail) use ($pompier, $compagnie) {
                    $mail->to($pompier->email)
                         ->subject("[URGENT] Accident signalé - {$compagnie->name}")
                         ->from(config('mail.from.address'), $compagnie->name);
                });
            } catch (\Exception $e) {
                Log::error("Erreur email pompier: " . $e->getMessage());
            }
        }

        return back()->with('success', "Le sapeur pompier \"{$pompier->name}\" ({$pompier->commune}) a été contacté avec succès.");
    }

    /**
     * Marquer un signalement comme traité.
     */
    public function markAsTraite($id)
    {
        $compagnieId = Auth::guard('compagnie')->id();

        $signalement = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->findOrFail($id);

        $signalement->statut = 'traite';
        $signalement->save();

        return back()->with('success', 'Le signalement a été marqué comme traité.');
    }

    /**
     * Action d'urgence : Interrompre le voyage suite à un accident.
     * Libère le chauffeur et immobilise le véhicule pour la journée.
     */
    public function interruptVoyage($id)
    {
        $compagnieId = Auth::guard('compagnie')->id();

        $signalement = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->with(['voyage', 'vehicule', 'personnel'])->findOrFail($id);

        // 1. Marquer le voyage comme interrompu (pour l'affichage utilisateur)
        if ($signalement->voyage) {
            $signalement->voyage->statut = 'interrompu';
            $signalement->voyage->save();
        }

        // 2. Immobiliser le véhicule pour la journée (Accident)
        if ($signalement->vehicule) {
            $signalement->vehicule->statut = 'indisponible';
            $signalement->vehicule->motif = 'Immobilisé suite à un accident';
            $signalement->vehicule->is_active = false;
            $signalement->vehicule->save();
        }

        // 3. Libérer le chauffeur mais le mettre au repos (Inapte pour aujourd'hui)
        if ($signalement->personnel) {
            $signalement->personnel->statut = 'indisponible'; // Ne pourra pas être repris aujourd'hui
            $signalement->personnel->save();
        }

        // 4. Marquer le signalement comme traité
        $signalement->statut = 'traite';
        $signalement->save();

        return back()->with('success', 'Le voyage a été interrompu. Le car et le chauffeur ont été mis hors service pour la journée.');
    }

    /**
     * Action Panne : Reprendre la route (La panne a été réparée)
     */
    public function resumeVoyage($id)
    {
        $compagnieId = Auth::guard('compagnie')->id();
        $signalement = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->findOrFail($id);

        $signalement->statut = 'traite';
        $signalement->save();

        return back()->with('success', 'Signalement de panne classé. Le voyage continue son cours.');
    }

    /**
     * Action Panne : Transbordement (Changement de véhicule)
     */
    public function transbordement(Request $request, $id)
    {
        $compagnieId = Auth::guard('compagnie')->id();
        $request->validate(['new_vehicule_id' => 'required|exists:vehicules,id']);

        $signalement = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->with(['voyage', 'vehicule'])->findOrFail($id);

        if (!$signalement->voyage) {
            return back()->with('error', 'Aucun voyage actif trouvé pour ce signalement.');
        }

        // 1. L'ancien véhicule passe en panne (donc indisponible)
        if ($signalement->vehicule) {
            $signalement->vehicule->statut = 'indisponible';
            $signalement->vehicule->motif = 'Panne sur route - En attente de dépannage';
            $signalement->vehicule->save();
        }

        // 2. Assigner le nouveau véhicule au voyage
        $signalement->voyage->vehicule_id = $request->new_vehicule_id;
        $signalement->voyage->save();

        // 3. Clôre le signalement
        $signalement->statut = 'traite';
        $signalement->save();

        return back()->with('success', 'Transbordement effectué. Un nouveau car a été assigné au voyage.');
    }

    /**
     * Marquer un signalement comme lu (AJAX depuis l'index).
     */
    public function markAsRead($id)
    {
        $compagnieId = Auth::guard('compagnie')->id();

        $signalement = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->findOrFail($id);

        if (!$signalement->is_read_by_company) {
            $signalement->is_read_by_company = true;
            $signalement->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Trouver le sapeur pompier le plus proche.
     */
    private function findNearestSapeurPompier($lat, $lon)
    {
        $pompiers = SapeurPompier::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('statut', 'actif')
            ->get();

        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($pompiers as $pompier) {
            $distance = $this->calculateDistance($lat, $lon, $pompier->latitude, $pompier->longitude);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $pompier;
            }
        }

        return $nearest;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Page de notification accident — afficher les passagers évacués avec contacts d'urgence.
     */
    public function notificationAccident($id)
    {
        $compagnieId = Auth::guard('compagnie')->id();
        $compagnie = Auth::guard('compagnie')->user();

        $signalement = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->with(['programme.gareDepart', 'programme.gareArrivee', 'vehicule', 'voyage', 'sapeurPompier'])
          ->findOrFail($id);

        $bilanPassagers = $signalement->bilan_passagers ?? [];
        $evacueeIds = collect($bilanPassagers)->where('statut', 'evacue')->pluck('reservation_id')->toArray();

        // Récupérer les réservations des passagers évacués
        $reservationsEvacuees = \App\Models\Reservation::whereIn('id', $evacueeIds)
            ->with('user')
            ->get();

        // Associer chaque réservation avec les données de l'hôpital
        $passagersEvacues = $reservationsEvacuees->map(function ($res) use ($bilanPassagers) {
            $bilanEntry = collect($bilanPassagers)->firstWhere('reservation_id', $res->id);
            return [
                'reservation' => $res,
                'nom' => trim(($res->passager_nom ?? '') . ' ' . ($res->passager_prenom ?? '')) ?: ($res->user->name ?? 'Inconnu'),
                'contact_urgence' => $res->passager_urgence ?? $res->ice_contact ?? null,
                'nom_contact_urgence' => $res->nom_passager_urgence ?? 'Contact d\'urgence',
                'email_passager' => $res->passager_email ?? ($res->user->email ?? null),
                'telephone_passager' => $res->passager_telephone ?? null,
                'hopital_nom' => $bilanEntry['hopital_nom'] ?? 'Non précisé',
                'hopital_adresse' => $bilanEntry['hopital_adresse'] ?? '',
                'seat' => $res->seat_number ?? '?',
            ];
        });

        // Grouper par hôpital
        $parHopital = $passagersEvacues->groupBy('hopital_nom');

        // Récupérer aussi les indemnes
        $indemneIds = collect($bilanPassagers)->where('statut', 'indemne')->pluck('reservation_id')->toArray();
        $countIndemnes = count($indemneIds);

        return view('compagnie.signalements.notification-accident', compact(
            'signalement',
            'compagnie',
            'passagersEvacues',
            'parHopital',
            'countIndemnes'
        ));
    }

    /**
     * Envoyer les notifications aux contacts d'urgence des passagers évacués.
     * Chaque contact reçoit un message personnalisé avec uniquement l'hôpital de SON proche.
     */
    public function envoyerNotifications(Request $request, $id)
    {
        $compagnieId = Auth::guard('compagnie')->id();
        $compagnie   = Auth::guard('compagnie')->user();

        $signalement = Signalement::whereHas('programme', function ($q) use ($compagnieId) {
            $q->where('compagnie_id', $compagnieId);
        })->with(['programme'])->findOrFail($id);

        $request->validate([
            'message'       => 'required|string|min:10',
            'reservations'  => 'required|array|min:1',
            'reservations.*'=> 'required|integer',
        ]);

        $messageTemplate = $request->input('message');
        $reservationIds  = $request->input('reservations');
        $bilanPassagers  = $signalement->bilan_passagers ?? [];

        $sent = 0;
        $errors = 0;
        $smsService = app(SmsService::class);

        // Récupérer uniquement les réservations sélectionnées
        $reservations = Reservation::whereIn('id', $reservationIds)->with('user')->get();

        foreach ($reservations as $res) {
            // Trouver l'entrée bilan spécifique à ce passager
            $bilanEntry = collect($bilanPassagers)->firstWhere('reservation_id', $res->id);
            if (!$bilanEntry || ($bilanEntry['statut'] ?? '') !== 'evacue') {
                continue;
            }

            $hopitalNom     = $bilanEntry['hopital_nom'] ?? 'hôpital non précisé';
            $hopitalAdresse = $bilanEntry['hopital_adresse'] ?? '';
            $hopitalInfo    = $hopitalNom . ($hopitalAdresse ? "\nLocalisation : " . $hopitalAdresse : '');

            // Construire le message personnalisé pour CE passager
            $messagePersonnalise = str_replace('{HOPITAL}', $hopitalInfo, $messageTemplate);

            $contactUrgence = $res->passager_urgence ?? $res->ice_contact ?? null;
            $emailPassager  = $res->passager_email ?? ($res->user->email ?? null);

            try {
                // 1. Envoi par email si disponible
                if ($emailPassager && filter_var($emailPassager, FILTER_VALIDATE_EMAIL)) {
                    Mail::raw($messagePersonnalise, function ($mail) use ($emailPassager, $compagnie, $signalement) {
                        $mail->to($emailPassager)
                             ->subject("Notification d'accident — {$compagnie->name}")
                             ->from(config('mail.from.address'), $compagnie->name);
                    });
                    $sent++;
                }

                // 2. Envoi SMS si contact d'urgence disponible
                if ($contactUrgence) {
                    $smsSent = $smsService->sendSms($contactUrgence, $messagePersonnalise);
                    if ($smsSent) {
                        $sent++;
                        Log::info("SMS accident envoyé", ['to' => $contactUrgence, 'reservation_id' => $res->id]);
                    } else {
                        $errors++;
                        Log::error("Échec SMS accident", [
                            'to'             => $contactUrgence,
                            'reservation_id' => $res->id,
                        ]);
                    }
                }

            } catch (\Exception $e) {
                $errors++;
                Log::error("Erreur envoi notification accident #{$signalement->id}: " . $e->getMessage(), [
                    'reservation_id' => $res->id,
                ]);
            }
        }

        $msg = "{$sent} notification(s) envoyée(s) avec succès.";
        if ($errors > 0) $msg .= " {$errors} échec(s) — vérifiez les logs.";

        return back()->with($errors > 0 && $sent === 0 ? 'error' : 'success', $msg);
    }
}
