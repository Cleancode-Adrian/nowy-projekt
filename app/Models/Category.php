<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'description',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function activeAnnouncements(): HasMany
    {
        return $this->hasMany(Announcement::class)
            ->where('is_approved', true)
            ->where('status', 'published');
    }
}

