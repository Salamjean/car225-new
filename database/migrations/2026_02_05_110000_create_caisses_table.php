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
        Schema::create('caisses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('prenom');
            $table->string('email', 191)->unique();
            $table->string('contact');
            $table->string('cas_urgence')->nullable();
            $table->string('commune')->nullable();
            $table->string('password');
            $table->string('profile_picture')->nullable();
            $table->foreignId('compagnie_id')->constrained('compagnies')->onDelete('cascade');
            $table->timestamp('archived_at')->nullable();
            $table->integer('tickets')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caisses');
    }
};
