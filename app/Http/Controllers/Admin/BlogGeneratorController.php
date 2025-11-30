<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogSchedule;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BlogGeneratorController extends Controller
{
    public function index()
    {
        $openaiKey = Setting::where('key', 'openai_api_key')->value('value');
        $unsplashKey = Setting::where('key', 'unsplash_access_key')->value('value');

        $categories = Category::orderBy('name')->get();
        $tags = Tag::forBlogs()->orderBy('name')->get();
        $schedule = BlogSchedule::first() ?? new BlogSchedule();

        return view('admin.blog.generator', compact('openaiKey', 'unsplashKey', 'categories', 'tags', 'schedule'));
    }

    public function runNow(Request $request)
    {
        $request->validate([
            'topics' => 'required|string|min:3',
            'count' => 'nullable|integer|min:1|max:10',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|string|max:500',
            'download_image' => 'nullable|boolean',
            'image_source' => 'nullable|in:unsplash,dalle3',
            'test_mode' => 'nullable|boolean',
        ], [
            'topics.required' => 'Pole tematy jest wymagane',
            'topics.min' => 'Tematy muszą mieć minimum 3 znaki',
        ]);

        return $this->generate($request);
    }

    public function saveSchedule(Request $request)
    {
        $request->validate([
            'is_enabled' => 'nullable|boolean',
            'time' => 'required|string|regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/',
            'frequency' => 'required|in:daily,twice_daily,weekly,weekdays',
            'count' => 'required|integer|min:1|max:10',
            'topics' => 'required|string|min:3',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|string|max:500',
            'download_image' => 'nullable|boolean',
            'image_source' => 'nullable|in:unsplash,dalle3',
            'auto_publish' => 'nullable|boolean',
        ], [
            'time.regex' => 'Godzina musi być w formacie HH:mm (np. 09:00)',
            'topics.required' => 'Tematy są wymagane',
            'topics.min' => 'Tematy muszą mieć minimum 3 znaki',
            'count.min' => 'Liczba wpisów musi być większa od 0',
            'count.max' => 'Maksymalnie 10 wpisów na raz',
        ]);

        // Sprawdź czy tematy nie są puste po przefiltrowaniu
        $topics = array_filter(array_map('trim', explode("\n", $request->topics)));
        if (empty($topics)) {
            return back()->withErrors(['topics' => 'Musisz podać przynajmniej jeden temat (nie mogą być puste linie)']);
        }

        $schedule = BlogSchedule::first();

        if (!$schedule) {
            $schedule = new BlogSchedule();
        }

        $schedule->fill([
            'is_enabled' => $request->has('is_enabled'),
            'time' => $request->time,
            'frequency' => $request->frequency,
            'count' => $request->count,
            'topics' => $request->topics,
            'category_id' => $request->category_id ?: null,
            'tags' => $request->tags ?: null,
            'download_image' => $request->has('download_image'),
            'image_source' => $request->image_source ?? 'unsplash',
            'auto_publish' => $request->has('auto_publish'),
        ]);

        $schedule->save();

        return back()->with('success', 'Harmonogram został zapisany!');
    }

    public function saveApiKeys(Request $request)
    {
        $request->validate([
            'openai_api_key' => 'nullable|string|max:255',
            'unsplash_access_key' => 'nullable|string|max:255',
        ]);

        Setting::updateOrCreate(
            ['key' => 'openai_api_key'],
            ['value' => $request->openai_api_key]
        );

        Setting::updateOrCreate(
            ['key' => 'unsplash_access_key'],
            ['value' => $request->unsplash_access_key]
        );

        return back()->with('success', 'Klucze API zostały zapisane!');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'topics' => 'required|string|min:3',
            'count' => 'nullable|integer|min:1|max:10',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|string|max:500',
            'download_image' => 'nullable|boolean',
            'image_source' => 'nullable|in:unsplash,dalle3',
            'test_mode' => 'nullable|boolean',
        ], [
            'topics.required' => 'Pole tematy jest wymagane',
            'topics.min' => 'Tematy muszą mieć minimum 3 znaki',
        ]);

        $openaiKey = Setting::where('key', 'openai_api_key')->value('value');

        if (!$openaiKey) {
            return back()->withErrors(['error' => 'Brak klucza OpenAI API. Dodaj go w ustawieniach.']);
        }

        $topics = array_filter(array_map('trim', explode("\n", $request->topics)));
        
        if (empty($topics)) {
            return back()->withErrors(['topics' => 'Musisz podać przynajmniej jeden temat']);
        }
        
        $count = min((int)($request->count ?? 1), count($topics), 10);
        $topics = array_slice($topics, 0, $count);

        $generated = [];
        $errors = [];

        foreach ($topics as $index => $topic) {
            if (empty($topic) || strlen(trim($topic)) < 3) continue;

            try {
                $result = $this->generatePost($topic, $openaiKey, $request);

                if ($result['success']) {
                    $generated[] = $result['post'];
                } else {
                    $errors[] = "Temat '{$topic}': " . $result['error'];
                }
            } catch (\Exception $e) {
                $errors[] = "Temat '{$topic}': " . $e->getMessage();
            }
        }

        $message = "Wygenerowano " . count($generated) . " wpisów!";
        if (!empty($errors)) {
            $message .= " Błędy: " . count($errors);
        }

        return back()->with('success', $message)->with('generated', $generated)->with('errors', $errors);
    }

    private function generatePost($topic, $openaiKey, $request)
    {
        // Generuj treść
        $content = $this->generateContent($topic, $openaiKey);

        if (!$content) {
            return ['success' => false, 'error' => 'Błąd generowania treści'];
        }

        // Pobierz obrazek
        $imageUrl = null;
        if ($request->download_image) {
            $imageSource = $request->image_source ?? 'unsplash'; // 'unsplash' lub 'dalle3'
            $imageUrl = $this->getImageForTopic($topic, $imageSource);
        }

        // Wybierz tagi i kategorię
        $tags = $this->selectTags($content['title'], $request->tags);
        $categoryId = $request->category_id ?: $this->selectCategory($content['title']);

        // Utwórz wpis
        $admin = auth()->user();
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
            'content' => $content['body'],
            'meta_title' => $content['meta_title'],
            'meta_description' => $content['meta_description'],
            'meta_keywords' => $content['keywords'],
            'featured_image' => $imageUrl,
            'featured_image_alt' => $content['featured_image_alt'] ?? $content['title'],
            'status' => $request->test_mode ? 'draft' : 'published',
            'published_at' => $request->test_mode ? null : now()->subDays(rand(0, 30)),
        ]);

        // Przypisz tagi
        if (!empty($tags)) {
            $post->tags()->sync($tags);
        }

        return ['success' => true, 'post' => $post];
    }

    private function generateContent($topic, $openaiKey)
    {
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
    \"body\": \"<h2>...</h2><p>...</p>...\",
    \"meta_title\": \"...\",
    \"meta_description\": \"...\",
    \"keywords\": \"słowo1, słowo2, słowo3\",
    \"featured_image_alt\": \"...\"
}";

        try {
            $response = Http::timeout(120)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $openaiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
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
                return null;
            }

            $result = $response->json();
            $text = $result['choices'][0]['message']['content'] ?? '';

            // Wyczyść odpowiedź
            $text = preg_replace('/```json\s*|\s*```/', '', $text);
            $text = trim($text);
            $text = preg_replace('/^[^{]*/', '', $text);
            $text = preg_replace('/[^}]*$/', '', $text) . '}';

            $content = json_decode($text, true);

            if (!$content || !isset($content['title'])) {
                return null;
            }

            // Konwertuj keywords na array jeśli jest stringiem
            if (isset($content['keywords']) && is_string($content['keywords'])) {
                $content['keywords'] = array_map('trim', explode(',', $content['keywords']));
            }

            return $content;

        } catch (\Exception $e) {
            return null;
        }
    }

    private function getImageForTopic($topic, $source = 'unsplash')
    {
        if ($source === 'dalle3') {
            return $this->generateImageWithDalle3($topic);
        }

        // Unsplash (domyślne)
        $keywords = $this->extractKeywords($topic);
        $keyword = urlencode($keywords[0] ?? 'freelancer');

        $unsplashKey = Setting::where('key', 'unsplash_access_key')->value('value');

        if ($unsplashKey) {
            try {
                $response = Http::get('https://api.unsplash.com/photos/random', [
                    'client_id' => $unsplashKey,
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

        return "https://source.unsplash.com/1200x630/?{$keyword}";
    }

    private function generateImageWithDalle3($topic)
    {
        $openaiKey = Setting::where('key', 'openai_api_key')->value('value');

        if (!$openaiKey) {
            return null;
        }

        // Przygotuj prompt dla DALL-E 3 na podstawie tematu
        $keywords = $this->extractKeywords($topic);
        $imagePrompt = "Professional, modern illustration for a blog article about: {$topic}. ";
        $imagePrompt .= "Style: clean, minimalist, business-oriented, suitable for freelancers. ";
        $imagePrompt .= "Colors: blue and purple gradient, professional. ";
        $imagePrompt .= "No text, no watermark, high quality, 16:9 aspect ratio.";

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $openaiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/images/generations', [
                    'model' => 'dall-e-3',
                    'prompt' => $imagePrompt,
                    'size' => '1024x1024', // DALL-E 3 obsługuje tylko 1024x1024, 1024x1792, 1792x1024
                    'quality' => 'standard', // 'standard' lub 'hd'
                    'n' => 1,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $imageUrl = $data['data'][0]['url'] ?? null;

                if ($imageUrl) {
                    // Pobierz obrazek i zapisz lokalnie
                    return $this->downloadAndSaveImage($imageUrl, $topic);
                }
            }
        } catch (\Exception $e) {
            Log::error('Błąd generowania obrazu DALL-E 3: ' . $e->getMessage());
            return null;
        }

        return null;
    }

    private function downloadAndSaveImage($imageUrl, $topic)
    {
        try {
            $imageContent = Http::timeout(30)->get($imageUrl)->body();

            // Generuj unikalną nazwę pliku
            $filename = 'blog/' . time() . '-' . Str::slug($topic) . '.png';

            // Zapisz do storage
            Storage::disk('public')->put($filename, $imageContent);

            return $filename;
        } catch (\Exception $e) {
            Log::error('Błąd pobierania obrazu: ' . $e->getMessage());
            return null;
        }
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

        if (str_contains($titleLower, 'automatyzacja') || str_contains($titleLower, 'ai')) {
            $category = Category::where('slug', 'automatyzacje')->first();
            if ($category) return $category->id;
        }

        if (str_contains($titleLower, 'seo') || str_contains($titleLower, 'optymalizacja')) {
            $category = Category::where('slug', 'seo')->first();
            if ($category) return $category->id;
        }

        return Category::first()?->id;
    }
}

