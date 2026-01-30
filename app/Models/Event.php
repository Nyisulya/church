<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'date',
        'start_time',
        'end_time',
        'agenda',
        'minutes',
        'location',
        'is_recurring',
        'recurrence_pattern',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get all attendances for this event
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get members who attended this event
     */
    public function members()
    {
        return $this->belongsToMany(Member::class, 'attendances')
            ->withPivot('status', 'scanned_at', 'scanned_by')
            ->withTimestamps();
    }

    /**
     * Get volunteer rosters for this event
     */
    public function rosters()
    {
        return $this->hasMany(Roster::class);
    }

    /**
     * Get action items for this meeting
     */
    public function actionItems()
    {
        return $this->hasMany(MeetingActionItem::class);
    }

    /**
     * Scope for upcoming events
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    /**
     * Scope for past events
     */
    public function scopePast($query)
    {
        return $query->where('date', '<', now()->toDateString());
    }

    /**
     * Scope for filtering by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
