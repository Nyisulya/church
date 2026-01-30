@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">🙏 {{ __('Pastoral Care Dashboard') }}</h1>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">{{ __('Visits This Month') }}</div>
            <div class="text-3xl font-bold text-blue-600">{{ $stats['total_visits'] }}</div>
        </div>
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">{{ __('Pending Follow-ups') }}</div>
            <div class="text-3xl font-bold text-yellow-600">{{ $stats['pending_followups'] }}</div>
        </div>
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">{{ __('Overdue Tasks') }}</div>
            <div class="text-3xl font-bold text-red-600">{{ $stats['overdue_followups'] }}</div>
        </div>
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">{{ __('Active Prayer Requests') }}</div>
            <div class="text-3xl font-bold text-purple-600">{{ $stats['active_prayers'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Recent Visits --}}
        <div class="bg-white shadow rounded p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">{{ __('Recent Visits') }}</h3>
                <a href="{{ route('pastoral-care.visits') }}" class="text-blue-600 hover:underline text-sm">{{ __('View All') }} →</a>
            </div>
            @if($recentVisits->count() > 0)
            <div class="space-y-3">
                @foreach($recentVisits as $visit)
                <div class="flex justify-between items-start pb-3 border-b">
                    <div>
                        <div class="font-medium">{{ $visit->member->full_name }}</div>
                        <div class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $visit->visit_type)) }} • {{ $visit->visit_date->format('M d') }}</div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-800">{{ $visit->visitor->name }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-sm">{{ __('No recent visits') }}</p>
            @endif
        </div>

        {{-- Pending Follow-ups --}}
        <div class="bg-white shadow rounded p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">{{ __('Pending Follow-ups') }}</h3>
                <a href="{{ route('pastoral-care.follow-ups') }}" class="text-blue-600 hover:underline text-sm">{{ __('View All') }} →</a>
            </div>
            @if($pendingFollowUps->count() > 0)
            <div class="space-y-3">
                @foreach($pendingFollowUps as $followUp)
                <div class="flex justify-between items-start pb-3 border-b">
                    <div>
                        <div class="font-medium">{{ $followUp->title }}</div>
                        <div class="text-sm text-gray-600">{{ $followUp->member->full_name }} • {{ __('Due') }}: {{ $followUp->due_date->format('M d') }}</div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded 
                        @if($followUp->priority == 'high') bg-red-100 text-red-800
                        @elseif($followUp->priority == 'medium') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst(__($followUp->priority)) }}
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-sm">{{ __('No pending follow-ups') }}</p>
            @endif
        </div>
    </div>

    {{-- Active Prayer Requests --}}
    <div class="bg-white shadow rounded p-6 mt-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">{{ __('Active Prayer Requests') }}</h3>
            <a href="{{ route('pastoral-care.prayers') }}" class="text-blue-600 hover:underline text-sm">{{ __('View All') }} →</a>
        </div>
        @if($activePrayerRequests->count() > 0)
        <div class="space-y-3">
            @foreach($activePrayerRequests as $prayer)
            <div class="pb-3 border-b">
                <div class="flex justify-between items-start mb-2">
                    <div class="font-medium">{{ $prayer->member->full_name }}</div>
                    <span class="text-xs text-gray-500">{{ $prayer->request_date->format('M d, Y') }}</span>
                </div>
                <div class="text-sm text-gray-700">{{ Str::limit($prayer->request, 100) }}</div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 text-sm">{{ __('No active prayer requests') }}</p>
        @endif
    </div>
</div>
@endsection
