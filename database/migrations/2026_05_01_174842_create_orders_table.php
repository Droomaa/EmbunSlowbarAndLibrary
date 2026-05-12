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
    Schema::create('orders', function (Blueprint $table) {
        $table->id('order_id');
        $table->string('customer_name');
        $table->string('table_number')->nullable(); // Boleh kosong kalau take-away
        $table->decimal('total_price', 10, 2)->default(0);
        $table->string('status')->default('Pending'); // Pending -> Processing -> Completed
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
