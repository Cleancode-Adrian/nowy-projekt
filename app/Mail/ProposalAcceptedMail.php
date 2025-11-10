<?php

namespace App\Mail;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProposalAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Proposal $proposal)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Twoja oferta została zaakceptowana! ✅',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.proposal-accepted',
        );
    }
}

