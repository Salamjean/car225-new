<?php

namespace App\Http\Controllers\Api\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Compagnie;
use App\Models\GareMessage;
use App\Models\Signalement;
use App\Models\Voyage;
use App\Models\Convoi;
use App\Models\SapeurPompier;
use App\Notifications\NewSignalementNotification;
use App\Mail\SignalementCompagnieNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SignalementApiController extends Controller
{
    /**
     * Liste des signalements du chauffeur
     */
    public function index(Request $request)
    {
        $chauffeur = $request->user();

        $signalements = Signalement::where('personnel_id', $chauffeur->id)
            ->whereNotNull('compagnie_id')
            ->whereNull('user_id')
            ->with(['voyage.gareDepart', 'voyage.gareArrivee', 'convoi.itineraire', 'compagnie', 'vehicule'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'signalements' => $signalements->map(function($s) {
                return [
                    'id' => $s->id,
                    'type' => $s->type,
                    'description' => $s->description,
                    'statut' => $s->statut,
                    'photo' => $s->photo_path ? asset($s->photo_path) : null,
                    'latitude' => $s->latitude,
                    'longitude' => $s->longitude,
                    'created_at' => $s->created_at->format('d/m/Y H:i'),
                    'voyage' => $s->voyage ? [
                        'id' => $s->voyage->id,
                        'gare_depart' => optional($s->voyage->gareDepart)->nom_gare ?? '',
                        'gare_arrivee' => optional($s->voyage->gareArrivee)->nom_gare ?? '',
                    ] : null,
                    'convoi' => $s->convoi ? [
                        'id'        => $s->convoi->id,
                        'reference' => $s->convoi->reference,
                        'trajet'    => ($s->convoi->lieu_depart ?? optional($s->convoi->itineraire)->point_depart ?? '')
                            . ' → '
                            . ($s->convoi->lieu_retour ?? optional($s->convoi->itineraire)->point_arrive ?? ''),
                    ] : null,
                    'vehicule' => $s->vehicule ? $s->vehicule->immatriculation : null,
                ];
            }),
            'pagination' => [
                'current_page' => $signalements->currentPage(),
                'last_page' => $signalements->lastPage(),
                'total' => $signalements->total(),
            ],
        ]);
    }

    /**
     * Détails d'un signalement
     */
    public function show(Request $request, Signalement $signalement)
    {
        $chauffeur = $request->user();
        if ($signalement->personnel_id !== $chauffeur->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
        }

        $signalement->load(['voyage.programme', 'voyage.gareDepart', 'convoi.itineraire', 'vehicule', 'compagnie']);

        return response()->json([
            'success' => true,
            'signalement' => [
                'id' => $signalement->id,
                'type' => $signalement->type,
                'description' => $signalement->description,
                'statut' => $signalement->statut,
                'photo' => $signalement->photo_path ? asset($signalement->photo_path) : null,
                'latitude' => $signalement->latitude,
                'longitude' => $signalement->longitude,
                'created_at' => $signalement->created_at->format('d/m/Y H:i'),
                'voyage' => $signalement->voyage ? [
                    'id' => $signalement->voyage->id,
                    'programme' => optional($signalement->voyage->programme)->point_depart . ' → ' . optional($signalement->voyage->programme)->point_arrive,
                    'gare_depart' => optional($signalement->voyage->gareDepart)->nom_gare ?? '',
                ] : null,
                'convoi' => $signalement->convoi ? [
                    'id'        => $signalement->convoi->id,
                    'reference' => $signalement->convoi->reference,
                    'trajet'    => ($signalement->convoi->lieu_depart ?? optional($signalement->convoi->itineraire)->point_depart ?? '')
                        . ' → '
                        . ($signalement->convoi->lieu_retour ?? optional($signalement->convoi->itineraire)->point_arrive ?? ''),
                ] : null,
                'vehicule' => $signalement->vehicule ? $signalement->vehicule->immatriculation : null,
                'compagnie' => $signalement->compagnie ? $signalement->compagnie->name : null,
            ],
        ]);
    }

    /**
     * Voyages disponibles pour signalement
     */
    public function getVoyagesForSignalement(Request $request)
    {
        $chauffeur = $request->user();

        $voyages = Voyage::where('personnel_id', $chauffeur->id)
            ->whereIn('statut', ['confirmé', 'en_cours'])
            ->with(['programme', 'vehicule', 'gareDepart'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($v) {
                return [
                    'id' => $v->id,
                    'date_voyage' => $v->date_voyage,
                    'statut' => $v->statut,
                    'programme' => $v->programme ? $v->programme->point_depart . ' → ' . $v->programme->point_arrive : '',
                    'vehicule' => $v->vehicule ? $v->vehicule->immatriculation : 'N/A',
                    'gare_depart' => optional($v->gareDepart)->nom_gare ?? '',
                ];
            });

        // Convois actifs du chauffeur (mêmes conditions que voyages : actifs)
        $convois = Convoi::where('personnel_id', $chauffeur->id)
            ->whereIn('statut', ['paye', 'en_cours'])
            ->with(['itineraire', 'vehicule'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($c) {
                $depart  = $c->lieu_depart ?? ($c->itineraire->point_depart ?? 'Départ');
                $arrivee = $c->lieu_retour ?? ($c->itineraire->point_arrive ?? 'Arrivée');
                return [
                    'id'         => $c->id,
                    'reference'  => $c->reference,
                    'statut'     => $c->statut,
                    'trajet'     => $depart . ' → ' . $arrivee,
                    'date'       => $c->aller_done ? $c->date_retour : $c->date_depart,
                    'vehicule'   => $c->vehicule ? $c->vehicule->immatriculation : 'N/A',
                    'aller_done' => (bool) $c->aller_done,
                ];
            });

        return response()->json([
            'success' => true,
            'voyages' => $voyages,
            'convois' => $convois,
        ]);
    }

    /**
     * Créer un signalement (voyage OU convoi)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'voyage_id'   => 'required_without:convoi_id|nullable|exists:voyages,id',
            'convoi_id'   => 'required_without:voyage_id|nullable|exists:convois,id',
            'type'        => 'required|in:accident,panne,retard,comportement,autre',
            'description' => 'required|string',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'photo'       => 'required_if:type,accident|image|max:10240',
        ], [
            'voyage_id.required_without' => 'Veuillez préciser le voyage ou le convoi concerné.',
            'convoi_id.required_without' => 'Veuillez préciser le voyage ou le convoi concerné.',
        ]);

        try {
            DB::beginTransaction();

            $chauffeur = $request->user();

            $signalement = new Signalement();
            $signalement->personnel_id = $chauffeur->id;
            $signalement->compagnie_id = $chauffeur->compagnie_id;

            if (!empty($validated['convoi_id'])) {
                $convoi = Convoi::findOrFail($validated['convoi_id']);
                if ((int) $convoi->personnel_id !== (int) $chauffeur->id) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Ce convoi ne vous appartient pas.',
                    ], 403);
                }
                $signalement->convoi_id   = $convoi->id;
                $signalement->vehicule_id = $convoi->vehicule_id;
                $signalement->compagnie_id = $convoi->compagnie_id ?? $chauffeur->compagnie_id;
            } else {
                $voyage = Voyage::findOrFail($validated['voyage_id']);
                $signalement->voyage_id    = $voyage->id;
                $signalement->programme_id = $voyage->programme_id;
                $signalement->vehicule_id  = $voyage->vehicule_id;
            }

            $signalement->type = $validated['type'];
            $signalement->description = $validated['description'];
            $signalement->latitude = $validated['latitude'] ?? null;
            $signalement->longitude = $validated['longitude'] ?? null;
            $signalement->statut = 'nouveau';

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('signalements', 'public');
                $signalement->photo_path = 'storage/' . $path;
            }

            $signalement->save();

            // Logique Sapeur Pompier pour les accidents
            if ($signalement->type === 'accident' && $signalement->latitude && $signalement->longitude) {
                $nearestFirefighter = $this->findNearestSapeurPompier($signalement->latitude, $signalement->longitude);
                if ($nearestFirefighter) {
                    $signalement->sapeur_pompier_id = $nearestFirefighter->id;
                    $signalement->save();
                    try {
                        Notification::send($nearestFirefighter, new NewSignalementNotification($signalement));
                        Log::info("Signalement #{$signalement->id} : pompier #{$nearestFirefighter->id} notifié.");
                    } catch (\Exception $e) {
                        Log::error('Erreur notification pompier API: ' . $e->getMessage());
                    }
                } else {
                    Log::warning("Signalement #{$signalement->id} accident : aucun sapeur-pompier géolocalisé trouvé.");
                }
            } elseif ($signalement->type === 'accident') {
                Log::warning("Signalement #{$signalement->id} accident : pas de coordonnées GPS — pompier non notifié.");
            }

            // Notification Compagnie — on cible la compagnie effectivement
            // associée au signalement (priorité au convoi/voyage), pas
            // uniquement celle du chauffeur (qui peut diverger).
            $compagnieRecipient = null;
            if ($signalement->compagnie_id) {
                $compagnieRecipient = Compagnie::find($signalement->compagnie_id);
            }
            $compagnieRecipient ??= optional($chauffeur)->compagnie;

            if ($compagnieRecipient && !empty($compagnieRecipient->email)) {
                try {
                    Mail::to($compagnieRecipient->email)
                        ->send(new SignalementCompagnieNotification($signalement));
                    Log::info("Signalement #{$signalement->id} : email envoyé à la compagnie {$compagnieRecipient->email}.");
                } catch (\Throwable $e) {
                    Log::error('Erreur email compagnie API: ' . $e->getMessage());
                }
            } else {
                Log::warning("Signalement #{$signalement->id} : compagnie sans email — pas de notification mail envoyée.");
            }

            // Pour un signalement de convoi, on prévient aussi la gare propriétaire
            // (canal interne /messages). Reproduit le pattern utilisé pour le désistement.
            if ($signalement->convoi_id) {
                try {
                    $convoi = Convoi::find($signalement->convoi_id);
                    if ($convoi && $convoi->gare_id) {
                        GareMessage::create([
                            'gare_id'        => $convoi->gare_id,
                            'sender_type'    => \App\Models\Personnel::class,
                            'sender_id'      => $chauffeur->id,
                            'recipient_type' => \App\Models\Gare::class,
                            'recipient_id'   => $convoi->gare_id,
                            'subject'        => 'Signalement convoi ' . ($convoi->reference ?? '#' . $convoi->id)
                                . ' — ' . ucfirst($signalement->type),
                            'message'        => "Le chauffeur {$chauffeur->prenom} {$chauffeur->name} a soumis un signalement "
                                . "via Mobile pour le convoi " . ($convoi->reference ?? '#' . $convoi->id) . ".\n\n"
                                . "Type : " . ucfirst($signalement->type) . "\n"
                                . "Description : " . $signalement->description,
                            'is_read'        => false,
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::error('Erreur notif gare signalement convoi: ' . $e->getMessage());
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Signalement envoyé avec succès.',
                'signalement' => [
                    'id' => $signalement->id,
                    'type' => $signalement->type,
                    'statut' => $signalement->statut,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur SignalementApiController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'envoi du signalement.',
            ], 500);
        }
    }

    private function findNearestSapeurPompier($lat, $lon)
    {
        $pompiers = SapeurPompier::whereNotNull('latitude')->whereNotNull('longitude')->get();
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
