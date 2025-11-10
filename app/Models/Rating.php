<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'announcement_id',
        'rater_id',
        'rated_id',
        'rating',
        'comment',
        'is_approved',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    public function rated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    protected static function booted()
    {
        static::created(function (Rating $rating) {
            $rating->updateUserAverage();
        });

        static::updated(function (Rating $rating) {
            $rating->updateUserAverage();
        });

        static::deleted(function (Rating $rating) {
            $rating->updateUserAverage();
        });
    }

    protected function updateUserAverage()
    {
        $user = User::find($this->rated_id);
        if ($user) {
            // Tylko zaakceptowane opinie
            $average = Rating::where('rated_id', $this->rated_id)
                ->where('is_approved', true)
                ->avg('rating');
            $count = Rating::where('rated_id', $this->rated_id)
                ->where('is_approved', true)
                ->count();

            $user->update([
                'average_rating' => round($average, 2),
                'ratings_count' => $count,
            ]);
        }
    }
}

