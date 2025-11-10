@extends('admin.layout')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="w-12 h-12 bg-gradient-to-r from-green-600 to-teal-600 rounded-xl flex items-center justify-center text-white text-2xl shadow-lg">
            👥
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Użytkownicy</h1>
            <p class="text-gray-600">Zarządzaj kontami użytkowników</p>
        </div>
    </div>
    <a href="{{ route('admin.users.create') }}"
       class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
        <i class="fa-solid fa-user-plus"></i>
        Dodaj użytkownika
    </a>
</div>

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
        <p class="font-semibold">✅ {{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
        <p class="font-semibold">❌ {{ session('error') }}</p>
    </div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium">Wszyscy</p>
                <p class="text-3xl font-bold text-gray-900">{{ \App\Models\User::count() }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-users text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium">Administratorzy</p>
                <p class="text-3xl font-bold text-gray-900">{{ \App\Models\User::where('role', 'admin')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-shield text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium">Klienci</p>
                <p class="text-3xl font-bold text-gray-900">{{ \App\Models\User::where('role', 'client')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-briefcase text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium">Freelancerzy</p>
                <p class="text-3xl font-bold text-gray-900">{{ \App\Models\User::where('role', 'freelancer')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-code text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

{{-- Users Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Użytkownik</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Rola</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dołączył</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Akcje</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" class="w-10 h-10 rounded-full object-cover mr-3">
                                @else
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                                    @if($user->company)
                                        <div class="text-xs text-gray-500">{{ $user->company }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->role === 'admin')
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">🛡️ Admin</span>
                            @elseif($user->role === 'client')
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">💼 Klient</span>
                            @else
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">💻 Freelancer</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->email_verified_at)
                                <span class="text-green-600 text-sm font-medium">✅ Zweryfikowany</span>
                            @else
                                <span class="text-gray-400 text-sm font-medium">⏳ Niezweryfikowany</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->created_at->format('d.m.Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('users.profile', $user) }}" target="_blank"
                                   class="text-blue-600 hover:text-blue-700 p-2 hover:bg-blue-50 rounded-lg transition-colors" title="Zobacz profil">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                   class="text-orange-600 hover:text-orange-700 p-2 hover:bg-orange-50 rounded-lg transition-colors" title="Edytuj">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.delete', $user->id) }}"
                                          onsubmit="return confirm('Czy na pewno chcesz usunąć tego użytkownika?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg transition-colors" title="Usuń">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fa-solid fa-users text-4xl mb-3 block"></i>
                            <p class="font-semibold">Brak użytkowników</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection

