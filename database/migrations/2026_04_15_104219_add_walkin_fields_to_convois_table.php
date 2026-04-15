<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            // Rendre user_id nullable (client physique sans compte)
            $table->foreignId('user_id')->nullable()->change();

            // Infos du client walk-in (quand user_id est null)
            $table->string('client_nom')->nullable()->after('user_id');
            $table->string('client_prenom')->nullable()->after('client_nom');
            $table->string('client_contact', 20)->nullable()->after('client_prenom');
            $table->string('client_email')->nullable()->after('client_contact');

            // Flag : convoi créé directement par la gare (client sur place)
            $table->boolean('created_by_gare')->default(false)->after('client_email');
        });
    }

    public function down(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->dropColumn(['client_nom', 'client_prenom', 'client_contact', 'client_email', 'created_by_gare']);
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
