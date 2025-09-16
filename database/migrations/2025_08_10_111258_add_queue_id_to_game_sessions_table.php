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
    Schema::table('game_sessions', function (Blueprint $table) {
    $table->foreignId('queue_id')
        ->nullable()
        ->constrained()
        ->onDelete('set null');
    });
    }

    public function down(): void
    {
    Schema::table('game_sessions', function (Blueprint $table) {
    $table->dropForeign(['queue_id']);
    $table->dropColumn('queue_id');
    });
    }



};
