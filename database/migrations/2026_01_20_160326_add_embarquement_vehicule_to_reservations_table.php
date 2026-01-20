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
            $table->unsignedBigInteger('embarquement_vehicule_id')->nullable()->after('embarquement_agent_id');
            $table->foreign('embarquement_vehicule_id')->references('id')->on('vehicules')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['embarquement_vehicule_id']);
            $table->dropColumn('embarquement_vehicule_id');
        });
    }
};
