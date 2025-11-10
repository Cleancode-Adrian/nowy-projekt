<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateBlogPost extends Command
{
    protected $signature = 'blog:generate {--test : Test mode - nie publikuj}';
    protected $description = 'Automatycznie generuje wpis na bloga używając AI (Gemini API)';

    private $topics = [
        'Jak znaleźć pierwszych klientów jako freelancer',
        'Najlepsze narzędzia dla freelancerów w 2025',
        'Jak ustalać stawki jako freelancer',
        'Time management dla freelancerów',
        'Jak budować portfolio freelancera',
        'Fakturowanie i podatki dla freelancerów',
        'Work-life balance w freelancingu',
        'Jak negocjować z klientami',
        'Najlepsze platformy freelancerskie',
        'Jak radzić sobie z trudnymi klientami',
        'Marketing dla freelancerów',
        'Budowanie marki osobistej',
        'Passive income dla freelancerów',
        'Jak unikać wypalenia zawodowego',
        'Networking dla freelancerów',
    ];

    public function handle()
    {
        $this->info('🤖 Rozpoczynam generowanie wpisu blogowego...');

        // 1. Wybierz temat
        $topic = $this->topics[array_rand($this->topics)];
        $this->info("📝 Temat: {$topic}");

        // 2. Sprawdź API keys
        if (!env('GEMINI_API_KEY')) {
            $this->error('❌ Brak GEMINI_API_KEY w .env');
            $this->info('💡 Dodaj: GEMINI_API_KEY=twoj_klucz_api');
            $this->info('🔗 Pobierz darmowy klucz: https://makersuite.google.com/app/apikey');
            return 1;
        }

        // 3. Generuj treść przez Gemini API
        $this->info('🧠 Generuję treść przez Gemini AI...');
        $content = $this->generateContent($topic);

        if (!$content) {
            $this->error('❌ Błąd generowania treści');
            return 1;
        }

        // 4. Pobierz obrazek z Unsplash
        $this->info('🖼️ Pobieram obrazek z Unsplash...');
        $imagePath = $this->downloadImage($topic);

        // 5. Wybierz tagi
        $tags = $this->selectTags($content['title']);

        // 6. Utwórz wpis
        $admin = User::where('role', 'admin')->first();

        $post = BlogPost::create([
            'author_id' => $admin->id,
            'title' => $content['title'],
            'slug' => Str::slug($content['title']),
            'excerpt' => $content['excerpt'],
            'content' => $content['body'],
            'meta_title' => $content['meta_title'],
            'meta_description' => $content['meta_description'],
            'meta_keywords' => $content['keywords'],
            'featured_image' => $imagePath,
            'status' => $this->option('test') ? 'draft' : 'published',
            'published_at' => $this->option('test') ? null : now(),
        ]);

        // 7. Przypisz tagi
        if (!empty($tags)) {
            $post->tags()->sync($tags);
        }

        $status = $this->option('test') ? 'SZKIC' : 'OPUBLIKOWANY';
        $this->info("✅ Wpis utworzony! Status: {$status}");
        $this->info("🔗 URL: /blog/{$post->slug}");

        return 0;
    }

    private function generateContent($topic)
    {
        try {
            $prompt = "Napisz profesjonalny artykuł na blog dla freelancerów w języku polskim o tytule: '{$topic}'.

Struktura:
1. Tytuł: Ciekawy, SEO-friendly (50-60 znaków)
2. Zajawka: 1-2 zdania (150-160 znaków)
3. Treść: 800-1200 słów, HTML (h2, p, ul, li, strong, em)
4. Meta tytuł: SEO (maks 60 znaków)
5. Meta opis: SEO (maks 160 znaków)
6. Słowa kluczowe: 5-7 słów oddzielonych przecinkami

Treść powinna być:
- Praktyczna i wartościowa
- Z konkretnymi przykładami
- Z listami punktowanymi
- Profesjonalna, ale przystępna
- Zakończona call-to-action do platformy WebFreelance

Zwróć odpowiedź w formacie JSON:
{
    \"title\": \"...\",
    \"excerpt\": \"...\",
    \"body\": \"<h2>...</h2><p>...</p>...\",
    \"meta_title\": \"...\",
    \"meta_description\": \"...\",
    \"keywords\": [\"słowo1\", \"słowo2\", ...]
}";

            $response = Http::timeout(60)->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . env('GEMINI_API_KEY'),
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]
            );

            if ($response->failed()) {
                $this->error('Błąd API: ' . $response->body());
                return null;
            }

            $result = $response->json();
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Wyczyść odpowiedź (usuń markdown code blocks jeśli są)
            $text = preg_replace('/```json\s*|\s*```/', '', $text);
            $text = trim($text);

            $content = json_decode($text, true);

            if (!$content || !isset($content['title'])) {
                $this->error('Nieprawidłowa odpowiedź AI');
                return null;
            }

            return $content;

        } catch (\Exception $e) {
            $this->error('Błąd: ' . $e->getMessage());
            return null;
        }
    }

    private function downloadImage($topic)
    {
        try {
            // Wybierz słowo kluczowe dla obrazka
            $keywords = ['freelancer', 'workspace', 'laptop', 'office', 'business', 'work', 'computer'];
            $keyword = $keywords[array_rand($keywords)];

            if (env('UNSPLASH_ACCESS_KEY')) {
                // Z Unsplash API (darmowe)
                $response = Http::get('https://api.unsplash.com/photos/random', [
                    'client_id' => env('UNSPLASH_ACCESS_KEY'),
                    'query' => $keyword,
                    'orientation' => 'landscape',
                    'w' => 1200,
                    'h' => 630,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $imageUrl = $data['urls']['regular'] ?? null;

                    if ($imageUrl) {
                        $imageContent = Http::get($imageUrl)->body();
                        $filename = 'blog/' . Str::random(20) . '.jpg';
                        Storage::disk('public')->put($filename, $imageContent);

                        $this->info("✅ Obrazek pobrany z Unsplash");
                        return $filename;
                    }
                }
            }

            // Fallback - brak obrazka (wyświetli się gradient)
            $this->warn('⚠️ Nie pobrano obrazka (użyty zostanie gradient)');
            return null;

        } catch (\Exception $e) {
            $this->warn('⚠️ Błąd pobierania obrazka: ' . $e->getMessage());
            return null;
        }
    }

    private function selectTags($title)
    {
        $tagKeywords = [
            'PHP' => ['php', 'laravel', 'symfony'],
            'JavaScript' => ['javascript', 'js', 'node', 'react', 'vue'],
            'WordPress' => ['wordpress', 'wp'],
            'UI/UX' => ['design', 'ui', 'ux', 'interfejs'],
            'SEO' => ['seo', 'optymalizacja', 'google'],
            'Laravel' => ['laravel', 'eloquent'],
        ];

        $selectedTags = [];
        $titleLower = strtolower($title);

        foreach ($tagKeywords as $tagName => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($titleLower, $keyword)) {
                    $tag = Tag::where('name', $tagName)->first();
                    if ($tag) {
                        $selectedTags[] = $tag->id;
                        break;
                    }
                }
            }
        }

        // Jeśli nie znaleziono tagów, wybierz losowy
        if (empty($selectedTags)) {
            $randomTags = Tag::inRandomOrder()->limit(2)->pluck('id')->toArray();
            $selectedTags = $randomTags;
        }

        return $selectedTags;
    }
}
