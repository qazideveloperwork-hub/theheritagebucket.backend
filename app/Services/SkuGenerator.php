<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariation;

class SkuGenerator
{
    // SKUs share one sequence across products and variations, formatted as "HB-<number>".
    // The first SKU ever generated picks a random starting number; every one after that
    // just increments the highest number seen so far, so they stay in order and unique.
    public function next(): string
    {
        $productMax = (int) Product::where('sku', 'like', 'HB-%')
            ->selectRaw('MAX(CAST(SUBSTRING(sku, 4) AS UNSIGNED)) as max_number')
            ->value('max_number');

        $variationMax = (int) ProductVariation::where('sku', 'like', 'HB-%')
            ->selectRaw('MAX(CAST(SUBSTRING(sku, 4) AS UNSIGNED)) as max_number')
            ->value('max_number');

        $next = max($productMax, $variationMax) + 1;

        return 'HB-' . ($next > 1 ? $next : random_int(100000, 999999));
    }
}
