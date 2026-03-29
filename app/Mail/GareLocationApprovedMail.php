<?php

namespace App\Mail;

use App\Models\Gare;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GareLocationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Gare $gare;
    public float $latitude;
    public float $longitude;
    public string $context; // 'approved' | 'manual'

    public function __construct(Gare $gare, float $latitude, float $longitude, string $context = 'approved')
    {
        $this->gare      = $gare;
        $this->latitude  = $latitude;
        $this->longitude = $longitude;
        $this->context   = $context;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Votre localisation GPS a été mise à jour - CAR225',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.gare-location-approved',
            with: [
                'gare'      => $this->gare,
                'latitude'  => $this->latitude,
                'longitude' => $this->longitude,
                'context'   => $this->context,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
