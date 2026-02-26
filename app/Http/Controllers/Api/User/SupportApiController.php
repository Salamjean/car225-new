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
            $query = Reservation::with(['programme.compagnie'])
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

            $reservations = $query->get();

            return response()->json([
                'success' => true,
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
     * Liste les demandes de support de l'utilisateur.
     */
    public function index()
    {
        try {
            $requests = SupportRequest::with(['reservation.programme'])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'support_requests' => $requests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}
