<?php

namespace App\Livewire\Blog;

use App\Models\BlogPost;
use Livewire\Component;

class BlogShow extends Component
{
    public BlogPost $post;

    public function mount($slug)
    {
        $this->post = BlogPost::with(['author', 'tags'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $this->post->incrementViews();
    }

    public function render()
    {
        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $this->post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('livewire.blog.blog-show', compact('relatedPosts'))->layout('layouts.app', [
            'title' => $this->post->meta_title ?: ($this->post->title . ' - Blog - Projekciarz.pl'),
            'description' => $this->post->meta_description ?: $this->post->excerpt,
            'keywords' => $this->post->meta_keywords ? implode(', ', $this->post->meta_keywords) : '',
            'og_title' => $this->post->meta_title ?: $this->post->title,
            'og_description' => $this->post->meta_description ?: $this->post->excerpt,
            'og_image' => $this->post->featured_image ? asset('storage/' . $this->post->featured_image) : asset('images/og-default.jpg'),
        ]);
    }
}

