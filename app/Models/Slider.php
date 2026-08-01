<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slider extends Model
{
    // Known `type` values used across the slider/banner admin screens.
    // Only home_banner is wired up to a screen so far; the others are reserved
    // for upcoming homepage sections (categories, new arrivals, artisan story).
    public const TYPE_HOME_BANNER = 'home_banner';

    public const TYPE_HOME_CATEGORY = 'home_category';

    public const TYPE_HOME_NEW_ARRIVAL = 'home_new_arrival';

    public const TYPE_ARTISAN_STORY = 'artisan_story';

    public const TYPE_FAVOURITE_PRODUCT = 'favourite_product';

    protected $fillable = [
        'type',
        'image_id',
        'category_id',
        'product_id',
        'heading_1',
        'heading_2',
        'description',
        'quote',
        'button_text',
        'button_url',
        'sort_order',
    ];

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
