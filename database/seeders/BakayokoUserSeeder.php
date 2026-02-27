<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BakayokoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'moussa.10@live.fr'],
            [
                'name' => 'Bakayoko',
                'prenom' => 'Moussa',
                // Le code_id est généré automatiquement par le trait HasCodeId du Model
                'solde' => 200000.00,
                'contact' => '0200154588',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );
    }
}
