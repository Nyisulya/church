<?php

namespace App\Http\Controllers;

use App\Models\Pledge;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PledgeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Pledge::with(['member', 'payments']);

        // Members see only their own pledges
        if (!$user->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer'])) {
            if (!$user->member) {
                $pledges = collect([]);
                $projects = collect([]);
                $ministryPledges = collect([]);
                return view('pledges.index', compact('pledges', 'projects', 'ministryPledges'));
            }
            $query->where('member_id', $user->member->id);
        }

        // Admin filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pledges = $query->latest()->paginate(15);

        // Summary statistics
        $totalPledged = $query->sum('amount');
        $totalPaid = $query->sum('amount_paid');
        $completionRate = $totalPledged > 0 ? round(($totalPaid / $totalPledged) * 100, 2) : 0;

        // Load Projects
        if ($user->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer'])) {
            $projects = \App\Models\Project::latest()->get();
        } else {
            $projects = \App\Models\Project::where('status', 'active')
                ->where(function($q) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
                })
                ->latest()
                ->get();
        }

        // Load Ministry Pledges
        if ($user->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer'])) {
            $ministryPledges = \App\Models\MinistryPledge::with(['department', 'creator', 'contributions'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $departmentIds = $user->member->departments->pluck('id');
            $ministryPledges = \App\Models\MinistryPledge::whereIn('department_id', $departmentIds)
                ->with(['department', 'creator', 'contributions'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('pledges.index', compact(
            'pledges', 
            'totalPledged', 
            'totalPaid', 
            'completionRate',
            'projects',
            'ministryPledges'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        
        // For regular members, auto-fill their member_id
        if (!$user->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer'])) {
            if (!$user->member) {
                return redirect()->route('pledges.index')
                    ->with('error', 'Please complete your profile first to make a pledge.');
            }
            
            // Member view - they can only create pledges for themselves
            return view('pledges.create-member', [
                'member' => $user->member,
                'projects' => $this->getAvailableProjects()
            ]);
        }
        
        // Admin view - they can create pledges for any member
        $members = Member::where('status', 'active')->orderBy('full_name')->get();
        return view('pledges.create', [
            'members' => $members,
            'projects' => $this->getAvailableProjects()
        ]);
    }

    private function getAvailableProjects()
    {
        // Fetch active projects from database
        return \App\Models\Project::where('status', 'active')
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // For regular members, force their own member_id
        if (!$user->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer'])) {
            if (!$user->member) {
                return redirect()->route('pledges.index')
                    ->with('error', 'Please complete your profile first.');
            }
            $request->merge(['member_id' => $user->member->id]);
        }

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:1',
            'purpose' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $validated['status'] = 'active';
        $validated['amount_paid'] = 0;
        $validated['created_by'] = Auth::id();

        Pledge::create($validated);

        return redirect()->route('pledges.index')
            ->with('success', 'Pledge created successfully!');
    }

    public function show(Pledge $pledge)
    {
        $user = Auth::user();

        // Check authorization
        if (!$user->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer'])) {
            if (!$user->member || $user->member->id !== $pledge->member_id) {
                abort(403);
            }
        }

        $pledge->load(['member', 'payments.transaction']);

        return view('pledges.show', compact('pledge'));
    }

    public function makePayment(Request $request, Pledge $pledge)
    {
        // Only authorized financial roles can record manual/offline payments
        if (!auth()->user()->hasAnyRole(['super_admin', 'admin', 'treasurer'])) {
            abort(403, 'Unauthorized action. Manual offline payments can only be recorded by church administrators.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $pledge->remaining_balance,
            'payment_method' => 'required|in:cash,mpesa,bank,check',
            'reference_number' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($pledge, $validated) {
            // 1. Create Financial Transaction
            $transaction = \App\Models\Transaction::create([
                'type' => 'income',
                'category' => 'Pledge Payment - ' . $pledge->purpose,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'member_id' => $pledge->member_id,
                'transaction_date' => $validated['payment_date'],
                'reference_number' => $validated['reference_number'] ?? null,
                'description' => $validated['notes'] ?? 'Pledge payment',
                'recorded_by' => Auth::id(),
            ]);

            // 2. Record Pledge Payment
            $pledge->recordPayment($transaction->id, $validated['amount'], $validated['payment_date']);
        });

        // Send Pledge Payment Confirmation SMS
        $pledge->refresh();
        $pledge->loadMissing('member');
        if ($pledge->member && $pledge->member->phone) {
            $member = $pledge->member;
            $remainingBalance = max(0, $pledge->amount - $pledge->amount_paid);
            try {
                $dateStr = \Carbon\Carbon::parse($validated['payment_date'])->format('d/m/Y');
            } catch (\Exception $e) {
                $dateStr = date('d/m/Y');
            }
            $message = "Bwana asifiwe " . $member->full_name . "! Tumepokea Shs " . number_format($validated['amount']) . " kwa ajili ya ahadi yako ya \"" . $pledge->purpose . "\". Salio lililobaki ni Shs " . number_format($remainingBalance) . ". Mungu akubariki!";
            
            try {
                \App\Services\SmsService::send($member->phone, $message);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("makePayment SMS Error: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Payment recorded successfully!');
    }
}
