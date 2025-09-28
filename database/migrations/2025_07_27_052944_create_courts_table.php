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
        Schema::create('courts', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('sport');
        $table->decimal('hourly_rate', 8, 2);
        $table->enum('status', ['available', 'in-use'])->default('available');
        $table->text('description')->nullable();
        $table->json('images')->nullable();
        $table->timestamps();
    });





    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courts');
    }
};
