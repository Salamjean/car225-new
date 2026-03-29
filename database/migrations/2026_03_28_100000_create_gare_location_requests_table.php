<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gare_location_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gare_id')->constrained()->onDelete('cascade');
            $table->foreignId('compagnie_id')->constrained()->onDelete('cascade');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->enum('statut', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('rejected_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->boolean('gare_notified')->default(false); // popup shown on dashboard
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gare_location_requests');
    }
};
