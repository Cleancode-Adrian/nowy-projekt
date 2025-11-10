<?php

namespace App\Mail;

use App\Models\Rating;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewRatingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Rating $rating)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⭐ Nowa opinia do moderacji - Projekciarz.pl',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.new-rating',
        );
    }
}
