<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Codes OTP pour la définition initiale et la réinitialisation du mot
 * de passe des agents ONPC (mêmes pattern que les autres rôles).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('reset_code_password_onpcs', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('email');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reset_code_password_onpcs');
    }
};
