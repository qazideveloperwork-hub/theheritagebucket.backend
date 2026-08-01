<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock_quantity');
        });

        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropColumn('stock_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('stock_quantity')->nullable()->after('regular_price');
        });

        Schema::table('product_variations', function (Blueprint $table) {
            $table->unsignedInteger('stock_quantity')->default(0)->after('purchase_price');
        });
    }
};
