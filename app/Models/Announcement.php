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

    /**
     * Klucze cache'a strony głównej. Muszą być zgodne z App\Livewire\HomePage -
     * rozjazd oznacza, że statystyki nie odświeżają się po dodaniu ogłoszenia.
     */
    public const HOME_CACHE_KEYS = [
        'home.categories',
        'home.featured',
        'home.stats.v2',
    ];

    protected static function booted()
    {
        static::saved(fn () => static::forgetHomeCache());
        static::deleted(fn () => static::forgetHomeCache());

        // qualify_lead (GA4): zlecenie zaakceptowane przez admina. Raz na zlecenie,
        // niezależnie od tego, którą ścieżką panelu zostało zatwierdzone.
        static::updated(function (Announcement $announcement) {
            if ($announcement->wasChanged('is_approved') && $announcement->is_approved && !$announcement->qualify_tracked_at) {
                $announcement->trackQualifiedLead();
            }
        });
    }

    /**
     * Wysyła qualify_lead przez GA4 Measurement Protocol (admin zatwierdza zlecenie
     * na serwerze - przeglądarki zleceniodawcy przy tym nie ma). Flagę ustawiamy
     * zawsze, żeby ponowne zatwierdzenie nie wysłało drugiego zdarzenia.
     */
    public function trackQualifiedLead(): void
    {
        \App\Support\Ga4MeasurementProtocol::send($this->ga_client_id, 'qualify_lead', [
            'lead_type' => 'zlecenie',
            'item_category' => $this->category?->slug,
            'value' => 0,
            'currency' => 'PLN',
        ]);

        $this->forceFill(['qualify_tracked_at' => now()])->saveQuietly();
    }

    public static function forgetHomeCache(): void
    {
        foreach (self::HOME_CACHE_KEYS as $key) {
            Cache::forget($key);
        }
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
        'ga_client_id',
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
            'lead_tracked_at' => 'datetime',
            'qualify_tracked_at' => 'datetime',
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

