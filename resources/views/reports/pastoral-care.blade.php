@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <div class="text-center mb-3">
        <h4 class="font-weight-bold">{{ $churchName }}</h4>
    </div>
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">🙏 Pastoral Care Reports</h1>

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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        {{-- Visits --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-sm text-gray-600 mb-2">Total Visits</h3>
            <div class="text-3xl font-bold text-blue-600 mb-4">{{ $totalVisits }}</div>
            @if($visitsByType->count() > 0)
            <div class="space-y-2">
                @foreach($visitsByType as $item)
                <div class="flex justify-between text-sm">
                    <span>{{ ucfirst(str_replace('_', ' ', $item->visit_type)) }}</span>
                    <span class="font-semibold">{{ $item->count }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Follow-ups --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-sm text-gray-600 mb-2">Follow-up Tasks</h3>
            <div class="text-3xl font-bold text-green-600 mb-4">{{ $totalFollowUps }}</div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span>Completed</span>
                    <span class="font-semibold">{{ $completedFollowUps }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-green-600 h-3 rounded-full flex items-center justify-center text-xs text-white font-bold" style="width: {{ $completionRate }}%">
                        {{ $completionRate }}%
                    </div>
                </div>
            </div>
        </div>

        {{-- Prayers --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-sm text-gray-600 mb-2">Prayer Requests</h3>
            <div class="text-3xl font-bold text-purple-600 mb-4">{{ $totalPrayers }}</div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span>Answered</span>
                    <span class="font-semibold">{{ $answeredPrayers }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Active</span>
                    <span class="font-semibold">{{ $totalPrayers - $answeredPrayers }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
