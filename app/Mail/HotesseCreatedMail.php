<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
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
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Bienvenue - Votre compte hôtesse a été créé',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.hotesse-created',
            with: [
                'hotesseData' => $this->hotesseData,
                'otpCode' => $this->otpCode,
                'compagnieName' => $this->compagnieName,
            ],
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
