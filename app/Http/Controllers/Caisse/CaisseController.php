<?php

namespace App\Http\Controllers\Caisse;

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
use Carbon\Carbon; // N'oublie pas d'importer Carbon

class CaisseController extends Controller
{
    public function dashboard()
    {
        $caisse = Auth::guard('caisse')->user();
        $today = now()->toDateString();
        
        $stats = [
            'compagnie' => $caisse->compagnie->name ?? 'N/A',
            'compagnie_logo' => $caisse->compagnie->path_logo ?? null,
            'compagnie_slogan' => $caisse->compagnie->slogan ?? null,
            'ventes_aujourdhui' => Reservation::where('caisse_id', $caisse->id)
                ->whereDate('created_at', $today)
                ->count(),
            'revenu_aujourdhui' => Reservation::where('caisse_id', $caisse->id)
                ->whereDate('created_at', $today)
                ->sum('montant'),
            'revenu_global' => Reservation::where('caisse_id', $caisse->id)
                ->sum('montant'),
        ];

        // Données pour le graphique (7 derniers jours)
        $salesData = Reservation::where('caisse_id', $caisse->id)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(montant) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');

        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $chartLabels[] = now()->subDays($i)->format('d/m');
            $chartData[] = $salesData[$date] ?? 0;
        }

