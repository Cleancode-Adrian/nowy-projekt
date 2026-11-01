<?php

namespace App\Models;

use App\Services\HtmlSanitizer;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
        'featured_image',
        'featured_image_alt',
        'status',
        'published_at',
        'content_updated_at',
        'views_count',
        'meta_keywords',
        'tldr',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'content_updated_at' => 'datetime',
        'meta_keywords' => 'array',
    ];

    /**
     * Treść wpisu renderujemy przez {!! !!}, więc sanityzujemy ją przy zapisie -
     * niezależnie od tego, czy przyszła z panelu admina, czy z generatora AI.
     */
    protected function content(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(HtmlSanitizer::class)->clean($value),
        );
    }

    /**
     * Do użycia w widokach zamiast {!! $post->content !!} - czyści również
     * wpisy zapisane zanim sanityzacja została wprowadzona.
     */
    protected function safeContent(): Attribute
    {
        return Attribute::make(
            get: fn () => app(HtmlSanitizer::class)->clean($this->content),
        )->shouldCache();
    }

    /**
     * TL;DR jako lista punktów (w bazie: jeden punkt na linię, opcjonalny prefiks "- ").
     *
     * @return list<string>
     */
    protected function tldrItems(): Attribute
    {
        return Attribute::make(
            get: fn () => collect(preg_split('/\R/u', (string) $this->tldr))
                ->map(fn ($line) => trim(preg_replace('/^\s*[-*•]\s*/u', '', $line)))
                ->filter()
                ->values()
                ->all(),
        )->shouldCache();
    }

    /**
     * Treść z kotwicami (id) na nagłówkach h2/h3 - do spisu treści.
     * Id nadajemy po sanityzacji, więc nie pochodzą z danych wejściowych.
     */
    protected function contentWithAnchors(): Attribute
    {
        return Attribute::make(get: fn () => $this->buildAnchors()['html'])->shouldCache();
    }

    /**
     * Spis treści: [['id' => ..., 'text' => ..., 'level' => 2|3], ...]
     *
     * @return list<array{id: string, text: string, level: int}>
     */
    protected function tableOfContents(): Attribute
    {
        return Attribute::make(get: fn () => $this->buildAnchors()['toc'])->shouldCache();
    }

    /**
     * Data, którą pokazujemy jako "zaktualizowano" - tylko gdy treść faktycznie
     * zmieniła się po publikacji (co najmniej dzień później, żeby poprawka
     * literówki w dniu publikacji nie robiła z wpisu "zaktualizowanego").
     */
    public function getDisplayUpdatedAtAttribute(): ?\Illuminate\Support\Carbon
    {
        if (!$this->content_updated_at || !$this->published_at) {
            return null;
        }

        return $this->content_updated_at->gt($this->published_at->copy()->addDay())
            ? $this->content_updated_at
            : null;
    }

    /**
     * @return array{html: string, toc: list<array{id: string, text: string, level: int}>}
     */
    private function buildAnchors(): array
    {
        $toc = [];
        $used = [];

        $html = preg_replace_callback(
            '/<h([23])\b([^>]*)>(.*?)<\/h\1>/isu',
            function (array $m) use (&$toc, &$used) {
                $level = (int) $m[1];
                $text = trim(html_entity_decode(strip_tags($m[3]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                $base = Str::slug($text) ?: 'sekcja';
                $id = $base;
                for ($i = 2; isset($used[$id]); $i++) {
                    $id = "{$base}-{$i}";
                }
                $used[$id] = true;

                $toc[] = ['id' => $id, 'text' => $text, 'level' => $level];

                // Usuwamy ewentualne id z wejścia, żeby nie było dwóch atrybutów.
                $attrs = preg_replace('/\s+id\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/iu', '', $m[2]);

                return "<h{$level}{$attrs} id=\"{$id}\">{$m[3]}</h{$level}>";
            },
            (string) $this->safe_content,
        );

        return ['html' => $html ?? (string) $this->safe_content, 'toc' => $toc];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });

        // Zmiana treści istniejącego wpisu = data aktualizacji. Ustawiana tylko
        // gdy ktoś nie podał jej jawnie (seeder, import).
        static::updating(function ($post) {
            if ($post->isDirty('content') && !$post->isDirty('content_updated_at')) {
                $post->content_updated_at = now();
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'blog_post_tag');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function incrementViews(): void
    {
        // Licznik wyświetleń nie jest zmianą wpisu - nie podbijamy updated_at
        // (sitemapa i "data aktualizacji" przestałyby cokolwiek znaczyć).
        static::withoutTimestamps(fn () => $this->increment('views_count'));
    }

    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags($this->content));
        return ceil($words / 200); // ~200 słów/minutę
    }
}

