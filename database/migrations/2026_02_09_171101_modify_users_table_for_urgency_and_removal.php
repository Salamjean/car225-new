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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nom_urgence')->nullable()->after('contact_urgence');
            $table->string('prenom_urgence')->nullable()->after('nom_urgence');
            $table->dropColumn(['adresse', 'pays']); // He said remove address, usually pays goes with it.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('adresse')->nullable();
            $table->string('pays')->nullable();
            $table->dropColumn(['nom_urgence', 'prenom_urgence']);
        });
    }
};
