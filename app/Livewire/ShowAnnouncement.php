<?php

namespace App\Livewire;

use App\Models\Announcement;
use Illuminate\Support\Str;
use Livewire\Component;

class ShowAnnouncement extends Component
{
    public Announcement $announcement;

    public function mount($id)
    {
        $this->announcement = Announcement::with(['user', 'category', 'tags', 'attachments'])
            ->published()
            ->findOrFail($id);

        $this->announcement->increment('views_count');
    }

    public function render()
    {
        $relatedAnnouncements = Announcement::with(['user', 'category'])
            ->published()
            ->where('category_id', $this->announcement->category_id)
            ->where('id', '!=', $this->announcement->id)
            ->take(3)
            ->get();

        return view('livewire.show-announcement', [
            'relatedAnnouncements' => $relatedAnnouncements,
        ])->layout('layouts.app', [
            'title' => $this->announcement->title . ' - Projekciarz.pl',
            'description' => Str::limit($this->announcement->description, 160),
            'og_title' => $this->announcement->title,
            'og_description' => Str::limit($this->announcement->description, 200),
        ]);
    }
}

