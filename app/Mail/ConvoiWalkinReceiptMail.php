<?php

namespace App\Mail;

use App\Models\Convoi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class ConvoiWalkinReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Convoi $convoi) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reçu de convoi CAR225 — {$this->convoi->reference}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.convoi_walkin_receipt',
        );
    }

    public function attachments(): array
    {
        $pdfContent = Pdf::loadView('pdf.convoi_recu', ['convoi' => $this->convoi])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'         => 'DejaVu Sans',
                'isHtml5ParserEnabled'=> true,
                'isRemoteEnabled'     => false,
                'dpi'                 => 120,
            ])
            ->output();

        return [
            Attachment::fromData(fn () => $pdfContent, "recu-convoi-{$this->convoi->reference}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
