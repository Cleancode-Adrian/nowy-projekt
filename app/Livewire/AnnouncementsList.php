<?php

namespace App\Livewire;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class AnnouncementsList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $search = '';

    #[Url]
    public $category = '';

    #[Url]
    public $minBudget = '';

    #[Url]
    public $maxBudget = '';

    public $selectedTags = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'category', 'minBudget', 'maxBudget', 'selectedTags']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Announcement::select([
                'id', 'user_id', 'category_id', 'title', 'description',
                'budget_min', 'budget_max', 'budget_currency', 'location',
                'deadline', 'is_urgent', 'created_at', 'views_count', 'proposals_count'
            ])
            ->with([
                'user:id,name,avatar,is_verified',
                'category:id,name,slug,color,icon',
                'tags:id,name'
            ])
            ->published()
            ->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->category) {
            $query->whereHas('category', fn($q) => $q->where('slug', $this->category));
        }

        if ($this->minBudget) {
            $query->where('budget_min', '>=', $this->minBudget);
        }
        if ($this->maxBudget) {
            $query->where('budget_max', '<=', $this->maxBudget);
        }

        if (!empty($this->selectedTags)) {
            $query->whereHas('tags', fn($q) => $q->whereIn('tags.id', $this->selectedTags));
        }

        $announcements = $query->paginate(12);
        $categories = Category::select('id', 'name', 'slug', 'color', 'icon')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
        $tags = Tag::select('id', 'name')->orderBy('name')->get();

        return view('livewire.announcements-list', [
            'announcements' => $announcements,
            'categories' => $categories,
            'tags' => $tags,
        ])->layout('layouts.app', [
            'title' => 'Przeglądaj ogłoszenia - WebFreelance',
            'description' => 'Znajdź idealne zlecenie dla siebie. Setki projektów czeka na freelancerów takich jak Ty.',
        ]);
    }
}

