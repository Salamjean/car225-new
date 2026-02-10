<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecificPersonnelSeeder extends Seeder
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

        $personnels = [];

        // Drivers for SBTA
        for ($i = 1; $i <= 6; $i++) {
            $personnels[] = [
                'compagnie_id' => $sbta->id,
                'name' => 'CHAUFFEUR',
                'prenom' => 'SBTA ' . $i,
                'type_personnel' => 'Chauffeur',
                'email' => 'chauffeur.sbta' . $i . '@sbta.ci',
                'contact' => '01010101' . $i,
                'contact_urgence' => '070707070' . $i,
                'statut' => 'disponible',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Drivers for UTB
        for ($i = 1; $i <= 6; $i++) {
            $personnels[] = [
                'compagnie_id' => $utb->id,
                'name' => 'CHAUFFEUR',
                'prenom' => 'UTB ' . $i,
                'type_personnel' => 'Chauffeur',
                'email' => 'chauffeur.utb' . $i . '@utb.ci',
                'contact' => '02020202' . $i,
                'contact_urgence' => '080808080' . $i,
                'statut' => 'disponible',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Drivers for AHT
        for ($i = 1; $i <= 6; $i++) {
            $personnels[] = [
                'compagnie_id' => $aht->id,
                'name' => 'CHAUFFEUR',
                'prenom' => 'AHT DRIVER ' . $i,
                'type_personnel' => 'Chauffeur',
                'email' => 'chauffeur.aht.' . $i . '@aht.ci',
                'contact' => '03030303' . $i,
                'contact_urgence' => '090909090' . $i,
                'statut' => 'disponible',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('personnels')->insert($personnels);
    }
}
