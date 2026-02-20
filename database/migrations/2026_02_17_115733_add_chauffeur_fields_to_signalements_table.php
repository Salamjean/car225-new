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
        Schema::table('signalements', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->foreignId('personnel_id')->nullable()->constrained('personnels')->onDelete('cascade');
            $table->foreignId('voyage_id')->nullable()->constrained('voyages')->onDelete('cascade');
            $table->foreignId('compagnie_id')->nullable()->constrained('compagnies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signalements', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->dropForeign(['personnel_id']);
            $table->dropColumn('personnel_id');
            $table->dropForeign(['voyage_id']);
            $table->dropColumn('voyage_id');
            $table->dropForeign(['compagnie_id']);
            $table->dropColumn('compagnie_id');
        });
    }
};
