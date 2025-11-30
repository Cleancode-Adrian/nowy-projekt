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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
        try {
            $request->validate([
                'is_enabled' => 'nullable|boolean',
                'time' => ['required', 'string', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
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

            $data = [
                'is_enabled' => $request->has('is_enabled'),
                'time' => $request->time,
                'frequency' => $request->frequency,
                'count' => $request->count,
                'topics' => $request->topics,
                'category_id' => $request->category_id ?: null,
                'tags' => $request->tags ?: null,
                'download_image' => $request->has('download_image'),
                'auto_publish' => $request->has('auto_publish'),
            ];

            // Sprawdź czy kolumna image_source istnieje przed dodaniem
            try {
                $data['image_source'] = $request->image_source ?? 'unsplash';
            } catch (\Exception $e) {
                Log::warning('Kolumna image_source nie istnieje w tabeli blog_schedules: ' . $e->getMessage());
                // Pomijamy image_source jeśli kolumna nie istnieje
            }

            $schedule->fill($data);
            $schedule->save();

            Log::info('Harmonogram zapisany pomyślnie', ['schedule_id' => $schedule->id]);

            return back()->with('success', 'Harmonogram został zapisany!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Błąd walidacji harmonogramu', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Błąd zapisywania harmonogramu', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->withErrors(['error' => 'Błąd zapisywania harmonogramu: ' . $e->getMessage()])->withInput();
        }
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
        try {
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
                Log::warning('Próba generowania bez klucza OpenAI API');
                return back()->withErrors(['error' => 'Brak klucza OpenAI API. Dodaj go w ustawieniach.']);
            }

            $topics = array_filter(array_map('trim', explode("\n", $request->topics)));

            if (empty($topics)) {
                Log::warning('Próba generowania z pustymi tematami');
                return back()->withErrors(['topics' => 'Musisz podać przynajmniej jeden temat']);
            }

            $count = min((int)($request->count ?? 1), count($topics), 10);
            $topics = array_slice($topics, 0, $count);

            Log::info('Rozpoczynam generowanie wpisów', ['count' => $count, 'topics' => $topics]);

            $generated = [];
            $errors = [];

            foreach ($topics as $index => $topic) {
                if (empty($topic) || strlen(trim($topic)) < 3) continue;

                try {
                    Log::info("Generuję wpis", ['topic' => $topic, 'index' => $index]);
                    $result = $this->generatePost($topic, $openaiKey, $request);

                    if ($result['success']) {
                        $generated[] = $result['post'];
                        Log::info("Wpis wygenerowany pomyślnie", ['post_id' => $result['post']->id]);
                    } else {
                        $errorMsg = "Temat '{$topic}': " . $result['error'];
                        $errors[] = $errorMsg;
                        Log::error("Błąd generowania wpisu", ['topic' => $topic, 'error' => $result['error']]);
                    }
                } catch (\Exception $e) {
                    $errorMsg = "Temat '{$topic}': " . $e->getMessage();
                    $errors[] = $errorMsg;
                    Log::error("Wyjątek podczas generowania wpisu", [
                        'topic' => $topic,
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            $message = "Wygenerowano " . count($generated) . " wpisów!";
            if (!empty($errors)) {
                $message .= " Błędy: " . count($errors);
            }

            Log::info('Zakończono generowanie', ['generated' => count($generated), 'errors' => count($errors)]);

            return back()->with('success', $message)->with('generated', $generated)->with('errors', $errors);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Błąd walidacji generowania', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Krytyczny błąd generowania', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Błąd generowania: ' . $e->getMessage()]);
        }
    }

    private function generatePost($topic, $openaiKey, $request)
    {
        try {
            // Generuj treść
            Log::info("Generuję treść dla tematu", ['topic' => $topic]);
            $content = $this->generateContent($topic, $openaiKey);

            if (!$content) {
                Log::error("Nie udało się wygenerować treści", ['topic' => $topic]);
                return ['success' => false, 'error' => 'Błąd generowania treści'];
            }

            // Pobierz obrazek
            $imageUrl = null;
            if ($request->has('download_image') && $request->download_image) {
                try {
                    $imageSource = $request->image_source ?? 'unsplash'; // 'unsplash' lub 'dalle3'
                    Log::info("Pobieram obrazek", ['topic' => $topic, 'source' => $imageSource]);
                    $imageUrl = $this->getImageForTopic($topic, $imageSource);
                    if ($imageUrl) {
                        Log::info("Obrazek pobrany", ['url' => $imageUrl]);
                    } else {
                        Log::warning("Nie udało się pobrać obrazka", ['topic' => $topic, 'source' => $imageSource]);
                    }
                } catch (\Exception $e) {
                    Log::error("Błąd pobierania obrazka", [
                        'topic' => $topic,
                        'message' => $e->getMessage()
                    ]);
                    // Kontynuuj bez obrazka
                }
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
                try {
                    $post->tags()->sync($tags);
                    Log::info("Tagi przypisane", ['post_id' => $post->id, 'tags_count' => count($tags)]);
                } catch (\Exception $e) {
                    Log::error("Błąd przypisywania tagów", [
                        'post_id' => $post->id,
                        'message' => $e->getMessage()
                    ]);
                    // Kontynuuj bez tagów
                }
            }

            Log::info("Wpis utworzony pomyślnie", ['post_id' => $post->id, 'title' => $post->title]);
            return ['success' => true, 'post' => $post];
        } catch (\Exception $e) {
            Log::error("Krytyczny błąd w generatePost", [
                'topic' => $topic,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return ['success' => false, 'error' => 'Błąd tworzenia wpisu: ' . $e->getMessage()];
        }
    }

    private function generateContent($topic, $openaiKey)
    {
        $prompt = "Napisz profesjonalny, szczegółowy artykuł na blog dla freelancerów w języku polskim o temacie: '{$topic}'.

WYMAGANIA:
1. Tytuł: Ciekawy, SEO-friendly (50-70 znaków), z liczbą roczną jeśli dotyczy (np. 2025)
2. Zajawka: 1-2 zdania (150-200 znaków), zachęcająca do czytania
3. Treść: MINIMUM 2000-2500 słów w HTML (używaj h2, h3, p, ul, li, strong, em, a, table, thead, tbody, tr, th, td, img)
4. Meta tytuł: SEO (maks 60 znaków)
5. Meta opis: SEO (maks 160 znaków), z call-to-action
6. Słowa kluczowe: 5-8 słów oddzielonych przecinkami
7. Alt text dla zdjęcia: Opisowy (maks 100 znaków)

OBOWIĄZKOWA STRUKTURA TREŚCI:
- Wprowadzenie (3-4 akapity, minimum 300 słów)
- MINIMUM 4-6 sekcji z nagłówkami h2 (każda po 300-400 słów)
- Podsekcje z h3 gdzie potrzebne
- Listy punktowane i numerowane (minimum 2-3 listy)
- Przykłady i case studies z konkretnymi danymi
- MINIMUM 1 TABELA porównawcza w formacie HTML (table, thead, tbody, tr, th, td)
- MINIMUM 2-3 zewnętrzne źródła z linkami (np. badania, raporty McKinsey, HubSpot, Statista)
- MINIMUM 1 obrazek w treści (użyj <img src=\"https://images.unsplash.com/photo-...\" alt=\"opis\" /> lub podobny placeholder)
- Wnioski i call-to-action na końcu (2-3 akapity)

STYL I ZAWARTOŚĆ:
- Praktyczny i wartościowy - daj konkretne, użyteczne informacje
- Z konkretnymi przykładami, liczbami, statystykami
- Profesjonalny, ale przystępny
- Z linkami wewnętrznymi do Projekciarz.pl (np. /ogloszenia, /ranking) - minimum 2 linki
- Z zewnętrznymi źródłami (dodaj linki do wiarygodnych badań, raportów, artykułów)
- Użyj tabel do porównań, zestawień, danych statystycznych
- Dodaj obrazki ilustrujące kluczowe koncepcje

ŹRÓDŁA:
- Dodaj sekcję \"Źródła\" lub \"Bibliografia\" na końcu z linkami do:
  * Raportów branżowych (np. McKinsey, Deloitte, PwC)
  * Badań statystycznych (np. Statista, GUS)
  * Artykułów eksperckich (np. Harvard Business Review, HubSpot)
  * Oficjalnych stron i dokumentów

Zakończ artykuł zachętą do rejestracji na Projekciarz.pl.

WAŻNE: Treść musi być DŁUGA (minimum 2000 słów), szczegółowa, z tabelami, źródłami i obrazkami. Nie skracaj!

ZWRÓĆ TYLKO JSON (bez markdown, bez dodatkowych komentarzy):
{
    \"title\": \"...\",
    \"excerpt\": \"...\",
    \"body\": \"<h2>...</h2><p>...</p><table>...</table>...\",
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
                    'max_tokens' => 8000, // Zwiększone dla dłuższych treści z tabelami i źródłami
                    'response_format' => ['type' => 'json_object'], // Wymusza format JSON
                ]);

            if ($response->failed()) {
                $errorBody = $response->body();
                $statusCode = $response->status();
                Log::error('OpenAI API request failed', [
                    'topic' => $topic,
                    'status' => $statusCode,
                    'response' => $errorBody
                ]);
                return null;
            }

            $result = $response->json();
            $text = $result['choices'][0]['message']['content'] ?? '';

            if (empty($text)) {
                Log::error('OpenAI API returned empty content', [
                    'topic' => $topic,
                    'response' => $result
                ]);
                return null;
            }

            // Wyczyść odpowiedź - usuń markdown code blocks
            $text = preg_replace('/```json\s*/', '', $text);
            $text = preg_replace('/\s*```/', '', $text);
            $text = trim($text);

            // Znajdź pierwszy { i ostatni } - to powinien być kompletny JSON
            $firstBrace = strpos($text, '{');
            $lastBrace = strrpos($text, '}');

            if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
                $text = substr($text, $firstBrace, $lastBrace - $firstBrace + 1);
            } else {
                // Fallback - spróbuj znaleźć JSON w inny sposób
                if (preg_match('/\{.*\}/s', $text, $matches)) {
                    $text = $matches[0];
                }
            }

            $content = json_decode($text, true);
            $jsonError = json_last_error();

            if ($jsonError !== JSON_ERROR_NONE) {
                Log::error('JSON decode error in generateContent', [
                    'topic' => $topic,
                    'json_error' => json_last_error_msg(),
                    'raw_text' => substr($text, 0, 500) // Pierwsze 500 znaków dla debugowania
                ]);
                return null;
            }

            if (!$content || !isset($content['title'])) {
                Log::error('Invalid content structure from OpenAI', [
                    'topic' => $topic,
                    'content_keys' => $content ? array_keys($content) : 'null',
                    'raw_text' => substr($text, 0, 500)
                ]);
                return null;
            }

            // Konwertuj keywords na array jeśli jest stringiem
            if (isset($content['keywords']) && is_string($content['keywords'])) {
                $content['keywords'] = array_map('trim', explode(',', $content['keywords']));
            }

            return $content;

        } catch (\Exception $e) {
            Log::error('Exception in generateContent', [
                'topic' => $topic,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
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

