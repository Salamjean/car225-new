<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->foreignId('gare_id')->nullable()->after('itineraire_id')->constrained('gares')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->dropConstrainedForeignId('gare_id');
        });
    }
};

