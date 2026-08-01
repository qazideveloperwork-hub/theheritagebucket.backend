@extends('layout')
@section('title', $product ? 'Edit Product' : 'Create Product')
@section('content')
    @php
        $isEdit = (bool) $product;
        $existingColorIds = $isEdit ? $product->colors->pluck('id')->all() : [];
        $existingCategoryIds = $isEdit ? $product->categories->pluck('id')->all() : [];
        $existingProductInfoIds = $isEdit ? $product->infos->pluck('id')->all() : [];
        $existingRelatedProductIds = $isEdit ? $product->relatedProducts->pluck('id')->all() : [];
        $existingVariations = $isEdit ? $product->variations->map(fn ($variation) => [
            'size_id' => $variation->size_id,
            'sku' => $variation->sku,
            'regular_price' => $variation->regular_price,
            'discount_percentage' => $variation->discount_percentage,
            'sale_price' => $variation->sale_price,
            'purchase_price' => $variation->purchase_price,
            'stock_quantity' => optional($variation->inventory)->quantity ?? 0,
            'stock_status_manual' => $variation->stock_status_manual,
            'stock_status' => $variation->stock_status,
            'weight' => $variation->weight,
            'info' => $variation->info->pluck('id')->all(),
        ])->all() : [];
        $existingCareInstructions = $isEdit ? implode("\n", $product->care_instructions ?? []) : '';
        $publishedAtDefault = ($isEdit && $product->published_at) ? $product->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i');
    @endphp
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Products', 'url' => route('products.list')],
        ['label' => $isEdit ? 'Edit Product' : 'Create Product'],
    ]])
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#523923]">{{ $isEdit ? 'Edit Product' : 'Create Product' }}</h1>
    </div>

    @if ($errors->any())
        <div id="form-errors" class="mb-6 scroll-mt-24 rounded-2xl border border-[#e3b3a3] bg-[#fdecea] px-4 py-3 text-sm text-[#7c2d12]">
            <p class="font-semibold">Please fix the following:</p>
            <ul class="mt-1 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <script>
            document.getElementById('form-errors').scrollIntoView({ behavior: 'smooth', block: 'start' });
        </script>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr_300px] lg:items-start">
    <form action="{{ $isEdit ? route('products.update', $product) : route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 min-w-0" id="product-form">
        <div hidden>
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif
        </div>

        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <h2 class="text-lg font-semibold text-[#523923]">Basic information</h2>
            <div class="mt-4 grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Product name <span class="text-[#b91c1c]">*</span></label>
                    <input type="text" id="product-name" name="name" value="{{ old('name', $product->name ?? '') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" placeholder="Indigo Floral Vase" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Categories <span class="text-[#b91c1c]">*</span></label>
                    <div class="flex flex-wrap gap-2 rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] p-3">
                        @forelse ($categories as $category)
                            <label class="flex cursor-pointer items-center gap-2 rounded-full border border-[#d9c9ae] bg-white px-4 py-2 text-sm has-[:checked]:border-[#7c2d12] has-[:checked]:bg-[#fdf1e6]">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}" @checked(collect(old('categories', $existingCategoryIds))->contains($category->id)) class="rounded border-[#d9c9ae]" />
                                {{ $category->name }}
                            </label>
                        @empty
                            <p class="text-sm text-[#8a6c56]">No categories yet — add one first.</p>
                        @endforelse
                    </div>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Supplier <span class="text-[#b91c1c]">*</span></label>
                    <div class="flex gap-2">
                        <select name="supplier_id" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">
                            <option value="">Select a supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected(old('supplier_id', $product->supplier_id ?? null) == $supplier->id)>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('suppliers.create') }}" target="_blank" class="shrink-0 rounded-2xl border border-[#d9c9ae] px-4 py-3 text-sm text-[#5a4538] hover:bg-[#f1e5d5]">+ New</a>
                    </div>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Product code</label>
                    <input type="text" name="product_code" value="{{ old('product_code', $product->product_code ?? '') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" placeholder="Optional" />
                    <p class="mt-1 text-xs text-[#8a6c56]">Shared by all sizes of this product.</p>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Expected delivery (days)</label>
                    <input type="number" min="0" step="1" name="expected_delivery_days" value="{{ old('expected_delivery_days', $product->expected_delivery_days ?? '') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" placeholder="e.g. 3" />
                    <p class="mt-1 text-xs text-[#8a6c56]">Days to prepare/ship. Used to flag delayed orders.</p>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Badge</label>
                    <input type="text" name="badge" value="{{ old('badge', $product->badge ?? '') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" placeholder="Limited Edition" />
                </div>
                <div class="flex items-center gap-6 pt-7">
                    <label class="flex items-center gap-2 text-sm text-[#5a4538]">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false)) class="rounded border-[#d9c9ae]" />
                        Featured
                    </label>
                    <label class="flex items-center gap-2 text-sm text-[#5a4538]">
                        <input type="checkbox" name="is_new_arrival" value="1" @checked(old('is_new_arrival', $product->is_new_arrival ?? false)) class="rounded border-[#d9c9ae]" />
                        New arrival
                    </label>
                    <label class="flex items-center gap-2 text-sm text-[#5a4538]">
                        <input type="checkbox" name="is_coming_soon" value="1" @checked(old('is_coming_soon', $product->is_coming_soon ?? false)) class="rounded border-[#d9c9ae]" />
                        Coming soon
                    </label>
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Product type <span class="text-[#b91c1c]">*</span></label>
                    <div class="flex gap-4" id="product-type-toggle">
                        <label class="flex flex-1 cursor-pointer items-center gap-3 rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm has-[:checked]:border-[#7c2d12] has-[:checked]:bg-[#fdf1e6]">
                            <input type="radio" name="type" value="simple" @checked(old('type', $product->type ?? 'simple') === 'simple') class="text-[#7c2d12]" />
                            <span>
                                <span class="block font-medium text-[#1b1b18]">Simple</span>
                                <span class="block text-xs text-[#8a6c56]">Single price and stock. SKU is generated automatically.</span>
                            </span>
                        </label>
                        <label class="flex flex-1 cursor-pointer items-center gap-3 rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm has-[:checked]:border-[#7c2d12] has-[:checked]:bg-[#fdf1e6]">
                            <input type="radio" name="type" value="variable" @checked(old('type', $product->type ?? 'simple') === 'variable') class="text-[#7c2d12]" />
                            <span>
                                <span class="block font-medium text-[#1b1b18]">Variable</span>
                                <span class="block text-xs text-[#8a6c56]">Available in multiple sizes, each with its own auto-generated SKU, price and stock.</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="sm:col-span-2" id="simple-product-info-field">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Info Icon <span class="text-xs text-[#8a6c56]">(info popups shown to customers for this product)</span></label>
                    <div class="info-tag-input relative" data-info-tag-input id="product-info-tag-input">
                        <div class="flex flex-wrap items-center gap-2 rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] p-2 info-tag-chips">
                            <input type="text" class="info-tag-search min-w-[10rem] flex-1 border-0 bg-transparent px-2 py-1.5 text-sm outline-none" placeholder="{{ $infos->isEmpty() ? 'No info entries yet — add some in the Info Library' : 'Search and add info…' }}" autocomplete="off" @disabled($infos->isEmpty()) />
                        </div>
                        <div class="info-tag-dropdown absolute z-10 mt-1 hidden max-h-56 w-full overflow-y-auto rounded-2xl border border-[#d9c9ae] bg-white p-1 shadow-lg"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-[#523923]">Colors</h2>
                <a href="{{ route('colors.create') }}" target="_blank" class="text-xs font-medium text-[#7c2d12] underline">+ Add new color</a>
            </div>
            <p class="mt-1 text-xs text-[#8a6c56]">Tag this product with the colors it's available in.</p>
            <div class="mt-4 flex flex-wrap gap-2">
                @forelse ($colors as $color)
                    <label class="flex cursor-pointer items-center gap-2 rounded-full border border-[#d9c9ae] bg-[#faf4ec] px-4 py-2 text-sm has-[:checked]:border-[#7c2d12] has-[:checked]:bg-[#fdf1e6]">
                        <input type="checkbox" name="colors[]" value="{{ $color->id }}" class="color-checkbox rounded border-[#d9c9ae]" data-color-id="{{ $color->id }}" @checked(collect(old('colors', $existingColorIds))->contains($color->id)) />
                        @if ($color->hex_code)
                            <span class="h-3.5 w-3.5 rounded-full border border-[#d9c9ae]" style="background-color: {{ $color->hex_code }}"></span>
                        @endif
                        {{ $color->name }}
                    </label>
                @empty
                    <p class="text-sm text-[#8a6c56]">No colors yet — add one first.</p>
                @endforelse
            </div>

            @if ($colors->isNotEmpty())
                <div class="mt-5 space-y-4">
                    <p class="text-xs text-[#8a6c56]">Optional: assign photos to a color so shoppers can click a swatch and see that color's own gallery.</p>
                    @foreach ($colors as $color)
                        @php $colorMediaForThis = $isEdit ? $product->colorMedia->where('color_id', $color->id) : collect(); @endphp
                        <div class="color-image-picker rounded-2xl border border-[#e6d9c5] p-4 {{ collect(old('colors', $existingColorIds))->contains($color->id) ? '' : 'hidden' }}" data-color-id="{{ $color->id }}">
                            <p class="mb-2 text-sm font-medium text-[#5a4538]">{{ $color->name }} images</p>
                            <div id="color-images-{{ $color->id }}" class="mb-2 flex flex-wrap gap-2">
                                @foreach ($colorMediaForThis as $pcm)
                                    <span class="relative inline-block">
                                        <img src="{{ $pcm->media->thumbnailUrl() }}" alt="" class="h-12 w-12 rounded-lg object-cover" />
                                        <button type="button" class="absolute -right-1.5 -top-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-[#e46a5c] text-[10px] text-white hover:bg-[#d65847]" aria-label="Remove" onclick="this.parentElement.remove()">&times;</button>
                                        <input type="hidden" name="color_media_ids[{{ $color->id }}][]" value="{{ $pcm->media_id }}" />
                                    </span>
                                @endforeach
                            </div>
                            <button type="button" class="rounded-full bg-[#7C2D12] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#6b2610]" onclick="openMediaPicker('multiple', function (items) { addColorLibraryImages({{ $color->id }}, items); })">Choose images for {{ $color->name }}</button>
                        </div>
                    @endforeach
                </div>
                <script>
                    document.querySelectorAll('.color-checkbox').forEach(function (checkbox) {
                        checkbox.addEventListener('change', function () {
                            const picker = document.querySelector('.color-image-picker[data-color-id="' + checkbox.dataset.colorId + '"]');
                            if (picker) picker.classList.toggle('hidden', !checkbox.checked);
                        });
                    });
                </script>
            @endif
        </section>

        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <h2 class="text-lg font-semibold text-[#523923]">Related products</h2>
            <p class="mt-1 text-xs text-[#8a6c56]">Shown as "You may also like" on the product page. Leave empty to fall back to other products in the same category.</p>
            <div class="mt-4 info-tag-input relative" data-related-tag-input id="product-related-tag-input">
                <div class="flex flex-wrap items-center gap-2 rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] p-2 info-tag-chips">
                    <input type="text" class="info-tag-search min-w-[10rem] flex-1 border-0 bg-transparent px-2 py-1.5 text-sm outline-none" placeholder="{{ $relatableProducts->isEmpty() ? 'No other products yet' : 'Search and add products…' }}" autocomplete="off" @disabled($relatableProducts->isEmpty()) />
                </div>
                <div class="info-tag-dropdown absolute z-10 mt-1 hidden max-h-56 w-full overflow-y-auto rounded-2xl border border-[#d9c9ae] bg-white p-1 shadow-lg"></div>
            </div>
        </section>

        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6" id="simple-pricing-section">
            <h2 class="text-lg font-semibold text-[#523923]">Pricing & stock</h2>
            <div class="mt-4 grid gap-5 sm:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Regular price (Rs.) <span class="text-[#b91c1c]">*</span></label>
                    <input type="number" step="0.01" min="0" name="regular_price" id="simple-regular-price" value="{{ old('regular_price', $product->regular_price ?? '') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Discount (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="discount_percentage" id="simple-discount-percentage" value="{{ old('discount_percentage', $product->discount_percentage ?? '') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                    <p class="mt-1 text-xs text-[#8a6c56]" id="simple-discount-preview"></p>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Sale price (Rs.) <span class="text-xs text-[#8a6c56]">(shows a Sale badge)</span></label>
                    <input type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price', $product->sale_price ?? '') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Purchase price (Rs.) <span class="text-xs text-[#8a6c56]">(cost, internal only)</span></label>
                    <input type="number" step="0.01" min="0" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price ?? '') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Stock quantity</label>
                    <input type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity', $isEdit ? (optional($product->inventory)->quantity ?? 0) : 0) }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12] simple-stock-qty" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Weight (kg)</label>
                    <input type="number" step="0.01" min="0" name="weight" value="{{ old('weight', $product->weight ?? '') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div class="flex flex-col justify-end">
                    <label class="mb-2 flex items-center gap-2 text-sm text-[#5a4538]">
                        <input type="checkbox" name="stock_status_manual" value="1" @checked(old('stock_status_manual', $product->stock_status_manual ?? false)) class="rounded border-[#d9c9ae] simple-stock-manual" />
                        Manually set stock status
                    </label>
                    <select name="stock_status" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12] simple-stock-status" @if (! old('stock_status_manual', $product->stock_status_manual ?? false)) disabled @endif>
                        <option value="in_stock" @selected(old('stock_status', $product->stock_status ?? 'in_stock') === 'in_stock')>In stock</option>
                        <option value="out_of_stock" @selected(old('stock_status', $product->stock_status ?? 'in_stock') === 'out_of_stock')>Out of stock</option>
                    </select>
                </div>
            </div>
            <p class="mt-3 text-xs text-[#8a6c56]">Stock status is set automatically from quantity unless you check "Manually set stock status".</p>
        </section>

        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6" id="variable-section">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-[#523923]">Variations <span class="text-[#b91c1c]">*</span></h2>
                    <p class="mt-1 text-xs text-[#8a6c56]">Each size gets its own auto-generated SKU, price and stock.</p>
                </div>
                <button type="button" id="add-variation" class="rounded-full bg-[#7C2D12] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">+ Add variation</button>
            </div>

            <div class="mt-3 flex items-center justify-between text-xs text-[#8a6c56]">
                <span id="variations-count">0 variations</span>
                <span>
                    <button type="button" id="expand-all-variations" class="font-medium text-[#7c2d12] underline">Expand</button>
                    /
                    <button type="button" id="collapse-all-variations" class="font-medium text-[#7c2d12] underline">Collapse</button>
                </span>
            </div>

            <div id="variations-list" class="mt-4 space-y-4"></div>
        </section>

        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <h2 class="text-lg font-semibold text-[#523923]">Media</h2>
            <div class="mt-4 grid items-stretch gap-5 sm:grid-cols-3">
                <div class="flex flex-col">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Feature image</label>
                    <img id="cover-image-preview" @if (! old('cover_image_media_id') && $isEdit && $product->cover_image_url) src="{{ $product->cover_image_url }}" @endif alt="Feature image preview" class="mb-2 h-16 w-16 rounded-xl object-cover {{ (! old('cover_image_media_id') && $isEdit && $product->cover_image_url) ? '' : 'hidden' }}" />
                    <input type="hidden" name="cover_image_media_id" id="cover-image-media-id" value="{{ old('cover_image_media_id', $product->cover_image_id ?? '') }}" />
                    <button type="button" class="mt-auto w-full rounded-full bg-[#7C2D12] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]" onclick="openMediaPicker('single', function (items) { setSingleImage('cover-image-media-id', 'cover-image-preview', items[0]); })">Choose from Library</button>
                    @if ($isEdit)
                        <p class="mt-1 text-xs text-[#8a6c56]">Leave unchanged to keep the current image.</p>
                    @endif
                </div>
                <div class="flex flex-col">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Floating image <span class="text-xs text-[#8a6c56]">(hover-swap)</span></label>
                    <img id="floating-image-preview" @if (! old('floating_image_media_id') && $isEdit && $product->floating_image_url) src="{{ $product->floating_image_url }}" @endif alt="Floating image preview" class="mb-2 h-16 w-16 rounded-xl object-cover {{ (! old('floating_image_media_id') && $isEdit && $product->floating_image_url) ? '' : 'hidden' }}" />
                    <input type="hidden" name="floating_image_media_id" id="floating-image-media-id" value="{{ old('floating_image_media_id', $product->floating_image_id ?? '') }}" />
                    <button type="button" class="mt-auto w-full rounded-full bg-[#7C2D12] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]" onclick="openMediaPicker('single', function (items) { setSingleImage('floating-image-media-id', 'floating-image-preview', items[0]); })">Choose from Library</button>
                    @if ($isEdit && $product->floating_image_url)
                        <p class="mt-1 text-xs text-[#8a6c56]">Leave unchanged to keep the current image.</p>
                    @endif
                </div>
                <div class="flex flex-col">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Gallery images</label>
                    @if ($isEdit && $product->images->isNotEmpty())
                        <div class="mb-2 flex flex-wrap gap-2">
                            @foreach ($product->images as $image)
                                <img src="{{ $image->url }}" alt="Gallery image" class="h-12 w-12 rounded-lg object-cover" />
                            @endforeach
                        </div>
                    @endif
                    <div id="gallery-library-selected" class="mb-2 flex flex-wrap gap-2">
                        @if ($isEdit)
                            @foreach ($product->galleryMedia as $media)
                                <span class="relative inline-block">
                                    <img src="{{ $media->thumbnailUrl() }}" alt="{{ $media->alt_text }}" class="h-12 w-12 rounded-lg object-cover" />
                                    <button type="button" class="absolute -right-1.5 -top-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-[#e46a5c] text-[10px] text-white hover:bg-[#d65847]" aria-label="Remove" onclick="this.parentElement.remove()">&times;</button>
                                    <input type="hidden" name="gallery_media_ids[]" value="{{ $media->id }}" />
                                </span>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" class="mt-auto w-full rounded-full bg-[#7C2D12] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]" onclick="openMediaPicker('multiple', function (items) { addGalleryLibraryImages(items); })">Choose from Library</button>
                    @if ($isEdit && $product->images->isNotEmpty())
                        <p class="mt-1 text-xs text-[#8a6c56]">Library picks are added alongside the existing gallery images shown above.</p>
                    @endif
                </div>
            </div>
        </section>

        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <h2 class="text-lg font-semibold text-[#523923]">Description</h2>
            <div class="mt-4 space-y-5">
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Short description <span class="text-[#b91c1c]">*</span></label>
                    <textarea id="short_description" name="short_description" rows="4" maxlength="255" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">{{ old('short_description', $product->description->short_description ?? '') }}</textarea>
                    <p id="short-description-count" class="mt-1 text-xs text-[#8a6c56]"></p>
                    <script>
                        (function () {
                            var textarea = document.getElementById('short_description');
                            var counter = document.getElementById('short-description-count');
                            var max = 255;

                            function updateCount() {
                                counter.textContent = (max - textarea.value.length) + ' characters remaining';
                            }

                            textarea.addEventListener('input', updateCount);
                            updateCount();
                        })();
                    </script>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Story</label>
                    <textarea id="story" name="story" rows="8">{{ old('story', $product->description->content ?? '') }}</textarea>
                </div>
            </div>
        </section>

        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <h2 class="text-lg font-semibold text-[#523923]">Artisan</h2>
            <div class="mt-4 grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Artisan name</label>
                    <input type="text" name="artisan_name" value="{{ old('artisan_name', $product->artisan_name ?? '') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div class="flex flex-col">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Artisan photo</label>
                    <img id="artisan-image-preview" @if (! old('artisan_image_media_id') && $isEdit && $product->artisan_image_url) src="{{ $product->artisan_image_url }}" @endif alt="Artisan photo preview" class="mb-2 h-16 w-16 rounded-full object-cover {{ (! old('artisan_image_media_id') && $isEdit && $product->artisan_image_url) ? '' : 'hidden' }}" />
                    <input type="hidden" name="artisan_image_media_id" id="artisan-image-media-id" value="{{ old('artisan_image_media_id', $product->artisan_image_id ?? '') }}" />
                    <button type="button" class="mt-auto w-full rounded-full bg-[#7C2D12] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]" onclick="openMediaPicker('single', function (items) { setSingleImage('artisan-image-media-id', 'artisan-image-preview', items[0]); })">Choose from Library</button>
                    @if ($isEdit && $product->artisan_image_url)
                        <p class="mt-1 text-xs text-[#8a6c56]">Leave unchanged to keep the current photo.</p>
                    @endif
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Artisan bio</label>
                    <textarea name="artisan_bio" rows="3" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">{{ old('artisan_bio', $product->artisan_bio ?? '') }}</textarea>
                </div>
            </div>
        </section>

        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <h2 class="text-lg font-semibold text-[#523923]">Specifications</h2>
            <div class="mt-4 grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Dimensions</label>
                    <input type="text" name="dimensions" value="{{ old('dimensions', $product->dimensions ?? '') }}" placeholder="H: 12&quot; | D: 8&quot; | W: 2.4kg" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Material</label>
                    <input type="text" name="material" value="{{ old('material', $product->material ?? '') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Care instructions <span class="text-xs text-[#8a6c56]">(one per line)</span></label>
                    <textarea name="care_instructions" rows="4" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">{{ old('care_instructions', $existingCareInstructions) }}</textarea>
                </div>
            </div>
        </section>

        <details class="group rounded-[28px] border border-[#e6d9c5] bg-white" open>
            <summary class="flex cursor-pointer list-none items-center justify-between rounded-[28px] px-6 py-4 [&::-webkit-details-marker]:hidden [&::marker]:content-none">
                <h2 class="text-lg font-semibold text-[#523923]">SEO section</h2>
                <svg class="h-4 w-4 shrink-0 text-[#8a6c56] transition-transform duration-200 group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
            </summary>

            <div class="border-t border-[#e6d9c5] p-6">
                <div class="flex gap-1 border-b border-[#e6d9c5]" id="seo-tabs">
                    <button type="button" data-seo-tab="general" class="seo-tab-btn border-b-2 border-[#7c2d12] px-4 py-2 text-sm font-medium text-[#7c2d12]">SEO General</button>
                    <button type="button" data-seo-tab="advanced" class="seo-tab-btn border-b-2 border-transparent px-4 py-2 text-sm font-medium text-[#8a6c56] hover:text-[#5a4538]">Advanced</button>
                </div>

                <div data-seo-panel="general" class="mt-5 space-y-5">
                    <div>
                        <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Search result example</label>
                        <div class="rounded-2xl border border-[#e6d9c5] bg-[#faf4ec] p-4">
                            <p id="seo-preview-title" class="truncate text-base text-[#1a0dab]">{{ old('seo_title', $product->seo->meta_title ?? null) ?: (old('name', $product->name ?? '') ?: 'Product title') }}</p>
                            <p id="seo-preview-url" class="text-sm text-[#006621]">theheritagebasket.com/products/{{ Str::slug(old('name', $product->name ?? '')) ?: 'product-slug' }}</p>
                            <p id="seo-preview-description" class="mt-1 text-sm text-[#545454]">{{ old('seo_description', $product->seo->meta_description ?? null) ?: 'Add a short description or SEO description to see a preview here.' }}</p>
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#5a4538]">Title</label>
                        <input type="text" id="seo_title" name="seo_title" value="{{ old('seo_title', $product->seo->meta_title ?? '') }}" maxlength="255" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" placeholder="{{ old('name', $product->name ?? '') ?: 'Defaults to product name' }}" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#5a4538]">Description</label>
                        <textarea id="seo_description" name="seo_description" rows="3" maxlength="500" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">{{ old('seo_description', $product->seo->meta_description ?? '') }}</textarea>
                    </div>
                </div>

                <div data-seo-panel="advanced" class="mt-5 hidden space-y-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-[#5a4538]">Index / No Index</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 text-sm text-[#5a4538]">
                                    <input type="radio" name="seo_robots_index" value="1" @checked(old('seo_robots_index', $isEdit ? ($product->seo->robots_index ?? true) : true)) class="text-[#7c2d12]" />
                                    Index
                                </label>
                                <label class="flex items-center gap-2 text-sm text-[#5a4538]">
                                    <input type="radio" name="seo_robots_index" value="0" @checked(! old('seo_robots_index', $isEdit ? ($product->seo->robots_index ?? true) : true)) class="text-[#7c2d12]" />
                                    No Index
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-[#5a4538]">Follow / No Follow</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 text-sm text-[#5a4538]">
                                    <input type="radio" name="seo_robots_follow" value="1" @checked(old('seo_robots_follow', $isEdit ? ($product->seo->robots_follow ?? true) : true)) class="text-[#7c2d12]" />
                                    Follow
                                </label>
                                <label class="flex items-center gap-2 text-sm text-[#5a4538]">
                                    <input type="radio" name="seo_robots_follow" value="0" @checked(! old('seo_robots_follow', $isEdit ? ($product->seo->robots_follow ?? true) : true)) class="text-[#7c2d12]" />
                                    No Follow
                                </label>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#5a4538]">Canonical URL</label>
                        <input type="url" id="seo_canonical_url" name="seo_canonical_url" value="{{ old('seo_canonical_url', $product->seo->canonical_url ?? '') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" placeholder="https://theheritagebasket.com/products/..." />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#5a4538]">Redirect URL</label>
                        <input type="url" name="seo_redirect_url" value="{{ old('seo_redirect_url', $product->seo->redirect_url ?? '') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                    </div>
                </div>
            </div>
        </details>

    </form>

        <aside class="space-y-4 lg:sticky lg:top-[71px]">
            <div class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold text-[#523923]">Publish</h2>

                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Status <span class="text-[#b91c1c]">*</span></label>
                    <select name="status" form="product-form" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">
                        <option value="publish" @selected(old('status', $product->status ?? 'draft') === 'publish')>Publish</option>
                        <option value="draft" @selected(old('status', $product->status ?? 'draft') === 'draft')>Draft</option>
                        @if ($isEdit && $product->status === 'deleted')
                            {{-- Only shown for a product that's already deleted, so opening it here doesn't
                                 silently flip it back to Publish on save. Use the Delete button to delete. --}}
                            <option value="deleted" @selected(old('status', $product->status) === 'deleted')>Deleted</option>
                        @endif
                    </select>
                </div>

                <div class="mb-5">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Published</label>
                    <input type="datetime-local" name="published_at" form="product-form" value="{{ old('published_at', $publishedAtDefault) }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>

                <button type="submit" form="product-form" class="w-full rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">{{ $isEdit ? 'Update product' : 'Create product' }}</button>
                <a href="{{ route('products.list') }}" class="mt-2 block rounded-full border border-[#d9c9ae] px-6 py-3 text-center text-sm text-[#5a4538] hover:bg-[#f1e5d5]">Cancel</a>

                @if ($isEdit)
                    <form id="delete-product-form" action="{{ route('products.destroy', $product) }}" method="POST" class="mt-4 border-t border-[#e6d9c5] pt-4">
                        @csrf
                        @method('DELETE')
                        <button type="submit" id="delete-product-btn" class="w-full rounded-full border border-[#e3b3a3] px-6 py-3 text-sm font-semibold text-[#b91c1c] hover:bg-[#fdecea]">Delete product</button>
                    </form>
                @endif
            </div>
        </aside>
    </div>

    {{-- Template for a single variation row, cloned by JS. Kept out of the form's tab order via a <template> tag. --}}
    <template id="variation-row-template">
        <div class="variation-row rounded-2xl border border-[#e6d9c5] bg-[#fffaf5]">
            <div class="variation-summary flex cursor-pointer items-center justify-between gap-3 px-5 py-3">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-[#523923] variation-summary-label">New variation</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="remove-variation p-1.5 text-[#7c2d12] hover:text-[#5a1d0c]" aria-label="Remove variation">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.808a2.75 2.75 0 0 0 2.741-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" /></svg>
                    </button>
                    <button type="button" class="variation-toggle rounded-full border border-[#d9c9ae] p-1.5 text-[#8a6c56] hover:bg-[#f1e5d5]" aria-label="Expand or collapse variation">
                        <svg class="variation-chevron h-4 w-4 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                    </button>
                </div>
            </div>
            <div class="variation-body hidden border-t border-[#e6d9c5] p-5">
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Size</label>
                    <select class="variation-size w-full rounded-xl border border-[#d9c9ae] bg-white px-3 py-2.5 text-sm outline-none focus:border-[#7c2d12]">
                        <option value="">Select size</option>
                        @foreach ($sizes as $size)
                            <option value="{{ $size->id }}" data-name="{{ $size->name }}">{{ $size->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Stock quantity</label>
                    <input type="number" min="0" class="variation-qty w-full rounded-xl border border-[#d9c9ae] bg-white px-3 py-2.5 text-sm outline-none focus:border-[#7c2d12]" value="0" />
                </div>
                <div>
                    <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Regular price (Rs.) <span class="normal-case text-[#b91c1c]">*</span></label>
                    <input type="number" step="0.01" min="0" class="variation-regular-price w-full rounded-xl border border-[#d9c9ae] bg-white px-3 py-2.5 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Discount (%)</label>
                    <input type="number" step="0.01" min="0" max="100" class="variation-discount-percentage w-full rounded-xl border border-[#d9c9ae] bg-white px-3 py-2.5 text-sm outline-none focus:border-[#7c2d12]" />
                    <p class="mt-1 text-xs text-[#8a6c56] variation-discount-preview"></p>
                </div>
                <div>
                    <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Sale price (Rs.) <span class="normal-case text-[#8a6c56]">(shows a Sale badge)</span></label>
                    <input type="number" step="0.01" min="0" class="variation-sale-price w-full rounded-xl border border-[#d9c9ae] bg-white px-3 py-2.5 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Purchase price (Rs.)</label>
                    <input type="number" step="0.01" min="0" class="variation-purchase-price w-full rounded-xl border border-[#d9c9ae] bg-white px-3 py-2.5 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Weight (kg)</label>
                    <input type="number" step="0.01" min="0" class="variation-weight w-full rounded-xl border border-[#d9c9ae] bg-white px-3 py-2.5 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 flex items-center gap-2 text-xs text-[#5a4538]">
                        <input type="checkbox" class="variation-stock-manual rounded border-[#d9c9ae]" />
                        Manually set stock status
                    </label>
                    <select class="variation-stock-status w-full rounded-xl border border-[#d9c9ae] bg-white px-3 py-2.5 text-sm outline-none focus:border-[#7c2d12]" disabled>
                        <option value="in_stock">In stock</option>
                        <option value="out_of_stock">Out of stock</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Info popups <span class="normal-case text-[#8a6c56]">(shown to customers for this size)</span></label>
                    <div class="info-tag-input relative" data-info-tag-input>
                        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-[#d9c9ae] bg-white p-2 info-tag-chips">
                            <input type="text" class="info-tag-search min-w-[8rem] flex-1 border-0 bg-transparent px-2 py-1.5 text-xs outline-none" placeholder="{{ $infos->isEmpty() ? 'No info entries yet' : 'Search and add info…' }}" autocomplete="off" @disabled($infos->isEmpty()) />
                        </div>
                        <div class="info-tag-dropdown absolute z-10 mt-1 hidden max-h-56 w-full overflow-y-auto rounded-xl border border-[#d9c9ae] bg-white p-1 text-xs shadow-lg"></div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </template>

    {{-- This block is intentionally ordered before the CKEditor CDN script below: it wires up
         the variation add/remove/expand and SEO controls, none of which depend on CKEditor.
         Keeping it first means those controls still work immediately even if the CDN script
         is slow or unreachable, instead of silently never attaching their click handlers. --}}
    <script>
        (function () {
            const typeInputs = document.querySelectorAll('#product-type-toggle input[name="type"]');
            const simpleSection = document.getElementById('simple-pricing-section');
            const simpleProductInfoField = document.getElementById('simple-product-info-field');
            const variableSection = document.getElementById('variable-section');
            const variationsList = document.getElementById('variations-list');
            const template = document.getElementById('variation-row-template');
            const productNameInput = document.getElementById('product-name');
            const addVariationBtn = document.getElementById('add-variation');
            const variationsCountEl = document.getElementById('variations-count');
            const expandAllBtn = document.getElementById('expand-all-variations');
            const collapseAllBtn = document.getElementById('collapse-all-variations');
            const oldVariations = @json(old('variations', $existingVariations));
            const hasValidationErrors = @json($errors->any());
            const ALL_INFOS = @json($infos->map(fn ($info) => ['id' => $info->id, 'title' => $info->title])->values());
            const ALL_RELATABLE_PRODUCTS = @json($relatableProducts->map(fn ($related) => ['id' => $related->id, 'title' => $related->name])->values());

            let variationCounter = 0;

            // Reusable chip/tag-style multi-select: click the input to open a dropdown of
            // available info entries, click one to add it as a removable chip. Each chip
            // carries a hidden `${fieldName}[]` input so selection order becomes sort_order.
            function createInfoTagInput(container, fieldName, selectedIds, options) {
                options = options || ALL_INFOS;
                selectedIds = (selectedIds || []).map(String);
                const chipsWrap = container.querySelector('.info-tag-chips');
                const searchInput = container.querySelector('.info-tag-search');
                const dropdown = container.querySelector('.info-tag-dropdown');

                function selectedSet() {
                    return new Set(Array.from(container.querySelectorAll('input.info-tag-value')).map((el) => el.value));
                }

                function addChip(info) {
                    const chip = document.createElement('span');
                    chip.className = 'inline-flex items-center gap-1.5 rounded-full border border-[#d9c9ae] bg-[#faf4ec] px-3 py-1.5 text-xs text-[#5a4538]';

                    const label = document.createElement('span');
                    label.textContent = info.title;
                    chip.appendChild(label);

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'text-[#8a6c56] hover:text-[#7c2d12]';
                    removeBtn.setAttribute('aria-label', `Remove ${info.title}`);
                    removeBtn.textContent = '×';
                    removeBtn.addEventListener('click', () => chip.remove());
                    chip.appendChild(removeBtn);

                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.className = 'info-tag-value';
                    hidden.name = `${fieldName}[]`;
                    hidden.value = info.id;
                    chip.appendChild(hidden);

                    chipsWrap.insertBefore(chip, searchInput);
                }

                function renderDropdown(filterText) {
                    const selected = selectedSet();
                    const term = (filterText || '').toLowerCase();
                    const matches = options.filter((info) => !selected.has(String(info.id)) && info.title.toLowerCase().includes(term));

                    dropdown.innerHTML = '';

                    if (!matches.length) {
                        const empty = document.createElement('p');
                        empty.className = 'px-3 py-2 text-xs text-[#8a6c56]';
                        empty.textContent = options.length ? 'No matches.' : 'Nothing available yet.';
                        dropdown.appendChild(empty);
                    } else {
                        matches.forEach((info) => {
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.className = 'block w-full rounded-xl px-3 py-2 text-left hover:bg-[#fdf1e6]';
                            item.textContent = info.title;
                            item.addEventListener('click', () => {
                                addChip(info);
                                searchInput.value = '';
                                dropdown.classList.add('hidden');
                                searchInput.blur();
                            });
                            dropdown.appendChild(item);
                        });
                    }

                    dropdown.classList.remove('hidden');
                }

                selectedIds.forEach((id) => {
                    const info = options.find((candidate) => String(candidate.id) === id);
                    if (info) addChip(info);
                });

                if (!searchInput.disabled) {
                    searchInput.addEventListener('focus', () => renderDropdown(searchInput.value));
                    searchInput.addEventListener('input', () => renderDropdown(searchInput.value));
                    searchInput.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape') dropdown.classList.add('hidden');
                    });
                }
            }

            document.addEventListener('click', (event) => {
                document.querySelectorAll('.info-tag-dropdown:not(.hidden)').forEach((dropdown) => {
                    const container = dropdown.closest('[data-info-tag-input], [data-related-tag-input]');
                    if (container && !container.contains(event.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            });

            const productInfoTagInput = document.getElementById('product-info-tag-input');
            if (productInfoTagInput) {
                createInfoTagInput(productInfoTagInput, 'info', @json(old('info', $existingProductInfoIds)));
            }

            const productRelatedTagInput = document.getElementById('product-related-tag-input');
            if (productRelatedTagInput) {
                createInfoTagInput(productRelatedTagInput, 'related_product_ids', @json(old('related_product_ids', $existingRelatedProductIds)), ALL_RELATABLE_PRODUCTS);
            }

            function syncTypeVisibility() {
                const selected = document.querySelector('#product-type-toggle input[name="type"]:checked');
                const type = selected ? selected.value : 'simple';
                simpleSection.classList.toggle('hidden', type !== 'simple');
                variableSection.classList.toggle('hidden', type !== 'variable');

                simpleSection.querySelectorAll('input, select').forEach((el) => {
                    el.disabled = type !== 'simple';
                });

                if (simpleProductInfoField) {
                    simpleProductInfoField.classList.toggle('hidden', type !== 'simple');
                    simpleProductInfoField.querySelectorAll('input').forEach((el) => {
                        el.disabled = type !== 'simple';
                    });
                }
            }

            function updateVariationsCount() {
                const count = variationsList.querySelectorAll('.variation-row').length;
                variationsCountEl.textContent = `${count} ${count === 1 ? 'variation' : 'variations'}`;
            }

            function setRowExpanded(row, expanded) {
                row.querySelector('.variation-body').classList.toggle('hidden', !expanded);
                row.querySelector('.variation-chevron').classList.toggle('rotate-180', expanded);
            }

            function addVariationRow(data, expanded) {
                data = data || {};
                const fragment = template.content.cloneNode(true);
                const row = fragment.querySelector('.variation-row');
                const index = variationCounter++;

                const sizeSelect = row.querySelector('.variation-size');
                const qtyInput = row.querySelector('.variation-qty');
                const regularPriceInput = row.querySelector('.variation-regular-price');
                const discountPercentageInput = row.querySelector('.variation-discount-percentage');
                const discountPreview = row.querySelector('.variation-discount-preview');
                const salePriceInput = row.querySelector('.variation-sale-price');
                const purchasePriceInput = row.querySelector('.variation-purchase-price');
                const weightInput = row.querySelector('.variation-weight');
                const manualCheckbox = row.querySelector('.variation-stock-manual');
                const statusSelect = row.querySelector('.variation-stock-status');
                const infoTagContainer = row.querySelector('[data-info-tag-input]');
                const summaryLabel = row.querySelector('.variation-summary-label');
                const summaryBar = row.querySelector('.variation-summary');
                const toggleBtn = row.querySelector('.variation-toggle');
                const removeBtn = row.querySelector('.remove-variation');

                sizeSelect.name = `variations[${index}][size_id]`;
                qtyInput.name = `variations[${index}][stock_quantity]`;
                regularPriceInput.name = `variations[${index}][regular_price]`;
                discountPercentageInput.name = `variations[${index}][discount_percentage]`;
                salePriceInput.name = `variations[${index}][sale_price]`;
                purchasePriceInput.name = `variations[${index}][purchase_price]`;
                weightInput.name = `variations[${index}][weight]`;
                manualCheckbox.name = `variations[${index}][stock_status_manual]`;
                statusSelect.name = `variations[${index}][stock_status]`;
                if (infoTagContainer) {
                    createInfoTagInput(infoTagContainer, `variations[${index}][info]`, data.info || []);
                }

                if (data.size_id) sizeSelect.value = data.size_id;
                if (data.stock_quantity !== undefined) qtyInput.value = data.stock_quantity;
                if (data.regular_price !== undefined) regularPriceInput.value = data.regular_price;
                if (data.discount_percentage !== undefined && data.discount_percentage !== null) discountPercentageInput.value = data.discount_percentage;
                if (data.sale_price !== undefined && data.sale_price !== null) salePriceInput.value = data.sale_price;
                if (data.purchase_price !== undefined) purchasePriceInput.value = data.purchase_price;
                if (data.weight !== undefined && data.weight !== null) weightInput.value = data.weight;
                if (data.stock_status_manual) {
                    manualCheckbox.checked = true;
                    statusSelect.disabled = false;
                }
                if (data.stock_status) statusSelect.value = data.stock_status;

                function updateSummary() {
                    const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
                    const sizeName = selectedOption ? selectedOption.dataset.name : '';
                    summaryLabel.textContent = sizeName || 'New variation';
                }

                function updateDiscountPreview() {
                    const regularPrice = parseFloat(regularPriceInput.value);
                    const percentage = parseFloat(discountPercentageInput.value);
                    if (!isNaN(regularPrice) && !isNaN(percentage) && percentage > 0) {
                        const discounted = regularPrice * (1 - percentage / 100);
                        discountPreview.textContent = `Discount price: Rs. ${discounted.toFixed(2)}`;
                    } else {
                        discountPreview.textContent = '';
                    }
                }

                sizeSelect.addEventListener('change', function () {
                    updateSummary();
                });

                regularPriceInput.addEventListener('input', updateDiscountPreview);
                discountPercentageInput.addEventListener('input', updateDiscountPreview);

                manualCheckbox.addEventListener('change', function () {
                    statusSelect.disabled = !manualCheckbox.checked;
                });

                removeBtn.addEventListener('click', function (event) {
                    event.stopPropagation();
                    if (!window.confirm(`Remove variation "${summaryLabel.textContent}"?`)) {
                        return;
                    }
                    row.remove();
                    updateVariationsCount();
                });

                toggleBtn.addEventListener('click', function (event) {
                    event.stopPropagation();
                    setRowExpanded(row, row.querySelector('.variation-body').classList.contains('hidden'));
                });

                summaryBar.addEventListener('click', function () {
                    setRowExpanded(row, row.querySelector('.variation-body').classList.contains('hidden'));
                });

                updateSummary();
                updateDiscountPreview();
                setRowExpanded(row, expanded === undefined ? true : expanded);
                variationsList.appendChild(row);
                updateVariationsCount();
            }

            typeInputs.forEach((input) => input.addEventListener('change', syncTypeVisibility));

            const simpleRegularPriceInput = document.getElementById('simple-regular-price');
            const simpleDiscountPercentageInput = document.getElementById('simple-discount-percentage');
            const simpleDiscountPreview = document.getElementById('simple-discount-preview');
            if (simpleRegularPriceInput && simpleDiscountPercentageInput && simpleDiscountPreview) {
                const updateSimpleDiscountPreview = function () {
                    const regularPrice = parseFloat(simpleRegularPriceInput.value);
                    const percentage = parseFloat(simpleDiscountPercentageInput.value);
                    if (!isNaN(regularPrice) && !isNaN(percentage) && percentage > 0) {
                        const discounted = regularPrice * (1 - percentage / 100);
                        simpleDiscountPreview.textContent = `Discount price: Rs. ${discounted.toFixed(2)}`;
                    } else {
                        simpleDiscountPreview.textContent = '';
                    }
                };
                simpleRegularPriceInput.addEventListener('input', updateSimpleDiscountPreview);
                simpleDiscountPercentageInput.addEventListener('input', updateSimpleDiscountPreview);
                updateSimpleDiscountPreview();
            }

            const simpleManualCheckbox = document.querySelector('.simple-stock-manual');
            const simpleStatusSelect = document.querySelector('.simple-stock-status');
            if (simpleManualCheckbox && simpleStatusSelect) {
                simpleManualCheckbox.addEventListener('change', function () {
                    simpleStatusSelect.disabled = !simpleManualCheckbox.checked;
                });
            }

            addVariationBtn.addEventListener('click', () => addVariationRow(null, true));

            expandAllBtn.addEventListener('click', () => {
                variationsList.querySelectorAll('.variation-row').forEach((row) => setRowExpanded(row, true));
            });

            collapseAllBtn.addEventListener('click', () => {
                variationsList.querySelectorAll('.variation-row').forEach((row) => setRowExpanded(row, false));
            });

            if (oldVariations && oldVariations.length) {
                oldVariations.forEach((variation) => addVariationRow(variation, hasValidationErrors));
            } else {
                syncTypeVisibility();
                if (document.querySelector('#product-type-toggle input[value="variable"]:checked')) {
                    addVariationRow(null, true);
                }
            }
            updateVariationsCount();

            syncTypeVisibility();

            // SEO section: tab switching
            const seoTabButtons = document.querySelectorAll('.seo-tab-btn');
            seoTabButtons.forEach((btn) => {
                btn.addEventListener('click', function () {
                    const target = btn.dataset.seoTab;

                    seoTabButtons.forEach((b) => {
                        b.classList.toggle('border-[#7c2d12]', b === btn);
                        b.classList.toggle('text-[#7c2d12]', b === btn);
                        b.classList.toggle('border-transparent', b !== btn);
                        b.classList.toggle('text-[#8a6c56]', b !== btn);
                    });

                    document.querySelectorAll('[data-seo-panel]').forEach((panel) => {
                        panel.classList.toggle('hidden', panel.dataset.seoPanel !== target);
                    });
                });
            });

            // SEO section: live search-result preview
            const seoTitleInput = document.getElementById('seo_title');
            const seoDescriptionInput = document.getElementById('seo_description');
            const seoCanonicalInput = document.getElementById('seo_canonical_url');
            const seoPreviewTitle = document.getElementById('seo-preview-title');
            const seoPreviewUrl = document.getElementById('seo-preview-url');
            const seoPreviewDescription = document.getElementById('seo-preview-description');

            function slugify(value) {
                return value.toString().toLowerCase().trim()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '') || 'product-slug';
            }

            function updateSeoPreview() {
                seoPreviewTitle.textContent = seoTitleInput.value.trim() || productNameInput.value.trim() || 'Product title';
                seoPreviewUrl.textContent = `theheritagebasket.com/products/${slugify(productNameInput.value)}`;
                seoPreviewDescription.textContent = seoDescriptionInput.value.trim() || 'Add a short description or SEO description to see a preview here.';
            }

            // Auto-fill SEO title as "{Product Name} | theheritagebasket.com" and the
            // Canonical URL as ".../products/{slug}" while the admin hasn't edited them
            // manually; typing into either field stops its auto-fill.
            productNameInput.addEventListener('input', function () {
                const name = productNameInput.value.trim();

                if (!seoTitleInput.value.trim() || seoTitleInput.dataset.autoFilled === 'true') {
                    seoTitleInput.value = name ? `${name} | theheritagebasket.com` : '';
                    seoTitleInput.dataset.autoFilled = 'true';
                }

                if (!seoCanonicalInput.value.trim() || seoCanonicalInput.dataset.autoFilled === 'true') {
                    seoCanonicalInput.value = name ? `http://theheritagebasket.com/products/${slugify(name)}` : '';
                    seoCanonicalInput.dataset.autoFilled = 'true';
                }
            });

            seoTitleInput.addEventListener('input', function () {
                seoTitleInput.dataset.autoFilled = 'false';
            });

            seoCanonicalInput.addEventListener('input', function () {
                seoCanonicalInput.dataset.autoFilled = 'false';
            });

            [productNameInput, seoTitleInput, seoDescriptionInput].forEach((el) => {
                el.addEventListener('input', updateSeoPreview);
            });

            const deleteProductBtn = document.getElementById('delete-product-btn');
            if (deleteProductBtn) {
                deleteProductBtn.addEventListener('click', function (event) {
                    const name = @json($isEdit ? $product->name : '');
                    if (!window.confirm(`Delete "${name}"? It will be marked as Deleted and hidden from the product list.`)) {
                        event.preventDefault();
                    }
                });
            }
        })();
    </script>

    <script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
    <script>
        if (window.CKEDITOR) {
            // Suppress the "this version is not secure" nag banner — this is an internal
            // admin tool, not the public internet-facing surface the check is meant for.
            CKEDITOR.config.versionCheck = false;
            CKEDITOR.replace('story');
        }
    </script>

    @include('partials.media-picker')
@endsection
