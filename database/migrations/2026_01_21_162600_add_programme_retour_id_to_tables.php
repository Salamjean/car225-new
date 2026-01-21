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
        // Ajouter programme_retour_id à la table programmes
        Schema::table('programmes', function (Blueprint $table) {
            $table->foreignId('programme_retour_id')->nullable()->after('is_aller_retour')->constrained('programmes')->nullOnDelete();
        });

        // Ajouter programme_retour_id à la table reservations
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('programme_retour_id')->nullable()->after('programme_id')->constrained('programmes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->dropForeign(['programme_retour_id']);
            $table->dropColumn('programme_retour_id');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['programme_retour_id']);
            $table->dropColumn('programme_retour_id');
        });
    }
};
