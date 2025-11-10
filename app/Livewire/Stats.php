<?php

namespace App\Livewire;

use App\Models\Announcement;
use App\Models\Proposal;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Stats extends Component
{
    public $stats;
    public $chartData;

    public function mount()
    {
        $user = auth()->user();

        if ($user->isClient()) {
            $this->stats = [
                'announcements' => $user->announcements()->count(),
                'proposals_received' => Proposal::whereHas('announcement', fn($q) => $q->where('user_id', $user->id))->count(),
                'avg_proposals' => round(Proposal::whereHas('announcement', fn($q) => $q->where('user_id', $user->id))->count() / max($user->announcements()->count(), 1), 1),
                'total_views' => $user->announcements()->sum('views_count'),
            ];
        } else {
            $this->stats = [
                'proposals_sent' => $user->proposals()->count(),
                'proposals_accepted' => $user->proposals()->where('status', 'accepted')->count(),
                'conversion_rate' => $user->proposals()->count() > 0 ? round(($user->proposals()->where('status', 'accepted')->count() / $user->proposals()->count()) * 100, 1) : 0,
                'profile_views' => $user->profile_views,
            ];
        }

        $this->loadChartData();
    }

    public function loadChartData()
    {
        $user = auth()->user();
        $last7Days = collect(range(6, 0))->map(fn($days) => now()->subDays($days)->format('Y-m-d'));

        if ($user->isClient()) {
            $this->chartData = $last7Days->map(function($date) use ($user) {
                return [
                    'date' => $date,
                    'count' => Proposal::whereHas('announcement', fn($q) => $q->where('user_id', $user->id))
                        ->whereDate('created_at', $date)
                        ->count()
                ];
            });
        } else {
            $this->chartData = $last7Days->map(function($date) use ($user) {
                return [
                    'date' => $date,
                    'count' => $user->proposals()->whereDate('created_at', $date)->count()
                ];
            });
        }
    }

    public function render()
    {
        return view('livewire.stats')->layout('layouts.app', [
            'title' => 'Statystyki - WebFreelance',
        ]);
    }
}

