@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">Pledges Management</h1>

    {{-- Filters --}}
    <form method="GET" class="bg-white shadow rounded p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border-gray-300 rounded">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
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
                <a href="{{ route('financial.pledges') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Clear</a>
            </div>
        </div>
    </form>

    @can('create', App\Models\Pledge::class)
    {{-- Create Pledge Form --}}
    <div class="bg-white shadow rounded p-6 mb-4">
        <h3 class="text-lg font-semibold mb-4">Create New Pledge</h3>
        <form action="{{ route('financial.pledges.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Member</label>
                    <select name="member_id" class="w-full border-gray-300 rounded" required>
                        <option value="">Select Member</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                    <input type="number" step="0.01" name="amount" class="w-full border-gray-300 rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purpose</label>
                    <input type="text" name="purpose" class="w-full border-gray-300 rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" class="w-full border-gray-300 rounded" required>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create Pledge</button>
            </div>
        </form>
    </div>
    @endcan
</div>

@if(session('status'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('status') }}
    </div>
@endif

@if($pledges->count() > 0)
<div class="space-y-4">
    @foreach($pledges as $pledge)
    <div class="bg-white shadow rounded p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-lg font-semibold">{{ $pledge->member->full_name }}</h3>
                <p class="text-gray-600">{{ $pledge->purpose }}</p>
                <p class="text-sm text-gray-500">Started: {{ $pledge->start_date->format('M d, Y') }}</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold {{ $pledge->status == 'completed' ? 'text-green-600' : 'text-blue-600' }}">
                    {{ number_format($pledge->amount_paid, 2) }} / {{ number_format($pledge->amount, 2) }}
                </div>
                <span class="text-sm px-2 py-1 rounded
                    @if($pledge->status == 'active') bg-blue-100 text-blue-800
                    @elseif($pledge->status == 'completed') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst($pledge->status) }}
                </span>
            </div>
        </div>
        
        {{-- Progress Bar --}}
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-1">
                <span>Progress</span>
                <span>{{ $pledge->completion_percentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $pledge->completion_percentage }}%"></div>
            </div>
        </div>

        @if($pledge->status == 'active')
        @can('update', $pledge)
        <details class="mt-4">
            <summary class="cursor-pointer text-blue-600 hover:underline">Record Payment</summary>
            <form action="{{ route('financial.pledges.payment', $pledge) }}" method="POST" class="mt-4 bg-gray-50 p-4 rounded">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                        <input type="number" step="0.01" name="amount" max="{{ $pledge->remaining_balance }}" class="w-full border-gray-300 rounded" required>
                        <span class="text-xs text-gray-500">Remaining: {{ number_format($pledge->remaining_balance, 2) }}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full border-gray-300 rounded" required>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Mobile Money">Mobile Money</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Card/POS">Card/POS</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded" required>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Record Payment</button>
                    </div>
                </div>
            </form>
        </details>
        @endcan
        @endif
    </div>
    @endforeach
</div>
<div class="mt-4">
    {{ $pledges->links() }}
</div>
@else
<div class="bg-white shadow rounded p-8 text-center">
    <p class="text-gray-600">No pledges found.</p>
</div>
@endif
@endsection
