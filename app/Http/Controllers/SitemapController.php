<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\BlogPost;
use App\Models\Page;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = Cache::remember('sitemap.xml', now()->addHour(), function () {
            return $this->build();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    private function build(): string
    {
        $urls = [];

        $urls[] = $this->url(route('home'), now(), 'daily', '1.0');
        $urls[] = $this->url(route('announcements.index'), now(), 'hourly', '0.9');
        $urls[] = $this->url(route('search.advanced'), now(), 'weekly', '0.5');
        $urls[] = $this->url(route('leaderboard'), now(), 'daily', '0.6');
        $urls[] = $this->url(route('blog.index'), now(), 'daily', '0.8');
        $urls[] = $this->url(route('faq'), now(), 'monthly', '0.4');
        $urls[] = $this->url(route('privacy-policy'), now(), 'yearly', '0.2');
        $urls[] = $this->url(route('terms-of-service'), now(), 'yearly', '0.2');

        $announcements = Announcement::published()
            ->orderByDesc('updated_at')
            ->limit(5000)
            ->get(['id', 'updated_at']);

        foreach ($announcements as $announcement) {
            $urls[] = $this->url(
                route('announcements.show', ['id' => $announcement->id]),
                $announcement->updated_at,
                'weekly',
                '0.7'
            );
        }

        $posts = BlogPost::published()
            ->orderByDesc('updated_at')
            ->limit(5000)
            ->get(['slug', 'updated_at']);

        foreach ($posts as $post) {
            $urls[] = $this->url(
                route('blog.show', ['slug' => $post->slug]),
                $post->updated_at,
                'monthly',
                '0.6'
            );
        }

        $pages = Page::active()
            ->whereNull('route_name')
            ->whereNull('external_url')
            ->get(['slug', 'updated_at']);

        foreach ($pages as $page) {
            $urls[] = $this->url(
                route('page.show', ['slug' => $page->slug]),
                $page->updated_at,
                'monthly',
                '0.5'
            );
        }

        $body = implode('', $urls);

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            . $body
            . '</urlset>';
    }

    private function url(string $loc, $lastmod, string $changefreq, string $priority): string
    {
        return '<url>'
            . '<loc>' . e($loc) . '</loc>'
            . '<lastmod>' . $lastmod->toAtomString() . '</lastmod>'
            . '<changefreq>' . $changefreq . '</changefreq>'
            . '<priority>' . $priority . '</priority>'
            . '</url>';
    }
}
