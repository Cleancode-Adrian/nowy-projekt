<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .button { display: inline-block; background: #10b981; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .info { background: white; padding: 15px; border-left: 4px solid #10b981; margin: 20px 0; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 20px; font-size: 12px; }
        .badge-client { background: #ddd6fe; color: #7c3aed; }
        .badge-freelancer { background: #d1fae5; color: #059669; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👤 Nowa rejestracja użytkownika</h1>
        </div>
        <div class="content">
            <p>Cześć Admin!</p>

            <p>Nowy użytkownik właśnie się zarejestrował i czeka na zatwierdzenie:</p>

            <div class="info">
                <h3>{{ $user->name }}</h3>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Telefon:</strong> {{ $user->phone ?? 'Nie podano' }}</p>
                <p><strong>Firma:</strong> {{ $user->company ?? 'Nie podano' }}</p>
                <p>
                    <strong>Rola:</strong>
                    @if($user->role === 'client')
                        <span class="badge badge-client">💼 Klient</span>
                    @else
                        <span class="badge badge-freelancer">💻 Freelancer</span>
                    @endif
                </p>
                <p><strong>Data rejestracji:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</p>
            </div>

            <center>
                <a href="{{ route('admin.users.index') }}" class="button">
                    👀 Zobacz w panelu admina
                </a>
            </center>

            <p style="color: #666; font-size: 14px; margin-top: 30px;">
                Ten e-mail został wysłany automatycznie przez system Projekciarz.pl.<br>
                Zaloguj się do panelu admina, aby zatwierdzić konto użytkownika.
            </p>
        </div>
    </div>
</body>
</html>

