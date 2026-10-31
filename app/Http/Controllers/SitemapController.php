<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\BlogPost;
use App\Models\Page;
use Carbon\CarbonInterface;
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
        $announcements = Announcement::published()
            ->orderByDesc('updated_at')
            ->limit(5000)
            ->get(['id', 'updated_at']);

        $posts = BlogPost::published()
            ->orderByDesc('updated_at')
            ->limit(5000)
            ->get(['slug', 'updated_at']);

        $pages = Page::active()
            ->whereNull('route_name')
            ->whereNull('external_url')
            ->get(['slug', 'updated_at']);

        // lastmod musi odzwierciedlać realną zmianę treści. Wcześniej strony statyczne
        // dostawały now() przy każdym odświeżeniu cache (co godzinę), przez co Google
        // uczy się ignorować lastmod w całej mapie. Tam, gdzie nie ma sensownej daty,
        // element pomijamy - jest opcjonalny.
        $latestAnnouncement = $announcements->first()?->updated_at;
        $latestPost = $posts->first()?->updated_at;

        $urls = [];
        $urls[] = $this->url(route('home'), max($latestAnnouncement, $latestPost), 'daily', '1.0');
        $urls[] = $this->url(route('announcements.index'), $latestAnnouncement, 'hourly', '0.9');
        $urls[] = $this->url(route('search.advanced'), null, 'weekly', '0.5');
        $urls[] = $this->url(route('leaderboard'), null, 'daily', '0.6');
        $urls[] = $this->url(route('blog.index'), $latestPost, 'daily', '0.8');
        $urls[] = $this->url(route('faq'), null, 'monthly', '0.4');
        $urls[] = $this->url(route('privacy-policy'), $this->systemPageUpdatedAt('polityka'), 'yearly', '0.2');
        $urls[] = $this->url(route('terms-of-service'), $this->systemPageUpdatedAt('regulamin'), 'yearly', '0.2');

        foreach ($announcements as $announcement) {
            $urls[] = $this->url(
                route('announcements.show', ['id' => $announcement->id]),
                $announcement->updated_at,
                'weekly',
                '0.7'
            );
        }

        foreach ($posts as $post) {
            $urls[] = $this->url(
                route('blog.show', ['slug' => $post->slug]),
                $post->updated_at,
                'monthly',
                '0.6'
            );
        }

        foreach ($pages as $page) {
            $urls[] = $this->url(
                route('page.show', ['slug' => $page->slug]),
                $page->updated_at,
                'monthly',
                '0.5'
            );
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            . implode('', $urls)
            . '</urlset>';
    }

    /**
     * Data ostatniej edycji strony systemowej (regulamin, polityka) z CMS.
     */
    private function systemPageUpdatedAt(string $titleFragment): ?CarbonInterface
    {
        return Page::active()
            ->where('is_system', true)
            ->where('title', 'like', "%{$titleFragment}%")
            ->first(['updated_at'])
            ?->updated_at;
    }

    private function url(string $loc, ?CarbonInterface $lastmod, string $changefreq, string $priority): string
    {
        return '<url>'
            . '<loc>' . e($loc) . '</loc>'
            . ($lastmod ? '<lastmod>' . $lastmod->toAtomString() . '</lastmod>' : '')
            . '<changefreq>' . $changefreq . '</changefreq>'
            . '<priority>' . $priority . '</priority>'
            . '</url>';
    }
}
