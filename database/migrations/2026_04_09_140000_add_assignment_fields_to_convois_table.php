<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->foreignId('personnel_id')->nullable()->after('gare_id')->constrained('personnels')->nullOnDelete();
            $table->foreignId('vehicule_id')->nullable()->after('personnel_id')->constrained('vehicules')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->dropConstrainedForeignId('personnel_id');
            $table->dropConstrainedForeignId('vehicule_id');
        });
    }
};

