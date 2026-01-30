<?php

namespace App\Notifications;

use App\Models\CareRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CareRequestResponseNotification extends Notification
{
    use Queueable;

    public CareRequest $careRequest;
    public string $responseType;

    /**
     * Create a new notification instance.
     * 
     * @param CareRequest $careRequest
     * @param string $responseType - 'response' or 'status_update'
     */
    public function __construct(CareRequest $careRequest, string $responseType = 'response')
    {
        $this->careRequest = $careRequest;
        $this->responseType = $responseType;
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
        $leaderName = $this->careRequest->leader->name;
        $status = $this->careRequest->status_badge;

        if ($this->responseType === 'response') {
            $message = "{$leaderName} has responded to your care request: \"{$this->careRequest->subject}\"";
        } else {
            $message = "Your care request \"{$this->careRequest->subject}\" status has been updated to: {$status['label']}";
        }

        return [
            'type' => 'care_request_response',
            'care_request_id' => $this->careRequest->id,
            'leader_name' => $leaderName,
            'subject' => $this->careRequest->subject,
            'status' => $this->careRequest->status,
            'response_type' => $this->responseType,
            'message' => $message,
            'url' => route('care-requests.show', $this->careRequest->id),
        ];
    }
}
