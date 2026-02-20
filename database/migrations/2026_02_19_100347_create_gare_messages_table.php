<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gare_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gare_id')->constrained('gares')->onDelete('cascade');
            $table->string('recipient_type');
            $table->unsignedBigInteger('recipient_id');
            $table->string('subject');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            $table->index(['recipient_id', 'recipient_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gare_messages');
    }
};
