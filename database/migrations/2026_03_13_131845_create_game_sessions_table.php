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
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_id')->nullable()->constrained()->nullOnDelete();
            $table->string('game_slug');
            $table->unsignedInteger('score')->default(0);
            $table->unsignedSmallInteger('duration_seconds')->nullable();
            $table->timestamp('played_at');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'played_at']);
            $table->index('game_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_sessions');
    }
};
