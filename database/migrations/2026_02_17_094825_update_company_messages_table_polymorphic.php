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
        Schema::table('company_messages', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropColumn('agent_id');
            $table->unsignedBigInteger('recipient_id');
            $table->string('recipient_type');
            $table->index(['recipient_id', 'recipient_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_messages', function (Blueprint $table) {
            $table->dropIndex(['recipient_id', 'recipient_type']);
            $table->dropColumn(['recipient_id', 'recipient_type']);
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade');
        });
    }
};
