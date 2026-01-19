<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyNewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $announcements) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📧 Cotygodniowy newsletter - Najlepsze oferty na Projekciarz.pl',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-newsletter',
        );
    }
}
