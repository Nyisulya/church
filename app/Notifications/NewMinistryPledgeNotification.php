<?php

namespace App\Notifications;

use App\Models\MinistryPledge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMinistryPledgeNotification extends Notification
{
    use Queueable;

    public $pledge;

    public function __construct(MinistryPledge $pledge)
    {
        $this->pledge = $pledge;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Ministry Pledge: ' . $this->pledge->title,
            'message' => 'A new pledge has been created for ' . $this->pledge->department->name,
            'action_url' => route('ministry-pledges.show', $this->pledge),
            'type' => 'ministry_pledge',
            'department_id' => $this->pledge->department_id,
            'pledge_id' => $this->pledge->id,
        ];
    }
}
