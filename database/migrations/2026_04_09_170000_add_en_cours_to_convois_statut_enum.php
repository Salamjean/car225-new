<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE convois MODIFY statut ENUM('en_attente', 'valide', 'en_cours', 'annule', 'termine') NOT NULL DEFAULT 'en_attente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE convois MODIFY statut ENUM('en_attente', 'valide', 'annule', 'termine') NOT NULL DEFAULT 'en_attente'");
    }
};

