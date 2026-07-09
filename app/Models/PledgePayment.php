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
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::created(function ($pledgePayment) {
            try {
                $pledgePayment->loadMissing('pledge.member');
                $pledge = $pledgePayment->pledge;
                if ($pledge && $pledge->member && $pledge->member->phone) {
                    $member = $pledge->member;

                    $dateStr = '';
                    if ($pledgePayment->payment_date) {
                        if ($pledgePayment->payment_date instanceof \DateTimeInterface) {
                            $dateStr = $pledgePayment->payment_date->format('d/m/Y');
                        } else {
                            $dateStr = date('d/m/Y', strtotime($pledgePayment->payment_date));
                        }
                    } else {
                        $dateStr = date('d/m/Y');
                    }

                    // Reload the pledge fresh from the database to get the newly updated amount_paid
                    $pledge->refresh();
                    $remainingBalance = max(0, $pledge->amount - $pledge->amount_paid);

                    $message = "Bwana asifiwe " . $member->full_name . "! Tumepokea malipo ya Ahadi yako ya kiasi cha Shs " . number_format($pledgePayment->amount) . " ya tarehe " . $dateStr . " kwa ajili ya \"" . $pledge->purpose . "\". Salio la ahadi lililobaki ni Shs " . number_format($remainingBalance) . ". Mungu akubariki sana!";
                    
                    \App\Services\SmsService::send($member->phone, $message);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("PledgePayment SMS Error: " . $e->getMessage());
            }
        });
    }

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
