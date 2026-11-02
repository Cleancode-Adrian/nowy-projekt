<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">❓ Centrum Pomocy</h1>
        <p class="text-gray-600 mb-12">Najczęściej zadawane pytania</p>

        @foreach($faqs as $section)
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $section['category'] }}</h2>

                <div class="space-y-4">
                    @foreach($section['questions'] as $faq)
                        <div class="card" x-data="{ open: false }" x-cloak>
                            <button type="button" @click="open = !open" class="w-full text-left flex items-center justify-between focus:outline-none hover:bg-gray-50 p-2 -m-2 rounded transition-colors">
                                <h3 class="text-lg font-semibold text-gray-900 pr-4">{{ $faq['q'] }}</h3>
                                <i class="fa-solid fa-chevron-down transition-transform duration-300 flex-shrink-0" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 max-h-0"
                                x-transition:enter-end="opacity-100 max-h-[500px]"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 max-h-[500px]"
                                x-transition:leave-end="opacity-0 max-h-0"
                                class="mt-4 text-gray-700 overflow-hidden">
                                <div class="pb-2">
                                    {{ $faq['a'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="card bg-blue-50 border-blue-200 mt-12">
            <h3 class="text-xl font-bold text-gray-900 mb-3">💬 Nie znalazłeś odpowiedzi?</h3>
            <p class="text-gray-700 mb-4">Zadzwoń albo napisz - odpowiadamy w dni robocze:</p>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-6">
                <x-phone-link location="faq" class="btn btn-primary" />
                <a href="mailto:{{ config('app.contact.email') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                    {{ config('app.contact.email') }}
                </a>
            </div>
        </div>
    </div>
</div>

