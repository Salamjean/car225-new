<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChauffeurOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $chauffeurName;
    public $chauffeurEmail;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otp, string $chauffeurName, string $chauffeurEmail)
    {
        $this->otp = $otp;
        $this->chauffeurName = $chauffeurName;
        $this->chauffeurEmail = $chauffeurEmail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Code de vérification - CAR225',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.chauffeur-otp',
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
