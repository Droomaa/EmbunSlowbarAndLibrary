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
    Schema::create('inventories', function (Blueprint $table) {
        $table->id(); // Menggunakan 'id' bawaan Laravel
        $table->string('item_name'); // Nama bahan (misal: Kopi Arabika)
        $table->decimal('quantity', 8, 2); // Jumlah (bisa desimal misal 1.5)
        $table->string('unit'); // Satuan (misal: Kg, Liter, Pcs, Gram)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
