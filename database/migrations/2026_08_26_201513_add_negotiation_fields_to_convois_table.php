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
            $table->decimal('montant_propose_client', 10, 2)->nullable()->after('montant');
            $table->string('dernier_offreur')->nullable()->after('montant_propose_client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->dropColumn(['montant_propose_client', 'dernier_offreur']);
        });
    }
};
