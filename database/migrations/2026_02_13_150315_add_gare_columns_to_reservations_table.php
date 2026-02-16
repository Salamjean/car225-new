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
        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('gare_depart_id')->nullable()->after('programme_id');
            $table->unsignedBigInteger('gare_arrivee_id')->nullable()->after('gare_depart_id');
            
            $table->foreign('gare_depart_id')->references('id')->on('gares')->onDelete('set null');
            $table->foreign('gare_arrivee_id')->references('id')->on('gares')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['gare_depart_id']);
            $table->dropForeign(['gare_arrivee_id']);
            $table->dropColumn(['gare_depart_id', 'gare_arrivee_id']);
        });
    }
};
