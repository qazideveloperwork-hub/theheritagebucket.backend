<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'size_id',
        'sku',
        'regular_price',
        'discount_price',
        'discount_percentage',
        'sale_price',
        'purchase_price',
        'stock_status',
        'stock_status_manual',
        'weight',
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
            'stock_status_manual' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    public function info(): BelongsToMany
    {
        return $this->belongsToMany(Info::class, 'product_info')->withPivot('sort_order')->orderByPivot('sort_order');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }
}
