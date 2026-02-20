<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voyage_id');
            $table->unsignedBigInteger('personnel_id');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 11, 7);
            $table->decimal('speed', 6, 2)->nullable(); // km/h
            $table->decimal('heading', 5, 2)->nullable(); // degrees 0-360
            $table->timestamps();

            $table->foreign('voyage_id')->references('id')->on('voyages')->onDelete('cascade');
            $table->foreign('personnel_id')->references('id')->on('personnels')->onDelete('cascade');
            
            $table->index(['voyage_id', 'updated_at']);
            $table->index('personnel_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_locations');
    }
};
