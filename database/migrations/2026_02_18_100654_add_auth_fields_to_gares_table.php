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
        Schema::table('gares', function (Blueprint $table) {
            $table->string('email')->unique()->nullable()->after('adresse');
            $table->string('password')->nullable()->after('email');
            $table->string('contact')->nullable()->after('password');
            $table->string('contact_urgence')->nullable()->after('contact');
            $table->string('commune')->nullable()->after('contact_urgence');
            $table->string('profile_image')->nullable()->after('commune');
            $table->string('responsable_nom')->nullable()->after('profile_image');
            $table->string('responsable_prenom')->nullable()->after('responsable_nom');
            $table->string('remember_token', 100)->nullable()->after('profile_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gares', function (Blueprint $table) {
            $table->dropColumn([
                'email', 'password', 'contact', 'contact_urgence',
                'commune', 'profile_image', 'responsable_nom',
                'responsable_prenom', 'remember_token'
            ]);
        });
    }
};
