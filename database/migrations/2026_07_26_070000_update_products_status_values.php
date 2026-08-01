<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')->where('status', 'active')->update(['status' => 'publish']);
        DB::table('products')->where('status', 'inactive')->update(['status' => 'draft']);

        Schema::table('products', function ($table) {
            $table->string('status')->default('draft')->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function ($table) {
            $table->string('status')->default('active')->change();
        });

        DB::table('products')->where('status', 'publish')->update(['status' => 'active']);
        DB::table('products')->where('status', 'draft')->update(['status' => 'inactive']);
    }
};
