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
        // Ajout de la colonne solde à la table users existante
        if (!Schema::hasColumn('users', 'solde')) {
            Schema::table('users', function (Blueprint $table) {
                // Utilise 'after' pour placer la colonne proprement si tu es en MySQL
                $table->decimal('solde', 12, 2)->default(0)->after('email');
            });
        }

        // Création de la table des transactions
        if (!Schema::hasTable('wallet_transactions')) {
            Schema::create('wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->decimal('amount', 12, 2);
                $table->enum('type', ['credit', 'debit']);
                $table->string('description')->nullable();
                $table->string('reference')->unique(); // Index unique automatique ici, très bien
                $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
                $table->string('payment_method')->nullable();
                $table->string('external_transaction_id')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                // OPTIMISATION : Index composite pour accélérer l'affichage de l'historique
                $table->index(['user_id', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        
        if (Schema::hasColumn('users', 'solde')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('solde');
            });
        }
    }
};