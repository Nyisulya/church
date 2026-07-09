<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class ContributionController extends Controller
{
    /**
     * Display a listing of contributions.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Contribution::with(['member', 'recorder']);

        // If user is not admin/leader, only show their own contributions
        if (!$user->hasAnyRole(['super_admin', 'admin', 'pastor', 'financial_officer'])) {
            if (!$user->member) {
                // If user has no member profile, show empty list
                $contributions = collect([]);
                return view('contributions.index', compact('contributions'));
            }
            $query->where('member_id', $user->member->id);
        } else {
            // Admin filters
            if ($request->filled('member_id')) {
                $query->where('member_id', $request->member_id);
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Clone query for totals before applying type filter
        $totalsQuery = clone $query;

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $contributions = $query->orderBy('date', 'desc')->paginate(15);
        
        // Calculate totals for summary cards using the totalsQuery (which is unfiltered by type)
        $totals = [
            'total' => $totalsQuery->sum('amount'),
            'zaka' => (clone $totalsQuery)->where('type', 'zaka')->sum('amount'),
            'sadaka' => (clone $totalsQuery)->where('type', 'sadaka')->sum('amount'),
            'project' => (clone $totalsQuery)->whereNotIn('type', ['zaka', 'sadaka'])->sum('amount'),
        ];

        return view('contributions.index', compact('contributions', 'totals'));
    }

    /**
     * Show the form for creating a new contribution.
     */
    public function create(): View
    {
        // Only authorized roles can add contributions
        $this->authorize('create', Contribution::class);
        
        $members = Member::orderBy('full_name')->get();
        return view('contributions.create', compact('members'));
    }

    /**
     * Store a newly created contribution in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Contribution::class);

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:zaka,sadaka,project,building,thanksgiving,other',
            'payment_method' => 'required|in:cash,mpesa,bank,check',
            'reference_number' => 'nullable|string|max:255',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['recorded_by'] = Auth::id();

        $contribution = null;
        DB::transaction(function () use ($validated, &$contribution) {
            // 1. Create Financial Transaction
            $transaction = \App\Models\Transaction::create([
                'type' => 'income',
                'category' => 'Contribution - ' . ucfirst($validated['type']),
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'member_id' => $validated['member_id'],
                'transaction_date' => $validated['date'],
                'reference_number' => $validated['reference_number'],
                'description' => $validated['notes'] ?? 'Contribution',
                'recorded_by' => Auth::id(),
            ]);

            // 2. Retrieve Contribution automatically created by Transaction observer
            $contribution = Contribution::where('transaction_id', $transaction->id)->first();
            if (!$contribution) {
                $validated['transaction_id'] = $transaction->id;
                $contribution = Contribution::create($validated);
            } else {
                // Ensure note and other fields are exactly aligned
                $contribution->update($validated);
            }
        });

        // Send automatic confirmation SMS if member has phone number
        if ($contribution && $contribution->member && $contribution->member->phone) {
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
            \App\Services\SmsService::send($member->phone, $message);
        }

        return redirect()->route('contributions.index')
            ->with('success', 'Contribution recorded successfully.');
    }

    /**
     * Display the specified contribution.
     */
    public function show(Contribution $contribution): View
    {
        $user = Auth::user();
        
        // Check authorization
        if (!$user->hasAnyRole(['super_admin', 'admin', 'pastor', 'financial_officer'])) {
            if (!$user->member || $user->member->id !== $contribution->member_id) {
                abort(403);
            }
        }

        return view('contributions.show', compact('contribution'));
    }

    /**
     * Download contribution receipt as PDF.
     */
    public function downloadReceipt(Contribution $contribution)
    {
        $user = Auth::user();
        
        // Check authorization
        if (!$user->hasAnyRole(['super_admin', 'admin', 'pastor', 'financial_officer'])) {
            if (!$user->member || $user->member->id !== $contribution->member_id) {
                abort(403);
            }
        }

        $pdf = Pdf::loadView('contributions.receipt_pdf', compact('contribution'));
        return $pdf->download("receipt_" . ($contribution->reference_number ?? $contribution->id) . ".pdf");
    }

    /**
     * Show the form for editing the specified contribution.
     */
    public function edit(Contribution $contribution): View
    {
        $this->authorize('update', $contribution);
        
        $members = Member::orderBy('full_name')->get();
        return view('contributions.edit', compact('contribution', 'members'));
    }

    /**
     * Update the specified contribution in storage.
     */
    public function update(Request $request, Contribution $contribution): RedirectResponse
    {
        $this->authorize('update', $contribution);

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:zaka,sadaka,project,building,thanksgiving,other',
            'payment_method' => 'required|in:cash,mpesa,bank,check',
            'reference_number' => 'nullable|string|max:255',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($contribution, $validated) {
            // 1. Update Contribution
            $contribution->update($validated);

            // 2. Update Linked Transaction
            if ($contribution->transaction) {
                $contribution->transaction->update([
                    'category' => 'Contribution - ' . ucfirst($validated['type']),
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method'],
                    'member_id' => $validated['member_id'],
                    'transaction_date' => $validated['date'],
                    'reference_number' => $validated['reference_number'],
                    'description' => $validated['notes'] ?? 'Contribution',
                ]);
            }
        });

        return redirect()->route('contributions.show', $contribution)
            ->with('success', 'Contribution updated successfully.');
    }

    /**
     * Remove the specified contribution from storage.
     */
    public function destroy(Contribution $contribution): RedirectResponse
    {
        $this->authorize('delete', $contribution);

        DB::transaction(function () use ($contribution) {
            // 1. Delete Linked Transaction first (if exists)
            if ($contribution->transaction) {
                $contribution->transaction->delete();
            }
            
            // 2. Delete Contribution
            $contribution->delete();
        });

        return redirect()->route('contributions.index')
            ->with('success', 'Contribution deleted successfully.');
    }
}
