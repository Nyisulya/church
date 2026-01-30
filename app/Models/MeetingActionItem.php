<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingActionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'assigned_to_member_id',
        'description',
        'due_date',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * Get the event this action item belongs to
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the member assigned to this action item
     */
    public function assignedTo()
    {
        return $this->belongsTo(Member::class, 'assigned_to_member_id');
    }
}
