@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">🎊 Celebrations Dashboard</h1>

    {{-- Today's Birthdays --}}
    @if($todaysBirthdays->count() > 0)
    <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow rounded p-6 mb-6">
        <h2 class="text-xl font-bold mb-3">🎉 Birthdays Today</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($todaysBirthdays as $member)
            <div class="bg-white/20 backdrop-blur rounded p-3">
                <div class="font-semibold">{{ $member->full_name }}</div>
                <div class="text-sm">{{ $member->age }} years old 🎂</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Upcoming Birthdays --}}
        <div class="bg-white shadow rounded p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">🎂 Upcoming Birthdays (Next 7 Days)</h3>
                <a href="{{ route('celebrations.birthdays') }}" class="text-blue-600 hover:underline text-sm">View All →</a>
            </div>
            @if($upcomingBirthdays->count() > 0)
            <div class="space-y-3">
                @foreach($upcomingBirthdays as $member)
                <div class="flex justify-between items-center pb-3 border-b">
                    <div>
                        <div class="font-medium">{{ $member->full_name }}</div>
                        <div class="text-sm text-gray-600">{{ $member->date_of_birth->format('M d') }} • {{ $member->age + 1 }} years</div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-800">
                        {{ $member->days_until_birthday }}d
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-sm">No upcoming birthdays</p>
            @endif
        </div>

        {{-- Upcoming Anniversaries --}}
        <div class="bg-white shadow rounded p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">💑 Upcoming Anniversaries (Next 7 Days)</h3>
                <a href="{{ route('celebrations.anniversaries') }}" class="text-blue-600 hover:underline text-sm">View All →</a>
            </div>
            @if($upcomingAnniversaries->count() > 0)
            <div class="space-y-3">
                @foreach($upcomingAnniversaries as $member)
                @php
                    $anniversary = $member->wedding_date->setYear(now()->year);
                    if ($anniversary->isPast()) $anniversary->addYear();
                    $daysUntil = now()->diffInDays($anniversary);
                @endphp
                <div class="flex justify-between items-center pb-3 border-b">
                    <div>
                        <div class="font-medium">{{ $member->full_name }}</div>
                        <div class="text-sm text-gray-600">{{ $member->wedding_date->format('M d') }} • {{ $member->years_married + 1 }} years</div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded bg-pink-100 text-pink-800">
                        {{ $daysUntil }}d
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-sm">No upcoming anniversaries</p>
            @endif
        </div>
    </div>
</div>
@endsection
