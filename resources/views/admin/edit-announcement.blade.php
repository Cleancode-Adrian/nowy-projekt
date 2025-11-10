@extends('admin.layout')

@section('title', 'Edycja ogłoszenia')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <form method="POST" action="{{ route('admin.announcements.update', $announcement->id) }}">
            @csrf

            <div class="space-y-6">
                <!-- Podstawowe info (readonly) -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Użytkownik</label>
                        <input type="text" value="{{ $announcement->user->name }}" disabled
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategoria</label>
                        <input type="text" value="{{ $announcement->category->name }}" disabled
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tytuł</label>
                    <input type="text" value="{{ $announcement->title }}" disabled
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Opis</label>
                    <textarea disabled rows="4"
                              class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg">{{ $announcement->description }}</textarea>
                </div>

                <!-- Moderacja -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Moderacja</h3>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="pending" {{ $announcement->status === 'pending' ? 'selected' : '' }}>Oczekujące</option>
                                <option value="published" {{ $announcement->status === 'published' ? 'selected' : '' }}>Opublikowane</option>
                                <option value="rejected" {{ $announcement->status === 'rejected' ? 'selected' : '' }}>Odrzucone</option>
                                <option value="closed" {{ $announcement->status === 'closed' ? 'selected' : '' }}>Zamknięte</option>
                            </select>
                        </div>

                        <div>
                            <label class="flex items-center space-x-2 mt-8">
                                <input type="checkbox" name="is_approved" value="1"
                                       {{ $announcement->is_approved ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Zatwierdzone</span>
                            </label>
                        </div>

                        <div>
                            <label class="flex items-center space-x-2 mt-8">
                                <input type="checkbox" name="is_urgent" value="1"
                                       {{ $announcement->is_urgent ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Pilne</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Powód odrzucenia (opcjonalnie)</label>
                        <textarea name="rejection_reason" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ $announcement->rejection_reason }}</textarea>
                    </div>
                </div>

                <!-- Przyciski -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.announcements') }}"
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        ← Wróć
                    </a>
                    <button type="submit"
                            class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">
                        💾 Zapisz zmiany
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

