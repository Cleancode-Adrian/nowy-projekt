<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_approved',
        'is_verified',
        'phone',
        'company',
        'bio',
        'avatar',
        'skills',
        'experience_level',
        'linkedin_url',
        'github_url',
        'website',
        'verification_document',
        'verified_at',
        'average_rating',
        'ratings_count',
        'completed_projects',
        'profile_views',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
            'is_verified' => 'boolean',
            'skills' => 'array',
            'verified_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isFreelancer(): bool
    {
        return $this->role === 'freelancer';
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function savedAnnouncements()
    {
        return $this->belongsToMany(Announcement::class, 'saved_announcements')
            ->withTimestamps();
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'rated_id');
    }

    public function portfolioItems()
    {
        return $this->hasMany(PortfolioItem::class)->ordered();
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function savedSearches()
    {
        return $this->hasMany(SavedSearch::class);
    }

    public function incrementProfileViews(): void
    {
        $this->increment('profile_views');
    }

    public function incrementCompletedProjects(): void
    {
        $this->increment('completed_projects');
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withTimestamps()
            ->withPivot('earned_at');
    }

    public function checkAndAwardBadges(): void
    {
        $allBadges = Badge::where('is_active', true)->get();

        foreach ($allBadges as $badge) {
            if ($badge->checkEligibility($this) && !$this->badges->contains($badge->id)) {
                $this->badges()->attach($badge->id, ['earned_at' => now()]);
            }
        }
    }

    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class, 'author_id');
    }

    /**
     * Get masked phone number for public display
     * Shows first 6 characters and masks the rest with XXX
     */
    public function getMaskedPhoneAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }

        $phone = $this->phone;
        $length = strlen($phone);

        if ($length <= 6) {
            // If phone is too short, show only first 2 characters
            return substr($phone, 0, 2) . str_repeat('X', $length - 2);
        }

        // Show first 6 characters, mask the rest
        return substr($phone, 0, 6) . str_repeat('X', $length - 6);
    }
}
