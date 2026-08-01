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
        DB::table('products')->whereNotNull('story')->where('story', '!=', '')->orderBy('id')
            ->each(function ($product) {
                DB::table('product_descriptions')->updateOrInsert(
                    ['product_id' => $product->id],
                    ['content' => $product->story, 'updated_at' => now(), 'created_at' => now()]
                );
            });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('story');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->longText('story')->nullable()->after('short_description');
        });

        DB::table('product_descriptions')->orderBy('id')->each(function ($description) {
            DB::table('products')->where('id', $description->product_id)->update(['story' => $description->content]);
        });
    }
};
