<?php

namespace App\Mail;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InactiveProjectReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Proposal $proposal,
        public string $recipientType // 'client' or 'freelancer'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->recipientType === 'client'
            ? '⏰ Przypomnienie: Projekt wymaga Twojej uwagi'
            : '⏰ Przypomnienie: Projekt wymaga Twojej uwagi';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inactive-project-reminder',
        );
    }
}
