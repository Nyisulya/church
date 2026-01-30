<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrayerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'request',
        'request_date',
        'status',
        'answer',
        'answered_at',
        'is_private',
        'created_by',
    ];

    protected $casts = [
        'request_date' => 'date',
        'answered_at' => 'datetime',
        'is_private' => 'boolean',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAnswered($query)
    {
        return $query->where('status', 'answered');
    }

    public function markAnswered($answer)
    {
        $this->status = 'answered';
        $this->answer = $answer;
        $this->answered_at = now();
        $this->save();
    }
}
