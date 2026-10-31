@extends('admin.layout')

@section('content')
<div class="mb-8 flex items-center gap-3">
    <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-xl flex items-center justify-center text-white text-2xl shadow-lg">
        🛡️
    </div>
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Weryfikacja wykonawców</h1>
        <p class="text-gray-600">Przejrzyj wnioski o odznakę "Zweryfikowany"</p>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
        <p class="font-semibold">✅ {{ session('success') }}</p>
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-6 py-4 border-b border-gray-200">
        <h2 class="font-bold text-gray-900">Oczekujące wnioski ({{ $pending->total() }})</h2>
    </div>

    <div class="divide-y divide-gray-100">
        @forelse($pending as $user)
            <div class="p-6 flex items-start justify-between gap-6 flex-wrap">
                <div>
                    <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <p class="text-xs text-gray-400 mt-1">Wniosek wysłany {{ $user->updated_at->diffForHumans() }}</p>

                    <a href="{{ route('admin.verifications.document', $user->id) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 mt-3 text-blue-600 hover:text-blue-700 text-sm font-medium">
                        <i class="fa-solid fa-file-lines"></i> Zobacz dokument
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('admin.verifications.approve', $user->id) }}">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                            ✅ Zatwierdź
                        </button>
                    </form>

                    <button type="button"
                            onclick="document.getElementById('reject-form-{{ $user->id }}').classList.toggle('hidden')"
                            class="bg-red-50 hover:bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm font-semibold">
                        ❌ Odrzuć
                    </button>
                </div>

                <form id="reject-form-{{ $user->id }}" method="POST" action="{{ route('admin.verifications.reject', $user->id) }}" class="hidden w-full flex gap-2 mt-2">
                    @csrf
                    <input type="text" name="note" placeholder="Powód odrzucenia (opcjonalnie)" class="input flex-1">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap">
                        Potwierdź odrzucenie
                    </button>
                </form>
            </div>
        @empty
            <div class="p-12 text-center text-gray-500">
                <div class="text-5xl mb-3">📭</div>
                Brak oczekujących wniosków
            </div>
        @endforelse
    </div>

    @if($pending->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $pending->links() }}
        </div>
    @endif
</div>
@endsection
