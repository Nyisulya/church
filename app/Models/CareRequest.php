<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'leader_id',
        'category',
        'subject',
        'message',
        'priority',
        'status',
        'leader_notes',
        'response',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    /**
     * Category labels for display
     */
    /**
     * Category labels for display
     */
    public const CATEGORIES = [
        'sick' => 'Sick / Health Issue',
        'need_visit' => 'Need a Visit',
        'need_prayer' => 'Need Prayer',
        'counseling' => 'Counseling',
        'financial_help' => 'Financial Help',
        'other' => 'Other',
    ];

    /**
     * Priority levels with colors
     */
    public const PRIORITIES = [
        'low' => ['label' => 'Low', 'color' => 'secondary'],
        'normal' => ['label' => 'Normal', 'color' => 'info'],
        'high' => ['label' => 'High', 'color' => 'warning'],
        'urgent' => ['label' => 'Urgent', 'color' => 'danger'],
    ];

    /**
     * Status labels with colors
     */
    public const STATUSES = [
        'pending' => ['label' => 'Pending', 'color' => 'warning'],
        'in_progress' => ['label' => 'In Progress', 'color' => 'info'],
        'completed' => ['label' => 'Completed', 'color' => 'success'],
        'cancelled' => ['label' => 'Cancelled', 'color' => 'secondary'],
    ];

    // =========== RELATIONSHIPS ===========

    /**
     * The member who submitted the request
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * The leader assigned to handle the request
     */
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    // =========== SCOPES ===========

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'in_progress']);
    }

    public function scopeForLeader($query, $leaderId)
    {
        return $query->where('leader_id', $leaderId);
    }

    public function scopeForMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    // =========== ACCESSORS ===========

    /**
     * Get the category label with emoji
     */
    public function getCategoryLabelAttribute()
    {
        $label = self::CATEGORIES[$this->category] ?? $this->category;
        $emojis = [
            'sick' => '🏥 ',
            'need_visit' => '🏠 ',
            'need_prayer' => '🙏 ',
            'counseling' => '💬 ',
            'financial_help' => '💰 ',
            'other' => '📝 ',
        ];
        
        return ($emojis[$this->category] ?? '') . __($label);
    }

    /**
     * Get priority badge info
     */
    public function getPriorityBadgeAttribute()
    {
        $priority = self::PRIORITIES[$this->priority] ?? ['label' => $this->priority, 'color' => 'secondary'];
        $priority['label'] = __($priority['label']);
        return $priority;
    }

    /**
     * Get status badge info
     */
    public function getStatusBadgeAttribute()
    {
        $status = self::STATUSES[$this->status] ?? ['label' => $this->status, 'color' => 'secondary'];
        $status['label'] = __($status['label']);
        return $status;
    }

    // =========== METHODS ===========

    /**
     * Mark request as in progress
     */
    public function markInProgress()
    {
        $this->update(['status' => 'in_progress']);
    }

    /**
     * Mark request as completed with a response
     */
    public function markCompleted($response = null)
    {
        $this->update([
            'status' => 'completed',
            'response' => $response,
            'responded_at' => now(),
        ]);
    }

    /**
     * Add a response from the leader
     */
    public function addResponse($response)
    {
        $this->update([
            'response' => $response,
            'responded_at' => now(),
        ]);
    }
}
