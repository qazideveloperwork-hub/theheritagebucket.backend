<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Simple products only — mirrors ProductsImport's column set exactly, so an
 * exported file can be edited and re-imported as-is. Variable products carry
 * pricing/stock per size variation and don't fit this flat one-row format.
 */
class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private readonly bool $templateOnly = false)
    {
    }

    public function collection(): Collection
    {
        if ($this->templateOnly) {
            return collect();
        }

        return Product::with(['categories', 'supplier', 'description', 'inventory'])
            ->where('type', 'simple')
            ->where('status', '!=', 'deleted')
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Name', 'Category', 'Supplier', 'SKU', 'Product Code',
            'Regular Price', 'Discount Percentage', 'Sale Price', 'Purchase Price',
            'Stock Quantity', 'Weight Kg', 'Short Description', 'Story',
            'Material', 'Dimensions', 'Badge', 'Expected Delivery Days', 'Status',
        ];
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->categories->first()?->name,
            $product->supplier?->name,
            $product->sku,
            $product->product_code,
            $product->regular_price,
            $product->discount_percentage,
            $product->sale_price,
            $product->purchase_price,
            $product->inventory?->quantity ?? 0,
            $product->weight,
            $product->description?->short_description,
            $product->description?->content,
            $product->material,
            $product->dimensions,
            $product->badge,
            $product->expected_delivery_days,
            $product->status,
        ];
    }
}
