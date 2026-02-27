<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use App\Models\CompanyMessage;
use App\Models\SapeurPompier;
use App\Notifications\NewSignalementNotification;
use App\Notifications\NewInternalMessageNotification;
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

        return view('compagnie.signalements.show', compact('signalement', 'gareDepart', 'sapeurPompier'));
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
}
