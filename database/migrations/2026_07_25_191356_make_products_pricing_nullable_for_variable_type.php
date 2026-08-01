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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('regular_price', 12, 2)->nullable()->change();
            $table->unsignedInteger('stock_quantity')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('regular_price', 12, 2)->nullable(false)->change();
            $table->unsignedInteger('stock_quantity')->nullable(false)->default(0)->change();
        });
    }
};
