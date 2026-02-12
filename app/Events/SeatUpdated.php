<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeatUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $programId;
    public $dateVoyage;
    public $reservedSeats;

    /**
     * Create a new event instance.
     */
    public function __construct($programId, $dateVoyage, $reservedSeats)
    {
        $this->programId = $programId;
        $this->dateVoyage = $dateVoyage;
        $this->reservedSeats = $reservedSeats;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('program.' . $this->programId . '.' . $this->dateVoyage),
        ];
    }

    public function broadcastAs(): string
    {
        return 'seat.updated';
    }
}
