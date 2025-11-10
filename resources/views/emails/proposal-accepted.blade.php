<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #fff; padding: 30px; border: 1px solid #e5e7eb; }
        .button { display: inline-block; background: #10b981; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; margin-top: 20px; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Gratulacje!</h1>
        </div>
        <div class="content">
            <p>Witaj <strong>{{ $proposal->freelancer->name }}</strong>!</p>
            <p>Twoja oferta została zaakceptowana! 🎉</p>
            <h2>{{ $proposal->announcement->title }}</h2>
            <p><strong>Zleceniodawca:</strong> {{ $proposal->announcement->user->name }}</p>
            <p><strong>Twoja cena:</strong> {{ number_format($proposal->price, 2) }} PLN</p>
            <p><strong>Termin:</strong> {{ $proposal->delivery_days }} dni</p>
            <p>Skontaktuj się ze zleceniodawcą aby ustalić szczegóły realizacji projektu.</p>
            <a href="{{ url('/wiadomosci/' . $proposal->announcement->user->id) }}" class="button">
                Napisz wiadomość
            </a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Projekciarz.pl. Wszystkie prawa zastrzeżone.</p>
        </div>
    </div>
</body>
</html>

