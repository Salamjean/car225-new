<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProgrammeTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get company data
        $compagnie = DB::table('compagnies')->where('id', 1)->first();
        
        if (!$compagnie) {
            $this->command->error('❌ Compagnie ID 1 introuvable');
            return;
        }

        // Get itineraries, vehicles, and drivers for company ID = 1
        $itineraires = DB::table('itineraires')->where('compagnie_id', 1)->get();
        $vehicules = DB::table('vehicules')->where('compagnie_id', 1)->get();
        $chauffeurs = DB::table('personnels')
            ->where('compagnie_id', 1)
            ->where('type_personnel', 'chauffeur')
            ->get();

        if ($itineraires->isEmpty()) {
            $this->command->error('❌ Aucun itinéraire trouvé pour la compagnie ID 1');
            return;
        }

        if ($vehicules->isEmpty()) {
            $this->command->error('❌ Aucun véhicule trouvé pour la compagnie ID 1');
            return;
        }

        if ($chauffeurs->isEmpty()) {
            $this->command->error('❌ Aucun chauffeur trouvé pour la compagnie ID 1');
            return;
        }

        $this->command->info("📊 Ressources disponibles:");
        $this->command->info("   - Itinéraires: {$itineraires->count()}");
        $this->command->info("   - Véhicules: {$vehicules->count()}");
        $this->command->info("   - Chauffeurs: {$chauffeurs->count()}");
        $this->command->info("");

        // Date range: today to end of February 2026
        $dateDebut = Carbon::today();
        $dateFin = Carbon::parse('2026-12-31');
        
        $programmes = [];
        $programmeCount = 0;

        // Create programmes for the next 30 days
        $currentDate = $dateDebut->copy();
        $daysToGenerate = 30;

        for ($day = 0; $day < $daysToGenerate; $day++) {
            // For each itinerary, create 2 programmes per day (morning and afternoon)
            foreach ($itineraires as $index => $itineraire) {
                // Morning programme (6:00 AM)
                $heureDepart = '06:00:00';
                $heureArrive = $this->calculateArrivalTime($heureDepart, $itineraire->durer_parcours);
                
                // Assign vehicle and driver (rotate through available resources)
                $vehicule = $vehicules[$programmeCount % $vehicules->count()];
                $chauffeur = $chauffeurs[$programmeCount % $chauffeurs->count()];

                $programmes[] = [
                    'compagnie_id' => 1,
                    'vehicule_id' => $vehicule->id,
                    'itineraire_id' => $itineraire->id,
                    'personnel_id' => $chauffeur->id,
                    'convoyeur_id' => null,
                    'point_depart' => $itineraire->point_depart,
                    'point_arrive' => $itineraire->point_arrive,
                    'durer_parcours' => $itineraire->durer_parcours,
                    'date_depart' => $currentDate->format('Y-m-d'),
                    'date_fin' => '2026-12-31', // As requested
                    'heure_depart' => $heureDepart,
                    'heure_arrive' => $heureArrive,
                    'montant_billet' => $this->calculateTicketPrice($itineraire->durer_parcours),
                    'statut' => 'actif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $programmeCount++;

                // Afternoon programme (2:00 PM)
                $heureDepart = '14:00:00';
                $heureArrive = $this->calculateArrivalTime($heureDepart, $itineraire->durer_parcours);
                
                $vehicule = $vehicules[$programmeCount % $vehicules->count()];
                $chauffeur = $chauffeurs[$programmeCount % $chauffeurs->count()];

                $programmes[] = [
                    'compagnie_id' => 1,
                    'vehicule_id' => $vehicule->id,
                    'itineraire_id' => $itineraire->id,
                    'personnel_id' => $chauffeur->id,
                    'convoyeur_id' => null,
                    'point_depart' => $itineraire->point_depart,
                    'point_arrive' => $itineraire->point_arrive,
                    'durer_parcours' => $itineraire->durer_parcours,
                    'date_depart' => $currentDate->format('Y-m-d'),
                    'date_fin' => '2026-12-31',
                    'heure_depart' => $heureDepart,
                    'heure_arrive' => $heureArrive,
                    'montant_billet' => $this->calculateTicketPrice($itineraire->durer_parcours),
                    'statut' => 'actif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $programmeCount++;
            }

            $currentDate->addDay();
        }

        // Insert in batches
        foreach (array_chunk($programmes, 50) as $batch) {
            DB::table('programmes')->insert($batch);
        }

        $this->command->info('');
        $this->command->info("🎉 {$programmeCount} programmes créés avec succès !");
        $this->command->info("📅 Du {$dateDebut->format('d/m/Y')} au {$currentDate->subDay()->format('d/m/Y')}");
        $this->command->info("🎫 Date de fin de validité: 31/12/2026");
    }

    /**
     * Calculate arrival time based on departure time and duration
     */
    private function calculateArrivalTime($heureDepart, $duree)
    {
        // Parse duration (format: "2 heures 12 minutes" or "02:30:00")
        $heures = 0;
        $minutes = 0;

        // Try text format first
        if (preg_match('/(\d+)\s*heure/', $duree, $matchesH)) {
            $heures = (int)$matchesH[1];
        }
        if (preg_match('/(\d+)\s*minute/', $duree, $matchesM)) {
            $minutes = (int)$matchesM[1];
        }

        // If no match, try time format (HH:MM:SS)
        if ($heures === 0 && $minutes === 0 && strpos($duree, ':') !== false) {
            $dureeParts = explode(':', $duree);
            $heures = (int)($dureeParts[0] ?? 0);
            $minutes = (int)($dureeParts[1] ?? 0);
        }

        $depart = Carbon::createFromFormat('H:i:s', $heureDepart);
        $arrive = $depart->copy()->addHours($heures)->addMinutes($minutes);

        return $arrive->format('H:i:s');
    }

    /**
     * Calculate ticket price based on duration
     * Basic formula: 1000 FCFA per hour + base price
     */
    private function calculateTicketPrice($duree)
    {
        // Parse duration (format: "2 heures 12 minutes" or "02:30:00")
        $heures = 0;
        $minutes = 0;

        // Try text format first
        if (preg_match('/(\d+)\s*heure/', $duree, $matchesH)) {
            $heures = (int)$matchesH[1];
        }
        if (preg_match('/(\d+)\s*minute/', $duree, $matchesM)) {
            $minutes = (int)$matchesM[1];
        }

        // If no match, try time format (HH:MM:SS)
        if ($heures === 0 && $minutes === 0 && strpos($duree, ':') !== false) {
            $dureeParts = explode(':', $duree);
            $heures = (int)($dureeParts[0] ?? 0);
            $minutes = (int)($dureeParts[1] ?? 0);
        }

        $totalHeures = $heures + ($minutes / 60);
        
        // Base price: 2000 FCFA + 1000 FCFA per hour
        return 2000 + ($totalHeures * 1000);
    }

}
