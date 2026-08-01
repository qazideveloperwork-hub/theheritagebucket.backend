<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Size extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }
}
