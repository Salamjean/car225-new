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
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'annulation_date')) {
                $table->datetime('annulation_date')->nullable()->after('statut');
            }
            if (!Schema::hasColumn('reservations', 'annulation_reason')) {
                $table->string('annulation_reason')->nullable()->after('annulation_date');
            }
            if (!Schema::hasColumn('reservations', 'refund_amount')) {
                $table->decimal('refund_amount', 12, 2)->nullable()->after('annulation_reason');
            }
            if (!Schema::hasColumn('reservations', 'refund_percentage')) {
                $table->integer('refund_percentage')->nullable()->after('refund_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['annulation_date', 'annulation_reason', 'refund_amount', 'refund_percentage']);
        });
    }
};
