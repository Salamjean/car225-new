<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds fields for aller-retour (round-trip) functionality
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Date de retour pour les voyages aller-retour (nullable pour programmes ponctuels - même jour)
            $table->date('date_retour')->nullable()->after('date_voyage');
            
            // QR Code pour le voyage retour
            $table->text('qr_code_retour')->nullable()->after('qr_code_data');
            $table->string('qr_code_retour_path')->nullable()->after('qr_code_retour');
            $table->json('qr_code_retour_data')->nullable()->after('qr_code_retour_path');
            
            // Statuts séparés pour aller et retour
            $table->enum('statut_aller', ['en_attente','confirmee', 'terminee', 'annulee'])->default('confirmee')->after('statut');
            $table->enum('statut_retour', ['en_attente','confirmee', 'terminee', 'annulee'])->nullable()->after('statut_aller');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'date_retour',
                'qr_code_retour',
                'qr_code_retour_path',
                'qr_code_retour_data',
                'statut_aller',
                'statut_retour'
            ]);
        });
    }
};