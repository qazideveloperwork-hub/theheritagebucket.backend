<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'whatsapp_number',
        'contact_email',
        'jazzcash_number',
        'jazzcash_account_title',
        'bank_name',
        'bank_account_number',
        'bank_account_title',
        'max_quantity_per_product',
        'base_url',
    ];

    public static function current(): self
    {
        return static::first() ?? static::create([]);
    }

    public static function mediaBaseUrl(): string
    {
        return rtrim(static::current()->base_url ?: config('app.url'), '/');
    }
}
