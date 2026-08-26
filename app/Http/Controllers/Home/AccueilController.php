<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Compagnie;
use App\Models\Itineraire;
use App\Models\Convoi;
use App\Models\Particulier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ContactFormNotification;
use Illuminate\Support\Str;

class AccueilController extends Controller
{
    public function about()
    {
        return view('home.pages.about');
    }

    public function destination(Request $request)
    {
        $query = Itineraire::with('compagnie');

        if ($request->has('compagnie_id')) {
            $query->where('compagnie_id', $request->compagnie_id);
        }

        $itineraires = $query->get();
        return view('home.pages.destination', compact('itineraires'));
    }
    public function compagny()
    {
        $compagnies = Compagnie::where('statut', 'actif')->get();
        return view('home.pages.company', compact('compagnies'));
    }
    public function services()
    {
        return view('home.pages.services');
    }
    public function contact()
    {
        return view('home.pages.contact');
    }

    public function privacy()
    {
        return view('home.pages.privacy');
    }

    public function deletion()
    {
        return view('home.pages.deletion');
    }

    public function signaler()
    {
        return view('home.pages.signaler');
    }

    public function storeSignaler(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
            'email' => 'required|email'
        ]);

        $objet = 'Signalement: ' . $request->type;
        $description = "Billet: " . ($request->billet ?? 'Non précisé') . "\n" .
                       "Téléphone: " . ($request->telephone ?? 'Non précisé') . "\n\n" .
                       "Description:\n" . $request->description;

        // Enregistrer dans la base de données (table support_requests)
        \App\Models\SupportRequest::create([
            'user_id' => auth()->check() ? auth()->id() : null, // S'il est connecté, on associe le compte
            'reservation_id' => null, // Pas de réservation spécifique à ce stade
            'type' => 'autre', // Le type peut être 'autre' ou un type spécifique défini par la bd
            'objet' => $objet,
            'description' => $description,
            'statut' => 'ouvert',
            'email' => $request->email,
            'telephone' => $request->telephone,
            'billet' => $request->billet,
        ]);

        $contactData = [
            'name' => 'Utilisateur (Signalement)',
            'email' => $request->email,
            'subject' => $objet,
            'message' => $description,
        ];

        // Envoyer la notification par email (optionnel, pour alerter l'admin immédiatement)
        Notification::route('mail', env('MAIL_FROM_ADDRESS', 'contact@car225.com'))
            ->notify(new ContactFormNotification($contactData));

        return back()->with('success', 'Votre signalement a bien été envoyé. Notre équipe le traitera dans les plus brefs délais.');
    }

    public function mesReservations(Request $request)
    {
        $reservations = collect();
        $grouped = collect();
        $searched = false;
        $searchRef = $request->reference;

        if ($request->filled('reference')) {
            $searched = true;

            // Extraire la base de la référence pour trouver les billets liés
            // Ex: TX-WAL-FPFXCVCBPE-20 → base = TX-WAL-FPFXCVCBPE
            // Ex: TX-WAL-FPFXCVCBPE-RET-20 → base = TX-WAL-FPFXCVCBPE
            // Ex: TX-WAL-JSKCZJZDMW-1 → base = TX-WAL-JSKCZJZDMW
            $ref = trim($searchRef);
            // Enlever le suffixe -RET-XX ou -XX (numéro de siège)
            $baseRef = preg_replace('/-RET-\d+$/', '', $ref);
            $baseRef = preg_replace('/-\d+$/', '', $baseRef);

            $query = \App\Models\Reservation::with(['programme.compagnie', 'programme.gareDepart', 'programme.gareArrivee', 'programme.itineraire', 'programme.voyages'])
                ->whereHas('programme')
                ->where(function($q) use ($searchRef, $baseRef) {
                    // Match exacte pour la référence ou l'ID de transaction
                    $q->where('reference', $searchRef)
                      ->orWhere('payment_transaction_id', $searchRef);

                    // On ne permet la recherche élargie (pour trouver les autres sièges) 
                    // que si la référence de base est suffisamment longue (min 12 caractères)
                    // Cela évite que "TX-WAL-" n'affiche toutes les réservations.
                    if (strlen($baseRef) >= 12) {
                        $q->orWhere('reference', 'like', $baseRef . '-%');
                    }
                })
                ->where('statut', '!=', 'en_attente')
                ->orderBy('created_at', 'desc');

            $reservations = $query->get();

            // Grouper par base de référence (multi-sièges + aller/retour)
            $grouped = $reservations->groupBy(function($r) {
                // Enlever -RET-XX et -XX pour obtenir la base commune
                $base = preg_replace('/-RET-\d+$/', '', $r->reference);
                $base = preg_replace('/-\d+$/', '', $base);
                return $base;
            })->map(function($group) {
                $aller = $group->filter(fn($r) => !str_contains($r->reference, '-RET-'))->sortBy('seat_number')->values();
                $retour = $group->filter(fn($r) => str_contains($r->reference, '-RET-'))->sortBy('seat_number')->values();
                $first = $aller->first() ?? $retour->first();
                return (object)[
                    'aller' => $aller,
                    'retour' => $retour,
                    'first' => $first,
                    'all_seats_aller' => $aller->pluck('seat_number')->filter()->implode(', '),
                    'all_seats_retour' => $retour->pluck('seat_number')->filter()->implode(', '),
                ];
            });
        }

        return view('home.pages.reservations', compact('grouped', 'searched', 'searchRef'));
    }

    public function downloadTicket(\App\Models\Reservation $reservation)
    {
        $reservation->load(['programme.compagnie', 'programme.gareDepart', 'programme.gareArrivee', 'user']);

        $programme = $reservation->programme;
        $ticketType = $reservation->is_aller_retour ? 'ALLER' : 'ALLER SIMPLE';
        $qrCodeBase64 = $reservation->qr_code;
        $dateVoyage = $reservation->date_voyage;
        $heureDepart = $programme->heure_depart;

        $prixUnitaire = (float) $programme->montant_billet;
        $isAllerRetour = (bool) $reservation->is_aller_retour;
        $tripType = $isAllerRetour ? 'Aller-Retour' : 'Aller Simple';
        $prixTotalIndividuel = $isAllerRetour ? $prixUnitaire * 2 : $prixUnitaire;
        $seatNumber = $reservation->seat_number;

        $nomFichier = 'billet-' . strtolower($ticketType) . '-' . $reservation->reference;
        if ($seatNumber) {
            $nomFichier .= '-Place-' . $seatNumber;
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ticket', [
            'reservation' => $reservation,
            'programme' => $programme,
            'user' => $reservation->user,
            'compagnie' => $programme->compagnie,
            'qrCodeBase64' => $qrCodeBase64,
            'tripType' => $tripType,
            'ticketType' => $ticketType,
            'dateVoyage' => $dateVoyage,
            'heureDepart' => $heureDepart,
            'prixUnitaire' => $prixUnitaire,
            'prixTotalIndividuel' => $prixTotalIndividuel,
            'isAllerRetour' => $isAllerRetour,
            'seatNumber' => $seatNumber,
        ])->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 150,
            ]);

        return $pdf->download($nomFichier . '.pdf');
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contactData = [
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ];

        // Envoyer la notification par email
        Notification::route('mail', '')
            ->notify(new ContactFormNotification($contactData));

        return back()->with('success', 'Votre message a bien été envoyé. Notre équipe vous répondra très prochainement.');
    }

    public function downloadApp()
    {
        return view('landing.download');
    }

    public function convoi(Request $request)
    {
        $type = $request->query('type', 'all');

        $query = Convoi::with(['compagnie', 'particulier', 'itineraire'])
            ->whereIn('statut', ['valide', 'paye', 'en_cours', 'termine']);

        if ($type === 'compagnie') {
            $query->whereNotNull('compagnie_id');
        } elseif ($type === 'particulier') {
            $query->whereNotNull('particulier_id');
        }

        $convois = $query->latest()->paginate(9)->withQueryString();

        $compagnies = Compagnie::where('statut', 'actif')->orderBy('name')->get();
        $particuliers = Particulier::where('statut', 'valide')->orderBy('name')->get();

        return view('home.pages.convoi', compact('convois', 'compagnies', 'particuliers'));
    }

    public function storeParticulierRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:particuliers,email',
            'contact' => 'required|digits:10',
            'nombre_place_car' => 'required|integer|min:10',
            'date_mise_service' => 'required|date|before_or_equal:today',
            'immatriculation' => 'required|string|max:255',
            'photo_proprietaire' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'photo_complete_car' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'photo_avant_car' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'photo_arriere_car' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'carte_grise' => 'required|file|mimes:pdf,jpeg,png,jpg|max:4096',
            'visite_technique' => 'required|file|mimes:pdf,jpeg,png,jpg|max:4096',
        ], [
            'contact.digits' => 'Le numéro de téléphone doit contenir exactement 10 chiffres.',
            'nombre_place_car.min' => 'Le véhicule doit disposer d\'au moins 10 places.',
            'date_mise_service.before_or_equal' => 'La date de mise en service ne peut pas être dans le futur.',
        ]);

        try {
            // Uploads
            $paths = [];
            $filesToUpload = [
                'photo_proprietaire' => 'particuliers/photos',
                'photo_complete_car' => 'particuliers/photos',
                'photo_avant_car'    => 'particuliers/photos',
                'photo_arriere_car'  => 'particuliers/photos',
                'carte_grise'        => 'particuliers/documents',
                'visite_technique'   => 'particuliers/documents'
            ];

            foreach ($filesToUpload as $field => $folder) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = $field . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $paths[$field] = $file->storeAs($folder, $fileName, 'public');
                }
            }

            Particulier::create([
                'name' => $validated['name'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'contact' => $validated['contact'],
                'nombre_place_car' => $validated['nombre_place_car'],
                'date_mise_service' => $validated['date_mise_service'],
                'immatriculation' => $validated['immatriculation'],
                'photo_proprietaire' => $paths['photo_proprietaire'] ?? null,
                'photo_complete_car' => $paths['photo_complete_car'] ?? null,
                'photo_avant_car'    => $paths['photo_avant_car'] ?? null,
                'photo_arriere_car'  => $paths['photo_arriere_car'] ?? null,
                'carte_grise'        => $paths['carte_grise'] ?? null,
                'visite_technique'   => $paths['visite_technique'] ?? null,
                'statut' => 'en_attente',
            ]);

            return redirect()->route('home.convoi')->with('success', 'Votre demande d\'inscription a été enregistrée avec succès. L\'administrateur va l\'étudier et vous recevrez vos accès très prochainement.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur inscription particulier convoi: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement : ' . $e->getMessage())
                ->withInput();
        }
    }
}
