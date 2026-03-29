<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Models\GareLocationRequest;
use App\Models\GareMessage;
use App\Models\Personnel;
use App\Models\Vehicule;
use App\Models\Voyage;
use App\Models\Programme;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class GareDashboardController extends Controller
{
    public function index()
    {
        $gare = Auth::guard('gare')->user();
        $compagnieId = $gare->compagnie_id;

        // Stats for this specific gare
        $totalPersonnel = Personnel::where('gare_id', $gare->id)->count();
        $totalChauffeurs = Personnel::where('gare_id', $gare->id)->where('type_personnel', 'Chauffeur')->count();
        $chauffeursDisponibles = Personnel::where('gare_id', $gare->id)->where('type_personnel', 'Chauffeur')->where('statut', 'disponible')->count();
        $totalVehicules = Vehicule::where('gare_id', $gare->id)->where('is_active', true)->count();
        $vehiculesDisponibles = Vehicule::where('gare_id', $gare->id)->where('is_active', true)->where('statut', 'disponible')->count();

        // Today's data
        $today = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->format('H:i:s');

        // 1. Assigned voyages for today
        $voyagesAujourdhui = Voyage::where('gare_depart_id', $gare->id)
            ->whereDate('date_voyage', $today)
            ->with(['programme.gareArrivee', 'chauffeur', 'vehicule'])
            ->get();

        // 2. Unassigned programs for today
        // We only show programs that are active and haven't been assigned a voyage for today
        $programmesNonAssignes = Programme::where('gare_depart_id', $gare->id)
            ->where('statut', 'actif')
            ->whereDate('date_depart', '<=', $today)
            ->where(function($q) use ($today) {
                $q->whereDate('date_fin', '>=', $today)
                  ->orWhereNull('date_fin');
            })
            ->whereDoesntHave('voyages', function($q) use ($today) {
                $q->whereDate('date_voyage', $today)
                  ->where('statut', '!=', 'annulé');
            })
            ->whereTime('heure_depart', '>', $currentTime) // Only upcoming ones
            ->with(['gareArrivee'])
            ->orderBy('heure_depart')
            ->get();

        $programmesActifs = Programme::where('compagnie_id', $compagnieId)
            ->where('gare_depart_id', $gare->id)
            ->where('statut', 'actif')
            ->whereDate('date_depart', '<=', $today)
            ->where(function($q) use ($today) {
                $q->whereDate('date_fin', '>=', $today)
                  ->orWhereNull('date_fin');
            })
            ->count();

        // Popup for approved location update
        $locationApprovedNotification = GareLocationRequest::where('gare_id', $gare->id)
            ->where('statut', 'approved')
            ->where('gare_notified', false)
            ->first();

        // Voyages en cours passant par cette gare (pour bannière live)
        $liveVoyagesCount = Voyage::where('statut', 'en_cours')
            ->whereHas('programme', function ($q) use ($gare) {
                $q->where('compagnie_id', $gare->compagnie_id)
                  ->where(function ($q2) use ($gare) {
                      $q2->where('gare_depart_id', $gare->id)
                         ->orWhere('gare_arrivee_id', $gare->id);
                  });
            })->count();

        return view('gare-espace.dashboard', compact(
            'gare', 'totalPersonnel', 'totalChauffeurs', 'chauffeursDisponibles',
            'totalVehicules', 'vehiculesDisponibles', 'voyagesAujourdhui',
            'programmesActifs', 'programmesNonAssignes', 'locationApprovedNotification',
            'liveVoyagesCount'
        ));
    }

    /**
     * Voir le profil de la gare
     */
    public function profile()
    {
        $gare = Auth::guard('gare')->user();
        $gare->load('compagnie');

        $locationRequests = GareLocationRequest::where('gare_id', $gare->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $pendingRequest = $locationRequests->firstWhere('statut', 'pending');

        return view('gare-espace.profile', compact('gare', 'locationRequests', 'pendingRequest'));
    }

    /**
     * Mettre à jour le profil de la gare
     */
    public function updateProfile(Request $request)
    {
        $gare = Auth::guard('gare')->user();
        
        $request->validate([
            'nom_gare' => 'required|string|max:255',
            'responsable_nom' => 'required|string|max:255',
            'responsable_prenom' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'contact_urgence' => 'nullable|string|max:20',
            'commune' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['nom_gare', 'responsable_nom', 'responsable_prenom', 'contact', 'contact_urgence', 'commune', 'adresse']);

        if ($request->hasFile('profile_image')) {
            if ($gare->profile_image) {
                Storage::disk('public')->delete($gare->profile_image);
            }
            $path = $request->file('profile_image')->store('gare_profiles', 'public');
            $data['profile_image'] = $path;
        }

        $gare->update($data);

        return back()->with('success', 'Profil de la gare mis à jour avec succès !');
    }

    /**
     * Soumettre une demande de mise à jour de localisation (requiert mot de passe)
     * La compagnie devra approuver avant que la position soit réellement mise à jour.
     */
    public function requestLocationUpdate(Request $request)
    {
        $gare = Auth::guard('gare')->user();

        $request->validate([
            'password'  => 'required|string',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if (!Hash::check($request->password, $gare->password)) {
            return response()->json(['success' => false, 'message' => 'Mot de passe incorrect.'], 422);
        }

        // Annuler toute demande pending existante
        GareLocationRequest::where('gare_id', $gare->id)
            ->where('statut', 'pending')
            ->update(['statut' => 'rejected', 'rejected_reason' => 'Remplacée par une nouvelle demande.']);

        GareLocationRequest::create([
            'gare_id'      => $gare->id,
            'compagnie_id' => $gare->compagnie_id,
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'statut'       => 'pending',
        ]);

        // Message dans la boîte de réception de la compagnie
        GareMessage::create([
            'gare_id'        => $gare->id,
            'sender_type'    => null,
            'sender_id'      => null,
            'recipient_type' => 'App\Models\Compagnie',
            'recipient_id'   => $gare->compagnie_id,
            'subject'        => '📍 Demande de mise à jour GPS — ' . $gare->nom_gare,
            'message'        => "La gare {$gare->nom_gare} ({$gare->ville}) a soumis une demande de mise à jour de sa position GPS.\n\nNouvelles coordonnées demandées :\n• Latitude : {$request->latitude}\n• Longitude : {$request->longitude}\n\n<a href=\"" . route('compagnie.gare-location-requests.index') . "\" style=\"display:inline-block;margin-top:8px;padding:8px 16px;background:#f97316;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;\">📍 Approuver la demande → Localisation GPS</a>",
            'is_read'        => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Demande envoyée à votre compagnie. La position sera mise à jour après approbation.',
        ]);
    }

    /**
     * Marquer le popup "localisation approuvée" comme affiché
     */
    public function markLocationNotified(Request $request)
    {
        $gare = Auth::guard('gare')->user();

        GareLocationRequest::where('gare_id', $gare->id)
            ->where('statut', 'approved')
            ->where('gare_notified', false)
            ->update(['gare_notified' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Changer le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $gare = Auth::guard('gare')->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $gare->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel ne correspond pas.']);
        }

        $gare->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Mot de passe mis à jour avec succès !');
    }
}
