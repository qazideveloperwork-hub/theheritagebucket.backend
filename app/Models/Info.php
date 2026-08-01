<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Info extends Model
{
    protected $fillable = ['title'];

    public function variations(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariation::class, 'product_info')->withPivot('sort_order');
    }
}
