<?php

namespace App\Mail;

use App\Models\Convoi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConvoiChauffeurAssigneMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Convoi $convoi) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Votre convoi CAR225 est prêt — Chauffeur assigné');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.convoi.chauffeur_assigne');
    }
}
