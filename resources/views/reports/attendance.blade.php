@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <div class="text-center mb-3">
        <h4 class="font-weight-bold">{{ $churchName }}</h4>
    </div>
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">📊 Attendance Reports</h1>

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

    {{-- Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">Average Attendance</div>
            <div class="text-3xl font-bold text-blue-600">{{ number_format($avgAttendance, 0) }}</div>
        </div>
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">Total Events</div>
            <div class="text-3xl font-bold text-purple-600">{{ $attendanceByEvent->count() }}</div>
        </div>
    </div>

    {{-- Attendance by Event --}}
    <div class="bg-white shadow rounded overflow-hidden">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Attendance by Event</h3>
        </div>
        @if($attendanceByEvent->count() > 0)
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Event</th>
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Attendance</th>
                    <th class="px-4 py-2 text-left">Visual</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceByEvent as $item)
                <tr class="border-t">
                    <td class="px-4 py-2 font-medium">{{ $item['event'] }}</td>
                    <td class="px-4 py-2 text-sm text-gray-600">{{ $item['date'] }}</td>
                    <td class="px-4 py-2 font-semibold text-blue-600">{{ $item['count'] }}</td>
                    <td class="px-4 py-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $avgAttendance > 0 ? ($item['count'] / $avgAttendance) * 50 : 0 }}%"></div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-8 text-center text-gray-500">No attendance data for selected period</div>
        @endif
    </div>
</div>
@endsection
