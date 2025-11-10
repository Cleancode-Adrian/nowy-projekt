@extends('admin.layout')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="w-12 h-12 bg-gradient-to-r from-yellow-600 to-orange-600 rounded-xl flex items-center justify-center text-white text-2xl shadow-lg">
            ⭐
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Moderacja opinii</h1>
            <p class="text-gray-600">Zarządzaj opiniami użytkowników</p>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
        <p class="font-semibold">✅ {{ session('success') }}</p>
    </div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium">Oczekujące</p>
                <p class="text-3xl font-bold text-orange-600">{{ \App\Models\Rating::where('is_approved', false)->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-clock text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium">Zaakceptowane</p>
                <p class="text-3xl font-bold text-green-600">{{ \App\Models\Rating::where('is_approved', true)->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-check text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

{{-- Pending Ratings --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
    <div class="bg-gradient-to-r from-orange-50 to-yellow-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fa-solid fa-hourglass-half text-orange-600"></i>
            Oczekujące na moderację ({{ $pending->total() }})
        </h2>
    </div>
    <div class="p-6">
        @forelse($pending as $rating)
            <div class="bg-gray-50 rounded-lg p-6 mb-4 border border-gray-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-4">
                        @if($rating->rater->avatar)
                            <img src="{{ asset('storage/' . $rating->rater->avatar) }}" class="w-12 h-12 rounded-full object-cover">
                        @else
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                {{ substr($rating->rater->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <p class="font-bold text-gray-900">{{ $rating->rater->name }}</p>
                            <p class="text-sm text-gray-600">wystawił opinię dla <strong>{{ $rating->rated->name }}</strong></p>
                            <p class="text-xs text-gray-500">{{ $rating->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $rating->rating)
                                    <span class="text-yellow-400">⭐</span>
                                @else
                                    <span class="text-gray-300">☆</span>
                                @endif
                            @endfor
                        </div>
                        <p class="text-sm text-gray-600 font-semibold">{{ $rating->rating }}/5</p>
                    </div>
                </div>

                @if($rating->comment)
                    <div class="bg-white p-4 rounded-lg border border-gray-200 mb-4">
                        <p class="text-gray-700 italic">"{{ $rating->comment }}"</p>
                    </div>
                @endif

                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        <p><strong>Projekt:</strong> {{ $rating->announcement->title }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('admin.ratings.approve', $rating->id) }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors flex items-center gap-2">
                                <i class="fa-solid fa-check"></i>
                                Zatwierdź
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.ratings.reject', $rating->id) }}"
                              onsubmit="return confirm('Czy na pewno chcesz odrzucić i usunąć tę opinię?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors flex items-center gap-2">
                                <i class="fa-solid fa-trash"></i>
                                Odrzuć
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-gray-500">
                <i class="fa-solid fa-check-circle text-6xl text-green-500 mb-4"></i>
                <p class="text-lg font-semibold">Brak opinii do moderacji!</p>
                <p class="text-sm">Wszystkie opinie zostały sprawdzone</p>
            </div>
        @endforelse

        @if($pending->hasPages())
            <div class="mt-6">
                {{ $pending->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Approved Ratings --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gradient-to-r from-green-50 to-teal-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fa-solid fa-check-circle text-green-600"></i>
            Zaakceptowane opinie ({{ $approved->total() }})
        </h2>
    </div>
    <div class="p-6">
        @forelse($approved as $rating)
            <div class="bg-gray-50 rounded-lg p-6 mb-4 border border-gray-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-4">
                        @if($rating->rater->avatar)
                            <img src="{{ asset('storage/' . $rating->rater->avatar) }}" class="w-12 h-12 rounded-full object-cover">
                        @else
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                {{ substr($rating->rater->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <p class="font-bold text-gray-900">{{ $rating->rater->name }}</p>
                            <p class="text-sm text-gray-600">wystawił opinię dla <strong>{{ $rating->rated->name }}</strong></p>
                            <p class="text-xs text-gray-500">
                                Zaakceptowano: {{ $rating->approved_at->format('d.m.Y H:i') }}
                                @if($rating->approvedBy)
                                    przez {{ $rating->approvedBy->name }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $rating->rating)
                                    <span class="text-yellow-400">⭐</span>
                                @else
                                    <span class="text-gray-300">☆</span>
                                @endif
                            @endfor
                        </div>
                        <p class="text-sm text-gray-600 font-semibold">{{ $rating->rating }}/5</p>
                    </div>
                </div>

                @if($rating->comment)
                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                        <p class="text-gray-700 italic">"{{ $rating->comment }}"</p>
                    </div>
                @endif

                <div class="text-sm text-gray-600 mt-4 pt-4 border-t border-gray-200">
                    <p><strong>Projekt:</strong> {{ $rating->announcement->title }}</p>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-gray-500">
                <i class="fa-solid fa-star text-6xl text-gray-300 mb-4"></i>
                <p class="text-lg font-semibold">Brak zaakceptowanych opinii</p>
            </div>
        @endforelse

        @if($approved->hasPages())
            <div class="mt-6">
                {{ $approved->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

