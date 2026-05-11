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
            $table->id();
            
            // Relasi ke tabel customers, kalau customer dihapus reservasi ikut hilang
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            
            $table->date('date');           // Tanggal booking
            $table->time('startTime');      // Jam mulai
            $table->integer('duration');    // Durasi (jam)
            $table->integer('jumlahOrang'); // Kapasitas meja
            
            // Status awal biasanya 'Pending', bisa diubah jadi 'Confirmed' atau 'Cancelled'
            $table->string('status')->default('Pending');
            
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
