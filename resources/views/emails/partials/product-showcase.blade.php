@php
    use App\Models\Category;
    use App\Models\Product;
    use App\Models\Slider;

    $storefrontUrl = rtrim(config('services.storefront.url'), '/');

    $categorySlides = Slider::ofType(Slider::TYPE_HOME_CATEGORY)
        ->with(['category', 'image'])
        ->whereHas('category', fn ($query) => $query->where('status', 'publish'))
        ->orderBy('sort_order')
        ->get();

    $showcaseCategories = $categorySlides;
    if ($showcaseCategories->isEmpty()) {
        $showcaseCategories = Category::where('status', 'publish')
            ->withCount('products')
            ->with(['products' => fn ($query) => $query->where('status', 'publish')->latest()->limit(1)])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->take(4)
            ->get();
    }

    $newArrivals = Slider::ofType(Slider::TYPE_HOME_NEW_ARRIVAL)
        ->whereHas('product', fn ($query) => $query->where('status', 'publish'))
        ->with('product')
        ->orderBy('sort_order')
        ->get()
        ->pluck('product');
    if ($newArrivals->isEmpty()) {
        $newArrivals = Product::where('status', 'publish')->where('is_new_arrival', true)->latest()->take(4)->get();
    }

    $featured = Slider::ofType(Slider::TYPE_FAVOURITE_PRODUCT)
        ->whereHas('product', fn ($query) => $query->where('status', 'publish'))
        ->with('product')
        ->orderBy('sort_order')
        ->get()
        ->pluck('product');
    if ($featured->isEmpty()) {
        $featured = Product::where('status', 'publish')->where('is_featured', true)->inRandomOrder()->take(4)->get();
    }

    // Resolved to plain [category, image] pairs and padded out to exactly 4
    // slots below — a row with fewer than 4 real <td>s at width="25%" gets
    // its cells stretched by some renderers to fill the row, which visibly
    // gaps the items apart instead of leaving trailing blank space.
    $categoryTiles = $showcaseCategories->take(4)->map(function ($slide) {
        $category = $slide->category ?? $slide;

        if (! $category) {
            return null;
        }

        return [
            'category' => $category,
            'image' => $slide->image?->mediumUrl() ?? $slide->products?->first()?->cover_image_url ?? null,
        ];
    })->filter()->values();
    while ($categoryTiles->count() < 4) {
        $categoryTiles->push(null);
    }

    $newArrivalTiles = $newArrivals->take(4)->values();
    while ($newArrivalTiles->count() < 4 && $newArrivalTiles->isNotEmpty()) {
        $newArrivalTiles->push(null);
    }

    $featuredTiles = $featured->take(4)->values();
    while ($featuredTiles->count() < 4 && $featuredTiles->isNotEmpty()) {
        $featuredTiles->push(null);
    }
@endphp

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-family: Helvetica, Arial, sans-serif;">
    <tr>
        <td style="padding-top:8px; border-top:1px solid #e6d9c5;">
            <p style="font-family: Georgia, serif; font-size:18px; color:#523923; margin:24px 0 16px;">Discover More</p>
        </td>
    </tr>

    @if ($categoryTiles->filter()->isNotEmpty())
        <tr>
            <td style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#a5360d; padding-bottom:8px;">Shop by Category</td>
        </tr>
        <tr>
            <td>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        @foreach ($categoryTiles as $tile)
                            <td width="25%" valign="top" style="padding:4px;">
                                @if ($tile)
                                    <a href="{{ $storefrontUrl }}/shop?categories[]={{ $tile['category']->id }}" style="text-decoration:none;">
                                        @if ($tile['image'])
                                            <img src="{{ $tile['image'] }}" alt="{{ $tile['category']->name }}" width="120" height="120" style="width:120px; height:120px; max-width:100%; object-fit:cover; border:1px solid #e6d9c5; display:block;">
                                        @endif
                                        <p style="font-size:12px; color:#1b1b18; text-align:center; margin:6px 0 0;">{{ $tile['category']->name }}</p>
                                    </a>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </table>
            </td>
        </tr>
    @endif

    @if ($newArrivalTiles->filter()->isNotEmpty())
        <tr>
            <td style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#a5360d; padding:20px 0 8px;">New Arrivals</td>
        </tr>
        <tr>
            <td>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        @foreach ($newArrivalTiles as $product)
                            <td width="25%" valign="top" style="padding:4px;">
                                @if ($product)
                                    <a href="{{ $storefrontUrl }}/products/{{ $product->slug }}" style="text-decoration:none;">
                                        <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" width="120" height="120" style="width:120px; height:120px; max-width:100%; object-fit:cover; border:1px solid #e6d9c5; display:block;">
                                        <p style="font-size:12px; color:#1b1b18; margin:6px 0 0;">{{ $product->name }}</p>
                                        <p style="font-size:12px; color:#a5360d; font-weight:bold; margin:2px 0 0;">Rs. {{ number_format((float) ($product->discount_price ?? $product->regular_price)) }}</p>
                                    </a>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </table>
            </td>
        </tr>
    @endif

    @if ($featuredTiles->filter()->isNotEmpty())
        <tr>
            <td style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#a5360d; padding:20px 0 8px;">Artisan Favourites</td>
        </tr>
        <tr>
            <td>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        @foreach ($featuredTiles as $product)
                            <td width="25%" valign="top" style="padding:4px;">
                                @if ($product)
                                    <a href="{{ $storefrontUrl }}/products/{{ $product->slug }}" style="text-decoration:none;">
                                        <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" width="120" height="120" style="width:120px; height:120px; max-width:100%; object-fit:cover; border:1px solid #e6d9c5; display:block;">
                                        <p style="font-size:12px; color:#1b1b18; margin:6px 0 0;">{{ $product->name }}</p>
                                        <p style="font-size:12px; color:#a5360d; font-weight:bold; margin:2px 0 0;">Rs. {{ number_format((float) ($product->discount_price ?? $product->regular_price)) }}</p>
                                    </a>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </table>
            </td>
        </tr>
    @endif

    <tr>
        <td align="center" style="padding-top:20px;">
            <a href="{{ $storefrontUrl }}/shop" style="display:inline-block; background-color:#a5360d; color:#ffffff; text-decoration:none; font-size:13px; font-weight:bold; padding:12px 28px;">Shop the Collection</a>
        </td>
    </tr>
</table>
