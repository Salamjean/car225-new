<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute la prise en charge de la connexion « Sign in with Apple ».
 *
 * - `apple_id` : le « sub » du JWT Apple (identifiant unique pérenne d'un
 *   compte Apple — ne change jamais, même si l'email change).
 * - Index unique nullable pour permettre la recherche rapide.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'apple_id')) {
                $table->string('apple_id')->nullable()->unique()->after('google_refresh_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'apple_id')) {
                $table->dropUnique(['apple_id']);
                $table->dropColumn('apple_id');
            }
        });
    }
};
