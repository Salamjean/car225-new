<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('paiement_id')->nullable()->after('user_id')->constrained('paiements')->onDelete('set null');
            $table->string('payment_transaction_id')->nullable()->after('paiement_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['paiement_id']);
            $table->dropColumn(['paiement_id', 'payment_transaction_id']);
        });
    }
};
