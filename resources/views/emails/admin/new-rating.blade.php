<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .button { display: inline-block; background: #f59e0b; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .info { background: white; padding: 15px; border-left: 4px solid #f59e0b; margin: 20px 0; }
        .stars { color: #fbbf24; font-size: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⭐ Nowa opinia do moderacji</h1>
        </div>
        <div class="content">
            <p>Cześć Admin!</p>

            <p>Użytkownik <strong>{{ $rating->rater->name }}</strong> wystawił opinię dla <strong>{{ $rating->rated->name }}</strong>:</p>

            <div class="info">
                <p class="stars">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $rating->rating)
                            ⭐
                        @else
                            ☆
                        @endif
                    @endfor
                    ({{ $rating->rating }}/5)
                </p>

                @if($rating->comment)
                    <p><strong>Komentarz:</strong></p>
                    <p style="background: #f3f4f6; padding: 15px; border-radius: 5px; font-style: italic;">
                        "{{ $rating->comment }}"
                    </p>
                @endif

                <p><strong>Projekt:</strong> {{ $rating->announcement->title }}</p>
                <p><strong>Data:</strong> {{ $rating->created_at->format('d.m.Y H:i') }}</p>
            </div>

            <center>
                <a href="{{ route('admin.dashboard') }}" class="button">
                    👀 Zobacz w panelu admina
                </a>
            </center>

            <p style="color: #666; font-size: 14px; margin-top: 30px;">
                Ten e-mail został wysłany automatycznie przez system WebFreelance.<br>
                Zaloguj się do panelu admina, aby zatwierdzić lub odrzucić opinię.
            </p>
        </div>
    </div>
</body>
</html>

