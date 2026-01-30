<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Services\FlutterwaveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $flutterwave;

    public function __construct(FlutterwaveService $flutterwave)
    {
        $this->flutterwave = $flutterwave;
    }

    /**
     * Show the online giving form.
     */
    public function showForm()
    {
        $categories = \App\Models\GivingCategory::active()->get();
        return view('payments.form', compact('categories'));
    }

    /**
     * Process the payment via Flutterwave Mobile Money.
     */
    public function process(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'category' => 'required|string',
            'phone_number' => 'required|string',
            'network' => 'required|string', // VODACOM, AIRTEL, TIGO
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();
        $member = $user->member;
        if (! $member) {
            return redirect()->back()->with('error', 'No member profile linked to your account.');
        }

        $reference = 'TX-' . Str::random(10);
        
        // Initiate Mobile Money Payment
        $response = $this->flutterwave->initiateMobileMoney(
            $request->amount,
            'TZS', // Currency for Tanzania
            $request->phone_number,
            $user->email,
            $member->full_name,
            $request->network,
            $reference
        );

        if (isset($response['status']) && $response['status'] === 'success') {
            // Create Payment record (Pending)
            $payment = Payment::create([
                'member_id' => $member->id,
                'transaction_id' => $response['data']['id'] ?? null, // Flutterwave ID
                'reference' => $reference,
                'status' => 'pending',
                'amount' => $request->amount,
                'currency' => 'TZS',
                'category' => $request->category,
                'description' => $request->description,
                'phone_number' => $request->phone_number,
                'network' => $request->network,
            ]);

            // Create linked Transaction record (Pending)
            // Note: You might want to create transaction only after success, but for tracking pending pledges/attempts:
            // For now, we'll create it but maybe mark it pending if Transaction model supports it, 
            // or just rely on Payment model for pending state.
            // Let's create it as income but we might need a status on Transaction if we want to filter pending.
            // For simplicity, we'll create it now.
            
            Transaction::create([
                'type' => 'income',
                'category' => $payment->category,
                'amount' => $payment->amount,
                'payment_method' => 'mobile_money',
                'member_id' => $member->id,
                'payment_id' => $payment->id,
                'description' => $payment->description . " (Ref: $reference)",
                'transaction_date' => now(),
                'recorded_by' => Auth::id(),
            ]);

            return redirect()->route('give.success', ['reference' => $reference])
                ->with('status', 'Payment initiated! Please check your phone to confirm the transaction.');
        } else {
            Log::error('Flutterwave Payment Failed', ['response' => $response]);
            return redirect()->back()->with('error', 'Payment initiation failed. Please try again or check your phone number.');
        }
    }

    /**
     * Handle successful payment redirect (or just show pending/success page).
     */
    public function success(Request $request)
    {
        $reference = $request->query('reference');
        // In a real app, we might check status again here using verifyTransaction
        
        return view('payments.success', ['reference' => $reference]);
    }

    /**
     * Flutterwave webhook endpoint.
     */
    public function webhook(Request $request)
    {
        // Verify signature
        $secretHash = config('services.flutterwave.secret_hash');
        $signature = $request->header('verif-hash');
        
        if (!$signature || ($signature !== $secretHash)) {
            return response('Invalid signature', 401);
        }

        $payload = $request->all();
        
        if (isset($payload['status']) && $payload['status'] === 'successful') {
            $reference = $payload['txRef'];
            
            $payment = Payment::where('reference', $reference)->first();
            if ($payment) {
                $payment->update([
                    'status' => 'succeeded',
                    'transaction_id' => $payload['id'],
                ]);
                
                // Update transaction description or status if needed
            }
        } elseif (isset($payload['status']) && $payload['status'] === 'failed') {
             $reference = $payload['txRef'];
             $payment = Payment::where('reference', $reference)->first();
             if ($payment) {
                 $payment->update(['status' => 'failed']);
             }
        }

        return response('Webhook handled', 200);
    }
}
