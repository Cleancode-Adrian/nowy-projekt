<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .button { display: inline-block; background: #2563eb; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 12px; }
        .info-box { background: #fff; padding: 20px; border-left: 4px solid #2563eb; margin: 20px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; font-size: 28px;">🎉 Witaj w Projekciarz.pl!</h1>
        </div>
        
        <div class="content">
            <p style="font-size: 16px;">Cześć <strong>{{ $user->name }}</strong>,</p>
            
            <p>Dziękujemy za rejestrację na platformie <strong>Projekciarz.pl</strong>!</p>
            
            <div class="info-box">
                <h3 style="margin-top: 0; color: #2563eb;">📋 Co dalej?</h3>
                <p>Twoje konto zostało utworzone i oczekuje na zatwierdzenie przez administratora.</p>
                <p><strong>Nie możesz się jeszcze zalogować</strong> - dostaniesz osobnego emaila gdy Twoje konto zostanie zatwierdzone.</p>
            </div>
            
            <h3 style="color: #2563eb;">📧 Twoje dane:</h3>
            <ul>
                <li><strong>Email:</strong> {{ $user->email }}</li>
                <li><strong>Rola:</strong> {{ $user->role === 'client' ? '💼 Klient' : '💻 Freelancer' }}</li>
                @if($user->company)
                <li><strong>Firma:</strong> {{ $user->company }}</li>
                @endif
            </ul>
            
            <div class="info-box" style="border-left-color: #10b981; background: #f0fdf4;">
                <h3 style="margin-top: 0; color: #10b981;">⏱️ Proces zatwierdzania</h3>
                <p>Administrator zazwyczaj zatwierdza nowe konta <strong>w ciągu 24 godzin</strong> (w dni robocze).</p>
                <p>Po zatwierdzeniu otrzymasz email z powiadomieniem i będziesz mógł się zalogować.</p>
            </div>
            
            <p>Jeśli masz pytania, skontaktuj się z nami: <a href="mailto:biuro@cleancodeas.pl">biuro@cleancodeas.pl</a></p>
            
            <p style="margin-top: 30px;">Pozdrawiamy,<br><strong>Zespół Projekciarz.pl</strong></p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Projekciarz.pl. Wszelkie prawa zastrzeżone.</p>
            <p>Ta wiadomość została wysłana na adres {{ $user->email }}</p>
        </div>
    </div>
</body>
</html>

