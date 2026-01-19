<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Przypomnienie o projekcie</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .project-box { background: white; border: 2px solid #e5e7eb; padding: 20px; border-radius: 8px; margin: 15px 0; }
        .button { display: inline-block; background: #3b82f6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⏰ Przypomnienie o projekcie</h1>
        </div>
        <div class="content">
            <p>Cześć,</p>
            <p>Zauważyliśmy, że projekt "<strong>{{ $proposal->announcement->title }}</strong>" nie był aktywny przez ostatnie 7 dni.</p>

            <div class="project-box">
                <h3 style="margin-top: 0;">{{ $proposal->announcement->title }}</h3>
                <p><strong>Status:</strong> Oferta zaakceptowana</p>
                <p><strong>Data akceptacji:</strong> {{ $proposal->accepted_at->format('d.m.Y') }}</p>
                @if($recipientType === 'client')
                    <p><strong>Freelancer:</strong> {{ $proposal->freelancer->name }}</p>
                @else
                    <p><strong>Klient:</strong> {{ $proposal->announcement->user->name }}</p>
                @endif
            </div>

            <p>Zachęcamy do kontynuacji współpracy i kontaktu z {{ $recipientType === 'client' ? 'freelancerem' : 'klientem' }}.</p>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ config('app.url') }}/wiadomosci/{{ $recipientType === 'client' ? $proposal->freelancer->id : $proposal->announcement->user->id }}" class="button">Wyślij wiadomość</a>
            </div>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Projekciarz.pl</p>
        </div>
    </div>
</body>
</html>
