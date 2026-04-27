<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table des agents ONPC (Office National de la Protection Civile).
 *
 * Les agents sont créés depuis l'interface admin et reçoivent un OTP par
 * email pour définir leur mot de passe. Une fois connectés, ils ont une
 * vue lecture seule sur l'ensemble des sapeurs-pompiers, signalements,
 * bilans et passagers évacués (rôle de superviseur national).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('onpcs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('contact')->nullable();
            $table->string('localisation')->nullable();      // Adresse / localisation libre
            $table->string('photo_path')->nullable();        // Photo de profil
            $table->enum('statut', ['actif', 'desactive'])->default('actif');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onpcs');
    }
};
