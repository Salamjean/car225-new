<?php

namespace App\Http\Controllers\Api\Hotesse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Programme;
use App\Models\Reservation;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Log;

class HotesseController extends Controller
{
    /**
     * Dashboard stats
     */
    public function dashboard(Request $request)
    {
        $hotesse = $request->user();
        $today = now()->toDateString();
        
        $stats = [
            'compagnie' => $hotesse->compagnie->name ?? 'N/A',
            'compagnie_logo' => $hotesse->compagnie->path_logo ?? null,
            'compagnie_slogan' => $hotesse->compagnie->slogan ?? null,
            'ventes_aujourdhui' => Reservation::where('hotesse_id', $hotesse->id)
                ->whereDate('created_at', $today)
                ->count(),
            'revenu_aujourdhui' => Reservation::where('hotesse_id', $hotesse->id)
                ->whereDate('created_at', $today)
                ->sum('montant'),
            'revenu_global' => Reservation::where('hotesse_id', $hotesse->id)
                ->sum('montant'),
        ];

        // Chart Data for last 7 days
        $chartData = [];
        $chartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('d/m');
            $chartData[] = Reservation::where('hotesse_id', $hotesse->id)
                ->whereDate('created_at', $date->toDateString())
                ->sum('montant');
        }

        $recent_reservations_models = Reservation::where('hotesse_id', $hotesse->id)
            ->with(['programme'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recent_reservations = $recent_reservations_models->map(function ($rv) {
            $ticketNo = 'TK-' . str_pad($rv->id, 3, '0', STR_PAD_LEFT);
            $formattedStatut = 'En attente';
            if ($rv->statut === 'confirmee') $formattedStatut = 'Confirmé';
            elseif ($rv->statut === 'annulee') $formattedStatut = 'Annulé';
            elseif ($rv->statut === 'terminee') $formattedStatut = 'Terminé';

            return [
                'id' => $rv->id,
                'ticket_no' => $ticketNo,
                'reference' => $rv->reference,
                'passager' => $rv->passager_prenom . ' ' . $rv->passager_nom,
                'trajet' => $rv->programme ? ($rv->programme->point_depart . ' → ' . $rv->programme->point_arrive) : 'N/A',
                'prix' => number_format($rv->montant, 0, ',', ' ') . ' FCFA',
                'date' => $rv->date_voyage ? $rv->date_voyage->format('d M Y') : '',
                'heure' => substr($rv->heure_depart, 0, 5),
                'siege' => 'Place ' . $rv->seat_number,
                'statut' => $formattedStatut,
                'qr_code' => $rv->qr_code,
                'qr_code_url' => $rv->qr_code_path ? asset('storage/' . $rv->qr_code_path) : null,
                'created_at' => $rv->created_at->format('d/m/Y H:i')
            ];
        });

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'chart' => [
                'labels' => $chartLabels,
                'data' => $chartData
            ],
            'recent_reservations' => $recent_reservations
        ]);
    }

    /**
     * Get Profile
     */
    public function profile(Request $request)
    {
        $hotesse = $request->user()->load('compagnie');

        if ($hotesse->profile_picture && !str_starts_with($hotesse->profile_picture, 'storage/')) {
            $hotesse->profile_picture = 'storage/' . $hotesse->profile_picture;
        }

        return response()->json([
            'success' => true,
            'hotesse' => $hotesse
        ]);
    }

    /**
     * Update Profile
     */
    public function updateProfile(Request $request)
    {
        $hotesse = $request->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'cas_urgence' => 'nullable|string|max:20',
            'commune' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'prenom', 'contact', 'cas_urgence', 'commune']);

        if ($request->hasFile('profile_picture')) {
            if ($hotesse->profile_picture) {
                Storage::disk('public')->delete($hotesse->profile_picture);
            }
            $path = $request->file('profile_picture')->store('hotesse_profiles', 'public');
            $data['profile_picture'] = $path;
        }

        $hotesse->update($data);

        $hotesse = $hotesse->fresh()->load('compagnie');
        if ($hotesse->profile_picture && !str_starts_with($hotesse->profile_picture, 'storage/')) {
            $hotesse->profile_picture = 'storage/' . $hotesse->profile_picture;
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'hotesse' => $hotesse
        ]);
    }

    /**
     * Update Password
     */
    public function updatePassword(Request $request)
    {
        $hotesse = $request->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $hotesse->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Le mot de passe actuel ne correspond pas.'
            ], 400);
        }

        $hotesse->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe mis à jour avec succès'
        ]);
    }

    /**
     * List of Sales (Ventes)
     */
    public function ventes(Request $request)
    {
        $hotesse = $request->user();
        
        $query = Reservation::where('hotesse_id', $hotesse->id)
            ->with(['programme']);

        if ($request->filled('date_debut')) {
            $query->whereDate('date_voyage', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_voyage', '<=', $request->date_fin);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $totalVentes = (clone $query)->where('statut', 'confirmee')->count();
        $totalRevenu = (clone $query)->where('statut', 'confirmee')->sum('montant');
        $totalAnnulations = (clone $query)->where('statut', 'annulee')->count();

        $ventes = $query->latest()->paginate($request->input('per_page', 10));

        $ventes->getCollection()->transform(function ($rv) {
            $formattedStatut = 'En attente';
            if ($rv->statut === 'confirmee') $formattedStatut = 'Confirmé';
            elseif ($rv->statut === 'annulee') $formattedStatut = 'Annulé';
            elseif ($rv->statut === 'terminee') $formattedStatut = 'Terminé';

            $ticketNo = 'TK-' . str_pad($rv->id, 3, '0', STR_PAD_LEFT);

            return [
                'id' => $rv->id,
                'ticket_no' => $ticketNo,
                'reference' => $rv->reference,
                'passager' => $rv->passager_prenom . ' ' . $rv->passager_nom,
                'trajet' => $rv->programme ? ($rv->programme->point_depart . ' → ' . $rv->programme->point_arrive) : 'N/A',
                'prix' => number_format($rv->montant, 0, ',', ' ') . ' FCFA',
                'date' => $rv->date_voyage ? $rv->date_voyage->format('d M Y') : '',
                'heure' => substr($rv->heure_depart, 0, 5),
                'date_heure' => ($rv->date_voyage ? $rv->date_voyage->format('d M Y') : '') . ' • ' . substr($rv->heure_depart, 0, 5),
                'siege' => 'Place ' . $rv->seat_number,
                'statut' => $formattedStatut,
                'qr_code' => $rv->qr_code,
                'qr_code_url' => $rv->qr_code_path ? asset('storage/' . $rv->qr_code_path) : null,
                'created_at' => $rv->created_at->format('d/m/Y H:i')
            ];
        });

        return response()->json([
            'success' => true,
            'ventes' => $ventes,
            'stats' => [
                'total_ventes' => $totalVentes,
                'total_revenu' => $totalRevenu,
                'total_annulations' => $totalAnnulations
            ]
        ]);
    }

    /**
     * Voir tous les programmes de la compagnie
     */
    public function indexProgrammes(Request $request)
    {
        $hotesse = $request->user();
        
        $programmes = Programme::with(['itineraire', 'compagnie'])
            ->where('compagnie_id', $hotesse->compagnie_id)
            ->where('statut', 'actif')
            ->orderBy('date_depart', 'asc')
            ->orderBy('heure_depart', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'programmes' => $programmes
        ]);
    }

    /**
     * Recherche de programmes pour vente de ticket (POST)
     */
    public function searchProgrammes(Request $request)
    {
        $hotesse = $request->user();
        
        $query = Programme::with(['compagnie'])
            ->where('compagnie_id', $hotesse->compagnie_id)
            ->where('statut', 'actif');

        // Filtre Point de Départ
        if ($request->filled('point_depart')) {
            $term = trim(explode(',', $request->point_depart)[0]);
            $query->where(function($q) use ($term) {
                $q->where('point_depart', 'LIKE', '%' . $term . '%')
                  ->orWhereHas('itineraire', function($subQ) use ($term) {
                      $subQ->where('point_depart', 'LIKE', '%' . $term . '%');
                  });
            });
        }

        // Filtre Point d'Arrivée
        if ($request->filled('point_arrive')) {
            $term = trim(explode(',', $request->point_arrive)[0]);
            $query->where(function($q) use ($term) {
                $q->where('point_arrive', 'LIKE', '%' . $term . '%')
                  ->orWhereHas('itineraire', function($subQ) use ($term) {
                      $subQ->where('point_arrive', 'LIKE', '%' . $term . '%');
                  });
            });
        }

        // Filtre Date
        $searchDateRequested = $request->input('date_voyage', $request->input('date_depart', date('Y-m-d')));
        if ($request->filled('date_voyage') || $request->filled('date_depart')) {
            $searchDate = $searchDateRequested;
            $query->where(function($q) use ($searchDate) {
                $q->whereDate('date_depart', '=', $searchDate)
                  ->orWhere(function($sub) use ($searchDate) {
                      $sub->whereDate('date_depart', '<=', $searchDate)
                          ->whereDate('date_fin', '>=', $searchDate);
                  });
            });
        } else {
            $today = now()->format('Y-m-d');
            $query->where(function($q) use ($today) {
                $q->whereDate('date_depart', '>=', $today)
                  ->orWhereDate('date_fin', '>=', $today);
            });
        }

        $programmes = $query->orderBy('date_depart')->orderBy('heure_depart')->get();

        // Grouping logic (même que vendreTicket)
        $groupedRoutes = $programmes->groupBy(function($item) {
            return strtolower(trim($item->point_depart)) . '-' . strtolower(trim($item->point_arrive));
        })->map(function ($progs) use ($request, $searchDateRequested) {
            $first = $progs->first();
            $now = now();
            $todayStr = $now->format('Y-m-d');
            $currentTime = $now->format('H:i');

            $allerHoraires = $progs->filter(function($p) use ($searchDateRequested, $todayStr, $currentTime) {
                if ($searchDateRequested == $todayStr && $p->heure_depart < $currentTime) {
                    return false; 
                }
                return true;
            })->values()->map(function($p) use ($searchDateRequested) {
                $reservedCount = $p->getPlacesReserveesForDate($searchDateRequested);
                $totalSeats = $p->getTotalSeats($searchDateRequested);
                $vehicule = $p->getVehiculeForDate($searchDateRequested);

                return [
                    'id' => $p->id,
                    'heure_depart' => $p->heure_depart,
                    'heure_arrive' => $p->heure_arrive,
                    'date_depart' => $p->date_depart,
                    'vehicule' => $vehicule ? $vehicule->type_range : 'Standard',
                    'vehicule_id' => $vehicule ? $vehicule->id : 0,
                    'reserved_count' => $reservedCount,
                    'total_seats' => $totalSeats
                ];
            });

            $hasRetour = Programme::where('point_depart', $first->point_arrive)
                ->where('point_arrive', $first->point_depart)
                ->where('compagnie_id', $first->compagnie_id)
                ->where('statut', 'actif')
                ->whereDate('date_depart', '>=', $first->date_depart)
                ->exists();

            return [
                'id_group' => $first->id,
                'compagnie' => collect($first->compagnie)->only(['id', 'name', 'path_logo']),
                'point_depart' => $first->point_depart,
                'point_arrive' => $first->point_arrive,
                'montant_billet' => $first->montant_billet,
                'durer_parcours' => $first->durer_parcours,
                'aller_horaires' => $allerHoraires,
                'has_retour' => $hasRetour,
                'default_date' => $searchDateRequested
            ];
        })->filter(function($route) {
            return count($route['aller_horaires']) > 0;
        })->values();

        return response()->json([
            'success' => true,
            'routes' => $groupedRoutes
        ]);
    }

    /**
     * Get programs available for ticket sales
     */
    public function vendreTicket(Request $request)
    {
        $hotesse = $request->user();
        
        $query = Programme::with(['compagnie'])
            ->where('compagnie_id', $hotesse->compagnie_id)
            ->where('statut', 'actif');

        // Filtre Point de Départ
        if ($request->filled('point_depart')) {
            $term = trim(explode(',', $request->point_depart)[0]);
            $query->where(function($q) use ($term) {
                $q->where('point_depart', 'LIKE', '%' . $term . '%')
                  ->orWhereHas('itineraire', function($subQ) use ($term) {
                      $subQ->where('point_depart', 'LIKE', '%' . $term . '%');
                  });
            });
        }

        // Filtre Point d'Arrivée
        if ($request->filled('point_arrive')) {
            $term = trim(explode(',', $request->point_arrive)[0]);
            $query->where(function($q) use ($term) {
                $q->where('point_arrive', 'LIKE', '%' . $term . '%')
                  ->orWhereHas('itineraire', function($subQ) use ($term) {
                      $subQ->where('point_arrive', 'LIKE', '%' . $term . '%');
                  });
            });
        }

        // Filtre Date
        if ($request->filled('date_depart')) {
            $searchDate = $request->date_depart;
            $query->where(function($q) use ($searchDate) {
                $q->whereDate('date_depart', '=', $searchDate)
                  ->orWhere(function($sub) use ($searchDate) {
                      $sub->whereDate('date_depart', '<=', $searchDate)
                          ->whereDate('date_fin', '>=', $searchDate);
                  });
            });
        } else {
            $today = now()->format('Y-m-d');
            $query->where(function($q) use ($today) {
                $q->whereDate('date_depart', '>=', $today)
                  ->orWhereDate('date_fin', '>=', $today);
            });
        }

        $programmes = $query->orderBy('date_depart')->orderBy('heure_depart')->get();
        $searchDateRequested = $request->input('date_depart', date('Y-m-d'));

        // Grouping logic (simplified for API)
        $groupedRoutes = $programmes->groupBy(function($item) {
            return strtolower(trim($item->point_depart)) . '-' . strtolower(trim($item->point_arrive));
        })->map(function ($progs) use ($request, $searchDateRequested) {
            $first = $progs->first();
            $now = now();
            $todayStr = $now->format('Y-m-d');
            $currentTime = $now->format('H:i');

            $allerHoraires = $progs->filter(function($p) use ($searchDateRequested, $todayStr, $currentTime) {
                if ($searchDateRequested == $todayStr && $p->heure_depart < $currentTime) {
                    return false; 
                }
                return true;
            })->values()->map(function($p) use ($searchDateRequested) {
                $reservedCount = $p->getPlacesReserveesForDate($searchDateRequested);
                $totalSeats = $p->getTotalSeats($searchDateRequested);
                $vehicule = $p->getVehiculeForDate($searchDateRequested);

                return [
                    'id' => $p->id,
                    'heure_depart' => $p->heure_depart,
                    'heure_arrive' => $p->heure_arrive,
                    'date_depart' => $p->date_depart,
                    'vehicule' => $vehicule ? $vehicule->type_range : 'Standard',
                    'vehicule_id' => $vehicule ? $vehicule->id : 0,
                    'reserved_count' => $reservedCount,
                    'total_seats' => $totalSeats
                ];
            });

            $hasRetour = Programme::where('point_depart', $first->point_arrive)
                ->where('point_arrive', $first->point_depart)
                ->where('compagnie_id', $first->compagnie_id)
                ->where('statut', 'actif')
                ->whereDate('date_depart', '>=', $first->date_depart)
                ->exists();

            return [
                'id_group' => $first->id,
                'compagnie' => collect($first->compagnie)->only(['id', 'name', 'path_logo']),
                'point_depart' => $first->point_depart,
                'point_arrive' => $first->point_arrive,
                'montant_billet' => $first->montant_billet,
                'durer_parcours' => $first->durer_parcours,
                'aller_horaires' => $allerHoraires,
                'has_retour' => $hasRetour,
                'default_date' => $searchDateRequested
            ];
        })->filter(function($route) {
            return count($route['aller_horaires']) > 0;
        })->values();

        return response()->json([
            'success' => true,
            'routes' => $groupedRoutes
        ]);
    }

    /**
     * Submit ticket sale
     */
    public function vendreTicketSubmit(Request $request)
    {
        $hotesse = $request->user();

        $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'date_voyage' => 'required|date',
            'heure_depart' => 'required',
            'nombre_passagers' => 'required|integer|min:1|max:70',
            'passenger_details' => 'required|array|min:1',
            'passenger_details.*.nom' => 'required|string|max:255',
            'passenger_details.*.prenom' => 'required|string|max:255',
            'passenger_details.*.telephone' => 'required|string|max:20',
            'passenger_details.*.email' => 'nullable|email|max:255',
            'passenger_details.*.urgence_nom' => 'nullable|string|max:255',
            'passenger_details.*.urgence_telephone' => 'nullable|string|max:20',
            'programme_retour_id' => 'nullable|exists:programmes,id',
            'date_retour' => 'nullable|required_with:programme_retour_id|date',
            'heure_retour' => 'nullable|required_with:programme_retour_id',
        ]);

        // Sécurité : l'heure de départ ne doit pas être passée
        $voyageDateTime = \Carbon\Carbon::parse($request->date_voyage . ' ' . $request->heure_depart);
        if ($voyageDateTime->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'L\'heure de départ de ce trajet est déjà passée.'
            ], 400);
        }

        $programmeAller = Programme::with(['compagnie'])->findOrFail($request->programme_id);
        $nombrePassagers = (int) $request->nombre_passagers;
        $isAllerRetour = $request->filled('programme_retour_id');

        $montantAller = $programmeAller->montant_billet * $nombrePassagers;
        $montantRetour = 0;
        if ($isAllerRetour) {
            $programmeRetour = Programme::findOrFail($request->programme_retour_id);
            $montantRetour = $programmeRetour->montant_billet * $nombrePassagers;
        }
        $montantTotal = $montantAller + $montantRetour;

        if (\App\Models\Setting::isTicketSystemEnabled() && $programmeAller->compagnie->tickets < $montantTotal) {
            return response()->json([
                'success' => false,
                'message' => 'Solde de la compagnie insuffisant.'
            ], 400);
        }

        if (count($request->passenger_details) !== $nombrePassagers) {
            return response()->json([
                'success' => false,
                'message' => 'Le nombre de passagers ne correspond pas aux informations fournies.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $programmeAller->compagnie->deductTickets($montantTotal, "Vente API Hôtesse - {$nombrePassagers} passager(s)");

            $reservationIds = [];

            // Aller
            $seatsAller = $this->assignSeatsAutomatically($programmeAller->id, $request->date_voyage, $nombrePassagers);
            if (count($seatsAller) < $nombrePassagers) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Pas assez de places disponibles pour le trajet aller.'
                ], 400);
            }

            foreach ($request->passenger_details as $index => $passenger) {
                $seatNumber = $seatsAller[$index];
                $reference = Reservation::generateReference($seatNumber, $hotesse->compagnie->sigle ?? 'RES');

                $reservation = Reservation::create([
                    'reference' => $reference,
                    'programme_id' => $programmeAller->id,
                    'user_id' => null,
                    'seat_number' => $seatNumber,
                    'passager_nom' => $passenger['nom'],
                    'passager_prenom' => $passenger['prenom'],
                    'passager_telephone' => $passenger['telephone'],
                    'passager_email' => $passenger['email'] ?? null,
                    'passager_urgence' => $passenger['urgence_telephone'] ?? null,
                    'nom_passager_urgence' => $passenger['urgence_nom'] ?? null,
                    'date_voyage' => $request->date_voyage,
                    'heure_depart' => $request->heure_depart,
                    'heure_arrive' => $programmeAller->heure_arrive,
                    'montant' => $programmeAller->montant_billet,
                    'statut' => 'confirmee', 
                    'is_retour' => false,
                    'hotesse_id' => $hotesse->id,
                    'compagnie_id' => $hotesse->compagnie_id,
                ]);

                try {
                    $qrCodeData = $this->generateAndSaveQRCode($reservation->reference, $reservation->id, $request->date_voyage);
                    $reservation->update([
                        'qr_code' => $qrCodeData['base64'],
                        'qr_code_path' => $qrCodeData['path']
                    ]);
                } catch (\Exception $e) {
                    Log::error('Erreur QR Code API: ' . $e->getMessage());
                }

                $reservationIds[] = $reservation->id;
            }

            // Retour
            if ($isAllerRetour) {
                $seatsRetour = $this->assignSeatsAutomatically($programmeRetour->id, $request->date_retour, $nombrePassagers);
                if (count($seatsRetour) < $nombrePassagers) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Pas assez de places disponibles pour le trajet retour.'
                    ], 400);
                }

                foreach ($request->passenger_details as $index => $passenger) {
                    $seatNumber = $seatsRetour[$index];
                    $reference = Reservation::generateReference($seatNumber, $hotesse->compagnie->sigle ?? 'RES');

                    $reservation = Reservation::create([
                        'reference' => $reference,
                        'programme_id' => $programmeRetour->id,
                        'user_id' => null,
                        'seat_number' => $seatNumber,
                        'passager_nom' => $passenger['nom'],
                        'passager_prenom' => $passenger['prenom'],
                        'passager_telephone' => $passenger['telephone'],
                        'passager_email' => $passenger['email'] ?? null,
                        'passager_urgence' => $passenger['urgence_telephone'] ?? null,
                        'nom_passager_urgence' => $passenger['urgence_nom'] ?? null,
                        'date_voyage' => $request->date_retour,
                        'heure_depart' => $request->heure_retour,
                        'heure_arrive' => $programmeRetour->heure_arrive,
                        'montant' => $programmeRetour->montant_billet,
                        'statut' => 'confirmee',
                        'is_retour' => true,
                        'hotesse_id' => $hotesse->id,
                        'compagnie_id' => $hotesse->compagnie_id,
                    ]);

                    try {
                        $qrCodeData = $this->generateAndSaveQRCode($reservation->reference, $reservation->id, $request->date_retour);
                        $reservation->update([
                            'qr_code' => $qrCodeData['base64'],
                            'qr_code_path' => $qrCodeData['path']
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Erreur QR Code Retour API: ' . $e->getMessage());
                    }

                    $reservationIds[] = $reservation->id;
                }
            }

            DB::commit();

            try {
                $allReservedAller = Reservation::where('programme_id', $programmeAller->id)
                    ->whereDate('date_voyage', $request->date_voyage)
                    ->where('statut', 'confirmee')
                    ->pluck('seat_number')->toArray();
                broadcast(new \App\Events\SeatUpdated($programmeAller->id, $request->date_voyage, $allReservedAller))->toOthers();

                if ($isAllerRetour && isset($programmeRetour)) {
                    $allReservedRetour = Reservation::where('programme_id', $programmeRetour->id)
                        ->whereDate('date_voyage', $request->date_retour)
                        ->where('statut', 'confirmee')
                        ->pluck('seat_number')->toArray();
                    broadcast(new \App\Events\SeatUpdated($programmeRetour->id, $request->date_retour, $allReservedRetour))->toOthers();
                }
            } catch (\Exception $e) {
                Log::error('Erreur Broadcast API Hotesse: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Réservation(s) effectuée(s) avec succès !',
                'reservations_ids' => $reservationIds
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réservation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reservation details
     */
    public function showReservation(Request $request, $id)
    {
        $hotesse = $request->user();
        
        $reservation = Reservation::with('programme.compagnie')
            ->where('id', $id)
            ->where(function($q) use ($hotesse) {
                $q->where('hotesse_id', $hotesse->id)
                  ->orWhere('compagnie_id', $hotesse->compagnie_id);
            })
            ->firstOrFail();

        $formattedStatut = 'En attente';
        if ($reservation->statut === 'confirmee') $formattedStatut = 'Confirmé';
        elseif ($reservation->statut === 'annulee') $formattedStatut = 'Annulé';
        elseif ($reservation->statut === 'terminee') $formattedStatut = 'Terminé';

        $ticketNo = 'TK-' . str_pad($reservation->id, 3, '0', STR_PAD_LEFT);

        $formattedReservation = [
            'id' => $reservation->id,
            'ticket_no' => $ticketNo,
            'reference' => $reservation->reference,
            'passager' => $reservation->passager_prenom . ' ' . $reservation->passager_nom,
            'trajet' => $reservation->programme ? ($reservation->programme->point_depart . ' → ' . $reservation->programme->point_arrive) : 'N/A',
            'date' => $reservation->date_voyage ? $reservation->date_voyage->format('d M Y') : '',
            'heure' => substr($reservation->heure_depart, 0, 5),
            'place' => $reservation->seat_number,
            'montant_total' => number_format($reservation->montant, 0, ',', ' ') . ' FCFA',
            'statut' => $formattedStatut,
            'qr_code' => $reservation->qr_code,
            'qr_code_url' => $reservation->qr_code_path ? asset('storage/' . $reservation->qr_code_path) : null,
            'created_at' => $reservation->created_at->format('d/m/Y H:i')
        ];

        return response()->json([
            'success' => true,
            'reservation' => $formattedReservation
        ]);
    }

    private function assignSeatsAutomatically($programmeId, $dateVoyage, $count)
    {
        $programme = Programme::findOrFail($programmeId);
        $vehicule = $programme->getVehiculeForDate($dateVoyage);
        $totalSeats = $vehicule ? $vehicule->nombre_place : 70;

        $reservedSeats = Reservation::where('programme_id', $programmeId)
            ->where('date_voyage', $dateVoyage)
            ->pluck('seat_number')
            ->toArray();

        $assignedSeats = [];
        $nextSeat = 1;

        while (count($assignedSeats) < $count && $nextSeat <= $totalSeats) {
            if (!in_array($nextSeat, $reservedSeats)) {
                $assignedSeats[] = $nextSeat;
            }
            $nextSeat++;
        }

        return $assignedSeats;
    }

    private function generateAndSaveQRCode(string $reference, int $reservationId, string $dateVoyage, int $userId = null)
    {
        $qrData = [
            'user_id' => $userId,
            'reference' => $reference,
            'timestamp' => time(),
            'date_voyage' => $dateVoyage,
            'reservation_id' => $reservationId,
            'verification_hash' => hash('sha256', $reference . $reservationId . $dateVoyage . config('app.key'))
        ];
        
        $qrContent = json_encode($qrData);
        $qrCode = QrCode::create($qrContent);
        $qrCode->setSize(180);
        $qrCode->setMargin(5);

        $writer = new PngWriter();
        $qrCodeImage = $writer->write($qrCode)->getString();
        
        $qrCodeBase64 = base64_encode($qrCodeImage);
        $qrCodePath = 'qrcodes/' . $reference . '.png';
        $fullPath = storage_path('app/public/' . $qrCodePath);

        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        file_put_contents($fullPath, $qrCodeImage);

        return [
            'base64' => $qrCodeBase64,
            'path' => $qrCodePath
        ];
    }
}
