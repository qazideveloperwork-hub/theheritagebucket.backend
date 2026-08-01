<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Info;
use App\Models\Media;
use App\Models\Product;
use App\Models\Size;
use App\Models\Supplier;
use App\Services\SkuGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private readonly SkuGenerator $skuGenerator)
    {
    }

    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('categories', 'variations.size', 'variations.inventory', 'inventory')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->string('q');
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('category'), fn ($query) => $query->whereHas('categories', fn ($query) => $query->where('categories.id', $request->input('category'))))
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->input('status')),
                fn ($query) => $query->where('status', '!=', 'deleted')
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::where('status', '!=', 'deleted')->orderBy('name')->get();

        $statusCounts = [
            'all' => Product::where('status', '!=', 'deleted')->count(),
            'publish' => Product::where('status', 'publish')->count(),
            'draft' => Product::where('status', 'draft')->count(),
            'deleted' => Product::where('status', 'deleted')->count(),
        ];

        return view('product.list', compact('products', 'categories', 'statusCounts'));
    }

    public function create(): View
    {
        return view('product.create', [
            'product' => null,
            'categories' => Category::where('status', '!=', 'deleted')->orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'colors' => Color::orderBy('name')->get(),
            'sizes' => Size::orderBy('sort_order')->orderBy('name')->get(),
            'infos' => Info::orderBy('title')->get(),
            'mediaLibrary' => Media::with('uploader')->latest()->limit(500)->get(),
            'relatableProducts' => Product::where('status', '!=', 'deleted')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->validationRules());

        $product = new Product();
        $product->slug = $this->generateSlug($data['name']);
        $product->published_at = $data['published_at'] ?? now();

        $this->applyProductAttributes($product, $data, $request);
        $product->save();

        $this->syncRelations($product, $data, $request);

        return redirect()->route('products.list')->with('success', "Product \"{$product->name}\" was created.");
    }

    public function edit(Product $product): View
    {
        $product->load(['categories', 'colors', 'colorMedia.media', 'infos', 'relatedProducts', 'variations.info', 'variations.inventory', 'inventory', 'description', 'seo', 'images', 'coverImage', 'floatingImage', 'artisanImage', 'galleryMedia']);

        return view('product.create', [
            'product' => $product,
            'categories' => Category::where('status', '!=', 'deleted')->orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'colors' => Color::orderBy('name')->get(),
            'sizes' => Size::orderBy('sort_order')->orderBy('name')->get(),
            'infos' => Info::orderBy('title')->get(),
            'mediaLibrary' => Media::with('uploader')->latest()->limit(500)->get(),
            'relatableProducts' => Product::where('status', '!=', 'deleted')->where('id', '!=', $product->id)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate($this->validationRules($product));

        if ($data['name'] !== $product->name) {
            $product->slug = $this->generateSlug($data['name'], $product);
        }

        if (! empty($data['published_at'])) {
            $product->published_at = $data['published_at'];
        }

        $this->applyProductAttributes($product, $data, $request);
        $product->save();

        $this->syncRelations($product, $data, $request);

        return redirect()->route('products.list')->with('success', "Product \"{$product->name}\" was updated.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->update(['status' => 'deleted']);

        return redirect()->route('products.list')->with('success', "Product \"{$product->name}\" was deleted.");
    }

    private function validationRules(?Product $product = null): array
    {
        return [
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:categories,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'type' => ['required', 'in:simple,variable'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:publish,draft,deleted'],
            'published_at' => ['nullable', 'date'],
            'colors' => ['nullable', 'array'],
            'colors.*' => ['exists:colors,id'],
            'color_media_ids' => ['nullable', 'array'],
            'color_media_ids.*' => ['array'],
            'color_media_ids.*.*' => ['exists:media,id'],
            'product_code' => ['nullable', 'string', 'max:100'],
            'expected_delivery_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'info' => ['nullable', 'array'],
            'info.*' => ['exists:infos,id'],
            'related_product_ids' => ['nullable', 'array'],
            'related_product_ids.*' => ['exists:products,id'],

            // Simple product fields
            'regular_price' => ['required_if:type,simple', 'nullable', 'numeric', 'min:0'],
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'stock_status_manual' => ['sometimes', 'boolean'],
            'stock_status' => ['nullable', 'in:in_stock,out_of_stock'],
            'weight' => ['nullable', 'numeric', 'min:0'],

            // Variable product fields
            'variations' => ['required_if:type,variable', 'array'],
            'variations.*.size_id' => ['required_if:type,variable', 'exists:sizes,id'],
            'variations.*.regular_price' => ['required_if:type,variable', 'numeric', 'min:0'],
            'variations.*.discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'variations.*.sale_price' => ['nullable', 'numeric', 'min:0'],
            'variations.*.purchase_price' => ['nullable', 'numeric', 'min:0'],
            'variations.*.stock_quantity' => ['nullable', 'integer', 'min:0'],
            'variations.*.stock_status_manual' => ['sometimes'],
            'variations.*.stock_status' => ['nullable', 'in:in_stock,out_of_stock'],
            'variations.*.weight' => ['nullable', 'numeric', 'min:0'],
            'variations.*.info' => ['nullable', 'array'],
            'variations.*.info.*' => ['exists:infos,id'],

            'short_description' => ['required', 'string', 'max:255'],
            'story' => ['nullable', 'string'],
            'artisan_name' => ['nullable', 'string', 'max:255'],
            'artisan_bio' => ['nullable', 'string'],
            'artisan_image' => ['nullable', 'image', 'max:4096'],
            'artisan_image_media_id' => ['nullable', 'exists:media,id'],
            'dimensions' => ['nullable', 'string', 'max:255'],
            'material' => ['nullable', 'string', 'max:255'],
            'care_instructions' => ['nullable', 'string'],
            'badge' => ['nullable', 'string', 'max:100'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_new_arrival' => ['sometimes', 'boolean'],
            'is_coming_soon' => ['sometimes', 'boolean'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'floating_image' => ['nullable', 'image', 'max:4096'],
            'gallery.*' => ['nullable', 'image', 'max:4096'],
            'cover_image_media_id' => [$product ? 'nullable' : 'required', 'exists:media,id'],
            'floating_image_media_id' => ['nullable', 'exists:media,id'],
            'gallery_media_ids' => ['nullable', 'array'],
            'gallery_media_ids.*' => ['exists:media,id'],

            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_robots_index' => ['sometimes', 'boolean'],
            'seo_robots_follow' => ['sometimes', 'boolean'],
            'seo_canonical_url' => ['nullable', 'url', 'max:2048'],
            'seo_redirect_url' => ['nullable', 'url', 'max:2048'],
        ];
    }

    private function generateSlug(string $name, ?Product $ignore = null): string
    {
        $slugBase = Str::slug($name);
        $slug = $slugBase;
        $suffix = 2;

        while (Product::where('slug', $slug)->when($ignore, fn ($query) => $query->where('id', '!=', $ignore->id))->exists()) {
            $slug = "{$slugBase}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    private function applyProductAttributes(Product $product, array $data, Request $request): void
    {
        $isSimple = $data['type'] === 'simple';

        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('products', 'public');
            $product->cover_image_url = Storage::disk('public')->url($coverPath);
            $product->cover_image_id = null;
        } elseif (! empty($data['cover_image_media_id'])) {
            $coverMedia = Media::find($data['cover_image_media_id']);
            if ($coverMedia) {
                $product->cover_image_url = $coverMedia->url();
                $product->cover_image_id = $coverMedia->id;
            }
        }

        if ($request->hasFile('floating_image')) {
            $floatingPath = $request->file('floating_image')->store('products', 'public');
            $product->floating_image_url = Storage::disk('public')->url($floatingPath);
            $product->floating_image_id = null;
        } elseif (! empty($data['floating_image_media_id'])) {
            $floatingMedia = Media::find($data['floating_image_media_id']);
            if ($floatingMedia) {
                $product->floating_image_url = $floatingMedia->url();
                $product->floating_image_id = $floatingMedia->id;
            }
        }

        if ($request->hasFile('artisan_image')) {
            $artisanPath = $request->file('artisan_image')->store('products/artisans', 'public');
            $product->artisan_image_url = Storage::disk('public')->url($artisanPath);
            $product->artisan_image_id = null;
        } elseif (! empty($data['artisan_image_media_id'])) {
            $artisanMedia = Media::find($data['artisan_image_media_id']);
            if ($artisanMedia) {
                $product->artisan_image_url = $artisanMedia->url();
                $product->artisan_image_id = $artisanMedia->id;
            }
        }

        $careInstructions = collect(preg_split('/\r\n|\r|\n/', (string) $request->input('care_instructions')))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        $discountPercentage = $isSimple ? ($data['discount_percentage'] ?? null) : null;
        $discountPrice = $discountPercentage
            ? round($data['regular_price'] * (1 - $discountPercentage / 100), 2)
            : null;

        $product->fill([
            'supplier_id' => $data['supplier_id'] ?? null,
            'type' => $data['type'],
            'name' => $data['name'],
            'sku' => $isSimple ? ($product->sku ?: $this->skuGenerator->next()) : null,
            'product_code' => $data['product_code'] ?? null,
            'expected_delivery_days' => $data['expected_delivery_days'] ?? null,
            'regular_price' => $isSimple ? $data['regular_price'] : null,
            'discount_percentage' => $discountPercentage,
            'discount_price' => $discountPrice,
            'sale_price' => $isSimple ? ($data['sale_price'] ?? null) : null,
            'purchase_price' => $isSimple ? ($data['purchase_price'] ?? null) : null,
            'weight' => $isSimple ? ($data['weight'] ?? null) : null,
            'artisan_name' => $data['artisan_name'] ?? null,
            'artisan_bio' => $data['artisan_bio'] ?? null,
            'dimensions' => $data['dimensions'] ?? null,
            'material' => $data['material'] ?? null,
            'care_instructions' => $careInstructions,
            'badge' => $data['badge'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_new_arrival' => $request->boolean('is_new_arrival'),
            'is_coming_soon' => $request->boolean('is_coming_soon'),
            'status' => $data['status'],
        ]);

        if ($isSimple) {
            $currentQuantity = isset($data['stock_quantity'])
                ? (int) $data['stock_quantity']
                : ($product->exists ? (int) optional($product->inventory)->quantity : 0);

            $product->stock_status_manual = $request->boolean('stock_status_manual');
            $product->stock_status = $product->stock_status_manual
                ? ($data['stock_status'] ?? 'in_stock')
                : $this->deriveStockStatus($currentQuantity);
        } else {
            $product->stock_status_manual = false;
            $product->stock_status = 'in_stock';
        }
    }

    private function syncRelations(Product $product, array $data, Request $request): void
    {
        $product->categories()->sync($data['categories'] ?? []);
        $product->colors()->sync($data['colors'] ?? []);

        // Wholesale replace, same "no diffing" convention as variations/gallery elsewhere in this file.
        $product->colorMedia()->delete();
        foreach ($data['color_media_ids'] ?? [] as $colorId => $mediaIds) {
            foreach (array_values($mediaIds) as $order => $mediaId) {
                $product->colorMedia()->create([
                    'color_id' => $colorId,
                    'media_id' => $mediaId,
                    'sort_order' => $order,
                ]);
            }
        }

        $infoSyncData = collect($data['info'] ?? [])
            ->mapWithKeys(fn ($infoId, $order) => [$infoId => ['sort_order' => $order]])
            ->all();
        $product->infos()->sync($infoSyncData);

        $relatedSyncData = collect($data['related_product_ids'] ?? [])
            ->reject(fn ($relatedId) => (int) $relatedId === $product->id)
            ->values()
            ->mapWithKeys(fn ($relatedId, $order) => [$relatedId => ['sort_order' => $order]])
            ->all();
        $product->relatedProducts()->sync($relatedSyncData);

        $product->description()->updateOrCreate(['product_id' => $product->id], [
            'short_description' => $data['short_description'] ?? null,
            'content' => $data['story'] ?? null,
        ]);

        $product->seo()->updateOrCreate(['product_id' => $product->id], [
            'meta_title' => $data['seo_title'] ?? null,
            'meta_description' => $data['seo_description'] ?? null,
            'robots_index' => $request->boolean('seo_robots_index', true),
            'robots_follow' => $request->boolean('seo_robots_follow', true),
            'canonical_url' => $data['seo_canonical_url'] ?? null,
            'redirect_url' => $data['seo_redirect_url'] ?? null,
        ]);

        if ($data['type'] === 'simple') {
            // If quantity was submitted from this form, it's authoritative; otherwise
            // keep whatever the Inventory screen currently has (or 0 for a brand-new row).
            $quantity = isset($data['stock_quantity'])
                ? (int) $data['stock_quantity']
                : (int) optional($product->inventory)->quantity;

            $product->inventory()->updateOrCreate([], ['quantity' => $quantity]);
        }

        // Variations are replaced wholesale rather than diffed — simpler and safe here since
        // nothing else in the app references a variation by id across requests. Stock lives in
        // a separate inventory row per variation, so as a fallback (only used if this submission
        // didn't include a quantity for a given size) we carry the old quantity forward by size_id
        // instead of silently zeroing it out when the old variation row is deleted. SKUs are
        // server-generated, so an unchanged size keeps its existing SKU instead of getting a new one.
        $previousVariations = $product->variations()
            ->with('inventory')
            ->get()
            ->keyBy('size_id');

        $product->variations()->delete();

        if ($data['type'] === 'variable') {
            foreach ($data['variations'] as $index => $variationData) {
                $manual = $request->boolean("variations.{$index}.stock_status_manual");
                $previous = $previousVariations->get($variationData['size_id']);
                $quantity = isset($variationData['stock_quantity'])
                    ? (int) $variationData['stock_quantity']
                    : (int) optional($previous?->inventory)->quantity;

                $variationDiscountPercentage = $variationData['discount_percentage'] ?? null;
                $variationDiscountPrice = $variationDiscountPercentage
                    ? round($variationData['regular_price'] * (1 - $variationDiscountPercentage / 100), 2)
                    : null;

                $variation = $product->variations()->create([
                    'size_id' => $variationData['size_id'],
                    'sku' => $previous?->sku ?? $this->skuGenerator->next(),
                    'regular_price' => $variationData['regular_price'],
                    'discount_percentage' => $variationDiscountPercentage,
                    'discount_price' => $variationDiscountPrice,
                    'sale_price' => $variationData['sale_price'] ?? null,
                    'purchase_price' => $variationData['purchase_price'] ?? null,
                    'stock_status_manual' => $manual,
                    'stock_status' => $manual
                        ? ($variationData['stock_status'] ?? 'in_stock')
                        : $this->deriveStockStatus($quantity),
                    'weight' => $variationData['weight'] ?? null,
                ]);

                $variation->inventory()->create(['quantity' => $quantity]);

                if (! empty($variationData['info'])) {
                    $syncData = collect($variationData['info'])
                        ->mapWithKeys(fn ($infoId, $order) => [$infoId => ['sort_order' => $order]])
                        ->all();
                    $variation->info()->sync($syncData);
                }
            }
        }

        if ($request->hasFile('gallery')) {
            $startIndex = $product->images()->count();

            foreach ($request->file('gallery') as $offset => $file) {
                $path = $file->store('products/gallery', 'public');
                $product->images()->create([
                    'url' => Storage::disk('public')->url($path),
                    'sort_order' => $startIndex + $offset,
                ]);
            }
        }

        // Gallery images picked from the Media Library are tracked separately via the
        // product_media pivot (see Product::galleryMedia()), replaced wholesale each save
        // to match the "no diffing" convention used for variations/gallery uploads above.
        $gallerySyncData = collect($data['gallery_media_ids'] ?? [])
            ->mapWithKeys(fn ($mediaId, $order) => [$mediaId => ['sort_order' => $order]])
            ->all();
        $product->galleryMedia()->sync($gallerySyncData);
    }

    private function deriveStockStatus(int $quantity): string
    {
        return $quantity > 0 ? 'in_stock' : 'out_of_stock';
    }

}
