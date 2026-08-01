<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['product_id', 'category_id']);
        });

        // Carry each product's existing single category_id forward as its first row in the pivot.
        DB::table('products')
            ->whereNotNull('category_id')
            ->orderBy('id')
            ->select('id', 'category_id')
            ->each(function ($product) {
                DB::table('product_category')->insert([
                    'product_id' => $product->id,
                    'category_id' => $product->category_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        DB::table('product_category')
            ->orderBy('product_id')
            ->select('product_id', 'category_id')
            ->each(function ($row) {
                DB::table('products')->where('id', $row->product_id)->update(['category_id' => $row->category_id]);
            });

        Schema::dropIfExists('product_category');
    }
};
