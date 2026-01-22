<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Compagnie;
use App\Models\Programme;
use App\Models\Itineraire;
use App\Models\Reservation;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function home()
    {
        $compagnies = Compagnie::get();
        return view('home.home', compact('compagnies'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'point_depart' => 'required|string|max:255',
            'point_arrive' => 'required|string|max:255',
            'date_depart' => 'required|date',
        ]);

        $point_depart = $request->point_depart;
        $point_arrive = $request->point_arrive;
        $date_depart_recherche = $request->date_depart;

        // Formatez la date au même format que dans la base de données
        $formattedDate = date('Y-m-d', strtotime($date_depart_recherche));

        // CONVERSION: Jour de la semaine en FRANÇAIS (comme dans votre base)
        $joursFrancais = [
            'monday' => 'lundi',
            'tuesday' => 'mardi',
            'wednesday' => 'mercredi',
            'thursday' => 'jeudi',
            'friday' => 'vendredi',
            'saturday' => 'samedi',
            'sunday' => 'dimanche'
        ];

        $jourAnglais = strtolower(date('l', strtotime($date_depart_recherche)));
        $jour_semaine = $joursFrancais[$jourAnglais] ?? $jourAnglais;

        Log::info('====== DEBUT RECHERCHE PROGRAMME ======');
        Log::info('Paramètres de recherche:', [
            'point_depart' => $point_depart,
            'point_arrive' => $point_arrive,
            'date_depart_recherche' => $date_depart_recherche,
            'formattedDate' => $formattedDate,
            'jour_anglais' => $jourAnglais,
            'jour_francais' => $jour_semaine,
        ]);

        // Recherche
        $query = Programme::with(['compagnie', 'vehicule', 'itineraire'])
            ->where('point_depart', 'like', "%{$point_depart}%")
            ->where('point_arrive', 'like', "%{$point_arrive}%");

        if ($request->has('is_aller_retour') && $request->is_aller_retour != '') {
            $query->where('is_aller_retour', $request->is_aller_retour);
        }

        $initialCount = $query->count();
        Log::info("Nombre de programmes après filtrage par villes: {$initialCount}");

        // CORRECTION: Cherchez les programmes selon leur type
        $query->where(function ($q) use ($formattedDate, $jour_semaine) {
            // 1. Programmes ponctuels à la date exacte
            $q->where(function ($sub) use ($formattedDate) {
                $sub->where('type_programmation', 'ponctuel')
                    // Utilisez DATE() si le champ contient des heures
                    ->whereRaw('DATE(date_depart) = ?', [$formattedDate]);
            });

            // 2. Programmes récurrents qui incluent cette date
            $q->orWhere(function ($sub) use ($formattedDate, $jour_semaine) {
                $sub->where('type_programmation', 'recurrent')
                    // Date de début <= date recherchée
                    ->whereRaw('DATE(date_depart) <= ?', [$formattedDate])
                    ->where(function ($dateCheck) use ($formattedDate) {
                        $dateCheck->where(function ($subDate) use ($formattedDate) {
                            // Si date_fin_programmation existe, elle doit être >= date recherchée
                            $subDate->whereNotNull('date_fin_programmation')
                                ->whereRaw('DATE(date_fin_programmation) >= ?', [$formattedDate]);
                        })
                            ->orWhereNull('date_fin_programmation');
                    })
                    ->where(function ($dayCheck) use ($jour_semaine) {
                        // Recherche en français dans le JSON
                        $dayCheck->whereJsonContains('jours_recurrence', $jour_semaine)
                            ->orWhere('jours_recurrence', 'like', "%{$jour_semaine}%");
                    });
            });
        });

        // Récupérer la requête SQL
        $sql = $query->toSql();
        $bindings = $query->getBindings();

        Log::info('Requête SQL générée:', [
            'sql' => $sql,
            'bindings' => $bindings,
        ]);

        // Exécuter pour debug
        $debugResults = $query->get(['id', 'point_depart', 'point_arrive', 'date_depart', 'type_programmation', 'jours_recurrence', 'date_fin_programmation']);

        Log::info('Résultats bruts (sans pagination):', [
            'count' => $debugResults->count(),
            'programmes' => $debugResults->map(function ($programme) {
                return [
                    'id' => $programme->id,
                    'point_depart' => $programme->point_depart,
                    'point_arrive' => $programme->point_arrive,
                    'date_depart' => $programme->date_depart,
                    'type_programmation' => $programme->type_programmation,
                    'jours_recurrence' => $programme->jours_recurrence,
                    'date_fin_programmation' => $programme->date_fin_programmation,
                ];
            })->toArray()
        ]);

        $query->orderBy('heure_depart', 'asc');

        $programmes = $query->paginate(10);
        $totalResults = $programmes->total();

        Log::info('Résultats finaux:', [
            'total_results' => $totalResults,
            'has_pages' => $programmes->hasPages(),
        ]);

        Log::info('====== FIN RECHERCHE PROGRAMME ======');

        $searchParams = [
            'point_depart' => $point_depart,
            'point_arrive' => $point_arrive,
            'date_depart' => $date_depart_recherche,
        ];

        return view('home.programmes.results', compact('programmes', 'totalResults', 'searchParams'));
    }
    /**
     * Afficher tous les programmes
     */
    public function all()
    {
        $programmes = Programme::with(['compagnie', 'vehicule', 'itineraire'])
            ->where(function ($query) {
                $query->whereNull('date_fin_programmation')
                    ->orWhereDate('date_fin_programmation', '>=', now());
            })
            ->where(function ($query) {
                $query->where(function ($sub) {
                    $sub->where('type_programmation', 'ponctuel')
                        ->whereDate('date_depart', '>=', now());
                })
                    ->orWhere('type_programmation', 'recurrent');
            })
            ->orderBy('date_depart', 'asc')
            ->orderBy('heure_depart', 'asc')
            ->paginate(12);

        return view('home.programmes.all', compact('programmes'));
    }

    /**
     * Afficher les détails d'un programme
     */
    public function show(Programme $programme)
    {
        $programme->load(['compagnie', 'vehicule', 'itineraire', 'chauffeur', 'convoyeur']);
        return view('home.programmes.show', compact('programme'));
    }

    /**
     * Obtenir les détails d'un véhicule avec les places réservées
     */
    public function getVehicleDetails($id, Request $request)
    {
        try {
            Log::info('=== DEBUT getVehicleDetails ===');
            Log::info('Paramètres:', [
                'vehicle_id' => $id,
                'date' => $request->get('date'),
                'url' => $request->fullUrl()
            ]);

            $vehicule = Vehicule::find($id);

            if (!$vehicule) {
                Log::warning('Véhicule non trouvé', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'error' => 'Véhicule non trouvé'
                ], 404);
            }

            Log::info('Véhicule trouvé:', [
                'id' => $vehicule->id,
                'marque' => $vehicule->marque,
                'modele' => $vehicule->modele,
                'type_range' => $vehicule->type_range,
                'nombre_place' => $vehicule->nombre_place
            ]);

            // Récupérer les places réservées si une date est fournie
            $reservedSeats = [];
            $dateVoyage = $request->get('date');

            if ($dateVoyage) {
                $formattedDate = date('Y-m-d', strtotime($dateVoyage));

                Log::info('Recherche programmes pour date', [
                    'date_voyage' => $dateVoyage,
                    'formatted_date' => $formattedDate
                ]);

                // Trouver tous les programmes associés à ce véhicule pour cette date
                $programmes = Programme::where('vehicule_id', $vehicule->id)
                    ->where(function ($query) use ($formattedDate) {
                        // Programmes ponctuels
                        $query->where(function ($q) use ($formattedDate) {
                            $q->where('type_programmation', 'ponctuel')
                                ->whereRaw('DATE(date_depart) = ?', [$formattedDate]);
                        })
                            // Programmes récurrents
                            ->orWhere(function ($q) use ($formattedDate) {
                            $joursFrancais = [
                                'monday' => 'lundi',
                                'tuesday' => 'mardi',
                                'wednesday' => 'mercredi',
                                'thursday' => 'jeudi',
                                'friday' => 'vendredi',
                                'saturday' => 'samedi',
                                'sunday' => 'dimanche'
                            ];
                            $jourAnglais = strtolower(date('l', strtotime($formattedDate)));
                            $jour_semaine = $joursFrancais[$jourAnglais] ?? $jourAnglais;

                            $q->where('type_programmation', 'recurrent')
                                ->whereRaw('DATE(date_depart) <= ?', [$formattedDate])
                                ->where(function ($dateCheck) use ($formattedDate) {
                                    $dateCheck->where(function ($subDate) use ($formattedDate) {
                                        $subDate->whereNotNull('date_fin_programmation')
                                            ->whereRaw('DATE(date_fin_programmation) >= ?', [$formattedDate]);
                                    })
                                        ->orWhereNull('date_fin_programmation');
                                })
                                ->where(function ($dayCheck) use ($jour_semaine) {
                                    $dayCheck->whereJsonContains('jours_recurrence', $jour_semaine)
                                        ->orWhere('jours_recurrence', 'like', "%{$jour_semaine}%");
                                });
                        });
                    })
                    ->get();

                Log::info('Programmes trouvés:', [
                    'count' => $programmes->count(),
                    'programmes' => $programmes->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'type' => $p->type_programmation,
                            'jours_recurrence' => $p->jours_recurrence
                        ];
                    })->toArray()
                ]);

                // Pour chaque programme, récupérer les places réservées
                foreach ($programmes as $programme) {
<<<<<<< HEAD
                    $programReservations = Reservation::where('programme_id', $programme->id)
                        ->where('statut', 'confirmee')
                        ->where(function ($query) use ($formattedDate) {
                            // Vérifier si la colonne date_voyage existe
                            $table = (new Reservation())->getTable();
                            $columns = Schema::getColumnListing($table);

                            if (in_array('date_voyage', $columns)) {
                                $query->where('date_voyage', $formattedDate);
                            } else {
                                $query->where('date_depart', $formattedDate);
                            }
                        })
=======
                    // Nouvelle structure: 1 réservation = 1 siège (seat_number)
                $programReservations = Reservation::where('programme_id', $programme->id)
                        ->where('statut', '!=', 'annulee')
                        ->where('date_voyage', $formattedDate)
>>>>>>> origin/Car225m
                        ->pluck('seat_number')
                        ->toArray();

                    Log::info('Réservations pour programme ' . $programme->id . ':', [
                        'count' => count($programReservations),
                        'places' => $programReservations
                    ]);

                    $reservedSeats = array_merge($reservedSeats, $programReservations);
                }

                $reservedSeats = array_unique($reservedSeats);
                Log::info('Places réservées totales:', [
                    'count' => count($reservedSeats),
                    'places' => $reservedSeats
                ]);
            }

            $response = [
                'success' => true,
                'vehicule' => $vehicule,
                'reservedSeats' => $reservedSeats,
                'date' => $dateVoyage
            ];

            Log::info('=== FIN getVehicleDetails ===', ['response' => $response]);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Erreur getVehicleDetails:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'vehicleId' => $id
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du chargement des données du véhicule: ' . $e->getMessage()
            ], 500);
        }
    }
}
