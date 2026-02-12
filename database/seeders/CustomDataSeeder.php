<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $compagnies = DB::table('compagnies')->get();
        
        foreach ($compagnies as $compagnie) {
            $prefix = strtoupper($compagnie->name);
            
            // Create 6 vehicles for each company
            $vehicules = [];
            for ($i = 1; $i <= 6; $i++) {
                $vehicules[] = [
                    'modele' => ($prefix == 'UTB' ? 'Mercedes Benz Bus' : 'Toyota Coaster'),
                    'immatriculation' => $prefix . '-' . str_pad($i + 10, 3, '0', STR_PAD_LEFT) . '-CI',
                    'type_range' => ($prefix == 'UTB' ? 'Gamme Prestige' : 'Gamme Standard'),
                    'nombre_place' => '70',
                    'compagnie_id' => $compagnie->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('vehicules')->insert($vehicules);

            // Create 6 chauffeurs for each company
            $personnels = [];
            for ($i = 1; $i <= 6; $i++) {
                $personnels[] = [
                    'compagnie_id' => $compagnie->id,
                    'name' => 'CHAUFFEUR',
                    'prenom' => $prefix . ' ' . $i,
                    'type_personnel' => 'Chauffeur',
                    'email' => strtolower('chauffeur.' . $compagnie->name . '.' . $i . '@' . $compagnie->name . '.ci'),
                    'contact' => ($compagnie->id == 1 ? '0101010' : '0202020') . $i . '0',
                    'contact_urgence' => ($compagnie->id == 1 ? '0707070' : '0808080') . $i . '0',
                    'statut' => 'disponible',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('personnels')->insert($personnels);
        }
    }
}
