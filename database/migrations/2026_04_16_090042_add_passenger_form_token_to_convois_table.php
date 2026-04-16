<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->string('passenger_form_token', 64)->nullable()->unique()->after('created_by_gare');
            $table->boolean('passagers_soumis')->default(false)->after('passenger_form_token');
        });
    }

    public function down(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->dropColumn(['passenger_form_token', 'passagers_soumis']);
        });
    }
};
