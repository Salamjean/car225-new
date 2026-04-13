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
        Schema::table('convoi_passagers', function (Blueprint $table) {
            $table->string('contact_urgence')->nullable()->after('contact');
        });

        // Initialiser la colonne pour les lignes existantes
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE convoi_passagers SET contact_urgence = contact WHERE contact_urgence IS NULL"
        );

        Schema::table('convoi_passagers', function (Blueprint $table) {
            $table->string('contact_urgence')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('convoi_passagers', function (Blueprint $table) {
            $table->dropColumn('contact_urgence');
        });
    }
};
