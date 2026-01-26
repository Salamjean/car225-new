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
    public $seatNumber;
    public $ticketType;
    public $qrCodeRetourBase64;
    public $programmeRetour;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation, Programme $programme, string $qrCodeBase64, string $recipientName = null, int $seatNumber = null, string $ticketType = null, string $qrCodeRetourBase64 = null, Programme $programmeRetour = null)
    {
        $this->reservation = $reservation;
        $this->programme = $programme;
        $this->qrCodeBase64 = $qrCodeBase64;
        $this->recipientName = $recipientName;
        $this->seatNumber = $seatNumber;
        $this->ticketType = $ticketType;
        $this->qrCodeRetourBase64 = $qrCodeRetourBase64;
        $this->programmeRetour = $programmeRetour;
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
            'seatNumber' => $this->seatNumber,
        ];

        // Générer le PDF Aller (ou Aller Simple)
        $pdfAller = $this->generateTicketPDF($this->programme, $this->qrCodeBase64, $this->ticketType ?: ($this->reservation->is_aller_retour ? 'ALLER' : 'ALLER SIMPLE'));

        // Envoyer l'email
        $mail = (new MailMessage)
            ->subject('CAR 225 : Confirmation de votre réservation N°' . $this->reservation->reference)
            ->from('contact@maelysimo.com', 'CAR 225')
            ->view('emails.reservation_confirmee', $emailData);

        // Attacher le PDF Aller
        $mail->attachData($pdfAller, 'Billet_ALLER_' . $this->reservation->reference . '.pdf', [
            'mime' => 'application/pdf',
        ]);

        // Si c'est un aller-retour avec ticket retour, attacher le PDF Retour
        if ($this->qrCodeRetourBase64) {
            $pdfRetour = $this->generateTicketPDF($this->programmeRetour ?: $this->programme, $this->qrCodeRetourBase64, 'RETOUR');
            $mail->attachData($pdfRetour, 'Billet_RETOUR_' . $this->reservation->reference . '.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }

    /**
     * Générer le PDF du ticket
     */
    private function generateTicketPDF($programme, $qrCodeBase64, $ticketType)
    {
        $prixUnitaire = (float) ($this->programme->montant_billet);
        $tripType = $this->reservation->is_aller_retour ? 'Aller-Retour' : 'Aller Simple';

        $data = [
            'reservation' => $this->reservation,
            'programme' => $programme,
            'qrCodeBase64' => $qrCodeBase64,
            'user' => $this->reservation->user,
            'compagnie' => $programme->compagnie ?? $this->programme->compagnie,
            'dateGeneration' => now(),
            'tripType' => $tripType,
            'ticketType' => $ticketType,
            'dateVoyage' => $ticketType === 'RETOUR' ? $this->reservation->date_retour : $this->reservation->date_voyage,
            'heureDepart' => $programme->heure_depart,
            'prixUnitaire' => $prixUnitaire,
            'prixTotalIndividuel' => (float)$this->reservation->montant,
            'isAllerRetour' => (bool) $this->reservation->is_aller_retour,
            'seatNumber' => $this->seatNumber,
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
            'montant' => $this->reservation->montant,
            'status' => 'confirmed',
            'is_aller_retour' => $this->programme->is_aller_retour,
        ];
    }
}