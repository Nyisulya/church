<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pledge extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'amount',
        'amount_paid',
        'purpose',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the member who made this pledge
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the user who created this pledge
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all payments for this pledge
     */
    public function payments()
    {
        return $this->hasMany(PledgePayment::class);
    }

    /**
     * Get remaining balance
     */
    public function getRemainingBalanceAttribute()
    {
        return $this->amount - $this->amount_paid;
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentageAttribute()
    {
        if ($this->amount == 0) {
            return 0;
        }
        return round(($this->amount_paid / $this->amount) * 100, 2);
    }

    /**
     * Record a payment towards this pledge
     */
    public function recordPayment($transactionId, $amount, $paymentDate)
    {
        $payment = $this->payments()->create([
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'payment_date' => $paymentDate,
        ]);

        $this->amount_paid += $amount;

        // Mark as completed if fully paid
        if ($this->amount_paid >= $this->amount) {
            $this->status = 'completed';
        }

        $this->save();

        return $payment;
    }

    /**
     * Mark pledge as complete
     */
    public function markComplete()
    {
        $this->status = 'completed';
        $this->save();
    }

    /**
     * Scope for active pledges
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for completed pledges
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
