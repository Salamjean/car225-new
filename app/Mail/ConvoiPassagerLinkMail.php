<?php

namespace App\Mail;

use App\Models\Convoi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConvoiPassagerLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Convoi $convoi,
        public readonly string $lien
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Renseignez vos passagers — Convoi CAR225 {$this->convoi->reference}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.convoi_passager_link',
        );
    }
}
