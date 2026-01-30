@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">🎂 Upcoming Birthdays</h1>

    {{-- Filter --}}
    <form method="GET" class="bg-white shadow rounded p-4 mb-4">
        <div class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Show birthdays within</label>
                <select name="days" class="border-gray-300 rounded" onchange="this.form.submit()">
                    <option value="7" {{ $days == 7 ? 'selected' : '' }}>Next 7 days</option>
                    <option value="14" {{ $days == 14 ? 'selected' : '' }}>Next 14 days</option>
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>Next 30 days</option>
                    <option value="60" {{ $days == 60 ? 'selected' : '' }}>Next 60 days</option>
                </select>
            </div>
        </div>
    </form>

    {{-- Today's Birthdays --}}
    @if($todaysBirthdays->count() > 0)
    <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow rounded p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4">🎉 Birthdays Today!</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($todaysBirthdays as $member)
            <div class="bg-white/20 backdrop-blur rounded p-4">
                <div class="font-bold text-lg">{{ $member->full_name }}</div>
                <div class="text-sm">Turning {{ $member->age }} years old 🎂</div>
                <div class="text-xs mt-2">📞 {{ $member->phone }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Upcoming Birthdays --}}
    @if($upcomingBirthdays->count() > 0)
    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Birth Date</th>
                    <th class="px-4 py-2 text-left">Turning Age</th>
                    <th class="px-4 py-2 text-left">Days Until</th>
                    <th class="px-4 py-2 text-left">Contact</th>
                </tr>
            </thead>
            <tbody>
                @foreach($upcomingBirthdays as $member)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2 font-medium">
                        <a href="{{ route('members.show', $member) }}" class="text-blue-600 hover:underline">
                            {{ $member->full_name }}
                        </a>
                    </td>
                    <td class="px-4 py-2">{{ $member->date_of_birth->format('M d') }}</td>
                    <td class="px-4 py-2">{{ $member->age + 1 }} years</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs rounded {{ $member->days_until_birthday <= 7 ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $member->days_until_birthday }} days
                        </span>
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-600">{{ $member->phone }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="bg-white shadow rounded p-8 text-center text-gray-500">
        No upcoming birthdays in the next {{ $days }} days.
    </div>
    @endif
</div>
@endsection
