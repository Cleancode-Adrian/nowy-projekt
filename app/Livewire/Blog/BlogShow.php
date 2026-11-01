<?php

namespace App\Livewire\Blog;

use App\Models\BlogPost;
use Livewire\Component;

class BlogShow extends Component
{
    public BlogPost $post;

    public function mount($slug)
    {
        $this->post = BlogPost::with(['author', 'tags', 'category'])
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
            ->limit(4)
            ->get(['id', 'slug', 'title', 'content', 'published_at']);

        return view('livewire.blog.blog-show', compact('relatedPosts'))->layout('layouts.app', [
            'title' => $this->post->meta_title ?: ($this->post->title . ' - Blog - Projekciarz.pl'),
            'description' => $this->post->meta_description ?: $this->post->excerpt,
            'keywords' => $this->post->meta_keywords ? implode(', ', $this->post->meta_keywords) : '',
            'og_title' => $this->post->meta_title ?: $this->post->title,
            'og_description' => $this->post->meta_description ?: $this->post->excerpt,
            'og_image' => $this->ogImage(),
        ]);
    }

    /**
     * Okładka może być pełnym URL-em (Unsplash) albo ścieżką w storage.
     */
    private function ogImage(): string
    {
        $image = $this->post->featured_image;

        if (!$image) {
            return asset('images/og-default.jpg');
        }

        return preg_match('#^https?://#i', $image) ? $image : asset('storage/' . $image);
    }
}

