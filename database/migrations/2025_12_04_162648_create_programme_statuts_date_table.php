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
        Schema::create('programme_statuts_date', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_id')->constrained('programmes')->onDelete('cascade');
            $table->date('date_voyage');
            $table->integer('nbre_siege_occupe')->default(0);
            $table->enum('staut_place', ['vide', 'presque_complet', 'rempli'])->default('vide');
            $table->timestamps();

            // Clé unique pour éviter les doublons
            $table->unique(['programme_id', 'date_voyage']);

            // Index pour les performances
            $table->index(['programme_id', 'date_voyage']);
            $table->index('date_voyage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programme_statuts_date');
    }
};
