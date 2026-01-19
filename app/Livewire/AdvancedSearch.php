<?php

namespace App\Livewire;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\SavedSearch;
use Livewire\Component;
use Livewire\WithPagination;

class AdvancedSearch extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';
    public $budget_min = '';
    public $budget_max = '';
    public $hourly_rate_min = '';
    public $hourly_rate_max = '';
    public $is_urgent = false;
    public $days_ago = '';
    public $sort = 'newest';
    public $saveSearchName = '';
    public $showSaveForm = false;

    public $categories;
    public $savedSearches = [];

    public function mount()
    {
        $this->categories = Category::where('is_active', true)->orderBy('order')->get();

        if (auth()->check()) {
            $this->savedSearches = auth()->user()->savedSearches()->latest()->get();
        }
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

        if ($this->hourly_rate_min) {
            $query->where(function($q) {
                $q->where('hourly_rate_max', '>=', $this->hourly_rate_min)
                  ->orWhereNull('hourly_rate_max');
            });
        }

        if ($this->hourly_rate_max) {
            $query->where(function($q) {
                $q->where('hourly_rate_min', '<=', $this->hourly_rate_max)
                  ->orWhereNull('hourly_rate_min');
            });
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

        // Rekomendacje na podstawie historii (tylko dla zalogowanych)
        $recommendations = collect();
        if (auth()->check() && auth()->user()->isFreelancer()) {
            $recommendations = $this->getRecommendations();
        }

        return view('livewire.advanced-search', [
            'announcements' => $announcements,
            'recommendations' => $recommendations,
        ])->layout('layouts.app', [
            'title' => 'Szukaj projektów - Projekciarz.pl',
        ]);
    }

    /**
     * Pobierz rekomendacje na podstawie historii użytkownika
     */
    private function getRecommendations()
    {
        $user = auth()->user();

        // Kategorie z poprzednich ofert
        $userCategories = $user->proposals()
            ->with('announcement.category')
            ->get()
            ->pluck('announcement.category_id')
            ->filter()
            ->unique()
            ->toArray();

        // Tagi z poprzednich ofert
        $userTags = $user->proposals()
            ->with('announcement.tags')
            ->get()
            ->flatMap(fn($p) => $p->announcement->tags->pluck('id'))
            ->unique()
            ->toArray();

        if (empty($userCategories) && empty($userTags)) {
            return collect();
        }

        $query = Announcement::published()
            ->whereNotIn('id', $this->getCurrentPageAnnouncementIds())
            ->with(['user', 'category', 'tags']);

        // Priorytetyzuj ogłoszenia z podobnymi kategoriami i tagami
        if (!empty($userCategories)) {
            $query->whereIn('category_id', $userCategories);
        }

        if (!empty($userTags)) {
            $query->whereHas('tags', fn($q) => $q->whereIn('tags.id', $userTags));
        }

        return $query->latest()->take(6)->get();
    }

    private function getCurrentPageAnnouncementIds()
    {
        // Pobierz ID ogłoszeń z aktualnej strony, aby nie duplikować w rekomendacjach
        $query = Announcement::published()->where('is_approved', true);

        // Zastosuj te same filtry co w głównym zapytaniu
        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        return $query->pluck('id')->toArray();
    }

    public function saveSearch()
    {
        if (!auth()->check()) {
            session()->flash('error', 'Musisz być zalogowany, aby zapisać wyszukiwanie');
            return;
        }

        $this->validate([
            'saveSearchName' => 'required|string|max:255',
        ], [
            'saveSearchName.required' => 'Podaj nazwę wyszukiwania',
        ]);

        SavedSearch::create([
            'user_id' => auth()->id(),
            'name' => $this->saveSearchName,
            'search' => $this->search,
            'category_id' => $this->category_id ?: null,
            'budget_min' => $this->budget_min ?: null,
            'budget_max' => $this->budget_max ?: null,
            'hourly_rate_min' => $this->hourly_rate_min ?: null,
            'hourly_rate_max' => $this->hourly_rate_max ?: null,
            'is_urgent' => $this->is_urgent ?: null,
            'notify_on_match' => true,
        ]);

        $this->saveSearchName = '';
        $this->showSaveForm = false;
        $this->savedSearches = auth()->user()->savedSearches()->latest()->get();

        session()->flash('success', 'Wyszukiwanie zostało zapisane!');
    }

    public function loadSavedSearch($id)
    {
        $savedSearch = SavedSearch::where('user_id', auth()->id())->findOrFail($id);

        $this->search = $savedSearch->search ?? '';
        $this->category_id = $savedSearch->category_id ?? '';
        $this->budget_min = $savedSearch->budget_min ?? '';
        $this->budget_max = $savedSearch->budget_max ?? '';
        $this->hourly_rate_min = $savedSearch->hourly_rate_min ?? '';
        $this->hourly_rate_max = $savedSearch->hourly_rate_max ?? '';
        $this->is_urgent = $savedSearch->is_urgent ?? false;

        $this->resetPage();
    }

    public function deleteSavedSearch($id)
    {
        SavedSearch::where('user_id', auth()->id())->findOrFail($id)->delete();
        $this->savedSearches = auth()->user()->savedSearches()->latest()->get();
        session()->flash('success', 'Zapisane wyszukiwanie zostało usunięte');
    }
}

