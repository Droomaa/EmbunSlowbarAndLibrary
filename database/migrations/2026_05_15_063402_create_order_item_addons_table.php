<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('order_item_addons', function (Blueprint $table) {
        $table->id();
        // Relasi ke item pesanan yang mana
        $table->unsignedBigInteger('order_item_id');
        $table->foreign('order_item_id')->references('item_id')->on('order_items')->onDelete('cascade');
        
        // Relasi ke add on apa yang ditambahkan
        $table->foreignId('add_on_id')->constrained('add_ons')->onDelete('cascade');
        
        // Rekam jejak harga saat dipesan (berjaga-jaga kalau harga add on naik di masa depan)
        $table->decimal('price', 10, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_addons');
    }
};
