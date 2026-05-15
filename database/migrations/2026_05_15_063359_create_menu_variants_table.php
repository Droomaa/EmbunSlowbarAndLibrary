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
    Schema::create('menu_variants', function (Blueprint $table) {
        $table->id();
        $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
        $table->string('name'); // Contoh: "Ice", "Hot", "Reguler"
        $table->decimal('price', 10, 2); // Harga spesifik untuk varian ini
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_variants');
    }
};
