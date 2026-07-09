<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PledgePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pledge_id',
        'transaction_id',
        'amount',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];



    /**
     * Get the pledge this payment belongs to
     */
    public function pledge()
    {
        return $this->belongsTo(Pledge::class);
    }

    /**
     * Get the transaction associated with this payment
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
