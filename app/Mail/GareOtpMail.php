<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GareOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $gareName;
    public $gareEmail;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otp, string $gareName, string $gareEmail)
    {
        $this->otp = $otp;
        $this->gareName = $gareName;
        $this->gareEmail = $gareEmail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Code de vérification Gare - CAR225',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.gare-otp',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
