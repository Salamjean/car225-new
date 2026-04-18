<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            // Indique que le trajet aller d'un convoi aller-retour est terminé
            // et que le retour est en attente. Tant que ce flag est false,
            // un completeConvoi() passe à statut = termine.
            // Quand true, completeConvoi() de l'aller passe à statut = paye
            // et le chauffeur peut démarrer le retour à la date prévue.
            $table->boolean('aller_done')->default(false)->after('passagers_soumis');
        });
    }

    public function down(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->dropColumn('aller_done');
        });
    }
};
