@extends('admin.layout')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.users') }}" class="text-blue-600 hover:text-blue-700 font-medium">
        ← Powrót do listy użytkowników
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-start justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $user->name }}</h2>
            <p class="text-gray-600">{{ $user->email }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                ✏️ Edytuj
            </a>
            @if($user->is_approved)
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                    ✅ Zatwierdzony
                </span>
            @else
                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-medium">
                    ⏳ Oczekuje
                </span>
                <form method="POST" action="{{ route('admin.users.approve', $user->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        ✅ Zatwierdź
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-6">
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Informacje</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Rola:</span>
                    <span class="font-medium text-gray-900">{{ $user->role === 'admin' ? 'Administrator' : ($user->role === 'freelancer' ? 'Freelancer' : 'Klient') }}</span>
                </div>
                @if($user->phone)
                <div class="flex justify-between">
                    <span class="text-gray-600">Telefon:</span>
                    <span class="font-medium text-gray-900">{{ $user->phone }}</span>
                </div>
                @endif
                @if($user->company)
                <div class="flex justify-between">
                    <span class="text-gray-600">Firma:</span>
                    <span class="font-medium text-gray-900">{{ $user->company }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600">Data rejestracji:</span>
                    <span class="font-medium text-gray-900">{{ $user->created_at->format('d.m.Y H:i') }}</span>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Statystyki</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Ogłoszenia:</span>
                    <span class="font-medium text-gray-900">{{ $user->announcements->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Opublikowane:</span>
                    <span class="font-medium text-green-600">{{ $user->announcements->where('is_approved', true)->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Oczekujące:</span>
                    <span class="font-medium text-yellow-600">{{ $user->announcements->where('is_approved', false)->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($user->bio)
    <div class="mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-2">Bio</h3>
        <p class="text-gray-600">{{ $user->bio }}</p>
    </div>
    @endif

    @if(!$user->isAdmin() && $user->id !== auth()->id())
    <div class="pt-6 border-t border-gray-200">
        <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" onsubmit="return confirm('Czy na pewno chcesz usunąć tego użytkownika? Wszystkie jego ogłoszenia również zostaną usunięte.')">
            @csrf
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium">
                🗑️ Usuń użytkownika
            </button>
        </form>
    </div>
    @endif
</div>

<!-- User's Announcements -->
@if($user->announcements->count() > 0)
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h3 class="text-xl font-bold text-gray-900 mb-4">Ogłoszenia użytkownika ({{ $user->announcements->count() }})</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-sm font-semibold text-gray-700">Tytuł</th>
                    <th class="text-left px-4 py-3 text-sm font-semibold text-gray-700">Kategoria</th>
                    <th class="text-left px-4 py-3 text-sm font-semibold text-gray-700">Status</th>
                    <th class="text-left px-4 py-3 text-sm font-semibold text-gray-700">Data</th>
                    <th class="text-right px-4 py-3 text-sm font-semibold text-gray-700">Akcje</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($user->announcements as $announcement)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $announcement->title }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-1 rounded" style="background-color: {{ $announcement->category->color }}20; color: {{ $announcement->category->color }}">
                            {{ $announcement->category->name }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if($announcement->is_approved && $announcement->status === 'published')
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">✅ Opublikowane</span>
                        @elseif($announcement->status === 'closed')
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs font-medium">🔒 Zamknięte</span>
                        @elseif($announcement->status === 'rejected')
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-medium">❌ Odrzucone</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-medium">⏳ Oczekuje</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ $announcement->created_at->format('d.m.Y') }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.announcements.edit', $announcement->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            ✏️ Edytuj
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

