<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSeo extends Model
{
    protected $table = 'product_seo';

    protected $fillable = [
        'product_id',
        'meta_title',
        'meta_description',
        'robots_index',
        'robots_follow',
        'canonical_url',
        'redirect_url',
    ];

    protected function casts(): array
    {
        return [
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
