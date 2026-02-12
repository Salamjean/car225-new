<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecificVehiculesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sbta = DB::table('compagnies')->where('name', 'SBTA')->first();
        $utb = DB::table('compagnies')->where('name', 'UTB')->first();
        $aht = DB::table('compagnies')->where('name', 'AHT')->first();

        if (!$sbta || !$utb || !$aht) {
            return;
        }

        $vehicules = [
            // SBTA
            [
                'modele' => 'Toyota Coaster',
                'immatriculation' => 'SBTA-001-CI',
                'type_range' => 'Gamme Standard',
                'nombre_place' => '70',
                'compagnie_id' => $sbta->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modele' => 'Toyota Coaster',
                'immatriculation' => 'SBTA-002-CI',
                'type_range' => 'Gamme Standard',
                'nombre_place' => '70',
                'compagnie_id' => $sbta->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modele' => 'Toyota Coaster',
                'immatriculation' => 'SBTA-003-CI',
                'type_range' => 'Gamme Standard',
                'nombre_place' => '70',
                'compagnie_id' => $sbta->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modele' => 'Toyota Coaster',
                'immatriculation' => 'SBTA-004-CI',
                'type_range' => 'Gamme Standard',
                'nombre_place' => '70',
                'compagnie_id' => $sbta->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modele' => 'Toyota Coaster',
                'immatriculation' => 'SBTA-005-CI',
                'type_range' => 'Gamme Standard',
                'nombre_place' => '70',
                'compagnie_id' => $sbta->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modele' => 'Toyota Coaster',
                'immatriculation' => 'SBTA-006-CI',
                'type_range' => 'Gamme Standard',
                'nombre_place' => '70',
                'compagnie_id' => $sbta->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // UTB
            [
                'modele' => 'Mercedes Benz Bus',
                'immatriculation' => 'UTB-101-CI',
                'type_range' => 'Gamme Prestige',
                'nombre_place' => '70',
                'compagnie_id' => $utb->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modele' => 'Mercedes Benz Bus',
                'immatriculation' => 'UTB-102-CI',
                'type_range' => 'Gamme Prestige',
                'nombre_place' => '70',
                'compagnie_id' => $utb->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modele' => 'Mercedes Benz Bus',
                'immatriculation' => 'UTB-103-CI',
                'type_range' => 'Gamme Prestige',
                'nombre_place' => '70',
                'compagnie_id' => $utb->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modele' => 'Mercedes Benz Bus',
                'immatriculation' => 'UTB-104-CI',
                'type_range' => 'Gamme Prestige',
                'nombre_place' => '70',
                'compagnie_id' => $utb->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modele' => 'Mercedes Benz Bus',
                'immatriculation' => 'UTB-105-CI',
                'type_range' => 'Gamme Prestige',
                'nombre_place' => '70',
                'compagnie_id' => $utb->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modele' => 'Mercedes Benz Bus',
                'immatriculation' => 'UTB-106-CI',
                'type_range' => 'Gamme Prestige',
                'nombre_place' => '70',
                'compagnie_id' => $utb->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Compagnie ID 3
            [
                'modele' => 'Iveco Bus',
                'immatriculation' => 'C3-V01-CI',
                'type_range' => 'Gamme Standard',
                'nombre_place' => '70',
                'compagnie_id' => $aht->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modele' => 'Iveco Bus',
                'immatriculation' => 'C3-V02-CI',
                'type_range' => 'Gamme Standard',
                'nombre_place' => '70',
                'compagnie_id' => $aht->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modele' => 'Iveco Bus',
                'immatriculation' => 'C3-V03-CI',
                'type_range' => 'Gamme Standard',
                'nombre_place' => '70',
                'compagnie_id' => $aht->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modele' => 'Iveco Bus',
                'immatriculation' => 'C3-V04-CI',
                'type_range' => 'Gamme Standard',
                'nombre_place' => '70',
                'compagnie_id' => $aht->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modele' => 'Iveco Bus',
                'immatriculation' => 'C3-V05-CI',
                'type_range' => 'Gamme Standard',
                'nombre_place' => '70',
                'compagnie_id' => $aht->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modele' => 'Iveco Bus',
                'immatriculation' => 'C3-V06-CI',
                'type_range' => 'Gamme Standard',
                'nombre_place' => '70',
                'compagnie_id' => $aht->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('vehicules')->insert($vehicules);
    }
}
