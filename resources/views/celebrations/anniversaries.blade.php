@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">💑 Wedding Anniversaries</h1>

    {{-- Filter --}}
    <form method="GET" class="bg-white shadow rounded p-4 mb-4">
        <div class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Show anniversaries within</label>
                <select name="days" class="border-gray-300 rounded" onchange="this.form.submit()">
                    <option value="7" {{ $days == 7 ? 'selected' : '' }}>Next 7 days</option>
                    <option value="14" {{ $days == 14 ? 'selected' : '' }}>Next 14 days</option>
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>Next 30 days</option>
                    <option value="60" {{ $days == 60 ? 'selected' : '' }}>Next 60 days</option>
                </select>
            </div>
        </div>
    </form>

    @if($upcomingAnniversaries->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($upcomingAnniversaries as $member)
        @php
            $anniversary = $member->wedding_date->setYear(now()->year);
            if ($anniversary->isPast()) {
                $anniversary->addYear();
            }
            $daysUntil = now()->diffInDays($anniversary);
            $yearsMarried = $member->years_married + 1;
        @endphp
        <div class="bg-white shadow rounded p-6 {{ $daysUntil <= 7 ? 'border-l-4 border-pink-500' : '' }}">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h3 class="font-semibold text-lg">{{ $member->full_name }}</h3>
                    <p class="text-sm text-gray-600">{{ $member->phone }}</p>
                </div>
                <span class="text-2xl">💍</span>
            </div>
            <div class="mt-4 space-y-1">
                <div class="text-sm">
                    <span class="font-medium">Wedding Date:</span> 
                    {{ $member->wedding_date->format('M d, Y') }}
                </div>
                <div class="text-sm">
                    <span class="font-medium">Celebrating:</span> 
                    <span class="text-pink-600 font-bold">{{ $yearsMarried }} years</span>
                </div>
                <div class="text-sm">
                    <span class="font-medium">In:</span> 
                    <span class="px-2 py-1 text-xs rounded {{ $daysUntil <= 7 ? 'bg-pink-100 text-pink-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $daysUntil }} days
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white shadow rounded p-8 text-center text-gray-500">
        No upcoming anniversaries in the next {{ $days }} days.
    </div>
    @endif
</div>
@endsection
