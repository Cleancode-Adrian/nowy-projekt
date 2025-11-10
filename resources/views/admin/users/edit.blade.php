@extends('admin.layout')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-3 mb-2">
        <div class="w-12 h-12 bg-gradient-to-r from-orange-600 to-red-600 rounded-xl flex items-center justify-center text-white text-2xl shadow-lg">
            ✏️
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edytuj użytkownika</h1>
            <p class="text-gray-600">{{ $user->name }} ({{ $user->email }})</p>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Basic Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-user text-blue-600"></i>
                    Podstawowe informacje
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Imię i nazwisko *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                        @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                        @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Hasło</label>
                        <input type="password" name="password"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                        @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-500 mt-1">Pozostaw puste aby nie zmieniać</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Telefon</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Firma/Organizacja</label>
                        <input type="text" name="company" value="{{ old('company', $user->company) }}"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            {{-- Bio --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-file-lines text-purple-600"></i>
                    O mnie
                </h3>
                <textarea name="bio" rows="4"
                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">{{ old('bio', $user->bio) }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Maksymalnie 1000 znaków</p>
            </div>

            {{-- Links --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-link text-blue-600"></i>
                    Linki społecznościowe
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">🌐 Strona WWW</label>
                        <input type="url" name="website" value="{{ old('website', $user->website) }}"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">💼 LinkedIn</label>
                        <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $user->linkedin_url) }}"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">💻 GitHub</label>
                        <input type="url" name="github_url" value="{{ old('github_url', $user->github_url) }}"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Role --}}
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl shadow-sm border-2 border-blue-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-shield text-blue-600"></i>
                    Rola *
                </h3>
                <select name="role" required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white @error('role') border-red-500 @enderror">
                    <option value="client" {{ old('role', $user->role) === 'client' ? 'selected' : '' }}>💼 Klient</option>
                    <option value="freelancer" {{ old('role', $user->role) === 'freelancer' ? 'selected' : '' }}>💻 Freelancer</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>🛡️ Administrator</option>
                </select>
                @error('role') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- Avatar --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-image text-pink-600"></i>
                    Avatar
                </h3>
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}"
                         class="w-full h-40 object-cover rounded-lg mb-4 border-2 border-gray-200">
                @endif
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors">
                    <input type="file" name="avatar" accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    @error('avatar') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-500 mt-2">{{ $user->avatar ? 'Wybierz nowe aby zmienić' : 'Maks: 2MB (JPG, PNG, WebP)' }}</p>
                </div>
            </div>

            {{-- Experience --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-star text-yellow-600"></i>
                    Doświadczenie
                </h3>
                <select name="experience_level"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white">
                    <option value="">-- Wybierz --</option>
                    <option value="junior" {{ old('experience_level', $user->experience_level) === 'junior' ? 'selected' : '' }}>🌱 Junior</option>
                    <option value="mid" {{ old('experience_level', $user->experience_level) === 'mid' ? 'selected' : '' }}>💼 Mid</option>
                    <option value="senior" {{ old('experience_level', $user->experience_level) === 'senior' ? 'selected' : '' }}>🎯 Senior</option>
                    <option value="expert" {{ old('experience_level', $user->experience_level) === 'expert' ? 'selected' : '' }}>🏆 Expert</option>
                </select>
            </div>

            {{-- Skills --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-code text-green-600"></i>
                    Umiejętności
                </h3>
                <input type="text" name="skills[]" value="{{ is_array($user->skills) ? implode(', ', $user->skills) : '' }}"
                       placeholder="np. PHP, Laravel, React"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 mb-2">
                <p class="text-xs text-gray-500">Oddziel przecinkami</p>
            </div>

            {{-- Approval & Verification --}}
            <div class="bg-green-50 rounded-xl border border-green-200 p-6 space-y-4">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_approved" value="1" {{ old('is_approved', $user->is_approved) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-green-600 focus:ring-green-500 mr-3">
                    <div>
                        <span class="font-bold text-gray-900">✅ Zatwierdzony</span>
                        <p class="text-xs text-gray-600 mt-1">Użytkownik może się logować</p>
                    </div>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_verified" value="1" {{ old('is_verified', $user->email_verified_at ? true : false) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                    <div>
                        <span class="font-bold text-gray-900">📧 Email zweryfikowany</span>
                        <p class="text-xs text-gray-600 mt-1">Email został potwierdzony</p>
                    </div>
                </label>
            </div>

            {{-- Stats --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-chart-simple"></i>
                    Statystyki
                </h4>
                <div class="space-y-2 text-sm text-gray-700">
                    <div class="flex justify-between">
                        <span>Utworzono:</span>
                        <strong>{{ $user->created_at->format('d.m.Y') }}</strong>
                    </div>
                    @if($user->updated_at != $user->created_at)
                        <div class="flex justify-between">
                            <span>Edytowano:</span>
                            <strong>{{ $user->updated_at->format('d.m.Y H:i') }}</strong>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span>Ocena:</span>
                        <strong>⭐ {{ number_format($user->average_rating, 1) }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Ukończone:</span>
                        <strong>{{ $user->completed_projects }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Wyświetlenia:</span>
                        <strong>{{ $user->profile_views }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="mt-8 flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900 font-medium flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            Powrót do listy
        </a>
        <div class="flex items-center gap-4">
            <a href="{{ route('users.profile', $user) }}" target="_blank"
               class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-2">
                <i class="fa-solid fa-eye"></i>
                Zobacz profil
            </a>
            <button type="submit"
                    class="bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white px-8 py-3 rounded-lg font-semibold transition-all shadow-lg hover:shadow-xl flex items-center gap-2">
                <i class="fa-solid fa-save"></i>
                Zapisz zmiany
            </button>
        </div>
    </div>
</form>
@endsection

