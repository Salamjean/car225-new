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
            $table->dropColumn(['qr_code_retour', 'qr_code_retour_path', 'qr_code_retour_data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->text('qr_code_retour')->nullable();
            $table->string('qr_code_retour_path')->nullable();
            $table->json('qr_code_retour_data')->nullable();
        });
    }
};
