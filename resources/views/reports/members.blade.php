@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <div class="text-center mb-3">
        <h4 class="font-weight-bold">{{ $churchName }}</h4>
    </div>
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">👥 Member Reports</h1>

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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Gender Distribution --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold mb-4">Gender Distribution</h3>
            @if($genderStats->count() > 0)
            @foreach($genderStats as $item)
            <div class="mb-3">
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium">{{ ucfirst($item->gender ?? 'Not specified') }}</span>
                    <span class="text-sm font-bold text-blue-600">{{ $item->count }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($item->count / $genderStats->sum('count')) * 100 }}%"></div>
                </div>
            </div>
            @endforeach
            @else
            <p class="text-gray-500">No data available</p>
            @endif
        </div>

        {{-- Marital Status Distribution --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold mb-4">Marital Status</h3>
            @if($maritalStats->count() > 0)
            @foreach($maritalStats as $item)
            <div class="mb-3">
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium">{{ ucfirst($item->marital_status ?? 'Not specified') }}</span>
                    <span class="text-sm font-bold text-green-600">{{ $item->count }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($item->count / $maritalStats->sum('count')) * 100 }}%"></div>
                </div>
            </div>
            @endforeach
            @else
            <p class="text-gray-500">No data available</p>
            @endif
        </div>
    </div>

    {{-- Department Distribution --}}
    <div class="bg-white shadow rounded p-6 mt-6">
        <h3 class="text-lg font-semibold mb-4">Department Distribution</h3>
        @if($departmentStats->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($departmentStats as $item)
            <div class="flex justify-between items-center pb-2 border-b">
                <span class="font-medium">{{ $item->name }}</span>
                <span class="text-blue-600 font-bold">{{ $item->count }} members</span>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500">No data available</p>
        @endif
    </div>
</div>
@endsection
