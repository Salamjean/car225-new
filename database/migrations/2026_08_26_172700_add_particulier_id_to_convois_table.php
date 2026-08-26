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
        Schema::table('convois', function (Blueprint $table) {
            // Défaire la contrainte de clé étrangère sur compagnie_id, le rendre nullable, puis recréer la contrainte avec nullOnDelete
            $table->dropForeign(['compagnie_id']);
            $table->unsignedBigInteger('compagnie_id')->nullable()->change();
            $table->foreign('compagnie_id')->references('id')->on('compagnies')->nullOnDelete();

            // Ajouter particulier_id en tant que clé étrangère nullable
            $table->foreignId('particulier_id')->nullable()->after('compagnie_id')->constrained('particuliers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->dropForeign(['particulier_id']);
            $table->dropColumn('particulier_id');

            $table->dropForeign(['compagnie_id']);
            $table->unsignedBigInteger('compagnie_id')->nullable(false)->change();
            $table->foreign('compagnie_id')->references('id')->on('compagnies')->onDelete('cascade');
        });
    }
};
