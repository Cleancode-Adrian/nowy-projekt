<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'announcement_id',
        'proposal_id',
        'client_id',
        'freelancer_id',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ContractMilestone::class)->orderBy('position');
    }

    public function isParticipant(int $userId): bool
    {
        return $this->client_id === $userId || $this->freelancer_id === $userId;
    }

    public function progressPercent(): int
    {
        $total = $this->milestones->count();

        if ($total === 0) {
            return 0;
        }

        $approved = $this->milestones->where('status', 'approved')->count();

        return (int) round(($approved / $total) * 100);
    }

    public function refreshStatus(): void
    {
        if ($this->status !== 'active') {
            return;
        }

        $total = $this->milestones->count();
        $approved = $this->milestones->where('status', 'approved')->count();

        if ($total > 0 && $total === $approved) {
            $this->update(['status' => 'completed', 'completed_at' => now()]);
        }
    }
}
