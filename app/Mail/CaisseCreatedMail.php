<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CaisseCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $caisseData;
    public $otpCode;
    public $compagnieName;

    /**
     * Create a new message instance.
     */
    public function __construct($caisseData, $otpCode, $compagnieName)
    {
        $this->caisseData = $caisseData;
        $this->otpCode = $otpCode;
        $this->compagnieName = $compagnieName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenue - Votre compte caissière a été créé',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.caisse-created',
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
