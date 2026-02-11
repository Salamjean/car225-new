<?php

namespace App\Http\Controllers\Hotesse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Programme;
use App\Models\Reservation;
use App\Models\Compagnie;
use App\Models\Paiement;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Log;

class HotesseController extends Controller
{
    public function dashboard()
    {
        $hotesse = Auth::guard('hotesse')->user();
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

        // Données pour le graphique des 7 derniers jours
        $chartData = [];
        $chartLabels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('d/m');
            $chartData[] = Reservation::where('hotesse_id', $hotesse->id)
                ->whereDate('created_at', $date->toDateString())
                ->sum('montant');
        }

        $recent_reservations = Reservation::where('hotesse_id', $hotesse->id)
            ->with(['programme', 'programme.vehicule']) // Eager load for performance
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('hotesse.dashboard', compact('hotesse', 'stats', 'chartData', 'chartLabels', 'recent_reservations'));
    }

    public function profile()
    {
        $hotesse = Auth::guard('hotesse')->user();
        return view('hotesse.profile', compact('hotesse'));
    }

    public function updateProfile(Request $request)
    {
        $hotesse = Auth::guard('hotesse')->user();
        
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

        return back()->with('success', 'Profil mis à jour avec succès !');
    }

    public function updatePassword(Request $request)
    {
        $hotesse = Auth::guard('hotesse')->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $hotesse->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel ne correspond pas.']);
        }

        $hotesse->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Mot de passe mis à jour avec succès !');
    }

    public function ventes(Request $request)
    {
        $hotesse = Auth::guard('hotesse')->user();
        
        $query = Reservation::where('hotesse_id', $hotesse->id)
            ->with(['programme']);

        // Filtre Date Début / Fin
        if ($request->filled('date_debut')) {
            $query->whereDate('date_voyage', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_voyage', '<=', $request->date_fin);
        }

        // Filtre Statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Stats globales (basées sur les filtres)
        $totalVentes = (clone $query)->where('statut', 'confirmee')->count();
        $totalRevenu = (clone $query)->where('statut', 'confirmee')->sum('montant');
        $totalAnnulations = (clone $query)->where('statut', 'annulee')->count();

        // Pagination
        $ventes = $query->latest()->paginate(10);

        return view('hotesse.ventes', compact('ventes', 'totalVentes', 'totalRevenu', 'totalAnnulations'));
    }

    public function venteSuccess(Request $request)
    {
        $ids = $request->get('reservations', []);
        $reservations = Reservation::whereIn('id', $ids)->with(['programme.vehicule'])->get();
        
        if ($reservations->isEmpty()) {
            return redirect()->route('hotesse.dashboard');
        }

        return view('hotesse.vente-success', compact('reservations'));
    }

    public function imprimerTicket(Reservation $reservation)
    {
        $hotesse = Auth::guard('hotesse')->user();
        
        // Vérifier que le ticket appartient bien à l'hotesse ou à sa compagnie
        // Vérifier que le ticket appartient bien à l'hotesse ou à sa compagnie
        if ($reservation->hotesse_id !== $hotesse->id && $reservation->compagnie_id !== $hotesse->compagnie_id) {
            abort(403);
        }

        return view('hotesse.ticket-pdf', compact('reservation'));
    }

   public function vendreTicket(Request $request)
    {
        $hotesse = Auth::guard('hotesse')->user();
        
        // Paramètres de recherche
        $searchParams = [
            'point_depart' => $request->input('point_depart'),
            'point_arrive' => $request->input('point_arrive'),
            'date_depart' => $request->input('date_depart', date('Y-m-d')),
        ];

        // Est-ce qu'on doit charger les résultats ?
     // Par défaut, l'hôtesse voit tous les voyages de sa compagnie
     $shouldLoad = true;

     $groupedRoutes = collect();

        if ($shouldLoad) {
            // ... (reste du code inchangé) ...
            Log::info('VendreTicket: Searching with params:', $request->all());

            $query = Programme::with(['compagnie', 'vehicule'])
                ->where('compagnie_id', $hotesse->compagnie_id)
                ->where('statut', 'actif');

            DB::enableQueryLog();

            // 1. Filtre Point de Départ
            if ($request->filled('point_depart')) {
                $term = trim(explode(',', $request->point_depart)[0]);
                Log::info('VendreTicket: Filtering by cleaned point_depart: ' . $term);
                
                $query->where(function($q) use ($term) {
                    $q->where('point_depart', 'LIKE', '%' . $term . '%')
                      ->orWhereHas('itineraire', function($subQ) use ($term) {
                          $subQ->where('point_depart', 'LIKE', '%' . $term . '%');
                      });
                });
            }

            // 2. Filtre Point d'Arrivée
            if ($request->filled('point_arrive')) {
                $term = trim(explode(',', $request->point_arrive)[0]);
                Log::info('VendreTicket: Filtering by cleaned point_arrive: ' . $term);

                $query->where(function($q) use ($term) {
                    $q->where('point_arrive', 'LIKE', '%' . $term . '%')
                      ->orWhereHas('itineraire', function($subQ) use ($term) {
                          $subQ->where('point_arrive', 'LIKE', '%' . $term . '%');
                      });
                });
            }

            // 3. Filtre Date
            // Gestion de la récurrence : On cherche les programmes actifs à la date demandée
            if ($request->filled('date_depart')) {
                $searchDate = $request->date_depart;
                Log::info('VendreTicket: Filtering by date: ' . $searchDate);
                
                $query->where(function($q) use ($searchDate) {
                    // Cas 1: Date de départ exacte (Voyage ponctuel ou début de récurrence)
                    $q->whereDate('date_depart', '=', $searchDate)
                      // Cas 2: Programme récurrent qui inclut cette date
                      ->orWhere(function($sub) use ($searchDate) {
                          $sub->whereDate('date_depart', '<=', $searchDate)
                              ->whereDate('date_fin', '>=', $searchDate);
                      });
                });
            } else {
                $today = now()->format('Y-m-d');
                Log::info('VendreTicket: Filtering by active programs (>= ' . $today . ')');
                
                $query->where(function($q) use ($today) {
                    // Programmes futurs
                    $q->whereDate('date_depart', '>=', $today)
                      // Ou programmes en cours (récurrents) qui ne sont pas finis
                      ->orWhereDate('date_fin', '>=', $today);
                });
            }

            $programmes = $query->orderBy('date_depart')->orderBy('heure_depart')->get();
            
            // Log moins verbeux pour la requete SQL
            // Log::info('VendreTicket: SQL Query:', DB::getQueryLog());
            Log::info('VendreTicket: Found ' . $programmes->count() . ' programmes.');

            $searchDateRequested = $request->input('date_depart', date('Y-m-d'));

            // 4. Groupement intelligent (Par trajet Départ-Arrivée)
            $groupedRoutes = $programmes->groupBy(function($item) {
                return strtolower(trim($item->point_depart)) . '-' . strtolower(trim($item->point_arrive));
            })->map(function ($progs) use ($request, $searchDateRequested) {
                $first = $progs->first();
                
                // On récupère les horaires pour ce trajet spécifique
                // On ne garde que ceux qui sont futurs si c'est la date d'aujourd'hui
                $now = now();
                $todayStr = $now->format('Y-m-d');
                $currentTime = $now->format('H:i');

                $allerHoraires = $progs->filter(function($p) use ($todayStr, $currentTime) {
                    if ($p->date_depart == $todayStr && $p->heure_depart < $currentTime) {
                        return false; 
                    }
                    return true;
                })->values()->map(function($p) use ($searchDateRequested) {
                    $reservedCount = Reservation::where('programme_id', $p->id)
                        ->where('date_voyage', $searchDateRequested)
                        ->where('statut', 'confirmee')
                        ->count();

                    return [
                        'id' => $p->id,
                        'heure_depart' => $p->heure_depart,
                        'heure_arrive' => $p->heure_arrive,
                        'date_depart' => $p->date_depart,
                        'vehicule' => $p->vehicule ? $p->vehicule->type_range : 'Standard',
                        'vehicule_id' => $p->vehicule_id,
                        'reserved_count' => $reservedCount,
                        'total_seats' => $p->vehicule ? $p->vehicule->nombre_place : 70
                    ];
                });

                // Vérification s'il y a des retours (Trajet inverse)
                $hasRetour = Programme::where('point_depart', $first->point_arrive)
                    ->where('point_arrive', $first->point_depart)
                    ->where('compagnie_id', $first->compagnie_id)
                    ->where('statut', 'actif')
                    ->whereDate('date_depart', '>=', $first->date_depart)
                    ->exists();

                return (object)[
                    'id_group' => $first->id, // Juste pour avoir un ID unique
                    'compagnie' => $first->compagnie,
                    'point_depart' => $first->point_depart,
                    'point_arrive' => $first->point_arrive,
                    'montant_billet' => $first->montant_billet,
                    'durer_parcours' => $first->durer_parcours,
                    'aller_horaires' => $allerHoraires,
                    'has_retour' => $hasRetour,
                    'default_date' => $searchDateRequested // Pour initialiser le calendrier
                ];
            })->filter(function($route) {
                // On ne garde que les routes qui ont au moins un horaire valide
                return count($route->aller_horaires) > 0;
            })->values();

            Log::info('VendreTicket: Grouped into ' . $groupedRoutes->count() . ' routes.');
        } else {
            Log::info('VendreTicket: Not loading results (shouldLoad=false)');
        }

        return view('hotesse.vendre-ticket', compact('hotesse', 'searchParams', 'groupedRoutes'));
    }

    public function vendreTicketSubmit(Request $request)
    {
        $hotesse = Auth::guard('hotesse')->user();

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
            'programme_retour_id' => 'nullable|exists:programmes,id',
            'date_retour' => 'nullable|required_with:programme_retour_id|date',
            'heure_retour' => 'nullable|required_with:programme_retour_id',
        ]);

        $programmeAller = Programme::with(['compagnie', 'vehicule'])->findOrFail($request->programme_id);
        $nombrePassagers = $request->nombre_passagers;
        $isAllerRetour = $request->filled('programme_retour_id');

        // Calcul du montant total
        $montantAller = $programmeAller->montant_billet * $nombrePassagers;
        $montantRetour = 0;
        if ($isAllerRetour) {
            $programmeRetour = Programme::findOrFail($request->programme_retour_id);
            $montantRetour = $programmeRetour->montant_billet * $nombrePassagers;
        }
        $montantTotal = $montantAller + $montantRetour;

        // VÉRIFICATION DU SOLDE DE LA COMPAGNIE
        if ($programmeAller->compagnie->tickets < $montantTotal) {
            return response()->json([
                'success' => false,
                'message' => 'Solde de la compagnie insuffisant pour effectuer cette vente. Veuillez contacter l\'administrateur.'
            ], 400);
        }

        // Vérifier que les passagers correspondent au nombre
        if (count($request->passenger_details) !== (int)$nombrePassagers) {
            Log::error('Validation Passagers Mismatch:', [
                'expected' => $nombrePassagers,
                'received_count' => count($request->passenger_details),
                'received_data' => $request->passenger_details
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Le nombre de passagers ne correspond pas aux informations fournies (' . count($request->passenger_details) . '/' . $nombrePassagers . ').'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Déduction du solde compagnie
            $programmeAller->compagnie->deductTickets($montantTotal, "Vente Hôtesse - {$nombrePassagers} passager(s)");

            $reservationIds = [];

            // ========== ALLER ==========
            // Assigner automatiquement les places pour l'aller
            $seatsAller = $this->assignSeatsAutomatically($programmeAller->id, $request->date_voyage, $nombrePassagers);
            
            if (count($seatsAller) < $nombrePassagers) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Pas assez de places disponibles pour le trajet aller.'
                ], 400);
            }

            // Créer les réservations aller
            foreach ($request->passenger_details as $index => $passenger) {
                $seatNumber = $seatsAller[$index];
                $reference = Reservation::generateReference($seatNumber);

                $reservation = Reservation::create([
                    'reference' => $reference,
                    'programme_id' => $programmeAller->id,
                    'user_id' => null,
                    'seat_number' => $seatNumber,
                    'passager_nom' => $passenger['nom'],
                    'passager_prenom' => $passenger['prenom'],
                    'passager_telephone' => $passenger['telephone'],
                    'passager_email' => $passenger['email'] ?? null,
                    'passager_urgence' => null,
                    'date_voyage' => $request->date_voyage,
                    'heure_depart' => $request->heure_depart,
                    'heure_arrive' => $programmeAller->heure_arrive,
                    'montant' => $programmeAller->montant_billet,
                    'statut' => 'confirmee', // Directement confirmé
                    'is_retour' => false,
                    'hotesse_id' => $hotesse->id,
                    'compagnie_id' => $hotesse->compagnie_id,
                ]);

                // Générer le QR Code
                try {
                    $qrCodeData = $this->generateAndSaveQRCode(
                        $reservation->reference,
                        $reservation->id,
                        $request->date_voyage,
                        null
                    );

                    $reservation->update([
                        'qr_code' => $qrCodeData['base64'],
                        'qr_code_path' => $qrCodeData['path']
                    ]);
                } catch (\Exception $e) {
                    Log::error('Erreur génération QR Code: ' . $e->getMessage());
                }

                $reservationIds[] = $reservation->id;
            }

            // ========== RETOUR (si Aller-Retour) ==========
            if ($isAllerRetour) {
                $programmeRetour = Programme::with(['compagnie', 'vehicule'])->findOrFail($request->programme_retour_id);
                
                // Assigner automatiquement les places pour le retour
                $seatsRetour = $this->assignSeatsAutomatically($programmeRetour->id, $request->date_retour, $nombrePassagers);
                
                if (count($seatsRetour) < $nombrePassagers) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Pas assez de places disponibles pour le trajet retour.'
                    ], 400);
                }

                // Créer les réservations retour
                foreach ($request->passenger_details as $index => $passenger) {
                    $seatNumber = $seatsRetour[$index];
                    $reference = Reservation::generateReference($seatNumber);

                    $reservation = Reservation::create([
                        'reference' => $reference,
                        'programme_id' => $programmeRetour->id,
                        'user_id' => null,
                        'seat_number' => $seatNumber,
                        'passager_nom' => $passenger['nom'],
                        'passager_prenom' => $passenger['prenom'],
                        'passager_telephone' => $passenger['telephone'],
                        'passager_email' => $passenger['email'] ?? null,
                        'passager_urgence' => null,
                        'date_voyage' => $request->date_retour,
                        'heure_depart' => $request->heure_retour,
                        'heure_arrive' => $programmeRetour->heure_arrive,
                        'montant' => $programmeRetour->montant_billet,
                        'statut' => 'confirmee',
                        'is_retour' => true,
                        'hotesse_id' => $hotesse->id,
                        'compagnie_id' => $hotesse->compagnie_id,
                    ]);

                    // Générer le QR Code
                    try {
                        $qrCodeData = $this->generateAndSaveQRCode(
                            $reservation->reference,
                            $reservation->id,
                            $request->date_retour,
                            null
                        );

                        $reservation->update([
                            'qr_code' => $qrCodeData['base64'],
                            'qr_code_path' => $qrCodeData['path']
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Erreur génération QR Code retour: ' . $e->getMessage());
                    }

                    $reservationIds[] = $reservation->id;
                }
            }

            DB::commit();

            $message = $isAllerRetour 
                ? "{$nombrePassagers} passager(s) réservé(s) pour un voyage aller-retour !"
                : "{$nombrePassagers} passager(s) réservé(s) avec succès !";

            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('hotesse.vente-success', ['reservations' => $reservationIds])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur vente hotesse: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réservation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assigner automatiquement les places disponibles
     */
    private function assignSeatsAutomatically($programmeId, $dateVoyage, $count)
    {
        // Récupérer les places déjà réservées pour ce programme et cette date
        $reservedSeats = Reservation::where('programme_id', $programmeId)
            ->where('date_voyage', $dateVoyage)
            ->pluck('seat_number')
            ->toArray();

        $assignedSeats = [];
        $nextSeat = 1;

        // Trouver les prochaines places disponibles
        while (count($assignedSeats) < $count && $nextSeat <= 70) {
            if (!in_array($nextSeat, $reservedSeats)) {
                $assignedSeats[] = $nextSeat;
            }
            $nextSeat++;
        }

        return $assignedSeats;
    }

    /**
     * Générer et sauvegarder le QR Code avec Endroid
     */
    private function generateAndSaveQRCode(string $reference, int $reservationId, string $dateVoyage, int $userId = null)
    {
        try {
            // Créer les données du QR Code (format JSON sécurisé)
            $qrData = [
                'user_id' => $userId,
                'reference' => $reference,
                'timestamp' => time(),
                'date_voyage' => $dateVoyage,
                'reservation_id' => $reservationId,
            ];

            // Ajouter un hash de vérification pour éviter la falsification
            $qrData['verification_hash'] = hash(
                'sha256',
                $reference . $reservationId . $dateVoyage . config('app.key')
            );
            
            $qrContent = json_encode($qrData);

            // Créer le QR Code
            $qrCode = QrCode::create($qrContent);
            $qrCode->setSize(180);
            $qrCode->setMargin(5);

            // Écrire le QR Code en PNG
            $writer = new PngWriter();
            $qrCodeResult = $writer->write($qrCode);
            $qrCodeImage = $qrCodeResult->getString();

            // Convertir en base64 pour stockage (facilite l'affichage direct dans les vues)
            $qrCodeBase64 = base64_encode($qrCodeImage);

            // Chemin de sauvegarde
            $qrCodePath = 'qrcodes/' . $reference . '.png';
            $fullPath = storage_path('app/public/' . $qrCodePath);

            // Créer le dossier si nécessaire
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            // Sauvegarder le fichier
            file_put_contents($fullPath, $qrCodeImage);

            return [
                'base64' => $qrCodeBase64,
                'path' => $qrCodePath,
                'qr_data' => $qrData,
                'qr_content' => $qrContent
            ];
        } catch (\Exception $e) {
            Log::error('Erreur génération QR Code Hotesse (private): ' . $e->getMessage());
            throw $e;
        }
    }
}
