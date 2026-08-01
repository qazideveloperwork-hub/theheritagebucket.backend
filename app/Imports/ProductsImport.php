<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\SkuGenerator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Simple products only — one row = one product with one price/stock. Variable
 * (sized) products and images aren't supported here; add those manually in
 * the product editor after import. Column set mirrors ProductsExport exactly.
 */
class ProductsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public int $imported = 0;

    public function __construct(private readonly SkuGenerator $skuGenerator)
    {
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::exists('categories', 'name')],
            'supplier' => ['required', Rule::exists('suppliers', 'name')],
            'sku' => ['nullable', 'string', 'max:100'],
            'product_code' => ['nullable', 'string', 'max:100'],
            'regular_price' => ['required', 'numeric', 'min:0'],
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'weight_kg' => ['nullable', 'numeric', 'min:0'],
            'short_description' => ['required', 'string', 'max:255'],
            'story' => ['nullable', 'string'],
            'material' => ['nullable', 'string', 'max:255'],
            'dimensions' => ['nullable', 'string', 'max:255'],
            'badge' => ['nullable', 'string', 'max:100'],
            'expected_delivery_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'status' => ['nullable', 'in:publish,draft'],
        ];
    }

    public function model(array $row)
    {
        $category = Category::whereRaw('LOWER(name) = ?', [strtolower(trim($row['category']))])->first();
        $supplier = Supplier::whereRaw('LOWER(name) = ?', [strtolower(trim($row['supplier']))])->first();

        $quantity = isset($row['stock_quantity']) && $row['stock_quantity'] !== null
            ? (int) $row['stock_quantity']
            : 0;

        $discountPercentage = isset($row['discount_percentage']) && $row['discount_percentage'] !== null
            ? (float) $row['discount_percentage']
            : null;
        $discountPrice = $discountPercentage
            ? round(((float) $row['regular_price']) * (1 - $discountPercentage / 100), 2)
            : null;

        $product = new Product();
        $product->fill([
            'type' => 'simple',
            'supplier_id' => $supplier->id,
            'name' => $row['name'],
            'sku' => ! empty($row['sku']) ? $row['sku'] : $this->skuGenerator->next(),
            'product_code' => $row['product_code'] ?? null,
            'regular_price' => $row['regular_price'],
            'discount_percentage' => $discountPercentage,
            'discount_price' => $discountPrice,
            'sale_price' => $row['sale_price'] ?? null,
            'purchase_price' => $row['purchase_price'] ?? null,
            'weight' => $row['weight_kg'] ?? null,
            'material' => $row['material'] ?? null,
            'dimensions' => $row['dimensions'] ?? null,
            'badge' => $row['badge'] ?? null,
            'expected_delivery_days' => $row['expected_delivery_days'] ?? null,
            'stock_status_manual' => false,
            'stock_status' => $quantity > 0 ? 'in_stock' : 'out_of_stock',
            // Always draft, regardless of the sheet's status column: imported products have
            // no cover image yet, and publishing one would show broken/placeholder art on
            // the storefront. The admin publishes manually after adding images.
            'status' => 'draft',
            'cover_image_url' => '',
            'published_at' => now(),
        ]);
        $product->slug = $this->generateSlug($row['name']);
        $product->save();

        $product->categories()->attach($category->id);
        $product->description()->create([
            'short_description' => $row['short_description'],
            'content' => $row['story'] ?? null,
        ]);
        $product->inventory()->create(['quantity' => $quantity]);

        $this->imported++;

        return null;
    }

    private function generateSlug(string $name): string
    {
        $slugBase = Str::slug($name);
        $slug = $slugBase;
        $suffix = 2;

        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$slugBase}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
