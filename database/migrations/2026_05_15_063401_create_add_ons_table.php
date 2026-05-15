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
    Schema::create('add_ons', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Contoh: "Telur", "Oat Milk"
        $table->decimal('price', 10, 2);
        $table->string('category')->nullable(); // Contoh: "Food", "Drink"
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_ons');
    }
};
