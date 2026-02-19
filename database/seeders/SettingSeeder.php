<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Setting::updateOrCreate(
            ['key' => 'is_ticket_system_enabled'],
            [
                'value' => '1',
                'type' => 'boolean',
                'label' => 'Activer le système de tickets/crédits'
            ]
        );
    }
}
