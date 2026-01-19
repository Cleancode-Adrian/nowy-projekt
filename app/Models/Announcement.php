<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted()
    {
        static::saved(function ($announcement) {
            Cache::forget('home.categories');
            Cache::forget('home.featured');
            Cache::forget('home.stats');
        });

        static::deleted(function ($announcement) {
            Cache::forget('home.categories');
            Cache::forget('home.featured');
            Cache::forget('home.stats');
        });
    }

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'budget_min',
        'budget_max',
        'budget_currency',
        'hourly_rate_min',
        'hourly_rate_max',
        'deadline',
        'location',
        'status',
        'is_approved',
        'is_urgent',
        'rejection_reason',
        'approved_at',
        'views_count',
        'proposals_count',
    ];

    protected function casts(): array
    {
        return [
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
            'hourly_rate_min' => 'decimal:2',
            'hourly_rate_max' => 'decimal:2',
            'is_approved' => 'boolean',
            'is_urgent' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'announcement_tag');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function attachments()
    {
        return $this->hasMany(AnnouncementAttachment::class);
    }

    public function scopePublished($query)
    {
        return $query->whereIn('status', ['published', 'closed'])->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending')->where('is_approved', false);
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function getBudgetRangeAttribute(): string
    {
        if ($this->budget_min && $this->budget_max) {
            return "{$this->budget_min}-{$this->budget_max} {$this->budget_currency}";
        }
        return "Do uzgodnienia";
    }
}

