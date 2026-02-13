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
        Schema::create('compagnies', function (Blueprint $table) {
            $table->id();
            $table->string('commune');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('path_logo')->nullable();
            $table->string('slogan')->nullable();
            $table->string('sigle')->nullable();
            $table->string('adresse');
            $table->string('contact');
            $table->string('prefix');
            $table->enum('statut',['actif','desactive'])->default('actif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compagnies');
    }
};
