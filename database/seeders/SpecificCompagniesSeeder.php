<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SpecificCompagniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $compagnies = [
            [
                'name' => 'SBTA',
                'email' => 'contact@sbta.ci',
                'password' => Hash::make('password'),
                'contact' => '0101010101',
                'slogan' => 'Voyagez en toute sécurité',
                'commune' => 'Adjamé',
                'adresse' => 'Gare Nord',
                'sigle' => 'SBTA',
                'prefix' => 'SBTA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'UTB',
                'email' => 'info@utb.ci',
                'password' => Hash::make('password'),
                'contact' => '0202020202',
                'slogan' => 'Le confort et la rapidité',
                'commune' => 'Yopougon',
                'adresse' => 'Gare Principale',
                'sigle' => 'UTB',
                'prefix' => 'UTB',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AHT',
                'email' => 'contact@aht.ci',
                'password' => Hash::make('password'),
                'contact' => '0303030303',
                'slogan' => 'Le transport à votre portée',
                'commune' => 'Abobo',
                'adresse' => 'Gare Routière',
                'sigle' => 'AHT',
                'prefix' => 'AHT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('compagnies')->insert($compagnies);
    }
}

