<?php

namespace App\Notifications;

use App\Models\Reservation;
use App\Models\Programme;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class ReservationConfirmeeNotification extends Notification
{
    use Queueable;

    public $reservation;
    public $programme;
    public $qrCodeBase64;
    public $pdfContent;
    public $recipientName;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation, Programme $programme, string $qrCodeBase64, string $recipientName = null)
    {
        $this->reservation = $reservation;
        $this->programme = $programme;
        $this->qrCodeBase64 = $qrCodeBase64;
        $this->recipientName = $recipientName;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        // Retirez 'database' temporairement si la table n'existe pas
        return ['mail']; // Retirez 'database' de la liste
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        // Récupérer l'utilisateur
        $user = $this->reservation->user ?? $notifiable;
        $displayName = $this->recipientName ?: ($user->name ?? 'Client');

        // Données pour l'email
        $emailData = [
            'user' => $user,
            'displayName' => $displayName,
            'reservation' => $this->reservation,
            'programme' => $this->programme,
            'qrCodeBase64' => $this->qrCodeBase64,
            'dateVoyage' => date('d/m/Y', strtotime($this->reservation->date_voyage)),
            'heureDepart' => date('H:i', strtotime($this->programme->heure_depart)),
            'places' => json_decode($this->reservation->places, true) ?? [],
            'compagnie' => $this->programme->compagnie ?? null,
        ];

        // Générer le PDF
        $pdf = $this->generateTicketPDF();

        // Envoyer l'email
        $mail = (new MailMessage)
            ->subject('CAR 225 : Confirmation de votre réservation N°' . $this->reservation->reference)
            ->from('contact@edemarchee-ci.com', 'CAR 225')
            ->view('emails.reservation_confirmee', $emailData);

        // Attacher le PDF
        $mail->attachData($pdf, 'Billet_' . $this->reservation->reference . '.pdf', [
            'mime' => 'application/pdf',
        ]);

        return $mail;
    }

    /**
     * Générer le PDF du ticket
     */
    private function generateTicketPDF()
    {
        $prixUnitaire = (float) ($this->programme->montant_billet);
        $tripType = $this->programme->is_aller_retour ? 'Aller-Retour' : 'Aller Simple';

        $data = [
            'reservation' => $this->reservation,
            'programme' => $this->programme,
            'qrCodeBase64' => $this->qrCodeBase64,
            'user' => $this->reservation->user,
            'compagnie' => $this->programme->compagnie ?? null,
            'dateGeneration' => now(),
            'tripType' => $tripType,
            'prixUnitaire' => $prixUnitaire,
            'prixTotalIndividuel' => $this->programme->is_aller_retour ? $prixUnitaire * 2 : $prixUnitaire,
            'isAllerRetour' => (bool) $this->programme->is_aller_retour,
        ];

        return Pdf::loadView('pdf.ticket', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 150,
            ])
            ->output();
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'reference' => $this->reservation->reference,
            'programme' => $this->programme->point_depart . ' → ' . $this->programme->point_arrive,
            'date_voyage' => $this->reservation->date_voyage,
            'montant' => $this->reservation->montant_total,
            'status' => 'confirmed',
            'is_aller_retour' => $this->programme->is_aller_retour,
        ];
    }
}