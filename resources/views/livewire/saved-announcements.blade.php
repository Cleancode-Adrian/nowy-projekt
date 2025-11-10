<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🔖 Zapisane ogłoszenia</h1>
            <p class="text-gray-600">{{ $savedAnnouncements->total() }} zapisanych projektów</p>
        </div>

        {{-- Loading --}}
        <div wire:loading class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="text-gray-600 mt-2">Ładowanie...</p>
        </div>

        {{-- Grid --}}
        <div wire:loading.remove>
            @if($savedAnnouncements->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach($savedAnnouncements as $announcement)
                        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow border border-gray-100 overflow-hidden">
                            <div class="p-6">
                                {{-- Header --}}
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-xs font-medium px-3 py-1 rounded-full"
                                          style="background-color: {{ $announcement->category->color }}20; color: {{ $announcement->category->color }}">
                                        {{ $announcement->category->name }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        {{ $announcement->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                {{-- Title --}}
                                <h3 class="text-xl font-semibold text-gray-900 mb-3 line-clamp-2">
                                    {{ $announcement->title }}
                                </h3>

                                {{-- Description --}}
                                <p class="text-gray-600 mb-4 line-clamp-3">
                                    {{ $announcement->description }}
                                </p>

                                {{-- Meta --}}
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <span class="text-green-500 mr-2">💰</span>
                                        <span class="font-semibold text-gray-900 text-sm">
                                            {{ $announcement->budget_range ?? 'Do uzgodnienia' }}
                                        </span>
                                    </div>
                                    @if($announcement->is_urgent)
                                        <span class="text-red-500 text-xl" title="Projekt pilny">🔥</span>
                                    @endif
                                </div>

                                {{-- Tags --}}
                                @if($announcement->tags->count() > 0)
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        @foreach($announcement->tags->take(3) as $tag)
                                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Actions --}}
                                <div class="flex items-center gap-2 pt-4 border-t border-gray-100">
                                    <button wire:click="unsave({{ $announcement->id }})"
                                            wire:confirm="Usunąć z zapisanych?"
                                            class="flex-1 border border-red-300 hover:border-red-400 text-red-600 hover:text-red-700 py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                                        🗑️ Usuń
                                    </button>
                                    <a href="{{ route('announcements.show', $announcement) }}"
                                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium text-center transition-colors">
                                        Zobacz
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $savedAnnouncements->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <div class="text-center py-16">
                    <div class="text-6xl mb-6">🔖</div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">Brak zapisanych ogłoszeń</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        Nie masz jeszcze żadnych zapisanych projektów. Przeglądaj ogłoszenia i zapisuj te, które Cię interesują!
                    </p>
                    <a href="{{ route('announcements.index') }}" class="inline-flex items-center btn btn-primary">
                        📋 Przeglądaj ogłoszenia
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>
