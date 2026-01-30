@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <div class="text-center mb-3">
        <h4 class="font-weight-bold">{{ $churchName }}</h4>
    </div>
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">📊 Financial Reports</h1>

    {{-- Date Filter --}}
    <form method="GET" class="bg-white shadow rounded p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border-gray-300 rounded">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border-gray-300 rounded">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Generate</button>
            </div>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">Total Income</div>
            <div class="text-3xl font-bold text-green-600">{{ number_format($totalIncome, 2) }}</div>
        </div>
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">Total Expenses</div>
            <div class="text-3xl font-bold text-red-600">{{ number_format($totalExpense, 2) }}</div>
        </div>
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">Net Balance</div>
            <div class="text-3xl font-bold {{ $netBalance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                {{ number_format($netBalance, 2) }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Income by Category --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold mb-4">Income by Category</h3>
            @if($incomeByCategory->count() > 0)
            <div class="space-y-3">
                @foreach($incomeByCategory as $item)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium">{{ $item->category }}</span>
                        <span class="text-sm text-green-600 font-bold">{{ number_format($item->total, 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($item->total / $totalIncome) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500">No income data</p>
            @endif
        </div>

        {{-- Expense by Category --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold mb-4">Expenses by Category</h3>
            @if($expenseByCategory->count() > 0)
            <div class="space-y-3">
                @foreach($expenseByCategory as $item)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium">{{ $item->category }}</span>
                        <span class="text-sm text-red-600 font-bold">{{ number_format($item->total, 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-600 h-2 rounded-full" style="width: {{ ($item->total / $totalExpense) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500">No expense data</p>
            @endif
        </div>
    </div>

    {{-- Pledge Statistics --}}
    <div class="bg-white shadow rounded p-6">
        <h3 class="text-lg font-semibold mb-4">Pledge Statistics</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <div class="text-sm text-gray-600">Total Pledged</div>
                <div class="text-2xl font-bold text-blue-600">{{ number_format($totalPledges, 2) }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600">Total Paid</div>
                <div class="text-2xl font-bold text-green-600">{{ number_format($totalPaid, 2) }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-2">Completion Rate</div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-blue-600 h-4 rounded-full flex items-center justify-center text-xs text-white font-bold" style="width: {{ $pledgeCompletion }}%">
                        {{ $pledgeCompletion }}%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
