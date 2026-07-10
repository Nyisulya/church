<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Project extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    protected $fillable = [
        'name',
        'description',
        'goal_amount',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'goal_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function pledges()
    {
        return $this->hasMany(Pledge::class, 'purpose', 'name');
    }

    public function groupGoals()
    {
        return $this->hasMany(ProjectGroupGoal::class);
    }
}
