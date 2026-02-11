<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;

class GeneralNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    protected $title;
    protected $message;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $type = 'info')
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'count' => $notifiable->unreadNotifications()->count() + 1, // +1 because this one isn't saved yet in some contexts
        ]);
    }

    /**
     * The type of the notification being broadcast.
     */
    public function broadcastType(): string
    {
        return 'general.notification';
    }
}
