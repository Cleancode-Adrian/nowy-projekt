<?php

namespace App\Livewire;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Tag;
use Livewire\Component;

class EditAnnouncement extends Component
{
    public Announcement $announcement;
    public $title;
    public $description;
    public $category_id;
    public $budget_min;
    public $budget_max;
    public $budget_currency;
    public $deadline;
    public $location;
    public $is_urgent;
    public $selectedTags = [];

    public $categories;
    public $tags;

    protected function rules()
    {
        return [
            'title' => 'required|min:10|max:255',
            'description' => 'required|min:50',
            'category_id' => 'required|exists:categories,id',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'deadline' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'selectedTags' => 'array|max:10',
            'selectedTags.*' => 'exists:tags,id',
        ];
    }

    public function mount(Announcement $announcement)
    {
        if ($announcement->user_id !== auth()->id()) {
            abort(403, 'Brak dostępu do tego ogłoszenia');
        }

        $this->announcement = $announcement;
        $this->title = $announcement->title;
        $this->description = $announcement->description;
        $this->category_id = $announcement->category_id;
        $this->budget_min = $announcement->budget_min;
        $this->budget_max = $announcement->budget_max;
        $this->budget_currency = $announcement->budget_currency;
        $this->deadline = $announcement->deadline?->format('Y-m-d');
        $this->location = $announcement->location;
        $this->is_urgent = $announcement->is_urgent;
        $this->selectedTags = $announcement->tags->pluck('id')->toArray();

        $this->categories = Category::where('is_active', true)->orderBy('order')->get();
        $this->tags = Tag::forAnnouncements()->orderBy('name')->get();
    }

    public function update()
    {
        $this->validate();

        $this->announcement->update([
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'budget_min' => $this->budget_min ?: null,
            'budget_max' => $this->budget_max ?: null,
            'budget_currency' => $this->budget_currency,
            'deadline' => $this->deadline ?: null,
            'location' => $this->location ?: null,
            'is_urgent' => $this->is_urgent,
        ]);

        $this->announcement->tags()->sync($this->selectedTags);

        $this->dispatch('notify', message: 'Ogłoszenie zaktualizowane!', type: 'success');

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.edit-announcement')->layout('layouts.app', [
            'title' => 'Edytuj ogłoszenie - Projekciarz.pl',
        ]);
    }
}

