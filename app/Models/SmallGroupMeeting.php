<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmallGroupMeeting extends Model
{
    protected $fillable = [
        'small_group_id',
        'meeting_date',
        'topic',
        'notes',
        'attendees_count',
        'created_by',
    ];

    protected $casts = [
        'meeting_date' => 'datetime',
    ];

    public function smallGroup(): BelongsTo
    {
        return $this->belongsTo(SmallGroup::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
