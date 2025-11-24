<?php

namespace App\Livewire;

use App\Models\Announcement;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class AdvancedSearch extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';
    public $budget_min = '';
    public $budget_max = '';
    public $is_urgent = false;
    public $days_ago = '';
    public $sort = 'newest';

    public $categories;

    public function mount()
    {
        $this->categories = Category::where('is_active', true)->orderBy('order')->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Announcement::with(['user', 'category', 'tags'])
            ->whereIn('status', ['published', 'closed'])
            ->where('is_approved', true);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        if ($this->budget_min) {
            $query->where('budget_max', '>=', $this->budget_min);
        }

        if ($this->budget_max) {
            $query->where('budget_min', '<=', $this->budget_max);
        }

        if ($this->is_urgent) {
            $query->where('is_urgent', true);
        }

        if ($this->days_ago) {
            $query->where('created_at', '>=', now()->subDays($this->days_ago));
        }

        switch ($this->sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'budget_high':
                $query->orderBy('budget_max', 'desc');
                break;
            case 'budget_low':
                $query->orderBy('budget_min', 'asc');
                break;
            default:
                $query->latest();
        }

        $announcements = $query->paginate(12);

        return view('livewire.advanced-search', ['announcements' => $announcements])->layout('layouts.app', [
            'title' => 'Szukaj projektów - Projekciarz.pl',
        ]);
    }
}

