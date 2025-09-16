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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_operation_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('court_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('expected_hours');
            $table->integer('expected_minutes');
            $table->decimal('amount', 8, 2);
            $table->string('transaction_no')->nullable();
            $table->enum('status', ['waiting', 'called', 'skipped', 'completed'])->default('waiting');
            $table->integer('queue_number')->nullable()->default(1);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
