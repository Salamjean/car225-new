<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $refundAmount;
    public $refundPercentage;
    public $isRoundTrip;
    public $pairedReference;

    /**
     * Create a new message instance.
     */
    public function __construct($reservation, $refundAmount, $refundPercentage, $isRoundTrip = false, $pairedReference = null)
    {
        $this->reservation = $reservation;
        $this->refundAmount = $refundAmount;
        $this->refundPercentage = $refundPercentage;
        $this->isRoundTrip = $isRoundTrip;
        $this->pairedReference = $pairedReference;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Annulation de réservation - CAR 225',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_cancelled',
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
