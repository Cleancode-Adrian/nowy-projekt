<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nowe pasujące ogłoszenia</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #3b82f6 0%, #6d28d9 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .announcement-box { background: white; border: 2px solid #e5e7eb; padding: 20px; border-radius: 8px; margin: 15px 0; }
        .button { display: inline-block; background: #3b82f6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Nowe pasujące ogłoszenia!</h1>
        </div>
        <div class="content">
            <p>Cześć,</p>
            <p>Znaleźliśmy <strong>{{ $announcements->count() }}</strong> nowych ogłoszeń pasujących do Twojego zapisanego wyszukiwania "<strong>{{ $savedSearch->name }}</strong>":</p>

            @foreach($announcements as $announcement)
                <div class="announcement-box">
                    <h3 style="margin-top: 0; color: #1f2937;">{{ $announcement->title }}</h3>
                    <p><strong>Kategoria:</strong> {{ $announcement->category->name }}</p>
                    <p><strong>Budżet:</strong> {{ $announcement->budget_range ?? 'Do uzgodnienia' }}</p>
                    @if($announcement->hourly_rate_min)
                        <p><strong>Stawka godzinowa:</strong> {{ $announcement->hourly_rate_min }}{{ $announcement->hourly_rate_max ? '-' . $announcement->hourly_rate_max : '' }} PLN/h</p>
                    @endif
                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($announcement->description), 150) }}</p>
                    <a href="{{ config('app.url') }}/announcements/{{ $announcement->id }}" class="button">Zobacz szczegóły</a>
                </div>
            @endforeach

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ config('app.url') }}/szukaj" class="button">Przeglądaj wszystkie ogłoszenia</a>
            </div>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Projekciarz.pl</p>
            <p><a href="{{ config('app.url') }}/profil">Zarządzaj zapisanymi wyszukiwaniami</a></p>
        </div>
    </div>
</body>
</html>
