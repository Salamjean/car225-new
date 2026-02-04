<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Simplifie la table reservations pour le modèle FlixBus:
     * - Suppression de is_aller_retour (chaque réservation = 1 voyage)
     * - Suppression de programme_retour_id (l'aller-retour = 2 réservations séparées)
     * - Suppression de date_retour, statut_aller, statut_retour
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Supprimer les colonnes aller-retour
            if (Schema::hasColumn('reservations', 'is_aller_retour')) {
                $table->dropColumn('is_aller_retour');
            }
            if (Schema::hasColumn('reservations', 'programme_retour_id')) {
                $table->dropColumn('programme_retour_id');
            }
            if (Schema::hasColumn('reservations', 'date_retour')) {
                $table->dropColumn('date_retour');
            }
            if (Schema::hasColumn('reservations', 'statut_aller')) {
                $table->dropColumn('statut_aller');
            }
            if (Schema::hasColumn('reservations', 'statut_retour')) {
                $table->dropColumn('statut_retour');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->boolean('is_aller_retour')->default(false);
            $table->unsignedBigInteger('programme_retour_id')->nullable();
            $table->date('date_retour')->nullable();
            $table->string('statut_aller')->nullable();
            $table->string('statut_retour')->nullable();
        });
    }
};
