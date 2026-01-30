<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageNotification extends Notification
{
    use Queueable;

    public $sender;
    public $subject;
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($sender, $subject, $message)
    {
        $this->sender = $sender;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'sender_name' => $this->sender->name,
            'sender_id' => $this->sender->id,
            'subject' => $this->subject,
            'message' => $this->message,
            'sent_at' => now(),
        ];
    }
}
