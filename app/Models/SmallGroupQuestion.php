<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmallGroupQuestion extends Model
{
    protected $fillable = [
        'question_sw',
        'question_en',
        'response_type',
        'category',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get all responses for this question
     */
    public function responses(): HasMany
    {
        return $this->hasMany(SmallGroupResponse::class, 'question_id');
    }

    /**
     * Scope to get only active questions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order questions by their order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get formatted response type label
     */
    public function getResponseTypeLabel(): string
    {
        return match($this->response_type) {
            'number' => 'Number',
            'yes_no' => 'Yes/No',
            'text' => 'Text',
            'amount' => 'Amount (TSh)',
            default => 'Text',
        };
    }

    /**
     * Get category badge color
     */
    public function getCategoryBadgeClass(): string
    {
        return match($this->category) {
            'evangelism' => 'badge-primary',
            'bible_study' => 'badge-success',
            'community_service' => 'badge-info',
            'other' => 'badge-secondary',
            default => 'badge-secondary',
        };
    }

    /**
     * Get formatted category label
     */
    public function getCategoryLabel(): string
    {
        return match($this->category) {
            'evangelism' => 'Evangelism',
            'bible_study' => 'Bible Study',
            'community_service' => 'Community Service',
            'other' => 'Other',
            default => 'Other',
        };
    }
}
