@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold text-gray-800">{{ __('Events') }}</h1>
        @can('create', App\Models\Event::class)
        <a href="{{ route('events.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            {{ __('Create New Event') }}
        </a>
        @endcan
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white shadow rounded p-4">
            <div class="text-sm text-gray-600">{{ __('Total Events') }}</div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white shadow rounded p-4">
            <div class="text-sm text-gray-600">{{ __('Upcoming Events') }}</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['upcoming'] }}</div>
        </div>
        <div class="bg-white shadow rounded p-4">
            <div class="text-sm text-gray-600">{{ __('This Month') }}</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['this_month'] }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('events.index') }}" class="bg-white shadow rounded p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Type') }}</label>
                <select name="type" class="w-full border-gray-300 rounded">
                    <option value="">{{ __('All Types') }}</option>
                    <option value="service" {{ request('type') == 'service' ? 'selected' : '' }}>{{ __('Service') }}</option>
                    <option value="meeting" {{ request('type') == 'meeting' ? 'selected' : '' }}>{{ __('Meeting') }}</option>
                    <option value="event" {{ request('type') == 'event' ? 'selected' : '' }}>{{ __('Event') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                <select name="status" class="w-full border-gray-300 rounded">
                    <option value="">{{ __('All') }}</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>{{ __('Upcoming') }}</option>
                    <option value="past" {{ request('status') == 'past' ? 'selected' : '' }}>{{ __('Past') }}</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 mr-2">
                    {{ __('Filter') }}
                </button>
                <a href="{{ route('events.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                    {{ __('Clear') }}
                </a>
            </div>
        </div>
    </form>
</div>

@if(session('status'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('status') }}
    </div>
@endif

@if($events->count())
<div class="bg-white shadow rounded overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">{{ __('Event Name') }}</th>
                <th class="px-4 py-2 text-left">{{ __('Type') }}</th>
                <th class="px-4 py-2 text-left">{{ __('Date') }}</th>
                <th class="px-4 py-2 text-left">{{ __('Time') }}</th>
                <th class="px-4 py-2 text-left">{{ __('Attendees') }}</th>
                <th class="px-4 py-2 text-left">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-2 font-medium">{{ $event->name }}</td>
                <td class="px-4 py-2">
                    <span class="px-2 py-1 text-xs rounded 
                        @if($event->type == 'service') bg-blue-100 text-blue-800
                        @elseif($event->type == 'meeting') bg-green-100 text-green-800
                        @else bg-purple-100 text-purple-800
                        @endif">
                        {{ ucfirst(__($event->type)) }}
                    </span>
                </td>
                <td class="px-4 py-2">{{ $event->date->format('M d, Y') }}</td>
                <td class="px-4 py-2">
                    @if($event->start_time)
                        {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                        @if($event->end_time)
                            - {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}
                        @endif
                    @else
                        -
                    @endif
                </td>
                <td class="px-4 py-2">
                    <span class="font-semibold">{{ $event->attendances_count }}</span>
                </td>
                <td class="px-4 py-2 space-x-2">
                    <a href="{{ route('events.show', $event) }}" class="text-blue-600 hover:underline">{{ __('View') }}</a>
                    @can('update', $event)
                    <a href="{{ route('events.edit', $event) }}" class="text-indigo-600 hover:underline">{{ __('Edit') }}</a>
                    @endcan
                    @can('delete', $event)
                    <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Delete this event?') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">{{ __('Delete') }}</button>
                    </form>
                    @endcan
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $events->links() }}
</div>
@else
<div class="bg-white shadow rounded p-8 text-center">
    <p class="text-gray-600">{{ __('No events found.') }}</p>
    @can('create', App\Models\Event::class)
    <a href="{{ route('events.create') }}" class="text-blue-600 hover:underline mt-2 inline-block">{{ __('Create your first event') }}</a>
    @endcan
</div>
@endif
@endsection
