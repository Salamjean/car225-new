<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->string('lieu_depart')->nullable()->after('itineraire_id');
            $table->string('lieu_retour')->nullable()->after('lieu_depart');
            $table->date('date_depart')->nullable()->after('lieu_retour');
            $table->time('heure_depart')->nullable()->after('date_depart');
            $table->date('date_retour')->nullable()->after('heure_depart');
            $table->time('heure_retour')->nullable()->after('date_retour');
            $table->decimal('montant', 10, 2)->nullable()->after('heure_retour');
            $table->text('motif_refus')->nullable()->after('montant');
        });

        // Mettre à jour l'enum statut pour ajouter 'refuse' et 'paye'
        DB::statement("ALTER TABLE convois MODIFY COLUMN statut ENUM(
            'en_attente',
            'valide',
            'refuse',
            'paye',
            'en_cours',
            'annule',
            'termine'
        ) NOT NULL DEFAULT 'en_attente'");
    }

    public function down(): void
    {
        Schema::table('convois', function (Blueprint $table) {
            $table->dropColumn([
                'lieu_depart', 'lieu_retour',
                'date_depart', 'heure_depart',
                'date_retour', 'heure_retour',
                'montant', 'motif_refus',
            ]);
        });

        DB::statement("ALTER TABLE convois MODIFY COLUMN statut ENUM(
            'en_attente','valide','en_cours','annule','termine'
        ) NOT NULL DEFAULT 'en_attente'");
    }
};
