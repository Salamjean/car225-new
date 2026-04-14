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
            $table->string('lieu_rassemblement')->nullable()->after('lieu_retour');
            $table->boolean('is_garant')->default(false)->after('lieu_rassemblement');
            $table->text('motif_annulation_chauffeur')->nullable()->after('motif_refus');
        });
    }

    public function down(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->dropColumn(['lieu_rassemblement', 'is_garant', 'motif_annulation_chauffeur']);
        });
    }
};
