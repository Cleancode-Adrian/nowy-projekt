<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'announcement_id',
        'user_id',
        'price',
        'currency',
        'delivery_days',
        'description',
        'status',
        'accepted_at',
        'rejected_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected $appends = ['formatted_price'];

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    public function accept()
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    public function reject()
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);
    }
}

