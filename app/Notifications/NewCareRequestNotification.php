<?php

namespace App\Notifications;

use App\Models\CareRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewCareRequestNotification extends Notification
{
    use Queueable;

    public CareRequest $careRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(CareRequest $careRequest)
    {
        $this->careRequest = $careRequest;
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
            'type' => 'care_request',
            'care_request_id' => $this->careRequest->id,
            'member_name' => $this->careRequest->member->full_name,
            'category' => $this->careRequest->category,
            'category_label' => $this->careRequest->category_label,
            'subject' => $this->careRequest->subject,
            'priority' => $this->careRequest->priority,
            'message' => 'You have received a new care request from ' . $this->careRequest->member->full_name,
            'url' => route('care-requests.show', $this->careRequest->id),
        ];
    }
}
