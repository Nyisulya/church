<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectGroupGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'small_group_id',
        'target_amount',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
    ];

    /**
     * Get the project associated with this goal
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the small group associated with this goal
     */
    public function smallGroup()
    {
        return $this->belongsTo(SmallGroup::class, 'small_group_id');
    }
}
