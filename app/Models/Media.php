<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'disk',
        'path',
        'variants',
        'file_name',
        'original_name',
        'mime_type',
        'extension',
        'size',
        'width',
        'height',
        'title',
        'alt_text',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'variants' => 'array',
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function variantUrl(string $size): string
    {
        $path = $this->variants[$size] ?? null;

        return $path ? Storage::disk($this->disk)->url($path) : $this->url();
    }

    public function thumbnailUrl(): string
    {
        return $this->variantUrl('thumbnail');
    }

    public function mediumUrl(): string
    {
        return $this->variantUrl('medium');
    }

    public function largeUrl(): string
    {
        return $this->variantUrl('large');
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2).' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    public function getDimensionsAttribute(): ?string
    {
        return ($this->width && $this->height) ? "{$this->width} x {$this->height}" : null;
    }

    /**
     * Every physical file this record owns: the original plus each generated variant.
     */
    public function allPaths(): array
    {
        return array_merge([$this->path], array_values($this->variants ?? []));
    }
}
