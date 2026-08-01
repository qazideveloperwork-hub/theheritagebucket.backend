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
        // Shared table for every slider/banner screen (home banner, category slider,
        // banner images, ...), distinguished by `type`. The home banner is a single
        // row per type; other types may hold several rows ordered by sort_order.
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->foreignId('image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('heading_1')->nullable();
            $table->string('heading_2')->nullable();
            $table->text('description')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
