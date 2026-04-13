<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compagnies', function (Blueprint $table) {
            $table->decimal('solde_convoie', 15, 2)->default(0)->after('tickets');
        });
    }

    public function down(): void
    {
        Schema::table('compagnies', function (Blueprint $table) {
            $table->dropColumn('solde_convoie');
        });
    }
};
