<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Compagnie;
use App\Models\Programme;
use App\Models\Personnel;
use App\Models\Vehicule;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CompagnieController extends Controller
{
    /**
     * Liste toutes les compagnies actives avec leurs statistiques
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $compagnies = Compagnie::where('statut', 'actif')
                ->get()
                ->map(function ($compagnie) {
                    return $this->formatCompagnieData($compagnie);
                });

            return response()->json([
                'success' => true,
                'message' => 'Compagnies récupérées avec succès',
                'data' => [
                    'total' => $compagnies->count(),
                    'compagnies' => $compagnies
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API: Erreur récupération compagnies:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des compagnies'
            ], 500);
        }
    }

    /**
     * Affiche les détails d'une compagnie spécifique
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $compagnie = Compagnie::find($id);

            if (!$compagnie) {
                return response()->json([
                    'success' => false,
                    'message' => 'Compagnie non trouvée'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Compagnie récupérée avec succès',
                'data' => $this->formatCompagnieData($compagnie, true)
            ]);

        } catch (\Exception $e) {
            Log::error('API: Erreur récupération compagnie:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la compagnie'
            ], 500);
        }
    }

    /**
     * Liste les programmes/trajets d'une compagnie
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function programmes($id)
    {
        try {
            $compagnie = Compagnie::find($id);

            if (!$compagnie) {
                return response()->json([
                    'success' => false,
                    'message' => 'Compagnie non trouvée'
                ], 404);
            }

            $programmes = Programme::where('compagnie_id', $id)
                ->where('date_depart', '>=', Carbon::today())
                ->with(['vehicule', 'itineraire'])
                ->get()
                ->map(function ($programme) {
                    return [
                        'id' => $programme->id,
                        'trajet' => $programme->point_depart . ' → ' . $programme->point_arrive,
                        'point_depart' => $programme->point_depart,
                        'point_arrive' => $programme->point_arrive,
                        'date_depart' => $programme->date_depart,
                        'heure_depart' => $programme->heure_depart,
                        'heure_arrive' => $programme->heure_arrive,
                        'montant_billet' => (float) $programme->montant_billet,
                        'places_disponibles' => $programme->places_disponibles,
                        'nombre_places' => $programme->nombre_places,
                        'is_aller_retour' => (bool) $programme->is_aller_retour,
                        'is_recurrent' => (bool) $programme->is_recurrent,
                        'vehicule' => $programme->vehicule ? [
                            'immatriculation' => $programme->vehicule->immatriculation,
                            'marque' => $programme->vehicule->marque,
                            'modele' => $programme->vehicule->modele,
                        ] : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Programmes récupérés avec succès',
                'data' => [
                    'compagnie_id' => $compagnie->id,
                    'compagnie_name' => $compagnie->name,
                    'total_programmes' => $programmes->count(),
                    'programmes' => $programmes
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API: Erreur récupération programmes compagnie:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des programmes'
            ], 500);
        }
    }

    /**
     * Formate les données d'une compagnie pour l'API
     * 
     * @param Compagnie $compagnie
     * @param bool $detailed
     * @return array
     */
    private function formatCompagnieData(Compagnie $compagnie, bool $detailed = false): array
    {
        // Statistiques de base
        $totalPersonnels = Personnel::where('compagnie_id', $compagnie->id)->count();
        $totalVehicules = Vehicule::where('compagnie_id', $compagnie->id)->count();
        $totalProgrammes = Programme::where('compagnie_id', $compagnie->id)
            ->count();

        // Statistiques de réservations
        $totalReservations = Reservation::whereHas('programme', function ($q) use ($compagnie) {
            $q->where('compagnie_id', $compagnie->id);
        })->where('statut', 'confirmee')->count();

        // Calculer la note moyenne (simulation - à adapter selon votre système de notation)
        // Pour l'instant, on simule une note basée sur le nombre de réservations
        $rating = $this->calculateRating($compagnie->id, $totalReservations);

        // Logo URL
        $logoUrl = $compagnie->path_logo 
            ? 'storage/' . $compagnie->path_logo 
            : null;

        // Générer les initiales pour le sigle
        $initials = $compagnie->sigle ?: $this->generateInitials($compagnie->name);

        // Tags/badges (à adapter selon votre logique métier)
        $tags = $this->generateTags($compagnie, $totalReservations, $totalVehicules);

        $data = [
            'id' => $compagnie->id,
            'name' => $compagnie->name,
            'sigle' => $initials,
            'slogan' => $compagnie->slogan ?? 'Transport de qualité',
            'logo_url' => $logoUrl,
            'rating' => $rating['score'],
            'reviews_count' => $rating['reviews_count'],
            'stats' => [
                'personnels' => $totalPersonnels,
                'vehicules' => $totalVehicules,
                'programmes' => $totalProgrammes,
                'reservations' => $totalReservations,
                'itineraires' => $compagnie->itineraires()->count(),
            ],
            'tags' => $tags,
            'itineraires' => $compagnie->itineraires->map(function($itineraire) {
                return [
                    'id' => $itineraire->id,
                    'point_depart' => $itineraire->point_depart,
                    'point_arrive' => $itineraire->point_arrive,
                    'durer_parcours' => $itineraire->durer_parcours,
                ];
            }),
        ];

        // Ajouter plus de détails si demandé
        if ($detailed) {
            $data['contact'] = [
                'email' => $compagnie->email,
                'telephone' => $compagnie->contact,
                'adresse' => $compagnie->adresse,
                'commune' => $compagnie->commune,
            ];
            
            $data['prefix'] = $compagnie->prefix;
            $data['statut'] = $compagnie->statut;
            
            // Derniers programmes
            $data['recent_programmes'] = Programme::where('compagnie_id', $compagnie->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'trajet' => $p->point_depart . ' → ' . $p->point_arrive,
                        'montant_billet' => (float) $p->montant_billet,
                        'places_disponibles' => $p->places_disponibles,
                    ];
                });
        }

        return $data;
    }

    /**
     * Calcule la note d'une compagnie (simulation basée sur les réservations)
     * 
     * @param int $compagnieId
     * @param int $totalReservations
     * @return array
     */
    private function calculateRating(int $compagnieId, int $totalReservations): array
    {
        // TODO: Implémenter un vrai système de notation avec avis clients
        // Pour l'instant, on simule une note basée sur l'activité
        
        // Base rating
        $baseRating = 4.0;
        
        // Bonus basé sur le nombre de réservations
        if ($totalReservations > 1000) {
            $baseRating += 0.8;
        } elseif ($totalReservations > 500) {
            $baseRating += 0.6;
        } elseif ($totalReservations > 100) {
            $baseRating += 0.4;
        } elseif ($totalReservations > 50) {
            $baseRating += 0.2;
        }

        // Limiter à 5.0
        $rating = min(5.0, $baseRating);
        
        // Nombre d'avis simulé (basé sur les réservations)
        $reviewsCount = (int) ($totalReservations * 0.3); // ~30% des clients laissent un avis

        return [
            'score' => round($rating, 1),
            'reviews_count' => max($reviewsCount, 0)
        ];
    }

    /**
     * Génère les initiales d'un nom de compagnie
     * 
     * @param string $name
     * @return string
     */
    private function generateInitials(string $name): string
    {
        $words = explode(' ', $name);
        $initials = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
            if (strlen($initials) >= 2) {
                break;
            }
        }

        return $initials ?: strtoupper(substr($name, 0, 2));
    }

    /**
     * Génère les tags/badges pour une compagnie
     * 
     * @param Compagnie $compagnie
     * @param int $totalReservations
     * @param int $totalVehicules
     * @return array
     */
    private function generateTags(Compagnie $compagnie, int $totalReservations, int $totalVehicules): array
    {
        $tags = [];

        // Tag "Certifiée" si la compagnie est active
        if ($compagnie->statut === 'actif') {
            $tags[] = [
                'label' => 'Certifiée',
                'color' => '#10b981', // green
            ];
        }

        // Tag "Fiable" si beaucoup de réservations
        if ($totalReservations > 100) {
            $tags[] = [
                'label' => 'Fiable',
                'color' => '#f59e0b', // yellow/orange
            ];
        }

        // Tag "Moderne" si flotte de véhicules conséquente
        if ($totalVehicules > 10) {
            $tags[] = [
                'label' => 'Moderne',
                'color' => '#3b82f6', // blue
            ];
        }

        // Tag "Populaire" si très beaucoup de réservations
        if ($totalReservations > 500) {
            $tags[] = [
                'label' => 'Populaire',
                'color' => '#8b5cf6', // purple
            ];
        }

        return $tags;
    }
}
