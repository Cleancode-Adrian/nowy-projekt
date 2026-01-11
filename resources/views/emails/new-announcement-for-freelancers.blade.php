<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nowe ogłoszenie</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .announcement-box { background: white; border: 2px solid #e5e7eb; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .urgent { border-color: #ef4444; background: #fef2f2; }
        .button { display: inline-block; background: #3b82f6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .button:hover { background: #2563eb; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
        .badge { display: inline-block; background: #ef4444; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; margin-left: 10px; }
        .tags { margin-top: 10px; }
        .tag { display: inline-block; background: #e5e7eb; color: #374151; padding: 4px 8px; border-radius: 4px; font-size: 12px; margin-right: 5px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🆕 Nowe ogłoszenie!</h1>
            <p>Sprawdź nową okazję na Projekciarz.pl</p>
        </div>
        <div class="content">
            <p>Cześć,</p>

            <p><strong>Pojawiło się nowe ogłoszenie, które może Cię zainteresować!</strong></p>

            <div class="announcement-box {{ $announcement->is_urgent ? 'urgent' : '' }}">
                @if($announcement->is_urgent)
                    <span class="badge">🔥 PILNE</span>
                @endif
                <h2 style="margin-top: 0; color: #1f2937;">{{ $announcement->title }}</h2>

                <p><strong>Kategoria:</strong> {{ $announcement->category->name }}</p>

                @if($announcement->budget_min || $announcement->budget_max)
                    <p><strong>Budżet:</strong>
                        @if($announcement->budget_min && $announcement->budget_max)
                            {{ number_format($announcement->budget_min, 0, ',', ' ') }} - {{ number_format($announcement->budget_max, 0, ',', ' ') }} {{ $announcement->budget_currency }}
                        @elseif($announcement->budget_min)
                            Od {{ number_format($announcement->budget_min, 0, ',', ' ') }} {{ $announcement->budget_currency }}
                        @elseif($announcement->budget_max)
                            Do {{ number_format($announcement->budget_max, 0, ',', ' ') }} {{ $announcement->budget_currency }}
                        @endif
                    </p>
                @else
                    <p><strong>Budżet:</strong> Do uzgodnienia</p>
                @endif

                @if($announcement->deadline)
                    <p><strong>Termin:</strong> {{ \Carbon\Carbon::parse($announcement->deadline)->format('d.m.Y') }}</p>
                @endif

                @if($announcement->location)
                    <p><strong>Lokalizacja:</strong> {{ $announcement->location }}</p>
                @endif

                <p style="margin-top: 15px; margin-bottom: 10px;"><strong>Opis:</strong></p>
                <p style="color: #4b5563;">{{ \Illuminate\Support\Str::limit(strip_tags($announcement->description), 200) }}</p>

                @if($announcement->tags->count() > 0)
                    <div class="tags">
                        <strong>Technologie:</strong><br>
                        @foreach($announcement->tags as $tag)
                            <span class="tag">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <p>Nie czekaj! Sprawdź szczegóły i złóż ofertę, zanim ktoś inny Cię wyprzedzi.</p>

            <div style="text-align: center;">
                <a href="{{ url('/announcements/' . $announcement->id) }}" class="button">Zobacz ogłoszenie i złóż ofertę</a>
            </div>

            <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;">
                Dostajesz ten email, ponieważ jesteś zarejestrowanym freelancerem na Projekciarz.pl.
                Możesz zarządzać powiadomieniami w ustawieniach swojego konta.
            </p>

            <p>Pozdrawiamy,<br>
            <strong>Zespół Projekciarz.pl</strong></p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Projekciarz.pl. Wszystkie prawa zastrzeżone.</p>
            <p style="font-size: 12px; margin-top: 10px;">
                <a href="{{ url('/profil') }}" style="color: #667eea;">Zarządzaj powiadomieniami</a>
            </p>
        </div>
    </div>
</body>
</html>
