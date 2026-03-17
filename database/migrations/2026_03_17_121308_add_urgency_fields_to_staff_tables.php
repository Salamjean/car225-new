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
        Schema::table('agents', function (Blueprint $table) {
            $table->string('nom_urgence')->nullable()->after('cas_urgence');
            $table->string('lien_parente_urgence')->nullable()->after('nom_urgence');
        });

        Schema::table('caisses', function (Blueprint $table) {
            $table->string('nom_urgence')->nullable()->after('cas_urgence');
            $table->string('lien_parente_urgence')->nullable()->after('nom_urgence');
        });

        Schema::table('personnels', function (Blueprint $table) {
            $table->string('nom_urgence')->nullable()->after('contact_urgence');
            $table->string('lien_parente_urgence')->nullable()->after('nom_urgence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn(['nom_urgence', 'lien_parente_urgence']);
        });

        Schema::table('caisses', function (Blueprint $table) {
            $table->dropColumn(['nom_urgence', 'lien_parente_urgence']);
        });

        Schema::table('personnels', function (Blueprint $table) {
            $table->dropColumn(['nom_urgence', 'lien_parente_urgence']);
        });
    }
};
