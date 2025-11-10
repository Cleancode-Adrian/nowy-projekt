<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .button { display: inline-block; background: #3b82f6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .info { background: white; padding: 15px; border-left: 4px solid #3b82f6; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🆕 Nowe ogłoszenie do zatwierdzenia</h1>
        </div>
        <div class="content">
            <p>Cześć Admin!</p>

            <p>Użytkownik <strong>{{ $announcement->user->name }}</strong> właśnie dodał nowe ogłoszenie:</p>

            <div class="info">
                <h3>{{ $announcement->title }}</h3>
                <p><strong>Kategoria:</strong> {{ $announcement->category->name }}</p>
                <p><strong>Budżet:</strong> {{ $announcement->budget_min ?? 'Nie podano' }} - {{ $announcement->budget_max ?? 'Nie podano' }} {{ $announcement->budget_currency }}</p>
                <p><strong>Termin:</strong> {{ $announcement->deadline ? $announcement->deadline->format('d.m.Y') : 'Nie podano' }}</p>
                <p><strong>Dodano:</strong> {{ $announcement->created_at->format('d.m.Y H:i') }}</p>
            </div>

            <p><strong>Opis:</strong></p>
            <p>{{ \Illuminate\Support\Str::limit($announcement->description, 200) }}</p>

            <center>
                <a href="{{ route('admin.announcements') }}" class="button">
                    👀 Zobacz w panelu admina
                </a>
            </center>

            <p style="color: #666; font-size: 14px; margin-top: 30px;">
                Ten e-mail został wysłany automatycznie przez system Projekciarz.pl.<br>
                Zaloguj się do panelu admina, aby zatwierdzić lub odrzucić ogłoszenie.
            </p>
        </div>
    </div>
</body>
</html>

