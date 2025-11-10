<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class UserProfile extends Component
{
    public User $user;
    public $portfolioItems;
    public $ratings;
    public $stats;

    public function mount(User $user)
    {
        $this->user = $user->load('badges');

        if (!$this->user->isFreelancer() && !$this->user->isClient()) {
            abort(404);
        }

        if (auth()->check() && auth()->id() !== $this->user->id) {
            $this->user->incrementProfileViews();
        }

        // Check and award new badges
        if ($this->user->isFreelancer()) {
            $this->user->checkAndAwardBadges();
        }

        $this->loadData();
    }

    public function loadData()
    {
        $this->portfolioItems = $this->user->portfolioItems()
            ->where('is_featured', true)
            ->orWhere('user_id', $this->user->id)
            ->ordered()
            ->limit(6)
            ->get();

        // Tylko zaakceptowane opinie
        $this->ratings = $this->user->ratingsReceived()
            ->where('is_approved', true)
            ->with(['rater', 'announcement'])
            ->latest()
            ->limit(5)
            ->get();

        $this->stats = [
            'projects' => $this->user->completed_projects,
            'rating' => $this->user->average_rating,
            'reviews' => $this->user->ratings_count,
            'views' => $this->user->profile_views,
        ];
    }

    public function render()
    {
        return view('livewire.user-profile')->layout('layouts.app', [
            'title' => $this->user->name . ' - ' . ($this->user->isFreelancer() ? 'Freelancer' : 'Klient') . ' - Projekciarz.pl',
            'description' => $this->user->bio ?? 'Profil użytkownika ' . $this->user->name,
        ]);
    }
}

