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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('daily_operation_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 8, 2);
            $table->string('payment_method')->default('cash');
            $table->string('transaction_no', 13)->nullable(); // GCash transaction number
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
