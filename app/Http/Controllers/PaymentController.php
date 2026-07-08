<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Pledge;
use App\Models\SmallGroupOffering;
use App\Models\SmallGroupPayment;
use App\Services\PesapalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $pesapal;

    public function __construct(PesapalService $pesapal)
    {
        $this->pesapal = $pesapal;
    }

    /**
     * Show the online giving form.
     */
    public function showForm(Request $request)
    {
        $user = Auth::user();
        if (!$user->member) {
            return redirect()->route('profile.index')->with('warning', 'Tafadhali kamilisha wasifu wa muumini kwanza.');
        }

        $categories = \App\Models\GivingCategory::active()->get();
        
        // Load active pledges for this member
        $pledges = Pledge::active()
            ->where('member_id', $user->member->id)
            ->where('amount_paid', '<', DB::raw('amount'))
            ->get();

        // Load active small group offerings for this member's groups
        $smallGroups = $user->member->smallGroups;
        $smallGroupOfferings = collect([]);
        if ($smallGroups->isNotEmpty()) {
            $smallGroupOfferings = SmallGroupOffering::whereIn('small_group_id', $smallGroups->pluck('id'))
                ->where('is_active', true)
                ->get();
        }

        // Check if pre-filled pledge
        $preselectedPledgeId = $request->query('pledge_id');
        $preselectedPledge = null;
        if ($preselectedPledgeId) {
            $preselectedPledge = $pledges->firstWhere('id', $preselectedPledgeId);
        }

        return view('payments.form', compact('categories', 'pledges', 'smallGroupOfferings', 'preselectedPledge'));
    }

    /**
     * Process the payment via Pesapal.
     */
    public function process(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|in:general,pledge,small_group',
            'amount' => 'required|numeric|min:100', // Minimum 100 TZS
            'phone_number' => 'nullable|string',
            'description' => 'nullable|string|max:255',
            
            // Conditional validation
            'category' => 'required_if:payment_type,general|nullable|string',
            'pledge_id' => 'required_if:payment_type,pledge|nullable|exists:pledges,id',
            'small_group_offering_id' => 'required_if:payment_type,small_group|nullable|exists:small_group_offerings,id',
        ]);

        $user = Auth::user();
        $member = $user->member;
        if (!$member) {
            return redirect()->back()->with('error', 'No member profile linked to your account.');
        }

        $reference = 'TX-' . Str::upper(Str::random(12));
        $category = 'General Giving';
        $pledgeId = null;
        $smallGroupOfferingId = null;

        // Determine category and details based on payment type
        if ($request->payment_type === 'general') {
            $category = 'Giving: ' . $request->category;
            $payDescription = "Manzese SDA Church - " . $request->category;
        } elseif ($request->payment_type === 'pledge') {
            $pledge = Pledge::findOrFail($request->pledge_id);
            $pledgeId = $pledge->id;
            $category = 'Pledge Payment';
            $payDescription = "Pledge Payment: " . $pledge->purpose;
        } elseif ($request->payment_type === 'small_group') {
            $offering = SmallGroupOffering::findOrFail($request->small_group_offering_id);
            $smallGroupOfferingId = $offering->id;
            $category = 'Small Group: ' . $offering->name;
            $payDescription = "Kanda Offering: " . $offering->name;
        }

        if ($request->filled('description')) {
            $payDescription .= " (" . $request->description . ")";
        }

        // Initiate Pesapal Order
        $response = $this->pesapal->submitOrder(
            $request->amount,
            $reference,
            $payDescription,
            $user->email,
            $member->full_name,
            $request->phone_number ?? $member->phone ?? '255700000000'
        );

        if ($response && isset($response['redirect_url'])) {
            // Create pending payment record
            Payment::create([
                'member_id' => $member->id,
                'pledge_id' => $pledgeId,
                'small_group_offering_id' => $smallGroupOfferingId,
                'reference' => $reference,
                'status' => 'pending',
                'amount' => $request->amount,
                'currency' => 'TZS',
                'category' => $category,
                'description' => $payDescription,
                'phone_number' => $request->phone_number ?? $member->phone ?? 'N/A',
            ]);

            // Redirect to Pesapal iframe / checkout redirect url
            return redirect()->away($response['redirect_url']);
        } else {
            Log::error('Pesapal Order Initiation Failed', ['response' => $response]);
            return redirect()->back()->with('error', 'Imeshindwa kuanzisha malipo. Tafadhali jaribu tena baadae au wasiliana na utawala.');
        }
    }

    /**
     * Handle successful payment redirect callback from Pesapal.
     */
    public function success(Request $request)
    {
        $trackingId = $request->query('OrderTrackingId');
        $reference = $request->query('OrderMerchantReference');

        if ($trackingId && $reference) {
            // Verify payment status from Pesapal API
            $verifyResponse = $this->pesapal->getTransactionStatus($trackingId);

            if ($verifyResponse && isset($verifyResponse['payment_status_description'])) {
                $status = $verifyResponse['payment_status_description'];

                if ($status === 'Completed' || $status === 'Success') {
                    $payment = Payment::where('reference', $reference)->first();

                    if ($payment) {
                        if ($payment->status === 'pending') {
                            $this->completePaymentTransaction($payment, $trackingId, $verifyResponse);
                        }
                        
                        return view('payments.success', compact('payment', 'reference'));
                    }
                }
            }
        }

        return redirect()->route('give.form')->with('error', 'Malipo hayakukamilika au yameshindikana.');
    }

    /**
     * Pesapal IPN webhook endpoint.
     */
    public function webhook(Request $request)
    {
        $trackingId = $request->query('OrderTrackingId');
        $reference = $request->query('OrderMerchantReference');
        $notificationType = $request->query('OrderNotificationType');

        if ($trackingId && $reference && $notificationType === 'IPNCHANGE') {
            $verifyResponse = $this->pesapal->getTransactionStatus($trackingId);

            if ($verifyResponse && isset($verifyResponse['payment_status_description'])) {
                $status = $verifyResponse['payment_status_description'];
                $payment = Payment::where('reference', $reference)->first();

                if ($payment && $payment->status === 'pending') {
                    if ($status === 'Completed' || $status === 'Success') {
                        $this->completePaymentTransaction($payment, $trackingId, $verifyResponse);
                    } elseif ($status === 'Failed') {
                        $payment->update(['status' => 'failed']);
                    }
                }
            }
        }

        return response()->json([
            'orderNotificationType' => $notificationType,
            'orderTrackingId' => $trackingId,
            'orderMerchantReference' => $reference,
            'status' => '200'
        ]);
    }

    /**
     * Complete the payment transaction and create appropriate logs/pledge records.
     */
    protected function completePaymentTransaction($payment, $trackingId, $pesapalData)
    {
        DB::transaction(function () use ($payment, $trackingId, $pesapalData) {
            $confirmationCode = $pesapalData['confirmation_code'] ?? $trackingId;
            
            // 1. Update payment record
            $payment->update([
                'status' => 'succeeded',
                'transaction_id' => $confirmationCode, // Store actual M-Pesa reference / Card code
                'network' => $pesapalData['payment_method'] ?? 'pesapal',
            ]);

            // 2. Create financial transaction
            $transaction = Transaction::create([
                'type' => 'income',
                'category' => $payment->category,
                'amount' => $payment->amount,
                'payment_method' => 'mobile_money', 
                'member_id' => $payment->member_id,
                'payment_id' => $payment->id,
                'reference_number' => $confirmationCode,
                'description' => $payment->description,
                'transaction_date' => now(),
                'recorded_by' => $payment->member->user_id ?? Auth::id() ?? 1,
            ]);

            // 3. If linked to a Pledge, record the pledge payment
            if ($payment->pledge_id) {
                $pledge = Pledge::findOrFail($payment->pledge_id);
                $pledge->recordPayment($transaction->id, $payment->amount, now());
            }

            // 4. If linked to a Kanda Contribution, record small group payment
            if ($payment->small_group_offering_id) {
                $offering = SmallGroupOffering::findOrFail($payment->small_group_offering_id);
                
                SmallGroupPayment::create([
                    'small_group_offering_id' => $offering->id,
                    'member_id' => $payment->member_id,
                    'amount' => $payment->amount,
                    'transaction_reference' => $confirmationCode,
                    'paid_at' => now(),
                    'payment_method' => 'mobile_money',
                    'recorded_by' => $payment->member->user_id ?? Auth::id() ?? 1,
                    'notes' => 'Online payment via Pesapal',
                ]);
            }
        });
    }
}
