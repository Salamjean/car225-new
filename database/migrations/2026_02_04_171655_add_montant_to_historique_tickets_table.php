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
        Schema::table('historique_tickets', function (Blueprint $table) {
            $table->integer('montant')->nullable()->after('motif')->comment('Montant payé pour le rechargement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historique_tickets', function (Blueprint $table) {
            //
        });
    }
};
