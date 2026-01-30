@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold text-gray-800">Transaction History</h1>
        <div class="space-x-2">
            @can('create', App\Models\Transaction::class)
            <a href="{{ route('financial.income.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                + Add Income
            </a>
            <a href="{{ route('financial.expense.create') }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                + Add Expense
            </a>
            @endcan
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white shadow rounded p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full border-gray-300 rounded">
                    <option value="">All</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border-gray-300 rounded">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border-gray-300 rounded">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Member</label>
                <select name="member_id" class="w-full border-gray-300 rounded">
                    <option value="">All Members</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}" {{ request('member_id') == $member->id ? 'selected' : '' }}>
                            {{ $member->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 mr-2">Filter</button>
                <a href="{{ route('financial.transactions') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Clear</a>
            </div>
        </div>
    </form>
</div>

@if(session('status'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('status') }}
    </div>
@endif

@if($transactions->count() > 0)
<div class="bg-white shadow rounded overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Date</th>
                <th class="px-4 py-2 text-left">Type</th>
                <th class="px-4 py-2 text-left">Category</th>
                <th class="px-4 py-2 text-left">Member</th>
                <th class="px-4 py-2 text-left">Payment Method</th>
                <th class="px-4 py-2 text-left">Amount</th>
                <th class="px-4 py-2 text-left">Recorded By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-2">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                <td class="px-4 py-2">
                    <span class="px-2 py-1 text-xs rounded {{ $transaction->type == 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($transaction->type) }}
                    </span>
                </td>
                <td class="px-4 py-2">{{ $transaction->category }}</td>
                <td class="px-4 py-2">
                    @if($transaction->member)
                        <a href="{{ route('members.show', $transaction->member) }}" class="text-blue-600 hover:underline">
                            {{ $transaction->member->full_name }}
                        </a>
                    @else
                        -
                    @endif
                </td>
                <td class="px-4 py-2">{{ $transaction->payment_method }}</td>
                <td class="px-4 py-2 font-semibold {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format($transaction->amount, 2) }}
                </td>
                <td class="px-4 py-2 text-sm text-gray-600">{{ $transaction->recordedBy->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $transactions->links() }}
</div>
@else
<div class="bg-white shadow rounded p-8 text-center">
    <p class="text-gray-600">No transactions found.</p>
</div>
@endif
@endsection
