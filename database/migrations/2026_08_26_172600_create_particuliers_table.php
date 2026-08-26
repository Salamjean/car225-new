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
        Schema::create('particuliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('contact');
            $table->unsignedInteger('nombre_place_car');
            $table->date('date_mise_service');
            $table->string('photo_proprietaire')->nullable();
            $table->string('photo_complete_car')->nullable();
            $table->string('photo_avant_car')->nullable();
            $table->string('photo_arriere_car')->nullable();
            $table->string('immatriculation');
            $table->string('carte_grise')->nullable();
            $table->string('visite_technique')->nullable();
            $table->enum('statut', ['en_attente', 'valide', 'rejete'])->default('en_attente');
            $table->string('code_id')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('fcm_token')->nullable();
            $table->decimal('solde_convoie', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('particuliers');
    }
};
