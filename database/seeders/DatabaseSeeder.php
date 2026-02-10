<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test',
            'prenom' => 'User',
            'contact' => '0102030405',
            'email' => 'test@example.com',
        ]);

        $this->call([
            SpecificCompagniesSeeder::class,
            SpecificVehiculesSeeder::class,
            SpecificPersonnelSeeder::class,
            AdminSeeder::class,
        ]);
    }
}
