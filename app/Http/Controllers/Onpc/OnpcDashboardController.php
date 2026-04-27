<?php

namespace App\Http\Controllers\Onpc;

use App\Http\Controllers\Controller;
use App\Models\SapeurPompier;
use App\Models\Signalement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Tableau de bord et vues consultatives de l'agent ONPC.
 *
 * L'agent ONPC est un superviseur national : il a un accès LECTURE
 * SEULE à toutes les casernes, signalements, bilans et passagers
 * évacués. Aucune action de modification n'est exposée ici.
 */
class OnpcDashboardController extends Controller
{
    /**
     * Vue d'ensemble : KPIs + derniers signalements + casernes actives.
     */
    public function dashboard(Request $request)
    {
        $today = now()->startOfDay();

        $stats = [
            'casernes_total'         => SapeurPompier::count(),
            'casernes_actives'       => SapeurPompier::where('statut', 'actif')->count(),
            'signalements_total'     => Signalement::count(),
            'signalements_nouveaux'  => Signalement::where('statut', 'nouveau')->count(),
            'signalements_traites'   => Signalement::where('statut', 'traite')->count(),
            'accidents_total'        => Signalement::where('type', 'accident')->count(),
            'morts_total'            => (int) Signalement::sum('nombre_morts'),
            'blesses_total'          => (int) Signalement::sum('nombre_blesses'),
            'evacues_total'          => $this->countEvacues(),
            'signalements_du_jour'   => Signalement::where('created_at', '>=', $today)->count(),
        ];

        // 5 derniers signalements (toutes casernes confondues)
        $recentSignalements = Signalement::with([
            'sapeurPompier',
            'compagnie',
            'voyage.programme',
            'convoi.itineraire',
        ])
            ->latest()
            ->limit(5)
            ->get();

        // Top casernes par nombre d'interventions
        $topCasernes = SapeurPompier::withCount('signalementsAssigned')
            ->orderByDesc('signalements_assigned_count')
            ->limit(5)
            ->get();

        return view('onpc.dashboard', compact('stats', 'recentSignalements', 'topCasernes'));
    }

