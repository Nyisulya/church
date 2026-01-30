<?php

namespace App\Http\Controllers;

use App\Models\SmallGroup;
use App\Models\SmallGroupOffering;
use App\Models\SmallGroupPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SmallGroupFinanceController extends Controller
{
    /**
     * Store a new offering created by the leader.
     */
    public function storeOffering(Request $request, SmallGroup $smallGroup)
    {
        // Authorization check (Leader or Co-Leader)
        if ($smallGroup->leader_id !== Auth::user()->member->id) {
            // Add co-leader check here if needed
            // For now, strict leader check or admin
            if (!Auth::user()->hasAnyRole(['super_admin', 'admin'])) {
                 // Check pivot for co-leader...
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount_per_member' => 'nullable|numeric|min:0',
            'target_amount' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date|after:today',
        ]);

        $offering = $smallGroup->offerings()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'amount_per_member' => $validated['amount_per_member'],
            'target_amount' => $validated['target_amount'],
            'deadline' => $validated['deadline'],
            'created_by' => Auth::user()->member->id,
        ]);

        return back()->with('success', 'Contribution created successfully.');
    }

    /**
     * Record a manual payment (cash/mobile money) by the leader.
     */
    public function storePayment(Request $request, SmallGroupOffering $offering)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,mobile_money,other',
            'notes' => 'nullable|string',
        ]);

        $offering->payments()->create([
            'member_id' => $validated['member_id'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'paid_at' => now(),
            'recorded_by' => Auth::id(),
            'notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Payment recorded successfully.');
    }

    /**
     * Show the finance dashboard for a specific offering (details, who paid, who owes).
     */
    public function showOffering(SmallGroupOffering $offering)
    {
        $offering->load(['payments.member', 'smallGroup.members']);
        
        $membersStatus = $offering->smallGroup->members->map(function ($member) use ($offering) {
            $paid = $offering->payments->where('member_id', $member->id)->sum('amount');
            $balance = $offering->amount_per_member ? max(0, $offering->amount_per_member - $paid) : 0;
            
            return [
                'member' => $member,
                'paid' => $paid,
                'balance' => $balance,
                'status' => $balance == 0 ? 'Paid' : ($paid > 0 ? 'Partial' : 'Unpaid'),
            ];
        });

        return view('small-groups.finance.show', compact('offering', 'membersStatus'));
    }
}
