@extends('admin.layout')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-14 h-14 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl flex items-center justify-center text-white text-3xl shadow-lg">
                📝
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Blog - Zarządzanie wpisami</h1>
                <p class="text-gray-600">Twórz i edytuj artykuły dla użytkowników</p>
            </div>
        </div>
        <a href="{{ route('admin.blog.create') }}"
           class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition-all shadow-lg hover:shadow-xl flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            Dodaj wpis
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-600 text-2xl"></i>
        <p class="text-green-900 font-medium">{{ session('success') }}</p>
    </div>
@endif

<div class="card">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 text-sm font-semibold">Tytuł</th>
                <th class="text-left px-4 py-3 text-sm font-semibold">Autor</th>
                <th class="text-left px-4 py-3 text-sm font-semibold">Status</th>
                <th class="text-left px-4 py-3 text-sm font-semibold">Wyświetlenia</th>
                <th class="text-left px-4 py-3 text-sm font-semibold">Data</th>
                <th class="text-right px-4 py-3 text-sm font-semibold">Akcje</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($posts as $post)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            @if($post->featured_image)
                                @if(str_starts_with($post->featured_image, 'http://') || str_starts_with($post->featured_image, 'https://'))
                                    <img src="{{ $post->featured_image }}"
                                         class="w-16 h-16 object-cover rounded"
                                         onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'64\' height=\'64\'%3E%3Crect fill=\'%23e5e7eb\' width=\'64\' height=\'64\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-size=\'24\'%3E📷%3C/text%3E%3C/svg%3E';">
                                @else
                                    <img src="{{ asset('storage/' . $post->featured_image) }}"
                                         class="w-16 h-16 object-cover rounded">
                                @endif
                            @endif
                            <div>
                                <div class="font-medium text-gray-900">{{ $post->title }}</div>
                                <div class="text-xs text-gray-500">/{{ $post->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-sm">{{ $post->author->name }}</td>
                    <td class="px-4 py-4">
                        @if($post->status === 'published')
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">✅ Opublikowany</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-medium">📝 Szkic</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-sm">{{ $post->views_count }}</td>
                    <td class="px-4 py-4 text-sm">{{ $post->created_at->format('d.m.Y') }}</td>
                    <td class="px-4 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($post->status === 'published')
                                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    👁️ Zobacz
                                </a>
                            @endif
                            <a href="{{ route('admin.blog.edit', $post) }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                                ✏️ Edytuj
                            </a>
                            <form method="POST" action="{{ route('admin.blog.delete', $post) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Czy na pewno chcesz usunąć ten wpis?')"
                                        class="text-red-600 hover:text-red-700 text-sm font-medium">
                                    🗑️ Usuń
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-6">
        {{ $posts->links() }}
    </div>
</div>
@endsection

