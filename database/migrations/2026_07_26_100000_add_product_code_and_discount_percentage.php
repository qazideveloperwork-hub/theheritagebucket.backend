<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_code')->nullable()->after('sku');
            $table->decimal('discount_percentage', 5, 2)->nullable()->after('discount_price');
            $table->decimal('sale_price', 12, 2)->nullable()->after('discount_percentage');
        });

        Schema::table('product_variations', function (Blueprint $table) {
            $table->decimal('discount_percentage', 5, 2)->nullable()->after('discount_price');
            $table->decimal('sale_price', 12, 2)->nullable()->after('discount_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['product_code', 'discount_percentage', 'sale_price']);
        });

        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropColumn(['discount_percentage', 'sale_price']);
        });
    }
};
