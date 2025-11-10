<?php

namespace App\Livewire\Blog;

use App\Models\BlogPost;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;

class BlogIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $tag = '';

    public function render()
    {
        $query = BlogPost::with(['author', 'tags'])
            ->published()
            ->latest('published_at');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('excerpt', 'like', "%{$this->search}%");
            });
        }

        if ($this->tag) {
            $query->whereHas('tags', fn($q) => $q->where('slug', $this->tag));
        }

        $posts = $query->paginate(9);
        $tags = Tag::select('id', 'name', 'slug')->orderBy('name')->get();

        return view('livewire.blog.blog-index', compact('posts', 'tags'))->layout('layouts.app', [
            'title' => 'Blog - WebFreelance',
            'description' => 'Porady, tutoriale i nowości ze świata freelancingu',
        ]);
    }
}

