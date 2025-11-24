<?php

namespace App\Livewire;

use App\Models\Announcement;
use Illuminate\Support\Str;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ShowAnnouncement extends Component
{
    public Announcement $announcement;

    public function mount($id)
    {
        // Allow viewing closed announcements for owner and admin
        $query = Announcement::with(['user', 'category', 'tags', 'attachments']);

        // First, get the announcement to check ownership
        $announcement = $query->where('id', $id)->firstOrFail();

        $isOwner = Auth::check() && Auth::id() === $announcement->user_id;
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';

        // Allow viewing published and closed announcements for everyone
        // Rejected and pending only for owner/admin
        if ($isOwner || $isAdmin) {
            // Owner or admin can view all their announcements
            $this->announcement = $announcement;
        } else {
            // Others can see published and closed announcements
            $this->announcement = $query->published()->where('id', $id)->firstOrFail();
        }

        // Only increment views for published announcements (not closed)
        if ($this->announcement->status === 'published' && $this->announcement->is_approved) {
            $this->announcement->increment('views_count');
        }
    }

    public function closeAnnouncement()
    {
        // Check if user is owner or admin
        if (!Auth::check()) {
            return;
        }

        $isOwner = Auth::id() === $this->announcement->user_id;
        $isAdmin = Auth::user()->role === 'admin';

        if (!$isOwner && !$isAdmin) {
            session()->flash('error', 'Nie masz uprawnień do zamknięcia tego ogłoszenia.');
            return;
        }

        $this->announcement->update([
            'status' => 'closed'
        ]);

        session()->flash('success', 'Ogłoszenie zostało zamknięte.');
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

