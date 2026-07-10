<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Transaction extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'type',
        'category',
        'amount',
        'payment_method',
        'member_id',
        'payment_id',
        'description',
        'transaction_date',
        'reference_number',
        'recorded_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the member associated with this transaction
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the user who recorded this transaction
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Scope for income transactions
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope for expense transactions
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }
    /**
     * Get the payment associated with this transaction
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::created(function ($transaction) {
            if ($transaction->type === 'income' && $transaction->member_id) {
                // Check if a contribution already exists for this transaction
                $contributionExists = Contribution::where('transaction_id', $transaction->id)->exists();
                if (!$contributionExists) {
                    $lowerCategory = strtolower($transaction->category);
                    $lowerDescription = strtolower($transaction->description ?? '');
                    $type = 'other';
                    if (str_contains($lowerCategory, 'zaka') || str_contains($lowerCategory, 'tithe') || str_contains($lowerDescription, 'zaka') || str_contains($lowerDescription, 'tithe')) {
                        $type = 'zaka';
                    } elseif (str_contains($lowerCategory, 'sadaka') || str_contains($lowerCategory, 'offering') || str_contains($lowerDescription, 'sadaka') || str_contains($lowerDescription, 'offering')) {
                        $type = 'sadaka';
                    } elseif (str_contains($lowerCategory, 'ujenzi') || str_contains($lowerCategory, 'building') || str_contains($lowerDescription, 'ujenzi') || str_contains($lowerDescription, 'building')) {
                        $type = 'building';
                    } elseif (str_contains($lowerCategory, 'shukrani') || str_contains($lowerCategory, 'thanksgiving') || str_contains($lowerDescription, 'shukrani') || str_contains($lowerDescription, 'thanksgiving')) {
                        $type = 'thanksgiving';
                    } elseif (str_contains($lowerCategory, 'project') || str_contains($lowerDescription, 'project')) {
                        $type = 'project';
                    }

                    $paymentMethod = 'cash';
                    $lowerMethod = strtolower($transaction->payment_method);
                    if (str_contains($lowerMethod, 'mpesa') || str_contains($lowerMethod, 'mobile') || str_contains($lowerMethod, 'simu') || str_contains($lowerMethod, 'airtel') || str_contains($lowerMethod, 'tigo') || str_contains($lowerMethod, 'halo') || str_contains($lowerMethod, 'money')) {
                        $paymentMethod = 'mpesa';
                    } elseif (str_contains($lowerMethod, 'bank') || str_contains($lowerMethod, 'transfer') || str_contains($lowerMethod, 'card') || str_contains($lowerMethod, 'pos')) {
                        $paymentMethod = 'bank';
                    } elseif (str_contains($lowerMethod, 'cheque') || str_contains($lowerMethod, 'check')) {
                        $paymentMethod = 'check';
                    }

                    Contribution::create([
                        'member_id' => $transaction->member_id,
                        'transaction_id' => $transaction->id,
                        'amount' => $transaction->amount,
                        'type' => $type,
                        'payment_method' => $paymentMethod,
                        'reference_number' => $transaction->reference_number,
                        'date' => $transaction->transaction_date,
                        'notes' => $transaction->description,
                        'recorded_by' => $transaction->recorded_by ?? 1,
                    ]);
                }
            }
        });

        static::updated(function ($transaction) {
            if ($transaction->type === 'income' && $transaction->member_id) {
                $lowerCategory = strtolower($transaction->category);
                $lowerDescription = strtolower($transaction->description ?? '');
                $type = 'other';
                if (str_contains($lowerCategory, 'zaka') || str_contains($lowerCategory, 'tithe') || str_contains($lowerDescription, 'zaka') || str_contains($lowerDescription, 'tithe')) {
                    $type = 'zaka';
                } elseif (str_contains($lowerCategory, 'sadaka') || str_contains($lowerCategory, 'offering') || str_contains($lowerDescription, 'sadaka') || str_contains($lowerDescription, 'offering')) {
                    $type = 'sadaka';
                } elseif (str_contains($lowerCategory, 'ujenzi') || str_contains($lowerCategory, 'building') || str_contains($lowerDescription, 'ujenzi') || str_contains($lowerDescription, 'building')) {
                    $type = 'building';
                } elseif (str_contains($lowerCategory, 'shukrani') || str_contains($lowerCategory, 'thanksgiving') || str_contains($lowerDescription, 'shukrani') || str_contains($lowerDescription, 'thanksgiving')) {
                    $type = 'thanksgiving';
                } elseif (str_contains($lowerCategory, 'project') || str_contains($lowerDescription, 'project')) {
                    $type = 'project';
                }

                $paymentMethod = 'cash';
                $lowerMethod = strtolower($transaction->payment_method);
                if (str_contains($lowerMethod, 'mpesa') || str_contains($lowerMethod, 'mobile') || str_contains($lowerMethod, 'simu') || str_contains($lowerMethod, 'airtel') || str_contains($lowerMethod, 'tigo') || str_contains($lowerMethod, 'halo') || str_contains($lowerMethod, 'money')) {
                    $paymentMethod = 'mpesa';
                } elseif (str_contains($lowerMethod, 'bank') || str_contains($lowerMethod, 'transfer') || str_contains($lowerMethod, 'card') || str_contains($lowerMethod, 'pos')) {
                    $paymentMethod = 'bank';
                } elseif (str_contains($lowerMethod, 'cheque') || str_contains($lowerMethod, 'check')) {
                    $paymentMethod = 'check';
                }

                $contribution = Contribution::where('transaction_id', $transaction->id)->first();
                if ($contribution) {
                    $contribution->update([
                        'member_id' => $transaction->member_id,
                        'amount' => $transaction->amount,
                        'type' => $type,
                        'payment_method' => $paymentMethod,
                        'reference_number' => $transaction->reference_number,
                        'date' => $transaction->transaction_date,
                        'notes' => $transaction->description,
                    ]);
                } else {
                    Contribution::create([
                        'member_id' => $transaction->member_id,
                        'transaction_id' => $transaction->id,
                        'amount' => $transaction->amount,
                        'type' => $type,
                        'payment_method' => $paymentMethod,
                        'reference_number' => $transaction->reference_number,
                        'date' => $transaction->transaction_date,
                        'notes' => $transaction->description,
                        'recorded_by' => $transaction->recorded_by ?? 1,
                    ]);
                }
            } else {
                Contribution::where('transaction_id', $transaction->id)->delete();
            }
        });

        static::deleted(function ($transaction) {
            Contribution::where('transaction_id', $transaction->id)->delete();
        });
    }
}

