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
        Schema::table('voyages', function (Blueprint $table) {
            $table->dateTime('estimated_arrival_at')->nullable()->after('statut');
            $table->integer('delay_seconds')->default(0)->after('estimated_arrival_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voyages', function (Blueprint $table) {
            $table->dropColumn(['estimated_arrival_at', 'delay_seconds']);
        });
    }
};
