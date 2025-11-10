<?php

namespace App\Mail;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewProposalMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Proposal $proposal)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nowa oferta do Twojego ogłoszenia! 📨',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-proposal',
        );
    }
}

