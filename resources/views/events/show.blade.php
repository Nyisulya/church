@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    @if(session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white shadow rounded p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-3xl font-semibold text-gray-800">{{ $event->name }}</h1>
                <div class="flex items-center space-x-4 mt-2 text-gray-600">
                    <span class="px-3 py-1 text-sm rounded 
                        @if($event->type == 'service') bg-blue-100 text-blue-800
                        @elseif($event->type == 'meeting') bg-green-100 text-green-800
                        @else bg-purple-100 text-purple-800
                        @endif">
                        {{ ucfirst($event->type) }}
                    </span>
                    <span>📅 {{ $event->date->format('l, F j, Y') }}</span>
                    @if($event->start_time)
                        <span>🕐 {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                        @if($event->end_time)
                            - {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}
                        @endif
                        </span>
                    @endif
                </div>
            </div>
            <div class="space-x-2">
                @can('update', $event)
                <a href="{{ route('events.edit', $event) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    {{ __('Edit Event') }}
                </a>
                @endcan
                
                @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'department_leader']))
                <a href="{{ route('attendance.scan') }}?event_id={{ $event->id }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    {{ __('QR Scanner') }}
                </a>
                @endif
            </div>
        </div>

        {{-- Statistics Cards - Admin Only --}}
        @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'department_leader']))
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-blue-50 rounded p-4">
                <div class="text-sm text-gray-600">{{ __('Total Attendees') }}</div>
                <div class="text-3xl font-bold text-blue-600">{{ $stats['total_attendees'] }}</div>
            </div>
            <div class="bg-green-50 rounded p-4">
                <div class="text-sm text-gray-600">{{ __('Present') }}</div>
                <div class="text-3xl font-bold text-green-600">{{ $stats['present'] }}</div>
            </div>
            <div class="bg-yellow-50 rounded p-4">
                <div class="text-sm text-gray-600">{{ __('Late') }}</div>
                <div class="text-3xl font-bold text-yellow-600">{{ $stats['late'] }}</div>
            </div>
            <div class="bg-purple-50 rounded p-4">
                <div class="text-sm text-gray-600">{{ __('Registered') }}</div>
                <div class="text-3xl font-bold text-purple-600">{{ $stats['registered'] ?? 0 }}</div>
            </div>
        </div>
        @endif

        {{-- Registration Button --}}
        @if($event->date->isFuture() || $event->date->isToday())
            <div class="mt-6 text-center">
                @if(isset($isRegistered) && $isRegistered)
                    <button class="bg-green-500 text-white px-6 py-3 rounded-lg font-semibold cursor-not-allowed opacity-75" disabled>
                        <i class="fas fa-check-circle mr-2"></i> {{ __('You are Registered') }}
                    </button>
                @else
                    <form action="{{ route('events.register', $event) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                            <i class="fas fa-user-plus mr-2"></i> {{ __('Register for Event') }}
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    {{-- Attendees List - Admin Only --}}
    @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'department_leader']))
    <div class="bg-white shadow rounded p-6">
        <h2 class="text-xl font-semibold mb-4">{{ __('Attendees') }}</h2>
        
        @if($event->attendances->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">{{ __('Member') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('Status') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('Scanned At') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($event->attendances as $attendance)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2">
                            <a href="{{ route('members.show', $attendance->member) }}" class="text-blue-600 hover:underline">
                                {{ $attendance->member->full_name }}
                            </a>
                        </td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded
                                @if($attendance->status == 'present') bg-green-100 text-green-800
                                @elseif($attendance->status == 'late') bg-yellow-100 text-yellow-800
                                @elseif($attendance->status == 'registered') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-gray-600">
                            {{ $attendance->scanned_at ? $attendance->scanned_at->format('g:i A') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <p>{{ __('No attendance records yet.') }}</p>
            <a href="{{ route('attendance.scan') }}?event_id={{ $event->id }}" class="text-blue-600 hover:underline mt-2 inline-block">
                {{ __('Start scanning QR codes') }}
            </a>
        </div>
        @endif
    </div>
    @endif

    {{-- Meeting-Specific Sections --}}
    @if($event->type === 'meeting')
    {{-- Agenda and Location --}}
    <div class="bg-white shadow rounded p-6 mt-6">
        <h2 class="text-xl font-semibold mb-4">📋 {{ __('Meeting Details') }}</h2>
        
        @if($event->location)
        <div class="mb-4">
            <span class="font-semibold">📍 Location:</span> {{ $event->location }}
            @if($event->is_recurring)
                <span class="ml-4 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                    🔄 Recurring ({{ ucfirst($event->recurrence_pattern) }})
                </span>
            @endif
        </div>
        @endif

        @if($event->agenda)
        <div>
            <h3 class="font-semibold mb-2">Agenda:</h3>
            <div class="bg-gray-50 p-4 rounded whitespace-pre-wrap">{{ $event->agenda }}</div>
        </div>
        @endif
    </div>

    {{-- Minutes --}}
    <div class="bg-white shadow rounded p-6 mt-6">
        <h2 class="text-xl font-semibold mb-4">📝 {{ __('Minutes') }}</h2>
        
        @can('update', $event)
            <form action="{{ route('events.update', $event) }}" method="POST">
                @csrf
                @method('PUT')
                <textarea name="minutes" class="w-full border-gray-300 rounded p-3" rows="8" placeholder="Record what was discussed in this meeting...">{{ $event->minutes }}</textarea>
                <button type="submit" class="mt-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    {{ __('Save Minutes') }}
                </button>
            </form>
        @else
            @if($event->minutes)
                <div class="bg-gray-50 p-4 rounded whitespace-pre-wrap">{{ $event->minutes }}</div>
            @else
                <p class="text-gray-500 italic">{{ __('No minutes recorded yet.') }}</p>
            @endif
        @endcan
    </div>

    {{-- Action Items--}}
    <div class="bg-white shadow rounded p-6 mt-6">
        <h2 class="text-xl font-semibold mb-4">✅ {{ __('Action Items') }}</h2>
        
        @if($event->actionItems->count() > 0)
        <div class="space-y-3">
            @foreach($event->actionItems as $item)
            <div class="border-l-4 {{ $item->status === 'completed' ? 'border-green-500 bg-green-50' : 'border-blue-500 bg-blue-50' }} p-4 rounded">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="font-medium {{ $item->status ===  'completed' ? 'line-through text-gray-500' : '' }}">
                            {{ $item->description }}
                        </p>
                        <div class="text-sm text-gray-600 mt-1">
                            @if($item->assignedTo)
                                <span>👤 Assigned to: {{ $item->assignedTo->full_name }}</span>
                            @endif
                            @if($item->due_date)
                                <span class="ml-4">📅 Due: {{ $item->due_date->format('M d, Y') }}</span>
                            @endif
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs rounded
                        {{ $item->status === 'completed' ? 'bg-green-200 text-green-800' : '' }}
                        {{ $item->status === 'in_progress' ? 'bg-yellow-200 text-yellow-800' : '' }}
                        {{ $item->status === 'pending' ? 'bg-gray-200 text-gray-800' : '' }}">
                        {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 italic">{{ __('No action items yet.') }}</p>
        @endif
    </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('events.index') }}" class="text-gray-600 hover:underline">← {{ __('Back to Events') }}</a>
    </div>
</div>
@endsection
