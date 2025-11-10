<div class="card">
    @if($hasRated)
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
            <div class="text-4xl mb-3">⭐</div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Ocena wystawiona!</h3>
            <p class="text-gray-600">Dziękujemy za feedback dla {{ $ratedUser->name }}</p>
        </div>
    @else
        <h3 class="text-2xl font-bold text-gray-900 mb-6">⭐ Oceń użytkownika</h3>

        <form wire:submit="submit" class="space-y-6">
            {{-- Star Rating --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    Ocena dla: <strong>{{ $ratedUser->name }}</strong>
                </label>
                <div class="flex items-center gap-2 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" wire:click="$set('rating', {{ $i }})"
                                class="text-4xl transition-all hover:scale-110">
                            @if($i <= $rating)
                                <i class="fa-solid fa-star text-yellow-400"></i>
                            @else
                                <i class="fa-regular fa-star text-gray-300"></i>
                            @endif
                        </button>
                    @endfor
                </div>
                <p class="text-sm text-gray-600">
                    @if($rating == 5) ⭐⭐⭐⭐⭐ Doskonale!
                    @elseif($rating == 4) ⭐⭐⭐⭐ Bardzo dobrze!
                    @elseif($rating == 3) ⭐⭐⭐ Dobrze
                    @elseif($rating == 2) ⭐⭐ Słabo
                    @else ⭐ Bardzo słabo
                    @endif
                </p>
            </div>

            {{-- Comment --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Komentarz (opcjonalnie)
                </label>
                <textarea wire:model="comment" rows="4"
                          class="input"
                          placeholder="Opisz współpracę, punktualność, jakość pracy..."></textarea>
                @error('comment') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 mt-1">{{ strlen($comment) }}/500 znaków</p>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                ⭐ Wystaw ocenę
            </button>
        </form>
    @endif
</div>

