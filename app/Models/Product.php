<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'type',
        'supplier_id',
        'name',
        'slug',
        'sku',
        'product_code',
        'regular_price',
        'discount_price',
        'discount_percentage',
        'sale_price',
        'purchase_price',
        'stock_status',
        'stock_status_manual',
        'cover_image_url',
        'cover_image_id',
        'floating_image_url',
        'floating_image_id',
        'artisan_name',
        'artisan_bio',
        'artisan_image_url',
        'artisan_image_id',
        'dimensions',
        'material',
        'weight',
        'care_instructions',
        'expected_delivery_days',
        'badge',
        'is_featured',
        'is_new_arrival',
        'is_coming_soon',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'regular_price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'purchase_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'care_instructions' => 'array',
            'is_featured' => 'boolean',
            'is_new_arrival' => 'boolean',
            'is_coming_soon' => 'boolean',
            'stock_status_manual' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_image_id');
    }

    public function floatingImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'floating_image_id');
    }

    public function artisanImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'artisan_image_id');
    }

    public function galleryMedia(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'product_media')->withPivot('sort_order')->orderByPivot('sort_order');
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class, 'product_colors');
    }

    public function colorMedia(): HasMany
    {
        return $this->hasMany(ProductColorMedia::class)->orderBy('sort_order');
    }

    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_related', 'product_id', 'related_product_id')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function infos(): BelongsToMany
    {
        return $this->belongsToMany(Info::class, 'product_infos')->withPivot('sort_order')->orderByPivot('sort_order');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function description(): HasOne
    {
        return $this->hasOne(ProductDescription::class);
    }

    public function seo(): HasOne
    {
        return $this->hasOne(ProductSeo::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function shippingCost(): HasOne
    {
        return $this->hasOne(ProductShippingCost::class);
    }

    public function isVariable(): bool
    {
        return $this->type === 'variable';
    }

    protected function coverImageUrl(): Attribute
    {
        return Attribute::make(get: fn (?string $value) => static::resolveMediaUrl($value));
    }

    protected function floatingImageUrl(): Attribute
    {
        return Attribute::make(get: fn (?string $value) => static::resolveMediaUrl($value));
    }

    protected function artisanImageUrl(): Attribute
    {
        return Attribute::make(get: fn (?string $value) => static::resolveMediaUrl($value));
    }

    public static function resolveMediaUrl(?string $value): ?string
    {
        if (blank($value) || Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return Setting::mediaBaseUrl().'/'.ltrim($value, '/');
    }
}
