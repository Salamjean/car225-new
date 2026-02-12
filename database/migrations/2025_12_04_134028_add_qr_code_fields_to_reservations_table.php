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
            $table->json('qr_code_data')->nullable()->after('qr_code_path');
            $table->timestamp('embarquement_scanned_at')->nullable()->after('qr_code_path');
            $table->integer('embarquement_agent_id')->nullable()->after('embarquement_scanned_at');
            $table->string('embarquement_location')->nullable()->after('embarquement_agent_id');
            $table->string('embarquement_status')->nullable()->after('embarquement_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'qr_code_data',
                'embarquement_scanned_at',
                'embarquement_agent_id',
                'embarquement_location',
                'embarquement_status'
            ]);
        });
    }
};
