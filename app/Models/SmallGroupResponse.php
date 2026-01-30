<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SmallGroupResponse extends Model
{
    protected $fillable = [
        'member_id',
        'small_group_id',
        'question_id',
        'week_starting',
        'response_value',
        'submitted_at',
    ];

    protected $casts = [
        'week_starting' => 'date',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the member who submitted this response
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the small group this response belongs to
     */
    public function smallGroup(): BelongsTo
    {
        return $this->belongsTo(SmallGroup::class);
    }

    /**
     * Get the question this response answers
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(SmallGroupQuestion::class, 'question_id');
    }

    /**
     * Get formatted response value based on question type
     */
    public function getFormattedValue(): string
    {
        if ($this->question) {
            return match($this->question->response_type) {
                'yes_no' => $this->response_value === '1' || $this->response_value === 'yes' ? 'Yes / Ndio' : 'No / Hapana',
                'amount' => 'TSh ' . number_format((float)$this->response_value, 0),
                'number' => number_format((int)$this->response_value),
                default => $this->response_value ?? '-',
            };
        }
        return $this->response_value ?? '-';
    }

    /**
     * Scope to get responses for a specific week
     */
    public function scopeForWeek($query, $weekStart)
    {
        return $query->where('week_starting', $weekStart);
    }

    /**
     * Scope to get responses for a specific member
     */
    public function scopeForMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    /**
     * Calculate the Saturday (start) of the current Adventist week
     * Week runs Saturday to Friday
     */
    public static function getCurrentWeekStart(): Carbon
    {
        $today = Carbon::now();
        $dayOfWeek = $today->dayOfWeek;
        
        // If today is Saturday (6), return today
        if ($dayOfWeek === Carbon::SATURDAY) {
            return $today->startOfDay();
        }
        
        // Otherwise, get the previous Saturday
        return $today->previous(Carbon::SATURDAY)->startOfDay();
    }

    /**
     * Get the end date of a week (Friday)
     */
    public static function getWeekEnd(Carbon $weekStart): Carbon
    {
        return $weekStart->copy()->addDays(6); // Saturday + 6 = Friday
    }

    /**
     * Format week range for display
     */
    public static function formatWeekRange(Carbon $weekStart): string
    {
        $weekEnd = self::getWeekEnd($weekStart);
        return $weekStart->format('M d') . ' - ' . $weekEnd->format('M d, Y');
    }
}
