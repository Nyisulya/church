<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'visit_date',
        'how_found_us',
        'assigned_to_member_id',
        'follow_up_status',
        'notes',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(Member::class, 'assigned_to_member_id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
