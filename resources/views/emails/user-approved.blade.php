<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konto zatwierdzone</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .button { display: inline-block; background: #3b82f6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Witamy w Projekciarz.pl!</h1>
        </div>
        <div class="content">
            <p>Cześć {{ $user->name }},</p>

            <p><strong>Twoje konto zostało zatwierdzone przez administratora!</strong></p>

            <p>Możesz teraz w pełni korzystać z naszej platformy:</p>
            <ul>
                <li>Publikować ogłoszenia</li>
                <li>Przeglądać oferty freelancerów</li>
                <li>Zarządzać swoimi projektami</li>
            </ul>

            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="button">Zaloguj się teraz</a>
            </div>

            <p>Jeśli masz jakiekolwiek pytania, skontaktuj się z nami.</p>

            <p>Pozdrawiamy,<br>
            Zespół Projekciarz.pl</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Projekciarz.pl. Wszystkie prawa zastrzeżone.</p>
        </div>
    </div>
</body>
</html>

