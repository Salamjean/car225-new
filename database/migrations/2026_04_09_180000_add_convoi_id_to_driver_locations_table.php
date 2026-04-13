<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driver_locations', function (Blueprint $table) {
            $table->unsignedBigInteger('convoi_id')->nullable()->after('voyage_id');
            $table->foreign('convoi_id')->references('id')->on('convois')->onDelete('cascade');
            $table->index(['convoi_id', 'updated_at']);
        });

        DB::statement('ALTER TABLE driver_locations DROP FOREIGN KEY driver_locations_voyage_id_foreign');
        DB::statement('ALTER TABLE driver_locations MODIFY voyage_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE driver_locations ADD CONSTRAINT driver_locations_voyage_id_foreign FOREIGN KEY (voyage_id) REFERENCES voyages(id) ON DELETE CASCADE');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE driver_locations DROP FOREIGN KEY driver_locations_voyage_id_foreign');
        DB::statement('ALTER TABLE driver_locations MODIFY voyage_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE driver_locations ADD CONSTRAINT driver_locations_voyage_id_foreign FOREIGN KEY (voyage_id) REFERENCES voyages(id) ON DELETE CASCADE');

        Schema::table('driver_locations', function (Blueprint $table) {
            $table->dropIndex(['convoi_id', 'updated_at']);
            $table->dropForeign(['convoi_id']);
            $table->dropColumn('convoi_id');
        });
    }
};

