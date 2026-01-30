<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'user_id',
        'title',
        'body',
        'is_general',
        'announcement_date',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'announcement_date' => 'date',
        'is_general' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeGeneral($query)
    {
        return $query->where('is_general', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where('announcement_date', '>=', now()->startOfWeek())
                    ->where('announcement_date', '<=', now()->endOfWeek())
                    ->orderBy('priority', 'desc')
                    ->orderBy('announcement_date');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
