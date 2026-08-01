<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductShippingCost extends Model
{
    protected $table = 'product_shipping_cost';

    protected $fillable = ['product_id', 'shipping_cost'];

    protected function casts(): array
    {
        return [
            'shipping_cost' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
