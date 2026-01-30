<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BirthdayGreetingNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $messages = [
            "Wishing you a blessed birthday filled with God's love!",
            "May God's grace abound in your life this new year!",
            "Wishing you a day filled with joy, laughter, and blessings.",
            "May this special day bring you endless happiness and peace.",
            "Celebrating you today! May your year be as wonderful as you are.",
            "Praying for a year of breakthrough and favor for you.",
            "Happy Birthday! May the Lord guide your steps in this new chapter.",
            "May the light of the Lord shine upon you today and always.",
            "Wishing you good health, prosperity, and joy in the Lord.",
            "Have a fantastic birthday celebration!"
        ];

        $randomMessage = $messages[array_rand($messages)];

        return [
            'title' => '🎂 Happy Birthday!',
            'message' => $randomMessage,
            'sender' => $this->senderName,
            'icon' => 'fas fa-birthday-cake',
        ];
    }
}
