<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class GareOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $gareName;
    public $gareEmail;
    public $codeId;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otp, string $gareName, string $gareEmail, string $codeId)
    {
        $this->otp = $otp;
        $this->gareName = $gareName;
        $this->gareEmail = $gareEmail;
        $this->codeId = $codeId;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
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
            with: [
                'otp' => $this->otp,
                'gareName' => $this->gareName,
                'gareEmail' => $this->gareEmail,
                'codeId' => $this->codeId,
            ],
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
