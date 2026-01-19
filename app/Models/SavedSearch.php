<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedSearch extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'search',
        'category_id',
        'budget_min',
        'budget_max',
        'hourly_rate_min',
        'hourly_rate_max',
        'tag_ids',
        'is_urgent',
        'notify_on_match',
        'last_notified_at',
    ];

    protected $casts = [
        'tag_ids' => 'array',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'hourly_rate_min' => 'decimal:2',
        'hourly_rate_max' => 'decimal:2',
        'is_urgent' => 'boolean',
        'notify_on_match' => 'boolean',
        'last_notified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Znajdź ogłoszenia pasujące do zapisanego wyszukiwania
     */
    public function findMatchingAnnouncements()
    {
        $query = Announcement::published();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        if ($this->budget_min) {
            $query->where('budget_max', '>=', $this->budget_min);
        }

        if ($this->budget_max) {
            $query->where('budget_min', '<=', $this->budget_max);
        }

        if ($this->hourly_rate_min) {
            $query->where(function($q) {
                $q->where('hourly_rate_max', '>=', $this->hourly_rate_min)
                  ->orWhereNull('hourly_rate_max');
            });
        }

        if ($this->hourly_rate_max) {
            $query->where(function($q) {
                $q->where('hourly_rate_min', '<=', $this->hourly_rate_max)
                  ->orWhereNull('hourly_rate_min');
            });
        }

        if ($this->tag_ids && !empty($this->tag_ids)) {
            $query->whereHas('tags', fn($q) => $q->whereIn('tags.id', $this->tag_ids));
        }

        if ($this->is_urgent !== null) {
            $query->where('is_urgent', $this->is_urgent);
        }

        // Wyklucz ogłoszenia, które były już powiadomione
        if ($this->last_notified_at) {
            $query->where('created_at', '>', $this->last_notified_at);
        }

        return $query->latest()->get();
    }
}
