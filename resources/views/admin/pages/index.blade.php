@extends('admin.layout')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-r from-indigo-600 to-blue-600 rounded-xl flex items-center justify-center text-white text-2xl shadow-lg">
                📄
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Zarządzanie stronami</h1>
                <p class="text-gray-600">Edytuj i zarządzaj stronami statycznymi</p>
            </div>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition-all shadow-lg hover:shadow-xl flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            Dodaj stronę
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-check-circle"></i>
            <p class="font-semibold">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-exclamation-circle"></i>
            <p class="font-semibold">{{ session('error') }}</p>
        </div>
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tytuł</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Slug</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Typ</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kolejność</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Akcje</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($pages as $page)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900">{{ $page->title }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-700">{{ $page->slug }}</code>
                        </td>
                        <td class="px-6 py-4">
                            @if($page->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fa-solid fa-check-circle mr-1"></i>
                                    Aktywna
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    <i class="fa-solid fa-times-circle mr-1"></i>
                                    Nieaktywna
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($page->is_system)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    <i class="fa-solid fa-cog mr-1"></i>
                                    Systemowa
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                    <i class="fa-solid fa-file mr-1"></i>
                                    Zwykła
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $page->order }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.pages.edit', $page->id) }}" class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                                    <i class="fa-solid fa-edit"></i>
                                    Edytuj
                                </a>
                                @if(!$page->is_system)
                                    <form method="POST" action="{{ route('admin.pages.delete', $page->id) }}" class="inline" onsubmit="return confirm('Czy na pewno chcesz usunąć tę stronę?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium flex items-center gap-1">
                                            <i class="fa-solid fa-trash"></i>
                                            Usuń
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fa-solid fa-inbox text-4xl mb-4 block"></i>
                            <p class="text-lg font-semibold">Brak stron</p>
                            <p class="text-sm mt-2">Dodaj pierwszą stronę, aby rozpocząć</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($pages->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pages->links() }}
        </div>
    @endif
</div>
@endsection

