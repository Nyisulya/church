<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'visitor_id',
        'visit_type',
        'visit_date',
        'purpose',
        'notes',
        'outcome',
        'follow_up_required',
        'created_by',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'follow_up_required' => 'boolean',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function visitor()
    {
        return $this->belongsTo(User::class, 'visitor_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
