<div class="card max-w-md mx-auto">
    @if($submitted)
        <div class="text-center py-8">
            <div class="text-5xl mb-4">✅</div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Zgłoszenie wysłane</h3>
            <p class="text-gray-600">Dziękujemy! Sprawdzimy to w ciągu 24h.</p>
        </div>
    @else
        <h3 class="text-xl font-bold text-gray-900 mb-6">🚨 Zgłoś treść</h3>

        <form wire:submit="submit" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Powód zgłoszenia</label>
                <select wire:model="reason" class="input">
                    <option value="spam">Spam</option>
                    <option value="inappropriate">Niewłaściwa treść</option>
                    <option value="fraud">Oszustwo</option>
                    <option value="other">Inne</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Szczegóły (opcjonalnie)</label>
                <textarea wire:model="description" rows="4" class="input"
                          placeholder="Opisz problem..."></textarea>
                @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full btn btn-primary">Wyślij zgłoszenie</button>
        </form>
    @endif
</div>

