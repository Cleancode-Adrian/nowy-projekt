@php
    $guest = $this->isGuest();
    $accountStep = $guest && $step === 2;
@endphp
<div class="bg-ink-50/60 py-10 lg:py-14">
    <div class="container-page max-w-4xl">

        {{-- Nagłówek --}}
        <div class="mb-8">
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-brand-600 hover:text-brand-700">← Powrót do panelu</a>
            @endauth
            <h1 class="mt-3 text-3xl font-bold tracking-tight text-ink-900">Dodaj zlecenie</h1>
            @if($guest)
                <p class="mt-2 text-ink-600">Opisz projekt, a na końcu załóż konto - zajmie to pół minuty. Publikacja jest bezpłatna i do niczego nie zobowiązuje.</p>
            @else
                <p class="mt-2 text-ink-600">Wypełnij formularz, a zlecenie zostanie opublikowane po zatwierdzeniu przez administratora.</p>
            @endif
        </div>

        {{-- Kroki (tylko gość) --}}
        @if($guest)
            <ol class="mb-6 grid grid-cols-2 gap-3 text-sm" aria-label="Kroki">
                <li @class(['flex items-center gap-3 rounded-xl border px-4 py-3', 'border-brand-200 bg-white font-semibold text-ink-900' => !$accountStep, 'border-ink-100 bg-ink-50 text-ink-500' => $accountStep])>
                    <span @class(['flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold', 'bg-brand-600 text-white' => !$accountStep, 'bg-emerald-500 text-white' => $accountStep])>{{ $accountStep ? '✓' : '1' }}</span>
                    Opis projektu
                </li>
                <li @class(['flex items-center gap-3 rounded-xl border px-4 py-3', 'border-brand-200 bg-white font-semibold text-ink-900' => $accountStep, 'border-ink-100 bg-white text-ink-500' => !$accountStep])>
                    <span @class(['flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold', 'bg-brand-600 text-white' => $accountStep, 'bg-ink-100 text-ink-500' => !$accountStep])>2</span>
                    Konto i publikacja
                </li>
            </ol>
        @endif

        @if(session()->has('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">{{ session('success') }}</div>
        @endif

        <form id="announcement-form" wire:submit.prevent="{{ $guest && $step === 1 ? 'next' : 'submit' }}" novalidate>
            @if(!$accountStep)
                <div class="card space-y-6">

                    {{-- Tytuł --}}
                    <div>
                        <label for="title" class="mb-2 block text-sm font-semibold text-ink-700">Tytuł zlecenia *</label>
                        <input type="text" id="title" wire:model="title" class="input @error('title') border-red-500 @enderror"
                               placeholder="np. Strona internetowa dla firmy budowlanej">
                        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-ink-500">Minimum 10 znaków</p>
                    </div>

                    {{-- Opis --}}
                    <div>
                        <label for="description" class="mb-2 block text-sm font-semibold text-ink-700">Opis projektu *</label>
                        <textarea id="description" wire:model="description" rows="9" class="input @error('description') border-red-500 @enderror"
                                  placeholder="Czego potrzebujesz? Jaki jest zakres prac, termin, przykładowe strony, które Ci się podobają?"></textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-ink-500">Minimum 50 znaków. Im więcej szczegółów, tym trafniejsze oferty.</p>
                    </div>

                    {{-- Kategoria --}}
                    <div>
                        <label for="category_id" class="mb-2 block text-sm font-semibold text-ink-700">Kategoria *</label>
                        <select id="category_id" wire:model="category_id" class="input @error('category_id') border-red-500 @enderror">
                            <option value="">-- Wybierz kategorię --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Budżet --}}
                    <div>
                        <span class="mb-2 block text-sm font-semibold text-ink-700">Budżet (opcjonalnie)</span>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="budget_min" class="mb-1 block text-xs text-ink-600">Od (PLN)</label>
                                <input type="number" id="budget_min" wire:model="budget_min" class="input @error('budget_min') border-red-500 @enderror" placeholder="np. 1000" min="0" step="100">
                                @error('budget_min') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="budget_max" class="mb-1 block text-xs text-ink-600">Do (PLN)</label>
                                <input type="number" id="budget_max" wire:model="budget_max" class="input @error('budget_max') border-red-500 @enderror" placeholder="np. 5000" min="0" step="100">
                                @error('budget_max') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-ink-500">Zostaw puste, jeśli budżet jest do uzgodnienia.</p>
                    </div>

                    {{-- Termin i lokalizacja --}}
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="deadline" class="mb-2 block text-sm font-semibold text-ink-700">Termin realizacji</label>
                            <input type="date" id="deadline" wire:model="deadline" class="input @error('deadline') border-red-500 @enderror">
                            @error('deadline') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="location" class="mb-2 block text-sm font-semibold text-ink-700">Lokalizacja</label>
                            <input type="text" id="location" wire:model="location" class="input @error('location') border-red-500 @enderror" placeholder="np. Warszawa, zdalnie">
                        </div>
                    </div>

                    {{-- Tagi --}}
                    @if($tags->isNotEmpty())
                        <div>
                            <span class="mb-2 block text-sm font-semibold text-ink-700">Technologie / umiejętności (opcjonalnie)</span>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                                @foreach($tags as $tag)
                                    <label class="flex cursor-pointer items-center">
                                        <input type="checkbox" wire:model="selectedTags" value="{{ $tag->id }}" class="mr-2 rounded border-ink-300 text-brand-600 focus:ring-brand-500">
                                        <span class="text-sm text-ink-700">{{ $tag->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('selectedTags') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-2 text-xs text-ink-500">Maksymalnie 10.</p>
                        </div>
                    @endif

                    {{-- Załączniki --}}
                    <div>
                        <label for="attachments" class="mb-2 block text-sm font-semibold text-ink-700">Załączniki (opcjonalnie)</label>
                        <input type="file" id="attachments" wire:model="attachments" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,.zip,.doc,.docx"
                               class="block w-full text-sm text-ink-500 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                        @error('attachments.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-ink-500">PDF, JPG, PNG, WEBP, ZIP, DOC, DOCX - maks. 10 MB na plik.</p>
                        @if(!empty($attachments))
                            <ul class="mt-3 space-y-2">
                                @foreach($attachments as $file)
                                    <li class="flex items-center gap-2 rounded bg-ink-50 p-2 text-sm text-ink-600">
                                        <span class="flex-1 truncate">{{ $file->getClientOriginalName() }}</span>
                                        <span class="text-xs text-ink-500">{{ round($file->getSize() / 1024, 1) }} KB</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Pilne --}}
                    <label for="is_urgent" class="flex cursor-pointer items-center gap-3">
                        <input type="checkbox" id="is_urgent" wire:model="is_urgent" class="h-5 w-5 rounded border-ink-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm font-semibold text-ink-900">Projekt pilny</span>
                        <span class="text-xs text-ink-500">(będzie wyróżniony)</span>
                    </label>

                    {{-- Dalej / Wyślij --}}
                    <div class="flex flex-col-reverse gap-3 border-t border-ink-100 pt-6 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-ink-500">* pola wymagane</p>
                        <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="next,submit">{{ $guest ? 'Dalej: konto i publikacja →' : 'Wyślij zlecenie' }}</span>
                            <span wire:loading wire:target="next,submit">Sprawdzam…</span>
                        </button>
                    </div>
                </div>
            @else
                {{-- Krok 2: konto i publikacja (gość) --}}
                <div class="card space-y-6">
                    <div class="rounded-xl border border-ink-100 bg-ink-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-ink-500">Twoje zlecenie</p>
                        <p class="mt-1 font-semibold text-ink-900">{{ $title }}</p>
                        <p class="mt-1 text-sm text-ink-500">
                            {{ $categories->firstWhere('id', (int) $category_id)?->name }}
                            @if($budget_min || $budget_max)
                                · budżet {{ $budget_min ? number_format((float) $budget_min, 0, ',', ' ') : '…' }}–{{ $budget_max ? number_format((float) $budget_max, 0, ',', ' ') : '…' }} zł
                            @endif
                        </p>
                        <button type="button" wire:click="back" class="mt-2 text-sm font-medium text-brand-600 hover:text-brand-700">← Popraw opis</button>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-ink-900">Ostatni krok: konto zleceniodawcy</h2>
                        <p class="mt-1 text-sm text-ink-600">Na konto trafią oferty wykonawców i wiadomości. Zlecenie opublikujemy po szybkiej weryfikacji (zwykle do 24 h).</p>
                    </div>

                    <div class="grid grid-cols-2 gap-1 rounded-xl bg-ink-100 p-1 text-sm font-semibold" role="tablist">
                        <button type="button" role="tab" wire:click="setAuthMode('register')" @class(['rounded-lg px-3 py-2 transition', 'bg-white text-ink-900 shadow-sm' => $authMode === 'register', 'text-ink-500 hover:text-ink-800' => $authMode !== 'register'])>Nowe konto</button>
                        <button type="button" role="tab" wire:click="setAuthMode('login')" @class(['rounded-lg px-3 py-2 transition', 'bg-white text-ink-900 shadow-sm' => $authMode === 'login', 'text-ink-500 hover:text-ink-800' => $authMode !== 'login'])>Mam już konto</button>
                    </div>

                    {{-- Pułapka na boty: niewidoczne dla ludzi i czytników ekranu --}}
                    <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
                        <label for="website">Strona www</label>
                        <input type="text" id="website" wire:model="website" tabindex="-1" autocomplete="off">
                    </div>

                    @if($authMode === 'register')
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="reg_name" class="label">Imię i nazwisko *</label>
                                <input id="reg_name" type="text" wire:model="reg_name" autocomplete="name" class="input @error('reg_name') border-red-500 @enderror">
                                @error('reg_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="reg_email" class="label">E-mail *</label>
                                <input id="reg_email" type="email" wire:model="reg_email" autocomplete="email" class="input @error('reg_email') border-red-500 @enderror">
                                @error('reg_email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="reg_phone" class="label">Telefon <span class="font-normal text-ink-400">(opcjonalnie)</span></label>
                                <input id="reg_phone" type="tel" wire:model="reg_phone" autocomplete="tel" class="input" placeholder="+48 123 456 789">
                                @error('reg_phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="reg_password" class="label">Hasło *</label>
                                <input id="reg_password" type="password" wire:model="reg_password" autocomplete="new-password" class="input @error('reg_password') border-red-500 @enderror">
                                @error('reg_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                <p class="mt-1 text-xs text-ink-500">Minimum 8 znaków.</p>
                            </div>
                            <div>
                                <label for="reg_password_confirmation" class="label">Powtórz hasło *</label>
                                <input id="reg_password_confirmation" type="password" wire:model="reg_password_confirmation" autocomplete="new-password" class="input">
                            </div>
                        </div>

                        <div class="space-y-3 text-sm text-ink-600">
                            <label class="flex items-start gap-2.5">
                                <input type="checkbox" wire:model="reg_accept_terms" class="mt-0.5 h-4 w-4 rounded border-ink-300 text-brand-600 focus:ring-brand-500">
                                <span>Akceptuję <a href="{{ route('terms-of-service') }}" target="_blank" class="font-medium text-brand-600 underline">regulamin</a> *</span>
                            </label>
                            @error('reg_accept_terms') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                            <label class="flex items-start gap-2.5">
                                <input type="checkbox" wire:model="reg_accept_privacy" class="mt-0.5 h-4 w-4 rounded border-ink-300 text-brand-600 focus:ring-brand-500">
                                <span>Zapoznałem się z <a href="{{ route('privacy-policy') }}" target="_blank" class="font-medium text-brand-600 underline">polityką prywatności</a> *</span>
                            </label>
                            @error('reg_accept_privacy') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="login_email" class="label">E-mail</label>
                                <input id="login_email" type="email" wire:model="login_email" autocomplete="email" class="input @error('login_email') border-red-500 @enderror">
                                @error('login_email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="login_password" class="label">Hasło</label>
                                <input id="login_password" type="password" wire:model="login_password" autocomplete="current-password" class="input @error('login_password') border-red-500 @enderror">
                                @error('login_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Nie pamiętasz hasła?</a>
                    @endif

                    <div class="flex flex-col-reverse gap-3 border-t border-ink-100 pt-6 sm:flex-row sm:items-center sm:justify-between">
                        <button type="button" wire:click="back" class="text-sm font-medium text-ink-600 hover:text-ink-900">← Wróć do opisu</button>
                        <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submit">{{ $authMode === 'login' ? 'Zaloguj i opublikuj zlecenie' : 'Załóż konto i opublikuj zlecenie' }}</span>
                            <span wire:loading wire:target="submit">Publikuję…</span>
                        </button>
                    </div>
                </div>
            @endif
        </form>

        <div class="mt-6 rounded-xl border border-brand-100 bg-brand-50/60 p-6">
            <h2 class="font-semibold text-ink-900">Co dalej?</h2>
            <ul class="mt-3 space-y-2 text-sm text-ink-700">
                <li>• Zlecenie sprawdzi administrator (zwykle do 24 h).</li>
                <li>• Po zatwierdzeniu zobaczą je wykonawcy i zaczną składać oferty.</li>
                <li>• Oferty i wiadomości dostaniesz na konto i e-mailem.</li>
                <li>• Zlecenie możesz w każdej chwili usunąć w swoim panelu.</li>
            </ul>
        </div>
    </div>

    {{-- lead_form_start: pierwsza interakcja z formularzem zlecenia, raz na wizytę. --}}
    @push('scripts')
    <script>
        (function () {
            var KEY = 'pp_lead_form_start';
            function onFirstInput(e) {
                if (!e.target.closest || !e.target.closest('#announcement-form')) return;
                try { if (sessionStorage.getItem(KEY)) return; sessionStorage.setItem(KEY, '1'); } catch (err) {}
                document.removeEventListener('input', onFirstInput, true);
                document.removeEventListener('change', onFirstInput, true);
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({ event: 'lead_form_start', lead_type: 'zlecenie' });
            }
            document.addEventListener('input', onFirstInput, true);
            document.addEventListener('change', onFirstInput, true);
        })();
    </script>
    @endpush
</div>
