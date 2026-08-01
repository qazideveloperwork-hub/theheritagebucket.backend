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
            $table->renameColumn('price', 'regular_price');
            $table->renameColumn('sale_price', 'discount_price');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->change();
            $table->string('type')->default('simple')->after('category_id');
            $table->foreignId('supplier_id')->nullable()->after('type')->constrained()->nullOnDelete();
            $table->decimal('purchase_price', 12, 2)->nullable()->after('discount_price');
            $table->string('stock_status')->default('in_stock')->after('stock_quantity');
            $table->boolean('stock_status_manual')->default(false)->after('stock_status');
            $table->text('floating_image_url')->nullable()->after('cover_image_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
            $table->dropColumn(['type', 'purchase_price', 'stock_status', 'stock_status_manual', 'floating_image_url']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable(false)->change();
            $table->renameColumn('regular_price', 'price');
            $table->renameColumn('discount_price', 'sale_price');
        });
    }
};
