<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8 md:p-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-6">{{ $page->title }}</h1>
        
        <div class="prose prose-lg max-w-none">
            {!! $page->content !!}
        </div>
    </div>
</div>

<style>
.prose {
    color: #374151;
    line-height: 1.75;
}

.prose h1, .prose h2, .prose h3, .prose h4 {
    color: #111827;
    font-weight: 700;
    margin-top: 2em;
    margin-bottom: 1em;
}

.prose h2 {
    font-size: 1.875em;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 0.5em;
}

.prose h3 {
    font-size: 1.5em;
}

.prose p {
    margin-top: 1.25em;
    margin-bottom: 1.25em;
}

.prose ul, .prose ol {
    margin-top: 1.25em;
    margin-bottom: 1.25em;
    padding-left: 1.625em;
}

.prose li {
    margin-top: 0.5em;
    margin-bottom: 0.5em;
}

.prose a {
    color: #2563eb;
    text-decoration: underline;
}

.prose a:hover {
    color: #1d4ed8;
}

.prose strong {
    font-weight: 600;
    color: #111827;
}

.prose blockquote {
    border-left: 4px solid #3b82f6;
    padding-left: 1em;
    margin: 1.5em 0;
    font-style: italic;
    color: #6b7280;
}

.prose code {
    background-color: #f3f4f6;
    padding: 0.2em 0.4em;
    border-radius: 0.25em;
    font-size: 0.875em;
}

.prose img {
    max-width: 100%;
    height: auto;
    border-radius: 0.5em;
    margin: 1.5em 0;
}
</style>

