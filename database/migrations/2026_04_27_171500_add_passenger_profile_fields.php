<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute des champs profil passager aux tables `users`, `reservations`
 * et `convoi_passagers` afin que l'ONPC (et les compagnies) puissent
 * afficher des informations détaillées : date de naissance, genre,
 * photo de profil pour les passagers de convoi.
 *
 * Tous les champs sont nullable → strictement rétrocompatible.
 */
return new class extends Migration {
    public function up(): void
    {
        // Comptes utilisateur (passagers ayant un compte CAR225)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'date_naissance')) {
                $table->date('date_naissance')->nullable()->after('contact_urgence');
            }
            if (!Schema::hasColumn('users', 'genre')) {
                $table->enum('genre', ['homme', 'femme', 'autre'])->nullable()->after('date_naissance');
            }
            if (!Schema::hasColumn('users', 'piece_identite')) {
                $table->string('piece_identite')->nullable()->after('genre');
            }
        });

        // Réservations (passagers walk-in d'un voyage)
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'passager_date_naissance')) {
                $table->date('passager_date_naissance')->nullable()->after('nom_passager_urgence');
            }
            if (!Schema::hasColumn('reservations', 'passager_genre')) {
                $table->enum('passager_genre', ['homme', 'femme', 'autre'])->nullable()->after('passager_date_naissance');
            }
            if (!Schema::hasColumn('reservations', 'passager_piece_identite')) {
                $table->string('passager_piece_identite')->nullable()->after('passager_genre');
            }
        });

        // Passagers de convoi
        Schema::table('convoi_passagers', function (Blueprint $table) {
            if (!Schema::hasColumn('convoi_passagers', 'date_naissance')) {
                $table->date('date_naissance')->nullable()->after('email');
            }
            if (!Schema::hasColumn('convoi_passagers', 'genre')) {
                $table->enum('genre', ['homme', 'femme', 'autre'])->nullable()->after('date_naissance');
            }
            if (!Schema::hasColumn('convoi_passagers', 'piece_identite')) {
                $table->string('piece_identite')->nullable()->after('genre');
            }
            if (!Schema::hasColumn('convoi_passagers', 'photo_path')) {
                $table->string('photo_path')->nullable()->after('piece_identite');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['date_naissance', 'genre', 'piece_identite'] as $col) {
                if (Schema::hasColumn('users', $col)) $table->dropColumn($col);
            }
        });

        Schema::table('reservations', function (Blueprint $table) {
            foreach (['passager_date_naissance', 'passager_genre', 'passager_piece_identite'] as $col) {
                if (Schema::hasColumn('reservations', $col)) $table->dropColumn($col);
            }
        });

        Schema::table('convoi_passagers', function (Blueprint $table) {
            foreach (['date_naissance', 'genre', 'piece_identite', 'photo_path'] as $col) {
                if (Schema::hasColumn('convoi_passagers', $col)) $table->dropColumn($col);
            }
        });
    }
};
