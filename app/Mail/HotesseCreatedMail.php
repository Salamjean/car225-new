<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HotesseCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $hotesseData;
    public $otpCode;
    public $compagnieName;

    /**
     * Create a new message instance.
     */
    public function __construct($hotesseData, $otpCode, $compagnieName)
    {
        $this->hotesseData = $hotesseData;
        $this->otpCode = $otpCode;
        $this->compagnieName = $compagnieName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenue - Votre compte hotesse a été créé',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.hotesse-created',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
