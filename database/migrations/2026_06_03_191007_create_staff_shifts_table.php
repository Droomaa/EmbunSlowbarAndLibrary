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
        Schema::create('staff_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('shift_date');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->string('status')->default('Active'); // Active, Completed, Cancelled
            $table->text('check_in_note')->nullable();
            $table->text('check_out_note')->nullable();
            $table->decimal('opening_cash', 10, 2)->nullable();
            $table->decimal('closing_cash', 10, 2)->nullable();
            $table->integer('total_orders')->default(0);
            $table->decimal('total_sales', 12, 2)->default(0);
            $table->integer('duration_minutes')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('shift_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_shifts');
    }
};
