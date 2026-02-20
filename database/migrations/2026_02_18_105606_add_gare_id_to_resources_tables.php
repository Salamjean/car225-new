<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajouter gare_id à la table personnels
        Schema::table('personnels', function (Blueprint $table) {
            $table->foreignId('gare_id')->nullable()->after('compagnie_id')->constrained('gares')->nullOnDelete();
        });

        // Ajouter gare_id à la table vehicules
        Schema::table('vehicules', function (Blueprint $table) {
            $table->foreignId('gare_id')->nullable()->after('compagnie_id')->constrained('gares')->nullOnDelete();
        });

        // Ajouter gare_id à la table caisses
        Schema::table('caisses', function (Blueprint $table) {
            $table->foreignId('gare_id')->nullable()->after('compagnie_id')->constrained('gares')->nullOnDelete();
        });

        // Ajouter gare_id à la table itineraires
        Schema::table('itineraires', function (Blueprint $table) {
            $table->foreignId('gare_id')->nullable()->after('compagnie_id')->constrained('gares')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->dropForeign(['gare_id']);
            $table->dropColumn('gare_id');
        });

        Schema::table('vehicules', function (Blueprint $table) {
            $table->dropForeign(['gare_id']);
            $table->dropColumn('gare_id');
        });

        Schema::table('caisses', function (Blueprint $table) {
            $table->dropForeign(['gare_id']);
            $table->dropColumn('gare_id');
        });

        Schema::table('itineraires', function (Blueprint $table) {
            $table->dropForeign(['gare_id']);
            $table->dropColumn('gare_id');
        });
    }
};
