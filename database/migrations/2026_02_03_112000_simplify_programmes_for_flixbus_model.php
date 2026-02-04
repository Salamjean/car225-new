<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Cette migration simplifie la table programmes pour adopter le modèle FlixBus:
     * - Chaque programme = 1 voyage à une date précise
     * - Suppression de la logique de récurrence
     * - Suppression de la logique aller-retour dans programmes
     */
    public function up(): void
    {
        Schema::table('programmes', function (Blueprint $table) {
            // Ajouter colonne statut pour gérer l'état du voyage
            $table->enum('statut', ['actif', 'annule', 'complet'])->default('actif')->after('heure_arrive');
        });
        
        // Suppression des colonnes devenues obsolètes
        Schema::table('programmes', function (Blueprint $table) {
            // Supprimer les colonnes de récurrence
            if (Schema::hasColumn('programmes', 'type_programmation')) {
                $table->dropColumn('type_programmation');
            }
            if (Schema::hasColumn('programmes', 'jours_recurrence')) {
                $table->dropColumn('jours_recurrence');
            }
            if (Schema::hasColumn('programmes', 'date_fin_programmation')) {
                $table->dropColumn('date_fin_programmation');
            }
            
            // Supprimer les colonnes aller-retour
            if (Schema::hasColumn('programmes', 'is_aller_retour')) {
                $table->dropColumn('is_aller_retour');
            }
            if (Schema::hasColumn('programmes', 'programme_retour_id')) {
                $table->dropColumn('programme_retour_id');
            }
            
            // Supprimer les colonnes de comptage (sera calculé dynamiquement)
            if (Schema::hasColumn('programmes', 'nbre_siege_occupe')) {
                $table->dropColumn('nbre_siege_occupe');
            }
            if (Schema::hasColumn('programmes', 'staut_place')) {
                $table->dropColumn('staut_place');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programmes', function (Blueprint $table) {
            // Supprimer la nouvelle colonne statut
            if (Schema::hasColumn('programmes', 'statut')) {
                $table->dropColumn('statut');
            }
            
            // Restaurer les colonnes supprimées
            $table->enum('type_programmation', ['ponctuel', 'recurrent'])->default('ponctuel');
            $table->json('jours_recurrence')->nullable();
            $table->string('date_fin_programmation')->nullable();
            $table->boolean('is_aller_retour')->default(false);
            $table->unsignedBigInteger('programme_retour_id')->nullable();
            $table->text('nbre_siege_occupe')->nullable();
            $table->enum('staut_place', ['vide', 'presque_complet', 'rempli'])->nullable();
        });
    }
};
