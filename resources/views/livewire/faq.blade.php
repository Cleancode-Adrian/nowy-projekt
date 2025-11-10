<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">❓ Centrum Pomocy</h1>
        <p class="text-gray-600 mb-12">Najczęściej zadawane pytania</p>

        @foreach($faqs as $section)
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $section['category'] }}</h2>

                <div class="space-y-4">
                    @foreach($section['questions'] as $faq)
                        <div class="card" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full text-left flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $faq['q'] }}</h3>
                                <i class="fa-solid fa-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div x-show="open" x-collapse class="mt-4 text-gray-700">
                                {{ $faq['a'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="card bg-blue-50 border-blue-200 mt-12">
            <h3 class="text-xl font-bold text-gray-900 mb-3">💬 Nie znalazłeś odpowiedzi?</h3>
            <p class="text-gray-700 mb-4">Skontaktuj się z nami:</p>
            <a href="mailto:biuro@cleancodeas.pl" class="text-blue-600 hover:text-blue-700 font-semibold">
                biuro@cleancodeas.pl
            </a>
        </div>
    </div>
</div>

