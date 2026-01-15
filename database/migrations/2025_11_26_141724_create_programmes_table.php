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
        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compagnie_id')->nullable()->constrained('compagnies')->onDelete('set null');
            $table->foreignId('vehicule_id')->nullable()->constrained('vehicules')->onDelete('set null');
            $table->foreignId('itineraire_id')->nullable()->constrained('itineraires')->onDelete('set null');
            $table->foreignId('personnel_id')->nullable()->constrained('personnels')->onDelete('set null');
            $table->foreignId('convoyeur_id')->nullable()->constrained('personnels')->onDelete('set null');
            $table->string('montant_billet');
            $table->string('point_depart');
            $table->string('point_arrive');
            $table->string('durer_parcours');
            $table->string('date_depart'); 
            $table->string('date_fin_programmation')->nullable(); 
            $table->string('heure_depart');
            $table->string('heure_arrive');
            $table->text('nbre_siege_occupe')->nullable();
            $table->enum('staut_place', ['vide', 'presque_complet', 'rempli'])->nullable();
            $table->enum('type_programmation', ['ponctuel', 'recurrent'])->default('ponctuel'); 
            $table->json('jours_recurrence')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programmes');
    }
};
