<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\SupportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SupportApiController extends Controller
{
    private const TYPES_REQUIRING_RESERVATION = ['bagage_perdu', 'objet_oublie', 'remboursement', 'qualite'];

    /**
     * Retourne les catégories de support avec leurs métadonnées.
     */
    public function getCategories()
    {
        $categories = [
            [
                'id' => 'bagage_perdu',
                'title' => 'Bagage Perdu',
                'description' => 'Vous n\'avez pas retrouvé votre bagage à l\'arrivée ? Signalez-le nous immédiatement.',
                'icon' => 'suitcase-rolling',
                'color' => '#e94f1b',
                'needs_reservation' => true,
                'reservation_label' => 'Voyage concerné',
                'empty_message' => 'Vous n\'avez aucun voyage terminé. Vous pourrez déclarer un bagage perdu une fois votre voyage effectué.',
                'placeholder_object' => 'Ex: Valise bleue à roulettes, sac à dos noir...',
                'placeholder_description' => 'Décrivez votre bagage (couleur, taille, contenu) et les circonstances de la perte.'
            ],
            [
                'id' => 'objet_oublie',
                'title' => 'Objet Oublié',
                'description' => 'Vous avez oublié un téléphone, des clés ou un vêtement dans le bus ? Nous allons vérifier.',
                'icon' => 'glasses',
                'color' => '#3b82f6',
                'needs_reservation' => true,
                'reservation_label' => 'Voyage concerné',
                'empty_message' => 'Vous n\'avez aucun voyage terminé. Vous pourrez signaler un objet oublié une fois votre voyage effectué.',
                'placeholder_object' => 'Ex: Téléphone Samsung, lunettes de soleil, clés...',
                'placeholder_description' => 'Décrivez l\'objet oublié et où il se trouvait dans le véhicule.'
            ],
            [
                'id' => 'remboursement',
                'title' => 'Remboursement',
                'description' => 'Une erreur de paiement ou un voyage annulé ? Demandez un remboursement sur votre solde.',
                'icon' => 'hand-holding-usd',
                'color' => '#22c55e',
                'needs_reservation' => true,
                'reservation_label' => 'Réservation annulée',
                'empty_message' => 'Vous n\'avez aucune réservation annulée. Le remboursement concerne uniquement les trajets déjà annulés.',
                'placeholder_object' => 'Ex: Remboursement réservation annulée du 15/02...',
                'placeholder_description' => 'Expliquez les circonstances de l\'annulation.'
            ],
            [
                'id' => 'qualite',
                'title' => 'Qualité de Service',
                'description' => 'Un problème avec le chauffeur, l\'hotesse ou le confort du véhicule ? Dites-le nous.',
                'icon' => 'star',
                'color' => '#a855f7',
                'needs_reservation' => true,
                'reservation_label' => 'Voyage concerné',
                'empty_message' => 'Vous n\'avez aucun voyage récent à signaler.',
                'placeholder_object' => 'Ex: Comportement du chauffeur, propreté du véhicule...',
                'placeholder_description' => 'Décrivez le problème rencontré (chauffeur, hôtesse, véhicule, ponctualité...).'
            ],
            [
                'id' => 'compte',
                'title' => 'Mon Compte',
                'description' => 'Problème d\'accès, modification de profil ou erreur de solde portefeuille.',
                'icon' => 'user-cog',
                'color' => '#6b7280',
                'needs_reservation' => false,
                'placeholder_object' => 'Ex: Erreur de solde portefeuille, accès impossible...',
                'placeholder_description' => 'Décrivez votre problème : erreur de solde, problème de connexion, modification de profil...'
            ],
            [
                'id' => 'autre',
                'title' => 'Autre demande',
                'description' => 'Pour toute autre question ou suggestion non listée ci-dessus.',
                'icon' => 'question',
                'color' => '#ef4444',
                'needs_reservation' => false,
                'placeholder_object' => 'Ex: Suggestion, question sur un service...',
                'placeholder_description' => 'Décrivez votre demande ou question en détail.'
            ]
        ];

        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    /**
     * Retourne les réservations contextuelles pour un type.
     */
    public function getReservations(Request $request)
    {
        $type = $request->get('type', 'autre');
        $needsReservation = in_array($type, self::TYPES_REQUIRING_RESERVATION);

        if (!$needsReservation) {
            return response()->json([
                'success' => true,
                'reservations' => []
            ]);
        }

        try {
            $query = Reservation::with([
                    'programme.gareDepart',
                    'programme.gareArrivee',
                ])
                ->where('user_id', Auth::id())
                ->orderBy('date_voyage', 'desc')
                ->take(20);

            switch ($type) {
                case 'bagage_perdu':
                case 'objet_oublie':
                    $query->where('statut', 'terminee');
                    break;
                case 'remboursement':
                    $query->where('statut', 'annulee');
                    break;
                case 'qualite':
                    $query->whereIn('statut', ['confirmee', 'terminee']);
                    break;
            }

            $reservations = $query->get()->map(function ($r) {
                $prog = $r->programme;
                return [
                    'id'           => $r->id,
                    'reference'    => $r->reference ?? null,
                    'gare_depart'  => $prog && $prog->gareDepart
                                        ? $prog->gareDepart->nom_gare . ' (' . $prog->gareDepart->ville . ')'
                                        : null,
                    'gare_arrivee' => $prog && $prog->gareArrivee
                                        ? $prog->gareArrivee->nom_gare . ' (' . $prog->gareArrivee->ville . ')'
                                        : null,
                    'point_depart' => $prog->point_depart ?? null,
                    'point_arrive' => $prog->point_arrive ?? null,
                    'date'         => $r->date_voyage
                                        ? \Carbon\Carbon::parse($r->date_voyage)->format('d/m/Y')
                                        : null,
                    'statut'       => $r->statut,
                ];
            });

            return response()->json([
                'success'      => true,
                'reservations' => $reservations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enregistre une demande de support.
     */
    public function store(Request $request)
    {
        $type = $request->input('type', 'autre');
        $needsReservation = in_array($type, self::TYPES_REQUIRING_RESERVATION);

        $rules = [
            'type' => 'required|string',
            'objet' => 'required|string|max:255',
            'description' => 'required|string',
        ];

        if ($needsReservation) {
            $rules['reservation_id'] = 'required|exists:reservations,id';
        } else {
            $rules['reservation_id'] = 'nullable|exists:reservations,id';
        }

        try {
            $validated = $request->validate($rules);

            $supportRequest = SupportRequest::create([
                'user_id' => Auth::id(),
                'type' => $validated['type'],
                'objet' => $validated['objet'],
                'description' => $validated['description'],
                'reservation_id' => $validated['reservation_id'] ?? null,
                'statut' => 'ouvert'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Votre demande a été enregistrée avec succès.',
                'support_request' => $supportRequest
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur SupportApiController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'enregistrement.'
            ], 500);
        }
    }

    /**
     * Labels lisibles par type de déclaration.
     */
    private const TYPE_LABELS = [
        'bagage_perdu'  => 'Bagage Perdu',
        'objet_oublie'  => 'Objet Oublié',
        'remboursement' => 'Remboursement',
        'qualite'       => 'Qualité de Service',
        'compte'        => 'Mon Compte',
        'autre'         => 'Autre demande',
    ];

    /**
     * Formate une déclaration en tableau allégé.
     */
    private function formatDeclaration(SupportRequest $s): array
    {
        $res  = $s->reservation;
        $prog = $res?->programme;

        return [
            'id'          => $s->id,
            'type'        => $s->type,
            'type_label'  => self::TYPE_LABELS[$s->type] ?? ucfirst($s->type),
            'objet'       => $s->objet,
            'description' => $s->description,
            'statut'      => $s->statut,
            'reponse'     => $s->reponse,
            'created_at'  => $s->created_at->format('d/m/Y H:i'),
            'reservation' => $res ? [
                'id'           => $res->id,
                'reference'    => $res->reference ?? null,
                'gare_depart'  => $prog && $prog->gareDepart
                                    ? $prog->gareDepart->nom_gare . ' (' . $prog->gareDepart->ville . ')'
                                    : null,
                'gare_arrivee' => $prog && $prog->gareArrivee
                                    ? $prog->gareArrivee->nom_gare . ' (' . $prog->gareArrivee->ville . ')'
                                    : null,
                'point_depart' => $prog->point_depart ?? null,
                'point_arrive' => $prog->point_arrive ?? null,
                'date'         => $res->date_voyage
                                    ? \Carbon\Carbon::parse($res->date_voyage)->format('d/m/Y')
                                    : null,
                'statut'       => $res->statut,
            ] : null,
        ];
    }

    /**
     * Liste les demandes de support de l'utilisateur.
     *
     * Query params optionnels :
     *   ?type=bagage_perdu   → filtre par type précis
     *   (aucun paramètre)    → toutes les déclarations groupées par type
     */
    public function index(Request $request)
    {
        try {
            $type = $request->get('type');

            $query = SupportRequest::with([
                    'reservation.programme.gareDepart',
                    'reservation.programme.gareArrivee',
                ])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc');

            // ── Filtre par type si fourni ──────────────────────────────────
            if ($type) {
                $validTypes = array_keys(self::TYPE_LABELS);
                if (!in_array($type, $validTypes)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Type invalide. Types acceptés : ' . implode(', ', $validTypes),
                    ], 422);
                }

                $items = $query->where('type', $type)->get()->map(fn($s) => $this->formatDeclaration($s));

                return response()->json([
                    'success'    => true,
                    'type'       => $type,
                    'type_label' => self::TYPE_LABELS[$type],
                    'total'      => $items->count(),
                    'declarations' => $items,
                ]);
            }

            // ── Sans filtre : groupées par type ────────────────────────────
            $all = $query->get();

            $grouped = [];
            foreach (self::TYPE_LABELS as $key => $label) {
                $items = $all->where('type', $key)->values();
                if ($items->isEmpty()) continue;

                $grouped[] = [
                    'type'       => $key,
                    'type_label' => $label,
                    'total'      => $items->count(),
                    'declarations' => $items->map(fn($s) => $this->formatDeclaration($s))->values(),
                ];
            }

            return response()->json([
                'success'       => true,
                'total_general' => $all->count(),
                'par_type'      => $grouped,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}
