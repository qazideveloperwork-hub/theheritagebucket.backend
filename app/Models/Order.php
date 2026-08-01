<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const ACTIVE_STATUSES = ['pending', 'processing', 'shipped'];

    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'email',
        'phone',
        'shipping_address',
        'city',
        'province',
        'postal_code',
        'subtotal',
        'shipping_fee',
        'total',
        'payment_method',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Order date + the slowest item's product lead time, same estimate shown
     * on the storefront cart (defaults to 3 days when a product has no lead
     * time set).
     */
    public function shippingDate(): \Illuminate\Support\Carbon
    {
        $maxLeadDays = $this->items->max(fn (OrderItem $item) => $item->product?->expected_delivery_days ?? 3);

        return $this->created_at->copy()->addDays($maxLeadDays);
    }

    public function isDelayed(): bool
    {
        if (! in_array($this->status, self::ACTIVE_STATUSES, true)) {
            return false;
        }

        return now()->greaterThan($this->shippingDate());
    }
}
