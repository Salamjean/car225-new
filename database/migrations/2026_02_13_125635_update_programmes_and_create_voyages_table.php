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
        Schema::table('programmes', function (Blueprint $table) {
            // Supprimer les colonnes chauffeur et véhicule de la table programmes
            $table->dropForeign(['vehicule_id']);
            $table->dropForeign(['personnel_id']);
            $table->dropColumn(['vehicule_id', 'personnel_id']);

            // Ajouter les gares de départ et d'arrivée
            $table->foreignId('gare_depart_id')->nullable()->constrained('gares')->onDelete('set null');
            $table->foreignId('gare_arrivee_id')->nullable()->constrained('gares')->onDelete('set null');
        });

        Schema::create('voyages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_id')->constrained('programmes')->onDelete('cascade');
            $table->date('date_voyage');
            $table->foreignId('vehicule_id')->nullable()->constrained('vehicules')->onDelete('set null');
            $table->foreignId('personnel_id')->nullable()->constrained('personnels')->onDelete('set null');
            $table->foreignId('gare_depart_id')->nullable()->constrained('gares')->onDelete('set null');
            $table->foreignId('gare_arrivee_id')->nullable()->constrained('gares')->onDelete('set null');
            $table->string('statut')->default('en_attente'); // en_attente, en_cours, termine, annule
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voyages');

        Schema::table('programmes', function (Blueprint $table) {
            $table->dropForeign(['gare_depart_id']);
            $table->dropForeign(['gare_arrivee_id']);
            $table->dropColumn(['gare_depart_id', 'gare_arrivee_id']);

            $table->foreignId('vehicule_id')->nullable()->constrained('vehicules')->onDelete('set null');
            $table->foreignId('personnel_id')->nullable()->constrained('personnels')->onDelete('set null');
        });
    }
};
