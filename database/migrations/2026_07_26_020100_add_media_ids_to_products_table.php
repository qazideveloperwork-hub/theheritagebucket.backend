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
            $table->foreignId('cover_image_id')->nullable()->after('cover_image_url')->constrained('media')->nullOnDelete();
            $table->foreignId('floating_image_id')->nullable()->after('floating_image_url')->constrained('media')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cover_image_id');
            $table->dropConstrainedForeignId('floating_image_id');
        });
    }
};
