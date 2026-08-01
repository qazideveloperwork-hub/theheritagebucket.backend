<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_descriptions', function (Blueprint $table) {
            $table->string('short_description')->nullable()->after('product_id');
        });

        DB::table('products')->whereNotNull('short_description')->where('short_description', '!=', '')->orderBy('id')
            ->each(function ($product) {
                DB::table('product_descriptions')->updateOrInsert(
                    ['product_id' => $product->id],
                    ['short_description' => $product->short_description, 'updated_at' => now(), 'created_at' => now()]
                );
            });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('short_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('short_description')->nullable()->after('cover_image_url');
        });

        DB::table('product_descriptions')->orderBy('id')->each(function ($description) {
            DB::table('products')->where('id', $description->product_id)->update(['short_description' => $description->short_description]);
        });

        Schema::table('product_descriptions', function (Blueprint $table) {
            $table->dropColumn('short_description');
        });
    }
};
