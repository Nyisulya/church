<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MinistryPledge extends Model
{
    protected $fillable = [
        'department_id',
        'created_by',
        'title',
        'description',
        'target_amount',
        'target_date',
        'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'target_date' => 'date',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contributions()
    {
        return $this->hasMany(MinistryContribution::class);
    }

    public function getTotalContributedAttribute()
    {
        return $this->contributions()->sum('amount');
    }

    public function getRemainingAmountAttribute()
    {
        return $this->target_amount - $this->total_contributed;
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->target_amount == 0) return 0;
        return min(100, ($this->total_contributed / $this->target_amount) * 100);
    }
}
