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
        Schema::create('programme_historiques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_id')->constrained('programmes')->onDelete('cascade');
            $table->string('action');
            $table->string('vehicule')->nullable();
            $table->string('itineraire')->nullable();
            $table->string('chauffeur')->nullable();
            $table->string('convoyeur')->nullable();
            $table->string('point_depart');
            $table->string('point_arrive');
            $table->string('duree_parcours');
            $table->string('date_depart');
            $table->string('heure_depart');
            $table->string('heure_arrivee');
            $table->string('sieges_occupes');
            $table->string('statut_places');
            $table->string('pourcentage_occupation');
            
            $table->text('raison')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programme_historiques');
    }
};
