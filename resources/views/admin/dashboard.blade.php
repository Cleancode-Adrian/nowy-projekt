@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Panel Administracyjny</h1>
    <p class="text-gray-600">Witaj z powrotem! Oto przegląd systemu</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    {{-- Użytkownicy --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Wszyscy użytkownicy</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                @if($stats['pending_users'] > 0)
                    <p class="text-sm text-yellow-600 mt-1">⏳ {{ $stats['pending_users'] }} oczekujących</p>
                @endif
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-2xl">
                👥
            </div>
        </div>
    </div>

    {{-- Ogłoszenia --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Wszystkie ogłoszenia</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_announcements'] }}</p>
                @if($stats['pending_announcements'] > 0)
                    <p class="text-sm text-orange-600 mt-1">🔔 {{ $stats['pending_announcements'] }} oczekujących</p>
                @endif
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-2xl">
                📢
            </div>
        </div>
    </div>

    {{-- Opinie - NOWE! --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Wszystkie opinie</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_ratings'] }}</p>
                @if($stats['pending_ratings'] > 0)
                    <p class="text-sm text-red-600 mt-1 font-bold">⚠️ {{ $stats['pending_ratings'] }} do moderacji!</p>
                @else
                    <p class="text-sm text-green-600 mt-1">✅ Wszystko sprawdzone</p>
                @endif
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center text-2xl">
                ⭐
            </div>
        </div>
    </div>
</div>

{{-- Szybkie akcje --}}
<div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 mb-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">⚡ Szybkie akcje</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.users.index') }}" class="bg-white rounded-xl shadow-sm border-2 border-gray-200 hover:border-blue-400 p-6 hover:shadow-lg transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    👥
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Użytkownicy</h3>
                    <p class="text-sm text-gray-600">Zarządzaj kontami</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.announcements') }}" class="bg-white rounded-xl shadow-sm border-2 border-gray-200 hover:border-purple-400 p-6 hover:shadow-lg transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    📢
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Ogłoszenia</h3>
                    <p class="text-sm text-gray-600">Moderuj treści</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.ratings.index') }}" class="bg-white rounded-xl shadow-sm border-2 border-gray-200 hover:border-yellow-400 p-6 hover:shadow-lg transition-all group {{ $stats['pending_ratings'] > 0 ? 'ring-4 ring-red-200 animate-pulse' : '' }}">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center text-2xl group-hover:scale-110 transition-transform relative">
                    ⭐
                    @if($stats['pending_ratings'] > 0)
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $stats['pending_ratings'] }}</span>
                    @endif
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">
                        Opinie
                        @if($stats['pending_ratings'] > 0)
                            <span class="text-red-600">(!)</span>
                        @endif
                    </h3>
                    <p class="text-sm {{ $stats['pending_ratings'] > 0 ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                        @if($stats['pending_ratings'] > 0)
                            Do moderacji: {{ $stats['pending_ratings'] }}
                        @else
                            Wszystko sprawdzone
                        @endif
                    </p>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Dodatkowe moduły --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <a href="{{ route('admin.blog.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow group">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">📝 Zarządzaj blogiem</h3>
                <p class="text-gray-600">Dodawaj i edytuj wpisy na blogu</p>
            </div>
            <i class="fa-solid fa-arrow-right text-2xl text-gray-400 group-hover:text-purple-600 group-hover:translate-x-2 transition-all"></i>
        </div>
    </a>

    <a href="{{ route('home') }}" target="_blank" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow group">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">🌐 Zobacz stronę</h3>
                <p class="text-gray-600">Przejdź do publicznej strony serwisu</p>
            </div>
            <i class="fa-solid fa-external-link text-2xl text-gray-400 group-hover:text-blue-600 group-hover:translate-x-2 transition-all"></i>
        </div>
    </a>
</div>
@endsection

