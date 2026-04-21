<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use App\Models\Convoi;
use App\Models\Voyage;
use App\Models\Programme;
use App\Models\SapeurPompier;
use App\Notifications\NewSignalementNotification;
use App\Mail\SignalementCompagnieNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ChauffeurSignalementController extends Controller
{
    public function index()
    {
        $chauffeur = Auth::guard('chauffeur')->user();
        $signalements = Signalement::where('personnel_id', $chauffeur->id)
            ->whereNotNull('compagnie_id')
            ->whereNull('user_id')
            ->with(['voyage.gareDepart', 'voyage.gareArrivee', 'compagnie', 'vehicule',
                    'convoi.itineraire', 'convoi.vehicule', 'convoi.gare'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('chauffeur.signalements.index', compact('signalements'));
    }

    public function create(Request $request)
    {
        $chauffeur = Auth::guard('chauffeur')->user();
        $preselectedVoyageId = $request->query('voyage_id');
        $preselectedConvoiId  = $request->query('convoi_id');

        // ── Convois en cours pour ce chauffeur ──
        $activeConvoi = null;
        if ($preselectedConvoiId) {
            $activeConvoi = Convoi::where('personnel_id', $chauffeur->id)
                ->where('statut', 'en_cours')
                ->with(['vehicule', 'itineraire'])
                ->find($preselectedConvoiId);
        }

        // Si un convoi est pré-sélectionné, on n'a pas besoin du dropdown de voyages
        if ($activeConvoi) {
            return view('chauffeur.signalements.create', [
                'voyages'              => collect(),
                'preselectedVoyageId'  => null,
                'activeVoyage'         => null,
                'activeConvoi'         => $activeConvoi,
            ]);
        }

        // ── Voyages en cours ou confirmés du chauffeur ──
        $voyages = Voyage::where('personnel_id', $chauffeur->id)
            ->whereIn('statut', ['confirmé', 'en_cours'])
            ->with(['programme', 'vehicule', 'gareDepart'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Auto-detect the active voyage (en_cours)
        $activeVoyage = $voyages->firstWhere('statut', 'en_cours');

        if ($preselectedVoyageId) {
            $activeVoyage = $voyages->firstWhere('id', $preselectedVoyageId) ?? $activeVoyage;
        }

        if ($activeVoyage && !$preselectedVoyageId) {
            $preselectedVoyageId = $activeVoyage->id;
        }

        return view('chauffeur.signalements.create', [
            'voyages'             => $voyages,
            'preselectedVoyageId' => $preselectedVoyageId,
            'activeVoyage'        => $activeVoyage,
            'activeConvoi'        => null,
        ]);
    }

    public function store(Request $request)
    {
        // Soit un voyage, soit un convoi — l'un des deux est obligatoire
        $request->validate([
            'type'        => 'required|in:accident,panne,retard,comportement,autre',
            'description' => 'required|string',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'photo'       => 'nullable|image|max:10240',
            'voyage_id'   => 'nullable|exists:voyages,id',
            'convoi_id'   => 'nullable|exists:convois,id',
        ]);

        if (!$request->filled('voyage_id') && !$request->filled('convoi_id')) {
            return back()->withErrors(['voyage_id' => 'Veuillez sélectionner un voyage ou un convoi.'])->withInput();
        }

        try {
            DB::beginTransaction();

            $chauffeur = Auth::guard('chauffeur')->user();

            $signalement = new Signalement();
            $signalement->personnel_id = $chauffeur->id;
            $signalement->compagnie_id = $chauffeur->compagnie_id;
            $signalement->type        = $request->type;
            $signalement->description = $request->description;
            $signalement->latitude    = $request->latitude ?? null;
            $signalement->longitude   = $request->longitude ?? null;
            $signalement->statut      = 'nouveau';

            if ($request->filled('convoi_id')) {
                $convoi = Convoi::with('gare')->findOrFail($request->convoi_id);
                $signalement->convoi_id   = $convoi->id;
                $signalement->vehicule_id = $convoi->vehicule_id;
                // programme_id nullable, on laisse null pour les convois
            } else {
                $voyage = Voyage::findOrFail($request->voyage_id);
                $signalement->voyage_id    = $voyage->id;
                $signalement->programme_id = $voyage->programme_id;
                $signalement->vehicule_id  = $voyage->vehicule_id;
            }

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
                    } catch (\Exception $e) {
                        Log::error('Erreur notification chauffeur pompier: ' . $e->getMessage());
                    }
                }
            }

            // Notification Compagnie
            try {
                if ($chauffeur->compagnie && $chauffeur->compagnie->email) {
                    Mail::to($chauffeur->compagnie->email)
                        ->send(new SignalementCompagnieNotification($signalement));
                }
            } catch (\Exception $e) {
                Log::error('Erreur envoi email chauffeur compagnie: ' . $e->getMessage());
            }

            // Notification Gare (pour les signalements liés à un convoi)
            if (isset($convoi) && $convoi->gare && $convoi->gare->email) {
                try {
                    Mail::to($convoi->gare->email)
                        ->send(new SignalementCompagnieNotification($signalement));
                } catch (\Exception $e) {
                    Log::error('Erreur envoi email gare signalement convoi: ' . $e->getMessage());
                }
            }

            DB::commit();

            return redirect()->route('chauffeur.signalements.index')->with('success', 'Votre signalement a été envoyé avec succès à la compagnie.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur ChauffeurSignalementController@store: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'envoi du signalement.')->withInput();
        }
    }

    public function show(Signalement $signalement)
    {
        $chauffeur = Auth::guard('chauffeur')->user();
        if ($signalement->personnel_id !== $chauffeur->id) {
            abort(403);
        }

        $signalement->load([
            'voyage.programme', 'voyage.gareDepart', 'vehicule', 'compagnie',
            'convoi.itineraire', 'convoi.gare', 'convoi.vehicule', 'convoi.passagers',
        ]);

        return view('chauffeur.signalements.show', compact('signalement'));
    }

    private function findNearestSapeurPompier($lat, $lon)
    {
        $pompiers = SapeurPompier::whereNotNull('latitude')
            ->whereNotNull('longitude')
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
