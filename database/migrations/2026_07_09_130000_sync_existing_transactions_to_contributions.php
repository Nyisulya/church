<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Transaction;
use App\Models\Contribution;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Get all income transactions that have a member_id
        $transactions = Transaction::where('type', 'income')
            ->whereNotNull('member_id')
            ->get();

        foreach ($transactions as $transaction) {
            // Check if contribution already exists
            $exists = Contribution::where('transaction_id', $transaction->id)->exists();
            if (!$exists) {
                // Map category to type
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

                // Map payment method to contribution enum
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to delete anything as this is just a data sync migration
    }
};
