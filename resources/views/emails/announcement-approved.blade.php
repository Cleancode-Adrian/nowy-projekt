<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ogłoszenie opublikowane</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .announcement-box { background: white; border: 2px solid #e5e7eb; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .button { display: inline-block; background: #3b82f6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Ogłoszenie opublikowane!</h1>
        </div>
        <div class="content">
            <p>Cześć {{ $announcement->user->name }},</p>

            <p><strong>Twoje ogłoszenie zostało zatwierdzone i opublikowane!</strong></p>

            <div class="announcement-box">
                <h3 style="margin-top: 0;">{{ $announcement->title }}</h3>
                <p><strong>Kategoria:</strong> {{ $announcement->category->name }}</p>
                <p><strong>Budżet:</strong> {{ $announcement->budget_range ?? 'Do uzgodnienia' }}</p>
            </div>

            <p>Twoje ogłoszenie jest teraz widoczne dla wszystkich użytkowników platformy.</p>

            <div style="text-align: center;">
                <a href="{{ config('app.frontend_url') }}/announcements/{{ $announcement->id }}" class="button">Zobacz ogłoszenie</a>
            </div>

            <p>Powodzenia w znalezieniu idealnego freelancera!</p>

            <p>Pozdrawiamy,<br>
            Zespół Projekciarz.pl</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Projekciarz.pl. Wszystkie prawa zastrzeżone.</p>
        </div>
    </div>
</body>
</html>

