<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Rating;
use Livewire\Component;

class UserRatings extends Component
{
    public User $user;
    public $ratings;
    public $stats;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->loadRatings();
    }

    public function loadRatings()
    {
        // Tylko zaakceptowane opinie
        $this->ratings = Rating::where('rated_id', $this->user->id)
            ->where('is_approved', true)
            ->with(['rater', 'announcement'])
            ->latest()
            ->get();

        $ratingCounts = $this->ratings->groupBy('rating')->map->count();

        $this->stats = [
            'average' => $this->user->average_rating,
            'total' => $this->user->ratings_count,
            'distribution' => [
                5 => $ratingCounts->get(5, 0),
                4 => $ratingCounts->get(4, 0),
                3 => $ratingCounts->get(3, 0),
                2 => $ratingCounts->get(2, 0),
                1 => $ratingCounts->get(1, 0),
            ],
        ];
    }

    public function render()
    {
        return view('livewire.user-ratings')->layout('layouts.app', [
            'title' => 'Opinie o ' . $this->user->name . ' - Projekciarz.pl',
        ]);
    }
}

