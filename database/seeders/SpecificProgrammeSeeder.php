<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SpecificProgrammeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Compagnie 1: UTB
        $utb_itineraires = DB::table('itineraires')->where('compagnie_id', 1)->get();
        $utb_vehicules = DB::table('vehicules')->where('compagnie_id', 1)->pluck('id')->toArray();
        $utb_personnels = DB::table('personnels')->where('compagnie_id', 1)->pluck('id')->toArray();
        
        // Compagnie 2: SBTA
        $sbta_itineraires = DB::table('itineraires')->where('compagnie_id', 2)->get();
        $sbta_vehicules = DB::table('vehicules')->where('compagnie_id', 2)->pluck('id')->toArray();
        $sbta_personnels = DB::table('personnels')->where('compagnie_id', 2)->pluck('id')->toArray();

        $now = Carbon::now();
        $date_depart = "2026-02-10 00:00:00";
        $date_fin = "2031-02-10";

        $programmes = [];

        // Add 3 programs for UTB on itinerary 3 (Abidjan -> Agboville) if it exists and has no programs
        $itin3 = $utb_itineraires->where('id', 3)->first();
        if ($itin3) {
            $schedules = [
                ['07:00', '08:25'],
                ['12:00', '13:25'],
                ['16:00', '17:25']
            ];
            foreach ($schedules as $index => $times) {
                // Check for duplicates
                $exists = DB::table('programmes')
                    ->where('compagnie_id', 1)
                    ->where('itineraire_id', 3)
                    ->where('heure_depart', $times[0])
                    ->exists();

                if (!$exists) {
                    $programmes[] = [
                        'compagnie_id' => 1,
                        'vehicule_id' => $utb_vehicules[$index % count($utb_vehicules)],
                        'itineraire_id' => 3,
                        'personnel_id' => $utb_personnels[$index % count($utb_personnels)],
                        'convoyeur_id' => null,
                        'montant_billet' => '3000',
                        'point_depart' => $itin3->point_depart,
                        'point_arrive' => $itin3->point_arrive,
                        'durer_parcours' => $itin3->durer_parcours,
                        'date_depart' => $date_depart,
                        'date_fin' => $date_fin,
                        'heure_depart' => $times[0],
                        'heure_arrive' => $times[1],
                        'statut' => 'actif',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        // Add 3 programs for SBTA (one for each of its itineraries 4, 5, 6)
        foreach ($sbta_itineraires as $index => $itin) {
            if ($index >= 3) break; // Only 3 programs

            // Calculate arrival based on duration
            $heure_depart = ($index == 0 ? '08:00' : ($index == 1 ? '10:00' : '22:00'));
            
            // Simple arrival calculation (manual for simplicity based on itinerary duration)
            $heure_arrive = '';
            if ($itin->id == 4) $heure_arrive = '15:45'; // 7h 45m after 08:00
            if ($itin->id == 5) $heure_arrive = '14:57'; // 4h 57m after 10:00
            if ($itin->id == 6) $heure_arrive = '05:20'; // 7h 20m after 22:00

            $exists = DB::table('programmes')
                ->where('compagnie_id', 2)
                ->where('itineraire_id', $itin->id)
                ->where('heure_depart', $heure_depart)
                ->exists();

            if (!$exists) {
                $programmes[] = [
                    'compagnie_id' => 2,
                    'vehicule_id' => $sbta_vehicules[$index % count($sbta_vehicules)],
                    'itineraire_id' => $itin->id,
                    'personnel_id' => $sbta_personnels[$index % count($sbta_personnels)],
                    'convoyeur_id' => null,
                    'montant_billet' => '5000',
                    'point_depart' => $itin->point_depart,
                    'point_arrive' => $itin->point_arrive,
                    'durer_parcours' => $itin->durer_parcours,
                    'date_depart' => $date_depart,
                    'date_fin' => $date_fin,
                    'heure_depart' => $heure_depart,
                    'heure_arrive' => $heure_arrive,
                    'statut' => 'actif',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (count($programmes) > 0) {
            DB::table('programmes')->insert($programmes);
        }
    }
}
