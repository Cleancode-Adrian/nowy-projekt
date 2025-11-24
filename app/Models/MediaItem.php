<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'disk',
        'path',
        'filename',
        'extension',
        'size',
        'is_image',
        'width',
        'height',
        'mime_type',
        'webp_path',
        'alt_text',
        'tags',
    ];

    protected $casts = [
        'is_image' => 'boolean',
        'tags' => 'array',
    ];

    public function getTagsListAttribute(): string
    {
        if (empty($this->tags)) {
            return '';
        }

        return implode(', ', $this->tags);
    }
}

