<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">📊 Statystyki</h1>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            @foreach($stats as $label => $value)
                <div class="card">
                    <div class="text-sm text-gray-600 mb-2">
                        @switch($label)
                            @case('announcements')
                                Moje ogłoszenia
                                @break
                            @case('proposals_received')
                                Otrzymane oferty
                                @break
                            @case('avg_proposals')
                                Średnia ofert/ogłoszenie
                                @break
                            @case('total_views')
                                Suma wyświetleń
                                @break
                            @case('proposals_sent')
                                Wysłane oferty
                                @break
                            @case('proposals_accepted')
                                Zaakceptowane oferty
                                @break
                            @case('conversion_rate')
                                Wskaźnik sukcesu
                                @break
                            @case('profile_views')
                                Wyświetlenia profilu
                                @break
                            @default
                                {{ ucfirst(str_replace('_', ' ', $label)) }}
                        @endswitch
                    </div>
                    <div class="text-3xl font-bold text-gray-900">
                        @if($label === 'conversion_rate')
                            {{ $value }}%
                        @else
                            {{ $value }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Chart --}}
        <div class="card">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Aktywność (ostatnie 7 dni)</h3>
            <div class="h-64 flex items-end justify-around gap-2">
                @foreach($chartData as $data)
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full bg-blue-600 rounded-t"
                             style="height: {{ $data['count'] > 0 ? ($data['count'] * 40) : 5 }}px;">
                        </div>
                        <div class="text-xs text-gray-600 mt-2">{{ date('d.m', strtotime($data['date'])) }}</div>
                        <div class="text-sm font-bold text-gray-900">{{ $data['count'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

