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
        Schema::table('support_requests', function (Blueprint $table) {
            $table->string('email')->nullable()->after('user_id');
            $table->string('telephone')->nullable()->after('email');
            $table->string('billet')->nullable()->after('telephone');
            // user_id and reservation_id can be null since unregistered users can submit
            $table->foreignId('user_id')->nullable()->change();
            $table->foreignId('reservation_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_requests', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('telephone');
            $table->dropColumn('billet');
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreignId('reservation_id')->nullable(false)->change();
        });
    }
};
