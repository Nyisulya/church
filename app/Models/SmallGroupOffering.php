<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmallGroupOffering extends Model
{
    protected $fillable = [
        'small_group_id',
        'name',
        'description',
        'amount_per_member',
        'target_amount',
        'deadline',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'amount_per_member' => 'decimal:2',
        'target_amount' => 'decimal:2',
        'deadline' => 'date',
        'is_active' => 'boolean',
    ];

    public function smallGroup(): BelongsTo
    {
        return $this->belongsTo(SmallGroup::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SmallGroupPayment::class);
    }

    /**
     * Calculate total collected amount
     */
    public function getTotalCollectedAttribute()
    {
        return $this->payments()->sum('amount');
    }

    /**
     * Calculate progress percentage towards target
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->target_amount > 0) {
            return min(100, round(($this->total_collected / $this->target_amount) * 100));
        }
        return 0;
    }

    /**
     * Get balance (debt) for a specific member
     */
    public function getMemberBalance($memberId)
    {
        if (!$this->amount_per_member) {
            return 0; // Voluntary offering has no debt
        }

        $paid = $this->payments()->where('member_id', $memberId)->sum('amount');
        return max(0, $this->amount_per_member - $paid);
    }
}
