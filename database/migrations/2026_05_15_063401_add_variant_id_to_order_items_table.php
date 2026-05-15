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
    Schema::table('order_items', function (Blueprint $table) {
        // Tambahkan variant_id, boleh null jika menu tidak punya varian (misal: French Fries)
        $table->foreignId('menu_variant_id')->nullable()->after('menu_id')->constrained('menu_variants')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('order_items', function (Blueprint $table) {
        $table->dropForeign(['menu_variant_id']);
        $table->dropColumn('menu_variant_id');
    });
}
};
