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

class CaisseController extends Controller
{
    public function dashboard()
    {
        $caisse = Auth::guard('caisse')->user();
        $today = now()->toDateString();
        
        $stats = [
            'tickets_disponibles' => $caisse->tickets,
            'compagnie' => $caisse->compagnie->name ?? 'N/A',
            'compagnie_logo' => $caisse->compagnie->path_logo ?? null,
            'compagnie_slogan' => $caisse->compagnie->slogan ?? null,
            'ventes_aujourdhui' => Reservation::where('caisse_id', $caisse->id)
                ->whereDate('created_at', $today)
                ->count(),
            'revenu_aujourdhui' => Reservation::where('caisse_id', $caisse->id)
                ->whereDate('created_at', $today)
                ->sum('montant'),
        ];

        return view('caisse.dashboard', compact('caisse', 'stats'));
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

        if ($request->filled('date')) {
            $query->whereDate('date_voyage', $request->date);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $ventes = $query->paginate(10);
        $totalVentes = $query->count();
        $totalRevenu = $query->sum('montant');

        return view('caisse.ventes', compact('ventes', 'totalVentes', 'totalRevenu'));
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
        
        // Récupérer les programmes actifs de la compagnie du caissier
        $programmes = Programme::with(['compagnie', 'vehicule'])
            ->where('compagnie_id', $caisse->compagnie_id)
            ->where('statut', 'actif')
            ->whereDate('date_depart', '>=', now()->toDateString())
            ->orderBy('date_depart')
            ->get();
        
        return view('caisse.vendre-ticket', compact('caisse', 'programmes'));
    }

    public function vendreTicketSubmit(Request $request)
    {
        $caisse = Auth::guard('caisse')->user();

        $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'nombre_tickets' => 'required|integer|min:1|max:' . $caisse->tickets,
            'passenger_details' => 'required|array|min:1',
            'passenger_details.*.nom' => 'required|string|max:255',
            'passenger_details.*.prenom' => 'required|string|max:255',
            'passenger_details.*.telephone' => 'required|string|max:20',
            'passenger_details.*.email' => 'nullable|email|max:255',
        ]);

        $programme = Programme::with(['compagnie', 'vehicule'])->findOrFail($request->programme_id);
        $nombreTickets = $request->nombre_tickets;

        // Vérifier que le caissier a assez de tickets
        if ($caisse->tickets < $nombreTickets) {
            return back()->withErrors(['nombre_tickets' => 'Vous n\'avez pas assez de tickets disponibles.']);
        }

        DB::beginTransaction();
        try {
            $reservations = [];

            // Récupérer les sièges déjà réservés pour ce programme
            $reservedSeats = Reservation::where('programme_id', $programme->id)
                ->pluck('seat_number')
                ->toArray();

            // Trouver le prochain siège disponible
            $nextSeat = 1;
            while (in_array($nextSeat, $reservedSeats)) {
                $nextSeat++;
            }

            foreach ($request->passenger_details as $index => $passenger) {
                // Trouver le prochain siège disponible pour chaque passager
                while (in_array($nextSeat, $reservedSeats)) {
                    $nextSeat++;
                }

                // Utiliser le helper du modèle pour la référence
                $reference = Reservation::generateReference($nextSeat);

                $reservation = Reservation::create([
                    'reference' => $reference,
                    'programme_id' => $programme->id,
                    'user_id' => null, // Vente caisse, pas d'utilisateur
                    'seat_number' => $nextSeat,
                    'passager_nom' => $passenger['nom'],
                    'passager_prenom' => $passenger['prenom'],
                    'passager_telephone' => $passenger['telephone'],
                    'passager_email' => $passenger['email'] ?? null,
                    'date_voyage' => $programme->date_depart,
                    'heure_depart' => $programme->heure_depart,
                    'heure_arrive' => $programme->heure_arrive,
                    'montant' => $programme->montant_billet,
                    'statut' => 'confirmee',
                    'caisse_id' => $caisse->id,
                ]);

                // Générer le QR Code pour cette réservation
                try {
                    $qrCodeData = $this->generateAndSaveQRCode(
                        $reservation->reference,
                        $reservation->id,
                        $programme->date_depart,
                        null // Pas d'utilisateur spécifique pour une vente caisse
                    );

                    $reservation->update([
                        'qr_code' => $qrCodeData['base64'],
                        'qr_code_path' => $qrCodeData['path']
                    ]);
                } catch (\Exception $e) {
                    Log::error('Erreur génération QR Code Caisse: ' . $e->getMessage());
                }

                $reservedSeats[] = $nextSeat;
                $nextSeat++;
                $reservations[] = $reservation;
            }

            // Déduire les tickets du caissier
            $caisse->deductTickets($nombreTickets);

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
