@extends('admin.layout')

@section('title', 'Ogłoszenia')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tytuł</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Użytkownik</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategoria</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budżet</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zatw.</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akcje</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($announcements as $announcement)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">{{ $announcement->title }}</div>
                    <div class="text-sm text-gray-500">{{ Str::limit($announcement->description, 50) }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $announcement->user->name }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full" style="background-color: {{ $announcement->category->color }}20; color: {{ $announcement->category->color }}">
                        {{ $announcement->category->name }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $announcement->budget_range }}</td>
                <td class="px-6 py-4">
                    @if($announcement->status === 'pending')
                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Oczekujące</span>
                    @elseif($announcement->status === 'published')
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Opublikowane</span>
                    @else
                        <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Odrzucone</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-center">
                    @if($announcement->is_approved)
                        <span class="text-green-500">✓</span>
                    @else
                        <span class="text-red-500">✗</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.announcements.edit', $announcement->id) }}"
                           class="text-blue-600 hover:text-blue-800">
                            ✏️
                        </a>
                        @if(!$announcement->is_approved)
                        <form method="POST" action="{{ route('admin.announcements.approve', $announcement->id) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-800"
                                    onclick="return confirm('Zatwierdzić i opublikować?')">
                                ✅
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    Brak ogłoszeń
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-6 py-4 bg-gray-50">
        {{ $announcements->links() }}
    </div>
</div>
@endsection

