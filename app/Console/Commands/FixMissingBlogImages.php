<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FixMissingBlogImages extends Command
{
    protected $signature = 'blog:fix-images {--limit=10 : Maksymalna liczba wpisów do naprawy}';
    protected $description = 'Naprawia brakujące zdjęcia w wpisach blogowych, pobierając je z Unsplash';

    public function handle()
    {
        $this->info('🔍 Szukam wpisów bez zdjęć...');

        $posts = BlogPost::whereNull('featured_image')
            ->orWhere('featured_image', '')
            ->limit($this->option('limit'))
            ->get();

        if ($posts->isEmpty()) {
            $this->info('✅ Wszystkie wpisy mają zdjęcia!');
            return 0;
        }

        $this->info("📝 Znaleziono {$posts->count()} wpisów bez zdjęć");

        $bar = $this->output->createProgressBar($posts->count());
        $bar->start();

        foreach ($posts as $post) {
            $imageUrl = $this->getImageForPost($post->title);
            
            if ($imageUrl) {
                $post->featured_image = $imageUrl;
                $post->save();
                $bar->advance();
            } else {
                $this->newLine();
                $this->warn("⚠️ Nie udało się pobrać zdjęcia dla: {$post->title}");
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info('✅ Zakończono naprawę zdjęć!');

        return 0;
    }

    private function getImageForPost($title)
    {
        // Wyciągnij słowa kluczowe z tytułu
        $keywords = $this->extractKeywords($title);

        // Spróbuj pobrać z Unsplash
        if (env('UNSPLASH_ACCESS_KEY')) {
            try {
                $response = Http::get('https://api.unsplash.com/photos/random', [
                    'client_id' => env('UNSPLASH_ACCESS_KEY'),
                    'query' => $keywords[0] ?? 'freelancer',
                    'orientation' => 'landscape',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['urls']['regular'] ?? null;
                }
            } catch (\Exception $e) {
                // Fallback
            }
        }

        // Fallback - użyj Unsplash Source (bez API key)
        $keyword = urlencode($keywords[0] ?? 'freelancer');
        return "https://source.unsplash.com/1200x630/?{$keyword}";
    }

    private function extractKeywords($title)
    {
        // Usuń polskie znaki i wyciągnij kluczowe słowa
        $stopWords = ['dla', 'jak', 'czy', 'co', 'kto', 'gdzie', 'kiedy', 'dlaczego', 'i', 'oraz', 'lub', 'ale', 'w', 'z', 'na', 'po', 'przed', 'pod', 'nad', 'przez', 'do', 'od', 'ze', 'o', 'a'];
        
        $words = str_word_count(strtolower($title), 1, 'ąćęłńóśźż');
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 3 && !in_array($word, $stopWords);
        });

        return array_values(array_slice($keywords, 0, 3));
    }
}