        return view('caisse.dashboard', compact('caisse', 'stats', 'chartLabels', 'chartData'));
    }

    public function profile()
    {
        $caisse = Auth::guard('caisse')->user();
        return view('caisse.profile', compact('caisse'));
    }

    public function updateProfile(Request $request)
    {
        $caisse = Auth::guard('caisse')->user();
        
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
            if ($caisse->profile_picture) {
                Storage::disk('public')->delete($caisse->profile_picture);
            }
            $path = $request->file('profile_picture')->store('caisse_profiles', 'public');
            $data['profile_picture'] = $path;
        }

        $caisse->update($data);

        return back()->with('success', 'Profil mis à jour avec succès !');
    }

    public function updatePassword(Request $request)
    {
        $caisse = Auth::guard('caisse')->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $caisse->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel ne correspond pas.']);
        }

        $caisse->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Mot de passe mis à jour avec succès !');
    }

    public function ventes(Request $request)
    {
        $caisse = Auth::guard('caisse')->user();
        
        $query = Reservation::where('caisse_id', $caisse->id)
            ->with(['programme'])
            ->latest();

        if ($request->filled('date_debut')) {
            $query->whereDate('date_voyage', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_voyage', '<=', $request->date_fin);
        }

        // Le filtre statut a été supprimé de la vue, mais on le garde conditionnel au cas où
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $ventes = $query->paginate(10);
        $totalVentes = $query->count();
        $totalRevenu = $query->sum('montant');
        
        // Calculer les tickets annulés pour remplacer "Tickets Restants"
        $totalAnnulations = Reservation::where('caisse_id', $caisse->id)
            ->where('statut', 'annulee')
            ->count();

        return view('caisse.ventes', compact('ventes', 'totalVentes', 'totalRevenu', 'totalAnnulations'));
    }

    public function venteSuccess(Request $request)
    {
        $ids = $request->get('reservations', []);
        $reservations = Reservation::whereIn('id', $ids)->with(['programme.vehicule'])->get();
        
        if ($reservations->isEmpty()) {
            return redirect()->route('caisse.dashboard');
        }

        return view('caisse.vente-success', compact('reservations'));
    }

    public function imprimerTicket(Reservation $reservation)
    {
        $caisse = Auth::guard('caisse')->user();
        
        // Vérifier que le ticket appartient bien au caissier ou à sa compagnie
        if ($reservation->caisse_id !== $caisse->id && $reservation->compagnie_id !== $caisse->compagnie_id) {
            abort(403);
        }

        return view('caisse.ticket-pdf', compact('reservation'));
    }

  public function vendreTicket()
    {
        $caisse = Auth::guard('caisse')->user();
        
        // 1. On récupère la date et l'heure actuelles
        $now = Carbon::now();
        $dateAujourdhui = $now->toDateString(); // Ex: 2025-05-20
        $heureActuelle = $now->format('H:i');   // Ex: 14:30
        
        // 2. Récupérer les programmes
        $programmes = Programme::with(['compagnie', 'vehicule'])
            ->where('compagnie_id', $caisse->compagnie_id)
            ->where('statut', 'actif') // On s'assure qu'il est actif
            
            // LOGIQUE DATE : Le programme doit être en cours de validité aujourd'hui
            // La date de fin doit être future ou aujourd'hui
            ->whereDate('date_fin', '>=', $dateAujourdhui)
            // La date de début doit être passée ou aujourd'hui (le programme a commencé)
            ->whereDate('date_depart', '<=', $dateAujourdhui)
            
            // LOGIQUE HEURE : On ne veut que les départs FUTURS pour la journée d'aujourd'hui
            ->where('heure_depart', '>', $heureActuelle)
            
            // On trie par heure de départ la plus proche
            ->orderBy('heure_depart', 'asc')
            ->get();
            
        Log::info('Caisse VendreTicket Filtré:', [
            'heure_actuelle' => $heureActuelle,
            'programmes_trouves' => $programmes->count()
        ]);
        
        return view('caisse.vendre-ticket', compact('caisse', 'programmes'));
    }

    public function vendreTicketSubmit(Request $request)
    {
        $caisse = Auth::guard('caisse')->user();

        $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'nombre_tickets' => 'required|integer|min:1',
            'passenger_details' => 'required|array|min:1',
            'passenger_details.*.nom' => 'required|string|max:255',
            'passenger_details.*.prenom' => 'required|string|max:255',
            'passenger_details.*.telephone' => 'required|string|max:20',
            'passenger_details.*.email' => 'nullable|email|max:255',
        ]);

        $programme = Programme::with(['compagnie', 'vehicule'])->findOrFail($request->programme_id);
        
        // Calcul du montant total
        $montantTotal = $programme->montant_billet * $request->nombre_tickets;

        // VÉRIFICATION DU SOLDE DE LA COMPAGNIE
        if ($programme->compagnie->tickets < $montantTotal) {
            return back()->withErrors(['error' => 'Solde de la compagnie insuffisant pour effectuer cette vente. Veuillez recharger votre compte.']);
        }

        // IMPORTANT : Puisqu'on vend pour "Aujourd'hui", la date de voyage est MAINTENANT
        // et non pas la date de création du programme ($programme->date_depart)
        $dateVoyageEffective = now()->toDateString(); 

        DB::beginTransaction();
        try {
            // Déduction du solde compagnie
            $programme->compagnie->deductTickets($montantTotal, "Vente Caisse - {$request->nombre_tickets} tickets");

            $reservations = [];

            // Récupérer les sièges déjà réservés pour ce programme ET pour cette date spécifique
            $reservedSeats = Reservation::where('programme_id', $programme->id)
                ->whereDate('date_voyage', $dateVoyageEffective) // Ajout crucial : filtre par date du jour
                ->pluck('seat_number')
                ->toArray();

            $nextSeat = 1;
            while (in_array($nextSeat, $reservedSeats)) {
                $nextSeat++;
            }

            foreach ($request->passenger_details as $index => $passenger) {
                while (in_array($nextSeat, $reservedSeats)) {
                    $nextSeat++;
                }

                $reference = Reservation::generateReference($nextSeat);

                $reservation = Reservation::create([
                    'reference' => $reference,
                    'programme_id' => $programme->id,
                    'user_id' => null,
                    'seat_number' => $nextSeat,
                    'passager_nom' => $passenger['nom'],
                    'passager_prenom' => $passenger['prenom'],
                    'passager_telephone' => $passenger['telephone'],
                    'passager_email' => $passenger['email'] ?? null,
                    
                    // ICI : On met la date d'aujourd'hui, pas la date de début du planning
                    'date_voyage' => $dateVoyageEffective, 
                    
                    'heure_depart' => $programme->heure_depart,
                    'heure_arrive' => $programme->heure_arrive,
                    'montant' => $programme->montant_billet,
                    'statut' => 'confirmee',
                    'caisse_id' => $caisse->id,
                ]);

                // Génération du QR Code avec la BONNE date de voyage
                try {
                    $qrCodeData = $this->generateAndSaveQRCode(
                        $reservation->reference,
                        $reservation->id,
                        $dateVoyageEffective, // Utilisation de la date effective
                        null
                    );

                    $reservation->update([
                        'qr_code' => $qrCodeData['base64'],
                        'qr_code_path' => $qrCodeData['path']
                    ]);
                } catch (\Exception $e) {
                    Log::error('Erreur QR: ' . $e->getMessage());
                }

                $reservedSeats[] = $nextSeat;
                $nextSeat++;
                $reservations[] = $reservation;
            }

            DB::commit();

            $ids = array_map(function($r) { return $r->id; }, $reservations);
            return redirect()->route('caisse.vente-success', ['reservations' => $ids])->with('success', $request->nombre_tickets . ' ticket(s) vendu(s) avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la vente : ' . $e->getMessage()]);
        }
    }

    public function vente()
    {
        $caisse = Auth::guard('caisse')->user();
        $now = Carbon::now();
        $dateAujourdhui = $now->toDateString();
        $heureActuelle = $now->format('H:i');
        
        $programmes = Programme::with(['compagnie', 'vehicule'])
            ->where('compagnie_id', $caisse->compagnie_id)
            ->where('statut', 'actif')
            ->whereDate('date_fin', '>=', $dateAujourdhui)
            ->whereDate('date_depart', '<=', $dateAujourdhui)
            ->where('heure_depart', '>', $heureActuelle)
            ->orderBy('heure_depart', 'asc')
            ->get();
            
        return view('caisse.vente', compact('caisse', 'programmes'));
    }

    public function venteSubmit(Request $request)
    {
        $caisse = Auth::guard('caisse')->user();

        $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'seat_numbers' => 'required|string',
        ]);

        $programme = Programme::with(['compagnie', 'vehicule'])->findOrFail($request->programme_id);
        $seatNumbers = explode(',', $request->seat_numbers);
        $nombreTickets = count($seatNumbers);
        
        $montantTotal = $programme->montant_billet * $nombreTickets;

        if ($programme->compagnie->tickets < $montantTotal) {
            return back()->withErrors(['error' => 'Solde de la compagnie insuffisant pour effectuer cette vente.']);
        }

        $dateVoyageEffective = now()->toDateString(); 

        DB::beginTransaction();
        try {
            $programme->compagnie->deductTickets($montantTotal, "Vente Caisse Express - {$nombreTickets} tickets");

            $reservations = [];

            foreach ($seatNumbers as $seatNumber) {
                // Vérifier si le siège est déjà pris (sécurité)
                $alreadyReserved = Reservation::where('programme_id', $programme->id)
                    ->whereDate('date_voyage', $dateVoyageEffective)
                    ->where('seat_number', $seatNumber)
                    ->exists();
                
                if ($alreadyReserved) {
                    throw new \Exception("Le siège {$seatNumber} est déjà réservé.");
                }

                $reference = Reservation::generateReference($seatNumber);

                $reservation = Reservation::create([
                    'reference' => $reference,
                    'programme_id' => $programme->id,
                    'user_id' => null,
                    'seat_number' => $seatNumber,
                    'passager_nom' => 'PASSAGER',
                    'passager_prenom' => $seatNumber,
                    'passager_telephone' => $caisse->compagnie->contact ?? 'N/A',
                    'date_voyage' => $dateVoyageEffective, 
                    'heure_depart' => $programme->heure_depart,
                    'heure_arrive' => $programme->heure_arrive,
                    'montant' => $programme->montant_billet,
                    'statut' => 'confirmee',
                    'caisse_id' => $caisse->id,
                ]);

                try {
                    $qrCodeData = $this->generateAndSaveQRCode(
                        $reservation->reference,
                        $reservation->id,
                        $dateVoyageEffective,
                        null
                    );

                    $reservation->update([
                        'qr_code' => $qrCodeData['base64'],
                        'qr_code_path' => $qrCodeData['path']
                    ]);
                } catch (\Exception $e) {
                    Log::error('Erreur QR: ' . $e->getMessage());
                }

                $reservations[] = $reservation;
            }

            DB::commit();

            $ids = array_map(function($r) { return $r->id; }, $reservations);
            return redirect()->route('caisse.vente-success', ['reservations' => $ids])->with('success', $nombreTickets . ' ticket(s) vendu(s) avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la vente : ' . $e->getMessage()]);
        }
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
            Log::error('Erreur génération QR Code Caisse (private): ' . $e->getMessage());
            throw $e;
        }
    }
}
