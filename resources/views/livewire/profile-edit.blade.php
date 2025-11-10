<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">⚙️ Ustawienia konta</h1>
            <p class="text-gray-600">Zarządzaj swoim profilem i preferencjami</p>
        </div>

        {{-- Avatar Section --}}
        <div class="card mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Zdjęcie profilowe</h2>

            <div class="flex items-center gap-6">
                @if($currentAvatar)
                    <img src="{{ asset('storage/' . $currentAvatar) }}"
                         alt="Avatar"
                         class="w-24 h-24 rounded-full object-cover border-4 border-gray-100">
                @else
                    <div class="w-24 h-24 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white text-3xl font-bold border-4 border-gray-100">
                        {{ substr($name, 0, 1) }}
                    </div>
                @endif

                <div class="flex-1">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Wybierz nowe zdjęcie (max 2MB)
                        </label>
                        <input type="file"
                               wire:model="avatar"
                               accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error('avatar')
                            <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($avatar)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Podgląd:</p>
                            <img src="{{ $avatar->temporaryUrl() }}" class="w-24 h-24 rounded-full object-cover">
                        </div>
                    @endif

                    <div class="flex gap-3">
                        @if($avatar)
                            <button wire:click="updateAvatar"
                                    wire:loading.attr="disabled"
                                    class="btn btn-primary">
                                <span wire:loading.remove wire:target="updateAvatar">💾 Zapisz avatar</span>
                                <span wire:loading wire:target="updateAvatar">⏳ Zapisywanie...</span>
                            </button>
                        @endif

                        @if($currentAvatar)
                            <button wire:click="removeAvatar"
                                    wire:confirm="Czy na pewno chcesz usunąć avatar?"
                                    class="btn btn-secondary">
                                🗑️ Usuń avatar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Profile Info --}}
        <div class="card mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Dane osobowe</h2>

            <form wire:submit.prevent="updateProfile" class="space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Imię i nazwisko *
                        </label>
                        <input type="text"
                               wire:model="name"
                               required
                               class="input">
                        @error('name')
                            <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email *
                        </label>
                        <input type="email"
                               wire:model="email"
                               required
                               class="input">
                        @error('email')
                            <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Telefon
                        </label>
                        <input type="tel"
                               wire:model="phone"
                               placeholder="+48 123 456 789"
                               class="input">
                        @error('phone')
                            <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nazwa firmy
                        </label>
                        <input type="text"
                               wire:model="company"
                               placeholder="np. Moja Firma Sp. z o.o."
                               class="input">
                        @error('company')
                            <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        O mnie / Bio
                    </label>
                    <textarea
                        wire:model="bio"
                        rows="4"
                        placeholder="Opowiedz coś o sobie, swoich umiejętnościach..."
                        class="input"></textarea>
                    @error('bio')
                        <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Maksymalnie 1000 znaków</p>
                </div>

                {{-- Social Links --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fa-solid fa-globe"></i> Strona WWW
                        </label>
                        <input type="url" wire:model="website" class="input" placeholder="https://twoja-strona.pl">
                        @error('website') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fa-brands fa-linkedin"></i> LinkedIn
                        </label>
                        <input type="url" wire:model="linkedin_url" class="input" placeholder="https://linkedin.com/in/...">
                        @error('linkedin_url') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fa-brands fa-github"></i> GitHub
                        </label>
                        <input type="url" wire:model="github_url" class="input" placeholder="https://github.com/...">
                        @error('github_url') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                @if(auth()->user()->isFreelancer())
                    {{-- Freelancer Skills --}}
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">💼 Profil freelancera</h3>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Poziom doświadczenia</label>
                                <select wire:model="experience_level" class="input">
                                    <option value="">-- Wybierz --</option>
                                    <option value="junior">Junior (0-2 lata)</option>
                                    <option value="mid">Mid (2-5 lat)</option>
                                    <option value="senior">Senior (5+ lat)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Umiejętności / Technologie</label>
                                <input type="text" wire:model="selectedSkills" class="input"
                                       placeholder="React, Laravel, Tailwind CSS, Figma (oddziel przecinkami)">
                                <p class="text-xs text-gray-500 mt-1">💡 Wpisz swoje umiejętności oddzielone przecinkami</p>
                                @if($selectedSkills)
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        @foreach(explode(',', $selectedSkills) as $skill)
                                            @if(trim($skill))
                                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">
                                                    {{ trim($skill) }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex justify-end pt-6 border-t border-gray-200">
                    <button type="submit" wire:loading.attr="disabled" class="btn btn-primary">
                        <span wire:loading.remove wire:target="updateProfile">💾 Zapisz zmiany</span>
                        <span wire:loading wire:target="updateProfile">⏳ Zapisywanie...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="card mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">🔒 Zmiana hasła</h2>

            <form wire:submit.prevent="changePassword" class="space-y-6">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Obecne hasło *
                    </label>
                    <input type="password"
                           wire:model="current_password"
                           required
                           class="input">
                    @error('current_password')
                        <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nowe hasło *
                        </label>
                        <input type="password"
                               wire:model="new_password"
                               required
                               class="input">
                        @error('new_password')
                            <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 znaków</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Potwierdź nowe hasło *
                        </label>
                        <input type="password"
                               wire:model="new_password_confirmation"
                               required
                               class="input">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="btn btn-primary">
                        <span wire:loading.remove wire:target="changePassword">🔑 Zmień hasło</span>
                        <span wire:loading wire:target="changePassword">⏳ Zmienianie...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Account Info --}}
        <div class="card">
            <h2 class="text-xl font-bold text-gray-900 mb-6">ℹ️ Informacje o koncie</h2>

            <div class="space-y-4 text-sm">
                <div class="flex justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">Rola:</span>
                    <span class="font-medium text-gray-900">
                        @if(auth()->user()->role === 'admin')
                            👑 Administrator
                        @elseif(auth()->user()->role === 'freelancer')
                            💼 Freelancer
                        @else
                            👤 Klient
                        @endif
                    </span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">Status konta:</span>
                    <span class="font-medium {{ auth()->user()->is_approved ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ auth()->user()->is_approved ? '✅ Zatwierdzone' : '⏳ Oczekuje na zatwierdzenie' }}
                    </span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">Konto utworzone:</span>
                    <span class="font-medium text-gray-900">{{ auth()->user()->created_at->format('d.m.Y H:i') }}</span>
                </div>
                <div class="flex justify-between py-3">
                    <span class="text-gray-600">Ostatnia aktywność:</span>
                    <span class="font-medium text-gray-900">{{ auth()->user()->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="card bg-red-50 border-red-200 mt-6">
            <h2 class="text-xl font-bold text-red-900 mb-4">⚠️ Strefa niebezpieczna</h2>
            <p class="text-red-700 text-sm mb-4">
                Usunięcie konta jest nieodwracalne. Wszystkie Twoje ogłoszenia zostaną usunięte.
            </p>
            <button onclick="confirm('Czy NA PEWNO chcesz usunąć konto? Tej operacji nie można cofnąć!') || event.stopImmediatePropagation()"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                🗑️ Usuń konto
            </button>
        </div>

    </div>
</div>
