<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->string('lieu_rassemblement_retour', 255)->nullable()->after('lieu_rassemblement');
        });
    }

    public function down(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->dropColumn('lieu_rassemblement_retour');
        });
    }
};
