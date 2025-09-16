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
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('court_id')->constrained()->onDelete('cascade');
            $table->foreignId('daily_operation_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->date('session_date');
            $table->enum('session_type', ['walk-in', 'booking', 'queue'])->default('walk-in');
            $table->string('customer_name')->nullable();
            $table->integer('expected_hours')->default(0);
            $table->integer('expected_minutes')->default(0);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('amount_paid', 8, 2)->nullable();
            $table->enum('status', ['pending', 'ongoing', 'completed']);
            $table->timestamps();
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
