<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Tag;
use App\Models\Category;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateBlogPostOpenAI extends Command
{
    protected $signature = 'blog:generate-openai
                            {topic? : Temat wpisu (opcjonalnie)}
                            {--count=1 : Liczba wpisów do wygenerowania}
                            {--category= : ID kategorii}
                            {--tags= : Tagi oddzielone przecinkami}
                            {--test : Tryb testowy - nie publikuj}
                            {--image : Pobierz obrazek z Unsplash}';

    protected $description = 'Generuje wpisy blogowe używając OpenAI API';

    private $defaultTopics = [
        'Jak znaleźć pierwszych klientów jako freelancer w 2025',
        'Najlepsze narzędzia automatyzacji dla freelancerów',
        'Jak ustalać stawki jako freelancer - kompletny przewodnik',
        'Time management dla freelancerów - 10 sprawdzonych metod',
        'Jak budować portfolio freelancera, które przyciąga klientów',
        'Fakturowanie i podatki dla freelancerów w Polsce',
        'Work-life balance w freelancingu - jak nie wypalić się',
        'Jak negocjować z klientami - praktyczne wskazówki',
        'Najlepsze platformy freelancerskie w 2025',
        'Jak radzić sobie z trudnymi klientami',
        'Marketing dla freelancerów - jak zdobywać klientów',
        'Budowanie marki osobistej jako freelancer',
        'Passive income dla freelancerów - pomysły i strategie',
        'Jak unikać wypalenia zawodowego w freelancingu',
        'Networking dla freelancerów - jak budować relacje',
        'Automatyzacja procesów biznesowych dla freelancerów',
        'Narzędzia AI dla freelancerów - ChatGPT, Claude i inne',
        'Jak zautomatyzować marketing jako freelancer',
        'SEO dla freelancerów - jak zdobywać klientów z Google',
        'Social media marketing dla freelancerów',
    ];

    public function handle()
    {
        $this->info('🤖 Generator wpisów blogowych z OpenAI');
        $this->newLine();

        // Sprawdź API key
        if (!env('OPENAI_API_KEY')) {
            $this->error('❌ Brak OPENAI_API_KEY w .env');
            $this->info('💡 Dodaj: OPENAI_API_KEY=sk-...');
            $this->info('🔗 Pobierz klucz: https://platform.openai.com/api-keys');
            return 1;
        }

        $count = (int) $this->option('count');
        $testMode = $this->option('test');
        $downloadImage = $this->option('image');

        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->error('❌ Brak użytkownika admin w bazie');
            return 1;
        }

        for ($i = 0; $i < $count; $i++) {
            $this->newLine();
            $this->info("📝 Generowanie wpisu " . ($i + 1) . "/{$count}...");

            // Wybierz temat
            $topic = $this->argument('topic') ?? $this->defaultTopics[array_rand($this->defaultTopics)];
            $this->info("🎯 Temat: {$topic}");

            // Generuj treść
            $this->info('🧠 Generuję treść przez OpenAI...');
            $content = $this->generateContent($topic);

            if (!$content) {
                $this->error('❌ Błąd generowania treści');
                continue;
            }

            // Pobierz obrazek
            $imageUrl = null;
            if ($downloadImage) {
                $this->info('🖼️ Pobieram obrazek...');
                $imageUrl = $this->getImageForTopic($topic);
            }

            // Wybierz tagi i kategorię
            $tags = $this->selectTags($content['title'], $this->option('tags'));
            $categoryId = $this->option('category') ?: $this->selectCategory($content['title']);

            // Utwórz wpis
            $slug = Str::slug($content['title']);
            $counter = 1;
            while (BlogPost::where('slug', $slug)->exists()) {
                $slug = Str::slug($content['title']) . '-' . $counter++;
            }

            $post = BlogPost::create([
                'author_id' => $admin->id,
                'category_id' => $categoryId,
                'title' => $content['title'],
                'slug' => $slug,
                'excerpt' => $content['excerpt'],
                'tldr' => is_array($content['tldr'] ?? null) ? implode("\n", $content['tldr']) : ($content['tldr'] ?? null),
                'content' => $content['body'],
                'meta_title' => $content['meta_title'],
                'meta_description' => $content['meta_description'],
                'meta_keywords' => $content['keywords'],
                'featured_image' => $imageUrl,
                'featured_image_alt' => $content['featured_image_alt'] ?? $content['title'],
                'status' => $testMode ? 'draft' : 'published',
                'published_at' => $testMode ? null : now()->subDays(rand(0, 30)),
            ]);

            // Przypisz tagi
            if (!empty($tags)) {
                $post->tags()->sync($tags);
            }

            $status = $testMode ? 'SZKIC' : 'OPUBLIKOWANY';
            $this->info("✅ Wpis utworzony! Status: {$status}");
            $this->info("🔗 URL: /blog/{$post->slug}");
        }

        $this->newLine();
        $this->info("🎉 Wygenerowano {$count} wpisów!");

        return 0;
    }

    private function generateContent($topic)
    {
        try {
            $prompt = "Napisz profesjonalny artykuł na blog dla freelancerów w języku polskim o temacie: '{$topic}'.

WYMAGANIA:
1. Tytuł: Ciekawy, SEO-friendly (50-70 znaków), z liczbą roczną jeśli dotyczy (np. 2025)
2. Zajawka: 1-2 zdania (150-200 znaków), zachęcająca do czytania
3. Treść: 1000-1500 słów w HTML (używaj h2, h3, p, ul, li, strong, em, a)
4. Meta tytuł: SEO (maks 60 znaków)
5. Meta opis: SEO (maks 160 znaków), z call-to-action
6. Słowa kluczowe: 5-8 słów oddzielonych przecinkami
7. Alt text dla zdjęcia: Opisowy (maks 100 znaków)

STRUKTURA TREŚCI:
- Wprowadzenie (2-3 akapity)
- 3-5 sekcji z nagłówkami h2
- Podsekcje z h3 gdzie potrzebne
- Listy punktowane i numerowane
- Przykłady i case studies
- Tabele porównawcze (jeśli dotyczy)
- Wnioski i call-to-action na końcu

STYL:
- Praktyczny i wartościowy
- Z konkretnymi przykładami
- Profesjonalny, ale przystępny
- Z linkami wewnętrznymi (wspomnij Projekciarz.pl)
- Z zewnętrznymi źródłami (dodaj linki do badań, raportów)

Zakończ artykuł zachętą do rejestracji na Projekciarz.pl.

ZWRÓĆ TYLKO JSON (bez markdown, bez dodatkowych komentarzy):
{
    \"title\": \"...\",
    \"excerpt\": \"...\",
    \"tldr\": [\"3-4 najważniejsze wnioski z artykułu, każdy jako jedno konkretne zdanie do 140 znaków\"],
    \"body\": \"<h2>...</h2><p>...</p>...\",
    \"meta_title\": \"...\",
    \"meta_description\": \"...\",
    \"keywords\": \"słowo1, słowo2, słowo3\",
    \"featured_image_alt\": \"...\"
}";

            $response = Http::timeout(120)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini', // lub 'gpt-4' dla lepszej jakości
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Jesteś ekspertem SEO i copywriterem specjalizującym się w treściach dla freelancerów. Zawsze zwracasz poprawny JSON bez dodatkowych komentarzy.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 4000,
                ]);

            if ($response->failed()) {
                $this->error('Błąd API: ' . $response->body());
                return null;
            }

            $result = $response->json();
            $text = $result['choices'][0]['message']['content'] ?? '';

            // Wyczyść odpowiedź
            $text = preg_replace('/```json\s*|\s*```/', '', $text);
            $text = trim($text);
            $text = preg_replace('/^[^{]*/', '', $text); // Usuń tekst przed {
            $text = preg_replace('/[^}]*$/', '', $text) . '}'; // Usuń tekst po }

            $content = json_decode($text, true);

            if (!$content || !isset($content['title'])) {
                $this->error('Nieprawidłowa odpowiedź AI');
                $this->line('Odpowiedź: ' . substr($text, 0, 200));
                return null;
            }

            // Konwertuj keywords na array jeśli jest stringiem
            if (isset($content['keywords']) && is_string($content['keywords'])) {
                $content['keywords'] = array_map('trim', explode(',', $content['keywords']));
            }

            return $content;

        } catch (\Exception $e) {
            $this->error('Błąd: ' . $e->getMessage());
            return null;
        }
    }

    private function getImageForTopic($topic)
    {
        // Wyciągnij słowa kluczowe
        $keywords = $this->extractKeywords($topic);
        $keyword = urlencode($keywords[0] ?? 'freelancer');

        // Spróbuj Unsplash API
        if (env('UNSPLASH_ACCESS_KEY')) {
            try {
                $response = Http::get('https://api.unsplash.com/photos/random', [
                    'client_id' => env('UNSPLASH_ACCESS_KEY'),
                    'query' => $keyword,
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

        // Fallback - Unsplash Source
        return "https://source.unsplash.com/1200x630/?{$keyword}";
    }

    private function extractKeywords($text)
    {
        $stopWords = ['dla', 'jak', 'czy', 'co', 'kto', 'gdzie', 'kiedy', 'dlaczego', 'i', 'oraz', 'lub', 'ale', 'w', 'z', 'na', 'po', 'przed', 'pod', 'nad', 'przez', 'do', 'od', 'ze', 'o', 'a', '2025'];

        $words = str_word_count(strtolower($text), 1, 'ąćęłńóśźż');
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 3 && !in_array($word, $stopWords);
        });

        return array_values(array_slice($keywords, 0, 3));
    }

    private function selectTags($title, $customTags = null)
    {
        if ($customTags) {
            $tagNames = array_map('trim', explode(',', $customTags));
            $tags = Tag::whereIn('name', $tagNames)
                ->where('type', 'blog')
                ->pluck('id')
                ->toArray();

            if (!empty($tags)) {
                return $tags;
            }
        }

        // Automatyczne dopasowanie tagów
        $tagKeywords = [
            'Automatyzacja' => ['automatyzacja', 'automatyzować', 'zapier', 'make', 'workflow'],
            'AI' => ['ai', 'chatgpt', 'claude', 'sztuczna inteligencja', 'machine learning'],
            'Produktywność' => ['produktywność', 'time management', 'zarządzanie czasem'],
            'Marketing' => ['marketing', 'promocja', 'reklama', 'social media'],
            'SEO' => ['seo', 'optymalizacja', 'google', 'wyszukiwarka'],
            'Freelancing' => ['freelancer', 'freelancing', 'zdalna', 'praca zdalna'],
            'Kariera' => ['kariera', 'rozwój', 'umiejętności', 'portfolio'],
            'Poradnik' => ['poradnik', 'przewodnik', 'tutorial', 'jak'],
        ];

        $selectedTags = [];
        $titleLower = strtolower($title);

        foreach ($tagKeywords as $tagName => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($titleLower, $keyword)) {
                    $tag = Tag::where('name', $tagName)
                        ->where('type', 'blog')
                        ->first();
                    if ($tag) {
                        $selectedTags[] = $tag->id;
                        break;
                    }
                }
            }
        }

        // Jeśli nie znaleziono, dodaj domyślne
        if (empty($selectedTags)) {
            $defaultTag = Tag::where('name', 'Freelancing')
                ->where('type', 'blog')
                ->first();
            if ($defaultTag) {
                $selectedTags[] = $defaultTag->id;
            }
        }

        return array_unique($selectedTags);
    }

    private function selectCategory($title)
    {
        $titleLower = strtolower($title);

        // Automatyczne dopasowanie kategorii
        if (str_contains($titleLower, 'automatyzacja') || str_contains($titleLower, 'ai')) {
            $category = Category::where('slug', 'automatyzacje')->first();
            if ($category) return $category->id;
        }

        if (str_contains($titleLower, 'seo') || str_contains($titleLower, 'optymalizacja')) {
            $category = Category::where('slug', 'seo')->first();
            if ($category) return $category->id;
        }

        // Domyślna kategoria
        return Category::first()?->id;
    }
}

