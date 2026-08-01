<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Textiles & Shawls', 'slug' => 'textiles-shawls', 'description' => 'Hand-woven Ajrak, Pashmina and embroidered fabrics from across Pakistan.'],
            ['name' => 'Traditional Footwear', 'slug' => 'traditional-footwear', 'description' => 'Hand-stitched Khussa and Peshawari chappal, made by master leather artisans.'],
            ['name' => 'Blue Pottery', 'slug' => 'blue-pottery', 'description' => 'Hand-painted Multani Kashi-kari ceramics in cobalt blue.'],
            ['name' => 'Brass & Copperware', 'slug' => 'brass-copperware', 'description' => 'Hand-hammered brass and copper vessels, trays and lanterns.'],
            ['name' => 'Home & Decor', 'slug' => 'home-decor', 'description' => 'Carved wood, coasters and decorative accents for the home.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
