<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <a href="{{ route('contracts.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                ← Moje kontrakty
            </a>
        </div>

        {{-- Header --}}
        <div class="card mb-8">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1">
                        <a href="{{ route('announcements.show', $contract->announcement_id) }}" class="hover:text-blue-600">
                            {{ $contract->announcement->title }}
                        </a>
                    </h1>
                    <p class="text-sm text-gray-600">
                        Klient: <span class="font-medium">{{ $contract->client->name }}</span>
                        &middot;
                        Wykonawca: <span class="font-medium">{{ $contract->freelancer->name }}</span>
                    </p>
                </div>

                @if($contract->status === 'active')
                    <span class="bg-blue-100 text-blue-700 px-3 py-1.5 rounded-full text-sm font-semibold">🔵 Aktywny</span>
                @elseif($contract->status === 'completed')
                    <span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-full text-sm font-semibold">✅ Zakończony</span>
                @else
                    <span class="bg-red-100 text-red-700 px-3 py-1.5 rounded-full text-sm font-semibold">❌ Anulowany</span>
                @endif
            </div>

            {{-- Progress bar --}}
            <div class="mt-6">
                <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                    <span>Postęp realizacji</span>
                    <span class="font-semibold">{{ $contract->progressPercent() }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full transition-all" style="width: {{ $contract->progressPercent() }}%"></div>
                </div>
            </div>

            @if($contract->status === 'active' && $contract->client_id === auth()->id())
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <button wire:click="cancelContract"
                            wire:confirm="Czy na pewno chcesz anulować ten kontrakt?"
                            class="text-red-600 hover:text-red-700 text-sm font-medium">
                        Anuluj kontrakt
                    </button>
                </div>
            @endif
        </div>

        {{-- Milestones --}}
        <div class="card">
            <h2 class="text-xl font-bold text-gray-900 mb-4">📋 Kamienie milowe</h2>

            <div class="space-y-3">
                @forelse($contract->milestones as $milestone)
                    <div class="border border-gray-200 rounded-lg p-4 {{ $milestone->status === 'approved' ? 'bg-green-50 border-green-200' : '' }}">
                        <div class="flex items-start justify-between gap-4 flex-wrap">
                            <div class="flex-1 min-w-[200px]">
                                <h3 class="font-semibold text-gray-900">{{ $milestone->title }}</h3>
                                @if($milestone->description)
                                    <p class="text-sm text-gray-600 mt-1 whitespace-pre-wrap">{{ $milestone->description }}</p>
                                @endif
                                @if($milestone->due_date)
                                    <p class="text-xs text-gray-500 mt-2">Termin: {{ $milestone->due_date->format('d.m.Y') }}</p>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 flex-wrap">
                                @if($milestone->status === 'pending')
                                    <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-semibold">⏳ Do zrobienia</span>
                                @elseif($milestone->status === 'in_progress')
                                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">🔧 W trakcie</span>
                                @elseif($milestone->status === 'submitted')
                                    <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-semibold">📤 Do odbioru</span>
                                @else
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">✅ Zatwierdzony</span>
                                @endif
                            </div>
                        </div>

                        @if($contract->status === 'active')
                            <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100">
                                @if($contract->freelancer_id === auth()->id())
                                    @if($milestone->status === 'pending')
                                        <button wire:click="startMilestone({{ $milestone->id }})" class="text-sm font-medium text-blue-600 hover:text-blue-700">Rozpocznij pracę</button>
                                    @endif
                                    @if(in_array($milestone->status, ['pending', 'in_progress']))
                                        <button wire:click="submitMilestone({{ $milestone->id }})" class="text-sm font-medium text-purple-600 hover:text-purple-700">Zgłoś do odbioru</button>
                                    @endif
                                @endif

                                @if($contract->client_id === auth()->id())
                                    @if($milestone->status === 'submitted')
                                        <button wire:click="approveMilestone({{ $milestone->id }})" class="text-sm font-medium text-green-600 hover:text-green-700">✅ Zatwierdź</button>
                                        <button wire:click="reopenMilestone({{ $milestone->id }})" class="text-sm font-medium text-orange-600 hover:text-orange-700">Odeślij do poprawy</button>
                                    @endif
                                    @if($milestone->status === 'pending')
                                        <button wire:click="deleteMilestone({{ $milestone->id }})"
                                                wire:confirm="Usunąć ten kamień milowy?"
                                                class="text-sm font-medium text-red-500 hover:text-red-600">Usuń</button>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">Brak kamieni milowych</p>
                @endforelse
            </div>

            {{-- Add milestone form --}}
            @if($contract->status === 'active')
                <form wire:submit="addMilestone" class="mt-6 pt-6 border-t border-gray-200 space-y-3">
                    <h3 class="font-semibold text-gray-900">Dodaj kamień milowy</h3>
                    <input type="text" wire:model="newTitle" placeholder="Nazwa etapu"
                           class="input">
                    @error('newTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                    <textarea wire:model="newDescription" rows="2" placeholder="Opis (opcjonalnie)" class="input"></textarea>

                    <div class="flex items-center gap-3">
                        <input type="date" wire:model="newDueDate" class="input max-w-xs">
                        <button type="submit" class="btn btn-primary">Dodaj etap</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
