<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-gray-900">📋 Moje kontrakty</h1>

            <select wire:model.live="statusFilter" class="input max-w-xs">
                <option value="">Wszystkie statusy</option>
                <option value="active">Aktywne</option>
                <option value="completed">Zakończone</option>
                <option value="cancelled">Anulowane</option>
            </select>
        </div>

        <div class="space-y-4">
            @forelse($contracts as $contract)
                @php
                    $isClient = $contract->client_id === auth()->id();
                    $otherParty = $isClient ? $contract->freelancer : $contract->client;
                @endphp
                <a href="{{ route('contracts.show', $contract->id) }}" class="card block hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between flex-wrap gap-4">
                        <div>
                            <h2 class="font-bold text-gray-900">{{ $contract->announcement->title }}</h2>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $isClient ? 'Wykonawca' : 'Klient' }}: <span class="font-medium">{{ $otherParty->name }}</span>
                            </p>
                        </div>

                        @if($contract->status === 'active')
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">🔵 Aktywny</span>
                        @elseif($contract->status === 'completed')
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">✅ Zakończony</span>
                        @else
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">❌ Anulowany</span>
                        @endif
                    </div>

                    <div class="mt-4">
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                            <span>Postęp</span>
                            <span>{{ $contract->progressPercent() }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $contract->progressPercent() }}%"></div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-16 bg-white rounded-lg border border-gray-100">
                    <div class="text-6xl mb-4">📭</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Brak kontraktów</h3>
                    <p class="text-gray-600">Kontrakt powstaje automatycznie po zaakceptowaniu oferty.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $contracts->links() }}
        </div>
    </div>
</div>
