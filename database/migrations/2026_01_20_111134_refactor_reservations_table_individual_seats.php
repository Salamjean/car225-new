<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Restructure: 1 reservation = 1 seat = 1 passenger
     * Instead of JSON arrays for multiple passengers
     */
    public function up(): void
    {
        // 1. Vider la table (repartir de 0)
        DB::table('reservations')->truncate();

        Schema::table('reservations', function (Blueprint $table) {
            // Supprimer les colonnes JSON
            $table->dropColumn(['places_reservees', 'nombre_places', 'places', 'passagers']);
        });

        Schema::table('reservations', function (Blueprint $table) {
            // Ajouter les nouvelles colonnes pour un passager individuel
            $table->integer('seat_number')->after('programme_id')->comment('Numéro de la place');
            $table->string('passager_nom')->after('seat_number');
            $table->string('passager_prenom')->after('passager_nom');
            $table->string('passager_email')->nullable()->after('passager_prenom');
            $table->string('passager_telephone')->nullable()->after('passager_email');
            $table->string('passager_urgence')->nullable()->after('passager_telephone')->comment('Contact urgence');
            
            // Renommer montant_total en montant (prix par place)
            $table->renameColumn('montant_total', 'montant');
        });

        // Modifier l'enum statut pour ajouter 'terminee'
        DB::statement("ALTER TABLE reservations MODIFY COLUMN statut ENUM('en_attente', 'confirmee', 'terminee', 'annulee') DEFAULT 'en_attente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Supprimer les nouvelles colonnes
            $table->dropColumn([
                'seat_number',
                'passager_nom',
                'passager_prenom',
                'passager_email',
                'passager_telephone',
                'passager_urgence'
            ]);
            
            // Restaurer les anciennes colonnes
            $table->json('places_reservees')->after('programme_id');
            $table->integer('nombre_places')->after('places_reservees');
            $table->json('places')->nullable()->after('nombre_places');
            $table->json('passagers')->nullable()->after('places');
            
            // Renommer montant en montant_total
            $table->renameColumn('montant', 'montant_total');
        });

        // Restaurer l'ancien enum
        DB::statement("ALTER TABLE reservations MODIFY COLUMN statut ENUM('en_attente', 'confirmee', 'annulee') DEFAULT 'en_attente'");
    }
};
