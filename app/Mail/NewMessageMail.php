<?php

namespace App\Mail;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Message $message)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nowa wiadomość od ' . $this->message->sender->name . ' 💬',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-message',
        );
    }
}

