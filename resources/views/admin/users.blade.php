@extends('admin.layout')

@section('title', 'Użytkownicy')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nazwa</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rola</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akcje</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($users as $user)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }} rounded-full">
                        {{ $user->role === 'admin' ? 'Administrator' : 'Użytkownik' }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    @if($user->is_approved)
                        <span class="text-green-500">✓ Zatwierdzony</span>
                    @else
                        <span class="text-yellow-600">⏳ Oczekuje</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->format('d.m.Y') }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.users.view', $user->id) }}"
                           class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            👁️ Podgląd
                        </a>
                        @if(!$user->is_approved)
                        <form method="POST" action="{{ route('admin.users.approve', $user->id) }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded"
                                    onclick="return confirm('Zatwierdzić użytkownika?')">
                                ✅ Zatwierdź
                            </button>
                        </form>
                        @endif
                        @if(!$user->isAdmin() && $user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="text-red-600 hover:text-red-700 text-sm font-medium"
                                    onclick="return confirm('Czy na pewno chcesz usunąć tego użytkownika? Wszystkie jego ogłoszenia również zostaną usunięte.')">
                                🗑️ Usuń
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="px-6 py-4 bg-gray-50">
        {{ $users->links() }}
    </div>
</div>
@endsection

