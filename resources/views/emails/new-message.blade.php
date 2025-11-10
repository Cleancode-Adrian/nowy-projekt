<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #fff; padding: 30px; border: 1px solid #e5e7eb; }
        .message-box { background: #f3f4f6; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .button { display: inline-block; background: #6366f1; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; margin-top: 20px; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💬 Nowa wiadomość</h1>
        </div>
        <div class="content">
            <p>Witaj!</p>
            <p>Masz nową wiadomość od <strong>{{ $message->sender->name }}</strong>:</p>
            <div class="message-box">
                {{ $message->content }}
            </div>
            <a href="{{ url('/wiadomosci/' . $message->sender->id) }}" class="button">
                Odpowiedz
            </a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} WebFreelance. Wszystkie prawa zastrzeżone.</p>
        </div>
    </div>
</body>
</html>

