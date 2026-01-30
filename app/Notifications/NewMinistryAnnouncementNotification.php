<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMinistryAnnouncementNotification extends Notification
{
    use Queueable;

    public $announcement;

    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Announcement: ' . $this->announcement->title,
            'message' => 'New announcement from ' . $this->announcement->department->name,
            'action_url' => route('departments.show', $this->announcement->department),
            'type' => 'ministry_announcement',
            'department_id' => $this->announcement->department_id,
            'announcement_id' => $this->announcement->id,
        ];
    }
}
