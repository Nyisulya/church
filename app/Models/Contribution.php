<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'transaction_id',
        'amount',
        'type',
        'payment_method',
        'reference_number',
        'date',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::created(function ($contribution) {
            $contribution->loadMissing('member');
            if ($contribution->member && $contribution->member->phone) {
                $member = $contribution->member;
                $typeLabel = match($contribution->type) {
                    'zaka' => 'Zaka',
                    'sadaka' => 'Sadaka',
                    'project' => 'Mchango wa Mradi',
                    'building' => 'Mchango wa Ujenzi',
                    'thanksgiving' => 'Shukrani',
                    default => 'Mchango',
                };
                $message = "Bwana asifiwe " . $member->full_name . "! Tumepokea " . $typeLabel . " yako ya kiasi cha Shs " . number_format($contribution->amount) . " ya tarehe " . date('d/m/Y', strtotime($contribution->date)) . ". Asante sana kwa kutoa kwa ajili ya kazi ya Bwana. Mungu akubariki sana!";
                try {
                    \App\Services\SmsService::send($member->phone, $message);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Contribution SMS Error: " . $e->getMessage());
                }
            }
        });
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return ucfirst($this->type);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
