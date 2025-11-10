<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Leaderboard extends Component
{
    public $topFreelancers;
    public $period = 'all'; // all, month, week
    public $sortBy = 'rating'; // rating, projects, earnings

    public function mount()
    {
        $this->loadLeaderboard();
    }

    public function updatedPeriod()
    {
        $this->loadLeaderboard();
    }

    public function updatedSortBy()
    {
        $this->loadLeaderboard();
    }

    public function loadLeaderboard()
    {
        $query = User::select([
                'id', 'name', 'email', 'avatar', 'bio', 'role', 'is_verified',
                'experience_level', 'average_rating', 'ratings_count',
                'completed_projects', 'profile_views'
            ])
            ->where('role', 'freelancer')
            ->where('is_approved', true)
            ->with('badges:id,slug,name,icon,color,description');

        switch ($this->sortBy) {
            case 'projects':
                $query->orderBy('completed_projects', 'desc')
                      ->orderBy('average_rating', 'desc');
                break;
            case 'rating':
                $query->where('ratings_count', '>=', 3)
                      ->orderBy('average_rating', 'desc')
                      ->orderBy('ratings_count', 'desc');
                break;
        }

        $this->topFreelancers = $query->limit(20)->get();
    }

    public function render()
    {
        return view('livewire.leaderboard')->layout('layouts.app', [
            'title' => 'Ranking Freelancerów - WebFreelance',
            'description' => 'Najlepsi freelancerzy na platformie WebFreelance',
        ]);
    }
}

