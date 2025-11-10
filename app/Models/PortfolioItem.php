<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image',
        'images',
        'url',
        'technologies',
        'completed_at',
        'is_featured',
        'order',
    ];

    protected $casts = [
        'images' => 'array',
        'technologies' => 'array',
        'completed_at' => 'date',
        'is_featured' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('completed_at', 'desc');
    }
}

