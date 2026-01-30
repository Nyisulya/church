<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmallGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'leader_id',
        'meeting_day',
        'meeting_time',
        'location',
        'max_members',
        'status',
    ];

    public function leader(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'leader_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'small_group_member')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(SmallGroupMeeting::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SmallGroupResponse::class);
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(SmallGroupOffering::class);
    }

    public function isFull(): bool
    {
        return $this->members()->count() >= $this->max_members;
    }
}
