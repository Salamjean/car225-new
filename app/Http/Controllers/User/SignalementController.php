<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use App\Models\SapeurPompier;
use App\Models\Programme;
use App\Notifications\SendSignalementToSapeurPompierNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

class SignalementController extends Controller
{
    /**
     * Show the form for creating a new report.
     */
    public function create($programmeId)
    {
        $programme = Programme::findOrFail($programmeId);
        return view('user.signalement.create', compact('programme'));
    }

    /**
     * Store a newly created report in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'type' => 'required|in:accident,panne,retard,comportement,autre',
            'description' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', // Validation Image (10MB max)
        ]);

        $signalement = new Signalement();
        $signalement->user_id = Auth::id() ?? 1;
        $signalement->programme_id = $validated['programme_id'];
        $signalement->type = $validated['type'];
        $signalement->description = $validated['description'];
        $signalement->latitude = $validated['latitude'];
        $signalement->longitude = $validated['longitude'];

        // Gestion de l'upload photo
        if ($request->hasFile('photo')) {
            // Stocke dans storage/app/public/signalements
            // Assurez-vous d'avoir fait : php artisan storage:link
            $path = $request->file('photo')->store('signalements', 'public');
            $signalement->photo_path = $path; // Assurez-vous d'avoir une colonne 'photo_path' dans votre migration
        }

        $signalement->save();

        if ($validated['type'] === 'accident') {
            $nearestSP = $this->findNearestSapeurPompier($validated['latitude'], $validated['longitude']);

            if ($nearestSP) {
                $signalement->sapeur_pompier_id = $nearestSP->id;
                $signalement->save();

                Notification::route('mail', $nearestSP->email)
                    ->notify(new SendSignalementToSapeurPompierNotification($signalement));
            }
        }

        return redirect()->route('user.reservations.index')
            ->with('success', 'Votre signalement a bien été enregistré.');
    }

    private function findNearestSapeurPompier($lat, $lng)
    {
        if (!$lat || !$lng)
            return null;

        $sapeurPompiers = SapeurPompier::where('statut', 'actif')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $nearest = null;
        $minDistance = PHP_INT_MAX;

        foreach ($sapeurPompiers as $sp) {
            $distance = $this->calculateDistance($lat, $lng, $sp->latitude, $sp->longitude);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $sp;
            }
        }

        return $nearest;
    }

    // Haversine formula
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
