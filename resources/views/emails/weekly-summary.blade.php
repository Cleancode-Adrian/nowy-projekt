<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotygodniowe podsumowanie</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #3b82f6 0%, #6d28d9 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .stat-box { background: white; border: 2px solid #e5e7eb; padding: 20px; border-radius: 8px; margin: 15px 0; text-align: center; }
        .button { display: inline-block; background: #3b82f6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Twoje cotygodniowe podsumowanie</h1>
        </div>
        <div class="content">
            <p>Cześć {{ $user->name }},</p>
            <p>Oto podsumowanie Twojej aktywności w ostatnim tygodniu:</p>

            @if($user->isFreelancer())
                <div class="stat-box">
                    <h3 style="font-size: 36px; margin: 0; color: #3b82f6;">{{ $summary['new_proposals'] }}</h3>
                    <p>Nowych ofert złożonych</p>
                </div>
                <div class="stat-box">
                    <h3 style="font-size: 36px; margin: 0; color: #10b981;">{{ $summary['accepted_proposals'] }}</h3>
                    <p>Zaakceptowanych ofert</p>
                </div>
                <div class="stat-box">
                    <h3 style="font-size: 36px; margin: 0; color: #8b5cf6;">{{ $summary['new_messages'] }}</h3>
                    <p>Nowych wiadomości</p>
                </div>
            @else
                <div class="stat-box">
                    <h3 style="font-size: 36px; margin: 0; color: #3b82f6;">{{ $summary['new_announcements'] }}</h3>
                    <p>Nowych ogłoszeń</p>
                </div>
                <div class="stat-box">
                    <h3 style="font-size: 36px; margin: 0; color: #10b981;">{{ $summary['new_proposals'] }}</h3>
                    <p>Nowych ofert otrzymanych</p>
                </div>
                <div class="stat-box">
                    <h3 style="font-size: 36px; margin: 0; color: #8b5cf6;">{{ $summary['new_messages'] }}</h3>
                    <p>Nowych wiadomości</p>
                </div>
            @endif

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ config('app.url') }}/dashboard" class="button">Przejdź do panelu</a>
            </div>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Projekciarz.pl</p>
        </div>
    </div>
</body>
</html>
