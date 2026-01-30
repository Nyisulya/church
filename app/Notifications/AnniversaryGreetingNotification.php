<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnniversaryGreetingNotification extends Notification
{
    use Queueable;

    public $senderName;

    /**
     * Create a new notification instance.
     */
    public function __construct($senderName = 'Church Family')
    {
        $this->senderName = $senderName;
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
        $messages = [
            "Happy Anniversary! May your love continue to grow stronger each day.",
            "Wishing you another year of love, joy, and blessings together.",
            "May God continue to bless your marriage abundantly.",
            "Celebrating the beautiful love you share. Happy Anniversary!",
            "May your journey together be filled with God's grace and peace.",
            "Happy Anniversary! May your home always be filled with laughter and love.",
            "Wishing you a lifetime of happiness and togetherness.",
            "May the bond of love between you get stronger every day.",
            "Happy Anniversary to a wonderful couple!",
            "May God's love be the center of your marriage always."
        ];

        $randomMessage = $messages[array_rand($messages)];

        return [
            'title' => '💍 Happy Anniversary!',
            'message' => $randomMessage,
            'sender' => $this->senderName,
            'icon' => 'fas fa-ring',
        ];
    }
}
