<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmallGroupPayment extends Model
{
    protected $fillable = [
        'small_group_offering_id',
        'member_id',
        'amount',
        'transaction_reference',
        'paid_at',
        'payment_method',
        'recorded_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function offering(): BelongsTo
    {
        return $this->belongsTo(SmallGroupOffering::class, 'small_group_offering_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
