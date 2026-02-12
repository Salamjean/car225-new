<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ReservationCancelledNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    public $reservation;
    public $refundAmount;
    public $refundPercentage;
    public $isMultiSeat;
    public $relatedReference;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation, $refundAmount, $refundPercentage, $isMultiSeat = false, $relatedReference = null)
    {
        $this->reservation = $reservation;
        $this->refundAmount = $refundAmount;
        $this->refundPercentage = $refundPercentage;
        $this->isMultiSeat = $isMultiSeat;
        $this->relatedReference = $relatedReference;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail', 'broadcast'];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Annulation de réservation ❌',
            'message' => "Votre réservation {$this->reservation->reference} a été annulée.",
            'reservation_id' => $this->reservation->id,
            'reference' => $this->reservation->reference,
            'type' => 'cancellation',
            'count' => $notifiable->unreadNotifications()->count() + 1,
        ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Annulation de réservation - CAR 225')
            ->view('emails.reservation_cancelled', [
                'reservation' => $this->reservation,
                'refundAmount' => $this->refundAmount,
                'refundPercentage' => $this->refundPercentage,
                'isRoundTrip' => $this->isMultiSeat, // The view uses isRoundTrip
                'pairedReference' => $this->relatedReference
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => 'Annulation de réservation ❌',
            'message' => "Votre réservation {$this->reservation->reference} a été annulée. " . ($this->refundAmount > 0 ? "Un montant de {$this->refundAmount} FCFA a été crédité." : "Aucun remboursement effectué."),
            'reservation_id' => $this->reservation->id,
            'reference' => $this->reservation->reference,
            'refund_amount' => $this->refundAmount,
            'type' => 'cancellation'
        ];
    }
}
