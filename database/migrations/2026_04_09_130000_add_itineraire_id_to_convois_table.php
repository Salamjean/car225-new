<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->foreignId('itineraire_id')->nullable()->after('compagnie_id')->constrained('itineraires')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->dropConstrainedForeignId('itineraire_id');
        });
    }
};

