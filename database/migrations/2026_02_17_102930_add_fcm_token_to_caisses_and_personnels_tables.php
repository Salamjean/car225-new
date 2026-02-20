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
        Schema::table('caisses', function (Blueprint $table) {
            $table->string('fcm_token')->nullable()->after('password');
        });

        Schema::table('personnels', function (Blueprint $table) {
            $table->string('fcm_token')->nullable()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('caisses', function (Blueprint $table) {
            $table->dropColumn('fcm_token');
        });

        Schema::table('personnels', function (Blueprint $table) {
            $table->dropColumn('fcm_token');
        });
    }
};
