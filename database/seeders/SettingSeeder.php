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

        \App\Models\Setting::updateOrCreate(
            ['key' => 'maintenance_mode'],
            [
                'value' => '0',
                'type' => 'boolean',
                'label' => 'Activer le mode maintenance (bloque l\'accès au site pour tous sauf l\'admin)'
            ]
        );

        \App\Models\Setting::updateOrCreate(
            ['key' => 'maintenance_message'],
            [
                'value' => 'Notre plateforme est actuellement en maintenance. Nous serons de retour très bientôt.',
                'type' => 'string',
                'label' => 'Message affiché aux visiteurs pendant la maintenance'
            ]
        );
    }
}