    /**
     * Liste / recherche des sapeurs-pompiers (casernes).
     */
    public function sapeursPompiers(Request $request)
    {
        $query = SapeurPompier::query()->withCount([
            'signalementsAssigned',
            'signalementsAssigned as signalements_traites_count' => function ($q) {
                $q->where('statut', 'traite');
            },
        ]);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('commune', 'like', "%{$q}%")
                  ->orWhere('adresse', 'like', "%{$q}%")
                  ->orWhere('contact', 'like', "%{$q}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $casernes = $query->latest()->paginate(15)->withQueryString();

        return view('onpc.sapeurs.index', compact('casernes'));
    }

    public function sapeurPompierShow(SapeurPompier $sapeurPompier)
    {
        $sapeurPompier->loadCount([
            'signalementsAssigned',
            'signalementsAssigned as signalements_traites_count' => function ($q) {
                $q->where('statut', 'traite');
            },
            'signalementsAssigned as signalements_nouveaux_count' => function ($q) {
                $q->where('statut', 'nouveau');
            },
        ]);

        $signalements = Signalement::where('sapeur_pompier_id', $sapeurPompier->id)
            ->with(['compagnie', 'voyage.programme', 'convoi.itineraire'])
            ->latest()
            ->paginate(15);

        return view('onpc.sapeurs.show', compact('sapeurPompier', 'signalements'));
    }

    /**
     * Liste / filtre des signalements (toute la base, lecture seule).
     */
    public function signalements(Request $request)
    {
        $query = Signalement::with([
            'sapeurPompier',
            'compagnie',
            'voyage.programme',
            'convoi.itineraire',
            'personnel',
            'vehicule',
        ]);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('description', 'like', "%{$q}%")
                  ->orWhereHas('sapeurPompier', fn ($s) => $s->where('name', 'like', "%{$q}%")->orWhere('commune', 'like', "%{$q}%"))
                  ->orWhereHas('compagnie', fn ($c) => $c->where('name', 'like', "%{$q}%"));
            });
        }

        foreach (['type', 'statut', 'sapeur_pompier_id'] as $f) {
            if ($request->filled($f)) {
                $query->where($f, $request->input($f));
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $signalements = $query->latest()->paginate(20)->withQueryString();
        $casernes     = SapeurPompier::orderBy('name')->get(['id', 'name']);

        return view('onpc.signalements.index', compact('signalements', 'casernes'));
    }

    public function signalementShow(Signalement $signalement)
    {
        $signalement->load([
            'sapeurPompier',
            'compagnie',
            'voyage.programme',
            'convoi.itineraire.compagnie',
            'convoi.passagers',
            'personnel',
            'vehicule',
            'user',
        ]);

        // Bilan passagers reconstitué
        $bilan         = $signalement->bilan_passagers ?? [];
        $bilanDetailed = $this->hydrateBilan($signalement, $bilan);

        return view('onpc.signalements.show', compact('signalement', 'bilanDetailed'));
    }

    /**
     * Liste agrégée des passagers évacués (extraits des bilans clôturés).
     */
    public function evacues(Request $request)
    {
        $query = Signalement::whereNotNull('bilan_passagers')
            ->where('statut', 'traite')
            ->with(['sapeurPompier', 'compagnie', 'convoi.passagers', 'voyage.programme']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->whereHas('sapeurPompier', fn ($s) => $s->where('name', 'like', "%{$q}%"))
                  ->orWhereHas('compagnie', fn ($c) => $c->where('name', 'like', "%{$q}%"));
            });
        }

        $signalements = $query->latest()->get();

        // Aplatissement : 1 ligne = 1 passager évacué
        $rows = [];
        foreach ($signalements as $s) {
            $hydrated = $this->hydrateBilan($s, $s->bilan_passagers ?? []);
            foreach ($hydrated as $entry) {
                if (($entry['statut'] ?? null) !== 'evacue') continue;
                $rows[] = [
                    'signalement_id'  => $s->id,
                    'date'            => $s->created_at,
                    'caserne'         => optional($s->sapeurPompier)->name,
                    'compagnie'       => optional($s->compagnie)->name,
                    'passager'        => $entry['nom_passager'] ?? '—',
                    'photo_url'       => $entry['photo_url'] ?? null,
                    'photo_initials'  => $entry['photo_initials'] ?? '?',
                    'age'             => $entry['age'] ?? null,
                    'date_naissance'  => $entry['date_naissance'] ?? null,
                    'genre'           => $entry['genre'] ?? null,
                    'contact'         => $entry['contact'] ?? null,
                    'contact_urgence' => $entry['contact_urgence'] ?? null,
                    'email'           => $entry['email'] ?? null,
                    'hopital'         => $entry['hopital_nom'] ?? '—',
                    'hopital_adresse' => $entry['hopital_adresse'] ?? '',
                    'trajet'          => $entry['trajet'] ?? '—',
                ];
            }
        }

        // Pagination manuelle (simple)
        $perPage = 20;
        $page    = max(1, (int) $request->input('page', 1));
        $total   = count($rows);
        $items   = array_slice($rows, ($page - 1) * $perPage, $perPage);

        return view('onpc.evacues.index', [
            'rows'    => $items,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
            'lastPage' => max(1, (int) ceil($total / $perPage)),
            'q'       => $request->input('q'),
        ]);
    }

    // -------------------------------------------------------------------
    // Profil
    // -------------------------------------------------------------------

    public function profile()
    {
        $onpc = Auth::guard('onpc')->user();
        return view('onpc.profile', compact('onpc'));
    }

    public function updateProfile(Request $request)
    {
        $onpc = Auth::guard('onpc')->user();

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'contact'      => 'required|digits:10',
            'localisation' => 'required|string|max:255',
            'photo_path'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password'     => 'nullable|string|min:8|confirmed',
        ], [
            'contact.digits' => 'Le numéro de téléphone doit contenir exactement 10 chiffres.',
        ]);

        if ($request->hasFile('photo_path')) {
            if ($onpc->photo_path && Storage::disk('public')->exists($onpc->photo_path)) {
                Storage::disk('public')->delete($onpc->photo_path);
            }
            $file = $request->file('photo_path');
            $name = 'onpc_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            $validated['photo_path'] = $file->storeAs('onpcs/photos', $name, 'public');
        }

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $onpc->update($validated);

        return back()->with('success', 'Profil mis à jour.');
    }

    // -------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------

    /**
     * Compte agrégé du nombre de passagers évacués sur l'ensemble
     * des signalements (extraits du JSON bilan_passagers).
     */
    private function countEvacues(): int
    {
        $count = 0;
        Signalement::whereNotNull('bilan_passagers')
            ->select('bilan_passagers')
            ->chunk(200, function ($chunk) use (&$count) {
                foreach ($chunk as $row) {
                    foreach (($row->bilan_passagers ?? []) as $entry) {
                        if (($entry['statut'] ?? null) === 'evacue') $count++;
                    }
                }
            });
        return $count;
    }

    /**
     * Enrichit chaque entrée du bilan avec un profil passager le plus
     * complet possible : photo, identité, contact, contact d'urgence,
     * date de naissance / âge, genre, place, etc.
     *
     * Tolérant aux champs manquants (legacy data) — affiche `null` si
     * l'information n'est pas disponible.
     */
    private function hydrateBilan(Signalement $s, array $bilan): array
    {
        if (empty($bilan)) return [];

        $programme = optional($s->voyage)->programme ?? optional($s->convoi)->itineraire;
        $trajet    = '—';
        if ($programme) {
            $depart  = $programme->point_depart ?? $programme->gare_depart ?? '';
            $arrivee = $programme->point_arrive ?? $programme->gare_arrivee ?? '';
            $trajet  = trim($depart . ' → ' . $arrivee, ' →');
        }

        $isConvoi = (bool) $s->convoi_id;

        $out = [];
        foreach ($bilan as $entry) {
            $rid       = $entry['reservation_id'] ?? null;
            $passenger = $isConvoi
                ? \App\Models\ConvoiPassager::find($rid)
                : \App\Models\Reservation::with('user')->find($rid);

            $profile = $this->buildPassengerProfile($passenger, $isConvoi);

            $out[] = array_merge($entry, $profile, [
                'trajet' => $trajet,
            ]);
        }

        return $out;
    }

    /**
     * Construit un profil normalisé pour un passager (Reservation ou
     * ConvoiPassager). Renvoie un tableau avec toujours les mêmes clés.
     */
    private function buildPassengerProfile($passenger, bool $isConvoi): array
    {
        $base = [
            'nom_passager'        => '—',
            'email'               => null,
            'contact'             => null,
            'contact_urgence'     => null,
            'nom_urgence'         => null,
            'photo_url'           => null,
            'photo_initials'      => '?',
            'date_naissance'      => null,
            'age'                 => null,
            'genre'               => null,
            'piece_identite'      => null,
            'seat_number'         => null,
            'reference'           => null,
            'montant'             => null,
            'reservation_id'      => null,
            'has_account'         => false,
        ];

        if (!$passenger) return $base;

        if ($isConvoi) {
            $nomComplet = trim(($passenger->prenoms ?? '') . ' ' . ($passenger->nom ?? ''));
            $base['nom_passager']     = $nomComplet ?: '—';
            $base['email']            = $passenger->email ?? null;
            $base['contact']          = $passenger->contact ?? null;
            $base['contact_urgence']  = $passenger->contact_urgence ?? null;
            $base['date_naissance']   = $passenger->date_naissance;
            $base['genre']            = $passenger->genre ?? null;
            $base['piece_identite']   = $passenger->piece_identite ?? null;
            $base['reservation_id']   = $passenger->id;
            $base['photo_url']        = $passenger->photo_path
                ? \Storage::url($passenger->photo_path)
                : null;
        } else {
            // Reservation : passager peut avoir un compte (user_id) ou être walk-in
            $user           = $passenger->user;
            $hasAccount     = (bool) $user;

            $nomComplet = trim(($passenger->passager_prenom ?? '') . ' ' . ($passenger->passager_nom ?? ''));
            if (!$nomComplet && $user) {
                $nomComplet = trim(($user->prenom ?? '') . ' ' . ($user->name ?? ''));
            }

            $base['nom_passager']     = $nomComplet ?: '—';
            $base['email']            = $passenger->passager_email ?? optional($user)->email;
            $base['contact']          = $passenger->passager_telephone ?? optional($user)->contact;
            $base['contact_urgence']  = $passenger->passager_urgence ?? optional($user)->contact_urgence;
            $base['nom_urgence']      = $passenger->nom_passager_urgence ?? optional($user)->nom_urgence;
            $base['date_naissance']   = $passenger->passager_date_naissance ?? optional($user)->date_naissance;
            $base['genre']            = $passenger->passager_genre ?? optional($user)->genre;
            $base['piece_identite']   = $passenger->passager_piece_identite ?? optional($user)->piece_identite;
            $base['seat_number']      = $passenger->seat_number ?? null;
            $base['reference']        = $passenger->reference ?? null;
            $base['montant']          = $passenger->montant ?? null;
            $base['reservation_id']   = $passenger->id;
            $base['has_account']      = $hasAccount;
            $base['photo_url']        = $hasAccount && $user->photo_profile_path
                ? \Storage::url($user->photo_profile_path)
                : null;
        }

        // Âge calculé depuis la date de naissance
        if ($base['date_naissance']) {
            try {
                $base['age'] = \Carbon\Carbon::parse($base['date_naissance'])->age;
            } catch (\Throwable $e) {
                $base['age'] = null;
            }
        }

        // Initiales pour l'avatar fallback
        $parts = preg_split('/\s+/', trim($base['nom_passager']));
        $initials = '';
        foreach ($parts as $p) {
            if ($p === '' || $p === '—') continue;
            $initials .= mb_strtoupper(mb_substr($p, 0, 1));
            if (mb_strlen($initials) >= 2) break;
        }
        $base['photo_initials'] = $initials !== '' ? $initials : '?';

        return $base;
    }
}
