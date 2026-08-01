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
        Schema::table('sliders', function (Blueprint $table) {
            // Used by home_category rows (which category tile this is) and
            // home_new_arrival rows (which product to feature) respectively.
            // Nullable/unused for other slider types like home_banner.
            $table->foreignId('category_id')->nullable()->after('image_id')->constrained('categories')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->after('category_id')->constrained('products')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->dropConstrainedForeignId('product_id');
        });
    }
};
