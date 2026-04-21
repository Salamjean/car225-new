<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('signalements', function (Blueprint $table) {
            $table->foreignId('convoi_id')
                  ->nullable()
                  ->after('voyage_id')
                  ->constrained('convois')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('signalements', function (Blueprint $table) {
            $table->dropForeign(['convoi_id']);
            $table->dropColumn('convoi_id');
        });
    }
};
