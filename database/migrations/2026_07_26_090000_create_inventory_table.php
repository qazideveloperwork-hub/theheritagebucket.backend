<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('product_variation_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamps();

            $table->unique('product_id');
            $table->unique('product_variation_id');
        });

        // Backfill from the stock_quantity columns that are about to be dropped, so
        // existing stock levels aren't lost in the move to the inventory table.
        DB::table('products')
            ->whereNotNull('stock_quantity')
            ->select('id', 'stock_quantity')
            ->orderBy('id')
            ->get()
            ->each(function ($product) {
                DB::table('inventory')->insert([
                    'product_id' => $product->id,
                    'quantity' => $product->stock_quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        DB::table('product_variations')
            ->select('id', 'stock_quantity')
            ->orderBy('id')
            ->get()
            ->each(function ($variation) {
                DB::table('inventory')->insert([
                    'product_variation_id' => $variation->id,
                    'quantity' => $variation->stock_quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
