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
        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();
            $table->string('marque');
            $table->string('modele')->nullable();
            $table->string('immatriculation');
            $table->string('numero_serie')->nullable();
            $table->string('type_range');
            $table->string('nombre_place');
            $table->boolean('is_active')->default(true);
            $table->string('motif')->nullable();
            $table->foreignId('compagnie_id')->nullable()->constrained('compagnies')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicules');
    }
};
