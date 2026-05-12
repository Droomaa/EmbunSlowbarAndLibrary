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
    Schema::create('order_items', function (Blueprint $table) {
        $table->id('item_id');
        
        // Relasi ke tabel orders (mencari kolom 'order_id' di tabel orders)
        $table->unsignedBigInteger('order_id');
        $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
        
        // Relasi ke tabel menus (mencari kolom 'id' di tabel menus)
        $table->unsignedBigInteger('menu_id');
        $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
        
        $table->integer('quantity');
        $table->decimal('subtotal', 10, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
