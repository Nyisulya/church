<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    //
    protected $fillable = ['name', 'description', 'chairman_id', 'secretary_id'];

    public function members()
    {
        return $this->belongsToMany(Member::class, 'department_member')
                    ->withPivot('role', 'joined_at', 'left_at', 'status')
                    ->withTimestamps();
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class)->latest();
    }

    public function chairman()
    {
        return $this->belongsTo(Member::class, 'chairman_id');
    }

    public function secretary()
    {
        return $this->belongsTo(Member::class, 'secretary_id');
    }

    public function pledges()
    {
        return $this->hasMany(MinistryPledge::class);
    }
}
