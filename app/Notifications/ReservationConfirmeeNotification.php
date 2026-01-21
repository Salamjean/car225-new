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
    public $qrCodeRetourBase64;
    public $pdfContent;
    public $recipientName;
    public $seatNumber;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation, Programme $programme, string $qrCodeBase64, string $recipientName = null, int $seatNumber = null, string $qrCodeRetourBase64 = null)
    {
        $this->reservation = $reservation;
        $this->programme = $programme;
        $this->qrCodeBase64 = $qrCodeBase64;
        $this->qrCodeRetourBase64 = $qrCodeRetourBase64;
        $this->recipientName = $recipientName;
        $this->seatNumber = $seatNumber;
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
            'qrCodeRetourBase64' => $this->qrCodeRetourBase64,
            'dateVoyage' => date('d/m/Y', strtotime($this->reservation->date_voyage)),
            'dateRetour' => $this->reservation->date_retour ? date('d/m/Y', strtotime($this->reservation->date_retour)) : null,
            'heureDepart' => date('H:i', strtotime($this->programme->heure_depart)),
            'places' => json_decode($this->reservation->places, true) ?? [],
            'compagnie' => $this->programme->compagnie ?? null,
            'seatNumber' => $this->seatNumber,
            'isAllerRetour' => (bool) $this->reservation->is_aller_retour,
        ];

        // Envoyer l'email
        $mail = (new MailMessage)
            ->subject('CAR 225 : Confirmation de votre réservation N°' . $this->reservation->reference)
            ->from('contact@maelysimo.com', 'CAR 225')
            ->view('emails.reservation_confirmee', $emailData);

        // Générer et attacher le PDF Aller
        $pdfAller = $this->generateTicketPDF('aller');
        $allerSuffix = $this->reservation->is_aller_retour ? '_Aller' : '';
        $mail->attachData($pdfAller, 'Billet' . $allerSuffix . '_' . $this->reservation->reference . '.pdf', [
            'mime' => 'application/pdf',
        ]);

        // Si aller-retour, générer et attacher le PDF Retour
        if ($this->reservation->is_aller_retour && $this->qrCodeRetourBase64) {
            $pdfRetour = $this->generateTicketPDF('retour');
            $mail->attachData($pdfRetour, 'Billet_Retour_' . $this->reservation->reference . '.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }

    /**
     * Générer le PDF du ticket (aller ou retour)
     */
    private function generateTicketPDF(string $type = 'aller')
    {
        $prixUnitaire = (float) ($this->programme->montant_billet);
        $tripType = $this->reservation->is_aller_retour ? 'Aller-Retour' : 'Aller Simple';
        
        // Déterminer quelle date et quel QR code utiliser
        $dateVoyage = $this->reservation->date_voyage;
        $qrCode = $this->qrCodeBase64;
        $ticketType = 'ALLER';
        
        // Points de départ et d'arrivée (inversés pour le retour)
        $pointDepart = $this->programme->point_depart;
        $pointArrive = $this->programme->point_arrive;
        
        if ($type === 'retour' && $this->reservation->is_aller_retour) {
            $dateVoyage = $this->reservation->date_retour ?? $this->reservation->date_voyage;
            $qrCode = $this->qrCodeRetourBase64 ?? $this->qrCodeBase64;
            $ticketType = 'RETOUR';
            
            // INVERSER les points pour le retour
            $pointDepart = $this->programme->point_arrive;
            $pointArrive = $this->programme->point_depart;
        }

        $data = [
            'reservation' => $this->reservation,
            'programme' => $this->programme,
            'qrCodeBase64' => $qrCode,
            'user' => $this->reservation->user,
            'compagnie' => $this->programme->compagnie ?? null,
            'dateGeneration' => now(),
            'tripType' => $tripType,
            'ticketType' => $ticketType, // 'ALLER' ou 'RETOUR'
            'dateVoyage' => $dateVoyage,
            'prixUnitaire' => $prixUnitaire,
            'prixTotalIndividuel' => $this->reservation->is_aller_retour ? $prixUnitaire * 2 : $prixUnitaire,
            'isAllerRetour' => (bool) $this->reservation->is_aller_retour,
            'seatNumber' => $this->seatNumber,
            // Points de départ/arrivée (inversés pour retour)
            'pointDepart' => $pointDepart,
            'pointArrive' => $pointArrive,
            // Infos passager
            'passagerNom' => $this->reservation->passager_nom,
            'passagerPrenom' => $this->reservation->passager_prenom,
            'passagerTelephone' => $this->reservation->passager_telephone,
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
            'date_retour' => $this->reservation->date_retour,
            'montant' => $this->reservation->montant,
            'status' => 'confirmed',
            'is_aller_retour' => $this->programme->is_aller_retour,
        ];
    }
}