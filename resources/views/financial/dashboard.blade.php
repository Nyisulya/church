@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold text-gray-800">{{ __('Financial Dashboard') }}</h1>
        <div class="space-x-2">
            @can('create', App\Models\Transaction::class)
            <a href="{{ route('financial.income.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                {{ __('Add Income') }}
            </a>
            <a href="{{ route('financial.expense.create') }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                {{ __('Add Expense') }}
            </a>
            @endcan
        </div>
    </div>

    {{-- Date Range Filter --}}
    <form method="GET" class="bg-white shadow rounded p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Start Date') }}</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border-gray-300 rounded">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('End Date') }}</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border-gray-300 rounded">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    {{ __('Filter') }}
                </button>
            </div>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">{{ __('Total Income') }}</div>
            <div class="text-3xl font-bold text-green-600">{{ number_format($totalIncome, 2) }}</div>
        </div>
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">{{ __('Total Expenses') }}</div>
            <div class="text-3xl font-bold text-red-600">{{ number_format($totalExpense, 2) }}</div>
        </div>
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">{{ __('Net Balance') }}</div>
            <div class="text-3xl font-bold {{ $netBalance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                {{ number_format($netBalance, 2) }}
            </div>
        </div>
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">{{ __('Active Pledges') }}</div>
            <div class="text-2xl font-bold text-purple-600">{{ $activePledges }}</div>
            <div class="text-xs text-gray-500 mt-1">
                {{ __('Paid') }}: {{ number_format($totalPledgePaid, 2) }} / {{ number_format($totalPledgeAmount, 2) }}
            </div>
        </div>
    </div>

    {{-- Income & Expense Breakdown --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold mb-4">{{ __('Income by Category') }}</h3>
            @if($incomeByCategory->count() > 0)
            <div class="space-y-2">
                @foreach($incomeByCategory as $item)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium">{{ $item->category }}</span>
                        <span class="text-sm text-green-600">{{ number_format($item->total, 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($item->total / $totalIncome) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500">{{ __('No income recorded for this period') }}</p>
            @endif
        </div>

        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold mb-4">{{ __('Expenses by Category') }}</h3>
            @if($expenseByCategory->count() > 0)
            <div class="space-y-2">
                @foreach($expenseByCategory as $item)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium">{{ $item->category }}</span>
                        <span class="text-sm text-red-600">{{ number_format($item->total, 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-600 h-2 rounded-full" style="width: {{ ($item->total / $totalExpense) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500">{{ __('No expenses recorded for this period') }}</p>
            @endif
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white shadow rounded p-6">
        <h3 class="text-lg font-semibold mb-4">{{ __('Recent Transactions') }}</h3>
        @if($recentTransactions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">{{ __('Date') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('Type') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('Category') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('Member') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $transaction)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded {{ $transaction->type == 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst(__($transaction->type)) }}
                            </span>
                        </td>
                        <td class="px-4 py-2">{{ $transaction->category }}</td>
                        <td class="px-4 py-2">{{ $transaction->member->full_name ?? '-' }}</td>
                        <td class="px-4 py-2 font-semibold {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($transaction->amount, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            <a href="{{ route('financial.transactions') }}" class="text-blue-600 hover:underline">{{ __('View all transactions') }} →</a>
        </div>
        @else
        <p class="text-gray-500">{{ __('No transactions recorded yet') }}</p>
        @endif
    </div>
</div>
@endsection
