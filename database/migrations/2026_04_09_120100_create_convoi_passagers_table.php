<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('convoi_passagers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('convoi_id')->constrained('convois')->onDelete('cascade');
            $table->string('nom');
            $table->string('prenoms');
            $table->string('contact');
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('convoi_passagers');
    }
};

