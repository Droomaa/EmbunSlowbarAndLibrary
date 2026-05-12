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
    Schema::create('reservations', function (Blueprint $table) {
        $table->id('reservation_id');
        $table->string('customer_name');
        $table->string('phone_number');
        $table->dateTime('reservation_date'); // Tanggal & jam booking
        $table->integer('pax'); // Jumlah orang
        $table->string('status')->default('Pending'); // Status default menunggu konfirmasi
        $table->text('notes')->nullable(); // Catatan tambahan (opsional)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
