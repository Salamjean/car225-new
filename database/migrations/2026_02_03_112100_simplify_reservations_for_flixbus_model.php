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
        // ⚠️ VERIFIE LE NOM DE LA TABLE ICI (l'erreur parlait de 'programmes')
        // Si c'est bien 'reservations', garde 'reservations'. 
        // Si c'est 'programmes', change-le.
        $tableName = 'programmes'; 

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            
            // 1. Gérer 'is_aller_retour'
            if (Schema::hasColumn($tableName, 'is_aller_retour')) {
                $table->dropColumn('is_aller_retour');
            }

            // 2. Gérer 'programme_retour_id' avec sa Clé Étrangère
            if (Schema::hasColumn($tableName, 'programme_retour_id')) {
                // ÉTAPE CRUCIALE : Supprimer la contrainte de clé étrangère d'abord !
                // On met ça dans un try/catch ou on utilise une syntaxe tableau pour que Laravel 
                // devine le nom (ex: programmes_programme_retour_id_foreign)
                
                // Méthode sécurisée : on essaie de drop la foreign key si elle existe
                try {
                    $table->dropForeign(['programme_retour_id']); 
                } catch (\Exception $e) {
                    // La contrainte n'existait peut-être pas, on continue
                }

                // Ensuite, on peut supprimer la colonne
                $table->dropColumn('programme_retour_id');
            }

            // 3. Les autres colonnes
            if (Schema::hasColumn($tableName, 'date_retour')) {
                $table->dropColumn('date_retour');
            }
            if (Schema::hasColumn($tableName, 'statut_aller')) {
                $table->dropColumn('statut_aller');
            }
            if (Schema::hasColumn($tableName, 'statut_retour')) {
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
