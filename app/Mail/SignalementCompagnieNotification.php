<?php

namespace App\Mail;

use App\Models\Signalement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SignalementCompagnieNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $signalement;

    /**
     * Create a new message instance.
     */
    public function __construct(Signalement $signalement)
    {
        $this->signalement = $signalement;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Signalement d\'un incident - Car225')
                    ->view('emails.signalement_compagnie');
    }
}
