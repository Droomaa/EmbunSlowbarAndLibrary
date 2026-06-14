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
        Schema::table('menus', function (Blueprint $table) {
            if (!Schema::hasColumn('menus', 'category')) {
                $table->string('category')->nullable()->default('Coffee');
            }
            if (!Schema::hasColumn('menus', 'status')) {
                $table->string('status')->default('Available');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (Schema::hasColumn('menus', 'category')) {
                $table->dropColumn('category');
            }
            if (Schema::hasColumn('menus', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
