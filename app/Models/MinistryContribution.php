<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MinistryContribution extends Model
{
    protected $fillable = [
        'ministry_pledge_id',
        'member_id',
        'amount',
        'contribution_date',
        'payment_method',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'contribution_date' => 'date',
    ];

    public function pledge()
    {
        return $this->belongsTo(MinistryPledge::class, 'ministry_pledge_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
