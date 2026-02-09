<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * * Cette migration simplifie la table programmes pour adopter le modèle FlixBus.
     */
    public function up(): void
    {
        // 1. Ajout des nouvelles colonnes
        Schema::table('programmes', function (Blueprint $table) {
            // Ajouter colonne statut pour gérer l'état du voyage
            if (!Schema::hasColumn('programmes', 'statut')) {
                $table->enum('statut', ['actif', 'annule', 'complet'])->default('actif')->after('heure_arrive');
            }
            // Note: Ton modèle utilise 'date_fin', assurons-nous qu'elle existe si tu en as besoin
            if (!Schema::hasColumn('programmes', 'date_fin')) {
                $table->date('date_fin')->nullable()->after('date_depart');
            }
        });
        
        // 2. Suppression des colonnes obsolètes avec gestion des Clés Étrangères
        Schema::table('programmes', function (Blueprint $table) {
            
            // --- CORRECTION DU BUG ICI ---
            // On gère la suppression de programme_retour_id et de sa contrainte
            if (Schema::hasColumn('programmes', 'programme_retour_id')) {
                try {
                    // On tente de supprimer la contrainte de clé étrangère d'abord
                    // Syntaxe tableau = Laravel devine le nom 'programmes_programme_retour_id_foreign'
                    $table->dropForeign(['programme_retour_id']);
                } catch (\Exception $e) {
                    // Si la contrainte n'existe pas (ex: en local), on continue silencieusement
                }
                
                // Maintenant on peut supprimer la colonne sans erreur 1828
                $table->dropColumn('programme_retour_id');
            }
            // -----------------------------

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
            
            // Supprimer les colonnes de comptage
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
            // Supprimer les nouvelles colonnes
            if (Schema::hasColumn('programmes', 'statut')) {
                $table->dropColumn('statut');
            }
            if (Schema::hasColumn('programmes', 'date_fin')) {
                $table->dropColumn('date_fin');
            }
            
            // Restaurer les colonnes supprimées (structure de base, sans les contraintes strictes pour éviter les erreurs)
            if (!Schema::hasColumn('programmes', 'type_programmation')) {
                $table->enum('type_programmation', ['ponctuel', 'recurrent'])->default('ponctuel');
            }
            if (!Schema::hasColumn('programmes', 'programme_retour_id')) {
                $table->unsignedBigInteger('programme_retour_id')->nullable();
            }
            if (!Schema::hasColumn('programmes', 'is_aller_retour')) {
                $table->boolean('is_aller_retour')->default(false);
            }
        });
    }
};