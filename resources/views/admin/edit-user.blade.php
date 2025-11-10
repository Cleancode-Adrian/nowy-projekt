@extends('admin.layout')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.users.view', $user->id) }}" class="text-blue-600 hover:text-blue-700 font-medium">
        ← Powrót do profilu użytkownika
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Form -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Edytuj użytkownika</h2>

            <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                @csrf

                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Imię i nazwisko *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Company -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Firma</label>
                        <input type="text" name="company" value="{{ old('company', $user->company) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rola *</label>
                        <select name="role" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="client" {{ old('role', $user->role) === 'client' ? 'selected' : '' }}>Klient</option>
                            <option value="freelancer" {{ old('role', $user->role) === 'freelancer' ? 'selected' : '' }}>Freelancer</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                    </div>

                    <!-- Approved -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_approved" value="1" {{ old('is_approved', $user->is_approved) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                            <span class="ml-2 text-sm font-medium text-gray-700">Konto zatwierdzone</span>
                        </label>
                    </div>

                    <!-- Bio -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                        <textarea name="bio" rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('bio', $user->bio) }}</textarea>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                            💾 Zapisz zmiany
                        </button>
                        <a href="{{ route('admin.users.view', $user->id) }}" class="text-gray-600 hover:text-gray-700">
                            Anuluj
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar - Change Password -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Zmień hasło</h3>

            <form method="POST" action="{{ route('admin.users.change-password', $user->id) }}">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nowe hasło</label>
                        <input type="password" name="new_password" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('new_password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Potwierdź hasło</label>
                        <input type="password" name="new_password_confirmation" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium">
                        🔑 Zmień hasło
                    </button>
                </div>
            </form>

            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-xs text-yellow-800">
                    ⚠️ Użytkownik zostanie automatycznie wylogowany po zmianie hasła.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

