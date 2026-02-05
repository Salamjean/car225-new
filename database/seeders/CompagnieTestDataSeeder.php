<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompagnieTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertion de 3 chauffeurs
        $chauffeurs = [
            [
                'compagnie_id' => 1,
                'vehicule_id' => null, // Sera assigné après création des véhicules
                'name' => 'Kouame',
                'prenom' => 'Jean',
                'type_personnel' => 'chauffeur',
                'email' => 'jean.kouame@car225.ci',
                'contact' => '+225 0701234567',
                'contact_urgence' => '+225 0709876543',
                'statut' => 'disponible',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'compagnie_id' => 1,
                'vehicule_id' => null,
                'name' => 'Traore',
                'prenom' => 'Mamadou',
                'type_personnel' => 'chauffeur',
                'email' => 'mamadou.traore@car225.ci',
                'contact' => '+225 0702345678',
                'contact_urgence' => '+225 0708765432',
                'statut' => 'disponible',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'compagnie_id' => 1,
                'vehicule_id' => null,
                'name' => 'Koffi',
                'prenom' => 'Ange',
                'type_personnel' => 'chauffeur',
                'email' => 'ange.koffi@car225.ci',
                'contact' => '+225 0703456789',
                'contact_urgence' => '+225 0707654321',
                'statut' => 'disponible',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('personnels')->insert($chauffeurs);
        $this->command->info('✅ 3 chauffeurs créés avec succès');

        // Insertion de 2 véhicules
        $vehicules = [
            [
                'compagnie_id' => 1,
                'marque' => 'Toyota',
                'modele' => 'Hiace',
                'immatriculation' => 'AB-1234-CI',
                'numero_serie' => 'VH2024001',
                'type_range' => '2x2',
                'nombre_place' => 14,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'compagnie_id' => 1,
                'marque' => 'Mercedes',
                'modele' => 'Sprinter',
                'immatriculation' => 'CD-5678-CI',
                'numero_serie' => 'VH2024002',
                'type_range' => '2x3',
                'nombre_place' => 21,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('vehicules')->insert($vehicules);
        $this->command->info('✅ 2 véhicules créés avec succès');

        // Insertion de 1 agent
        $agent = [
            'compagnie_id' => 1,
            'name' => 'Yao',
            'prenom' => 'Patrick',
            'email' => 'patrick.yao@car225.ci',
            'contact' => '+225 0704567890',
            'cas_urgence' => '+225 0706543210',
            'commune' => 'Yopougon',
            'password' => Hash::make('password123'), // Mot de passe par défaut
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('agents')->insert($agent);
        $this->command->info('✅ 1 agent créé avec succès');
        
        $this->command->info('');
        $this->command->info('🎉 Toutes les données de test ont été créées !');
        $this->command->info('');
        $this->command->info('📋 Résumé :');
        $this->command->info('   - 3 chauffeurs (Jean Kouame, Mamadou Traore, Ange Koffi)');
        $this->command->info('   - 2 véhicules (AB-1234-CI, CD-5678-CI)');
        $this->command->info('   - 1 agent (Patrick Yao - Email: patrick.yao@car225.ci, Password: password123)');
    }
}
