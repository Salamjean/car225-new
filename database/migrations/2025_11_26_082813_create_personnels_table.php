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
        Schema::create('personnels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compagnie_id')->nullable()->constrained('compagnies')->onDelete('set null');
            $table->foreignId('vehicule_id')->nullable()->constrained('vehicules')->onDelete('set null');
            $table->string('name');
            $table->string('type_personnel');
            $table->string('prenom');
            $table->string('email');
            $table->string('contact');
            $table->string('contact_urgence');
            $table->string('profile_image')->nullable();
            $table->enum('statut',['disponible','indisponible'])->default('disponible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnels');
    }
};
