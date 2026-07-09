<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Pledge;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    public function dashboard(Request $request): View
    {
        $this->authorize('viewAny', Transaction::class);

        // Check if user is a regular member (not admin/staff)
        $isRegularMember = auth()->user()->hasRole('member') && 
                          !auth()->user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer', 'department_leader']);

        // Get default date range (from last Saturday to today)
        $defaultStartDate = Carbon::now()->isSaturday() 
            ? Carbon::now()->subWeek()->toDateString() 
            : Carbon::now()->previous(Carbon::SATURDAY)->toDateString();
        $defaultEndDate = Carbon::now()->toDateString();

        $startDate = $request->get('start_date', $defaultStartDate);
        $endDate = $request->get('end_date', $defaultEndDate);

        // Base query - filter by member if regular member
        $baseQuery = function($query) use ($isRegularMember) {
            if ($isRegularMember && auth()->user()->member) {
                $query->where('member_id', auth()->user()->member->id);
            }
        };

        // Calculate totals
        $totalIncome = Transaction::income()
            ->byDateRange($startDate, $endDate)
            ->where($baseQuery)
            ->sum('amount');

        $totalExpense = Transaction::expense()
            ->byDateRange($startDate, $endDate)
            ->where($baseQuery)
            ->sum('amount');

        $netBalance = $totalIncome - $totalExpense;

        // Total pledges
        if ($isRegularMember && auth()->user()->member) {
            $activePledges = Pledge::active()
                ->where('member_id', auth()->user()->member->id)
                ->count();
            $totalPledgeAmount = Pledge::active()
                ->where('member_id', auth()->user()->member->id)
                ->sum('amount');
            $totalPledgePaid = Pledge::active()
                ->where('member_id', auth()->user()->member->id)
                ->sum('amount_paid');
        } else {
            $activePledges = Pledge::active()->count();
            $totalPledgeAmount = Pledge::active()->sum('amount');
            $totalPledgePaid = Pledge::active()->sum('amount_paid');
        }

        // Recent transactions
        $recentTransactionsQuery = Transaction::with(['member', 'recordedBy']);
        if ($isRegularMember && auth()->user()->member) {
            $recentTransactionsQuery->where('member_id', auth()->user()->member->id);
        }
        $recentTransactions = $recentTransactionsQuery
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Income by category
        $incomeByCategory = Transaction::income()
            ->byDateRange($startDate, $endDate)
            ->where($baseQuery)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        // Expense by category
        $expenseByCategory = Transaction::expense()
            ->byDateRange($startDate, $endDate)
            ->where($baseQuery)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        return view('financial.dashboard', compact(
            'totalIncome',
            'totalExpense',
            'netBalance',
            'activePledges',
            'totalPledgeAmount',
            'totalPledgePaid',
            'recentTransactions',
            'incomeByCategory',
            'expenseByCategory',
            'startDate',
            'endDate',
            'isRegularMember'
        ));
    }

    public function transactions(Request $request): View
    {
        $this->authorize('viewAny', Transaction::class);

        // Check if user is a regular member
        $isRegularMember = auth()->user()->hasRole('member') && 
                          !auth()->user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer', 'department_leader']);

        $query = Transaction::with(['member', 'recordedBy']);

        // For regular members, only show their own transactions
        if ($isRegularMember && auth()->user()->member) {
            $query->where('member_id', auth()->user()->member->id);
        }

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        if ($request->filled('member_id') && !$isRegularMember) {
            $query->where('member_id', $request->member_id);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $members = Member::orderBy('full_name')->get();

        return view('financial.transactions.index', compact('transactions', 'members', 'isRegularMember'));
    }

    /**
     * Show form to create income
     */
    public function createIncome(): View
    {
        $this->authorize('create', Transaction::class);
        $members = Member::orderBy('full_name')->get();
        $categories = \App\Models\GivingCategory::active()->income()->get();
        
        // Load active pledges grouped by member_id
        $activePledges = Pledge::active()->get()->groupBy('member_id');
        
        return view('financial.transactions.create-income', compact('members', 'categories', 'activePledges'));
    }

    /**
     * Store income transaction
     */
    public function storeIncome(Request $request): RedirectResponse
    {
        $this->authorize('create', Transaction::class);

        $validated = $request->validate([
            'payment_method' => 'required|string',
            'member_id' => 'nullable|exists:members,id',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            
            // Validate items array
            'items' => 'required|array|min:1',
            'items.*.category' => 'required|string|max:255',
            'items.*.amount' => 'required|numeric|min:0.01',
            'items.*.pledge_id' => 'nullable|exists:pledges,id',
        ]);

        foreach ($validated['items'] as $item) {
            $transaction = Transaction::create([
                'type' => 'income',
                'category' => $item['category'],
                'amount' => $item['amount'],
                'payment_method' => $validated['payment_method'],
                'member_id' => $validated['member_id'] ?? null,
                'transaction_date' => $validated['transaction_date'],
                'reference_number' => $validated['reference_number'] ?? null,
                'description' => $validated['description'] ?? null,
                'recorded_by' => auth()->id(),
            ]);

            // Link with pledge if provided
            if (!empty($item['pledge_id'])) {
                $pledge = Pledge::findOrFail($item['pledge_id']);
                $pledge->recordPayment($transaction->id, $transaction->amount, $transaction->transaction_date);
            }
        }

        return redirect()->route('financial.transactions')
            ->with('status', 'Income recorded successfully');
    }

    /**
     * Show form to create expense
     */
    public function createExpense(): View
    {
        $this->authorize('create', Transaction::class);
        $categories = \App\Models\GivingCategory::active()->expense()->get();
        return view('financial.transactions.create-expense', compact('categories'));
    }

    /**
     * Store expense transaction
     */
    public function storeExpense(Request $request): RedirectResponse
    {
        $this->authorize('create', Transaction::class);

        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['type'] = 'expense';
        $validated['recorded_by'] = auth()->id();

        Transaction::create($validated);

        return redirect()->route('financial.transactions')
            ->with('status', 'Expense recorded successfully');
    }

    public function pledges(Request $request): View
    {
        $this->authorize('viewAny', Pledge::class);

        // Check if user is a regular member
        $isRegularMember = auth()->user()->hasRole('member') && 
                          !auth()->user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer', 'department_leader']);

        $query = Pledge::with('member');

        // For regular members, only show their own pledges
        if ($isRegularMember && auth()->user()->member) {
            $query->where('member_id', auth()->user()->member->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('member_id') && !$isRegularMember) {
            $query->where('member_id', $request->member_id);
        }

        $pledges = $query->orderBy('created_at', 'desc')->paginate(15);
        $members = Member::orderBy('full_name')->get();

        return view('financial.pledges.index', compact('pledges', 'members', 'isRegularMember'));
    }

    /**
     * Store a new pledge
     */
    public function storePledge(Request $request): RedirectResponse
    {
        $this->authorize('create', Pledge::class);

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0.01',
            'purpose' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $validated['created_by'] = auth()->id();

        Pledge::create($validated);

        return redirect()->route('financial.pledges')
            ->with('status', 'Pledge created successfully');
    }

    /**
     * Record payment for a pledge
     */
    public function recordPledgePayment(Request $request, Pledge $pledge): RedirectResponse
    {
        $this->authorize('update', $pledge);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string',
        ]);

        // Create transaction
        $transaction = Transaction::create([
            'type' => 'income',
            'category' => 'Pledge Payment',
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'member_id' => $pledge->member_id,
            'transaction_date' => $validated['payment_date'],
            'reference_number' => $validated['reference_number'] ?? null,
            'description' => "Payment for pledge: {$pledge->purpose}",
            'recorded_by' => auth()->id(),
        ]);

        // Record payment
        $pledge->recordPayment($transaction->id, $validated['amount'], $validated['payment_date']);

        return redirect()->route('financial.pledges')
            ->with('status', 'Payment recorded successfully');
    }

    /**
     * Display reports
     */
    public function reports(Request $request): View
    {
        $this->authorize('viewAny', Transaction::class);

        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        // Monthly income vs expense
        $driver = DB::connection()->getDriverName();
        $dateExpr = $driver === 'pgsql' ? "to_char(transaction_date, 'YYYY-MM')" : ($driver === 'mysql' ? "DATE_FORMAT(transaction_date, '%Y-%m')" : "strftime('%Y-%m', transaction_date)");

        $monthlyData = Transaction::byDateRange($startDate, $endDate)
            ->select(
                DB::raw("$dateExpr as month"),
                'type',
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month', 'type')
            ->orderBy('month')
            ->get();

        return view('financial.reports.index', compact('monthlyData', 'startDate', 'endDate'));
    }
}
