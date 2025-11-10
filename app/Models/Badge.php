<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'icon',
        'color',
        'requirement_value',
        'requirement_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withTimestamps()
            ->withPivot('earned_at');
    }

    public function checkEligibility(User $user): bool
    {
        return match($this->slug) {
            'top-freelancer' => $user->completed_projects >= 20 && $user->average_rating >= 4.5,
            'verified-pro' => $user->is_verified && $user->linkedin_url && $user->experience_level === 'senior',
            'rising-star' => $user->completed_projects >= 10 && $user->created_at->diffInMonths(now()) <= 3,
            'highly-rated' => $user->average_rating >= 4.8 && $user->ratings_count >= 10,
            'fast-responder' => true, // TODO: Track response time
            'trusted' => $user->is_verified && $user->completed_projects >= 5,
            default => false,
        };
    }
}

