@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">{{ __('Follow-up Tasks') }}</h1>

    {{-- Create Task Form --}}
    <details class="bg-white shadow rounded p-6 mb-6">
        <summary class="text-lg font-semibold cursor-pointer">+ {{ __('Create Follow-up Task') }}</summary>
        <form action="{{ route('pastoral-care.follow-ups.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-gray-700 mb-1">{{ __('Member') }} *</label>
                    <select name="member_id" class="w-full border-gray-300 rounded" required>
                        <option value="">{{ __('Select Member') }}</option>
                        @foreach(App\Models\Member::orderBy('full_name')->get() as $member)
                            <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">{{ __('Assign To') }} *</label>
                    <select name="assigned_to" class="w-full border-gray-300 rounded" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $user->id == auth()->id() ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">{{ __('Title') }} *</label>
                    <input type="text" name="title" class="w-full border-gray-300 rounded" required>
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">{{ __('Due Date') }} *</label>
                    <input type="date" name="due_date" class="w-full border-gray-300 rounded" required>
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">{{ __('Priority') }} *</label>
                    <select name="priority" class="w-full border-gray-300 rounded" required>
                        <option value="low">{{ __('Low') }}</option>
                        <option value="medium" selected>{{ __('Medium') }}</option>
                        <option value="high">{{ __('High') }}</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block font-medium text-gray-700 mb-1">{{ __('Description') }} *</label>
                    <textarea name="description" rows="3" class="w-full border-gray-300 rounded" required></textarea>
                </div>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">{{ __('Create Task') }}</button>
        </form>
    </details>

    @if(session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('status') }}</div>
    @endif

    {{-- Filter --}}
    <form method="GET" class="bg-white shadow rounded p-4 mb-4">
        <select name="status" class="border-gray-300 rounded" onchange="this.form.submit()">
            <option value="">{{ __('All Status') }}</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
        </select>
    </form>

    {{-- Tasks List --}}
    @if($followUps->count() > 0)
    <div class="space-y-4">
        @foreach($followUps as $followUp)
        <div class="bg-white shadow rounded p-6">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="font-semibold text-lg">{{ $followUp->title }}</h3>
                    <p class="text-sm text-gray-600">{{ $followUp->member->full_name }} • {{ __('Assigned to') }}: {{ $followUp->assignedTo->name }}</p>
                </div>
                <div class="flex gap-2">
                    <span class="text-xs px-2 py-1 rounded 
                        @if($followUp->priority == 'high') bg-red-100 text-red-800
                        @elseif($followUp->priority == 'medium') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst(__($followUp->priority)) }}
                    </span>
                    <span class="text-xs px-2 py-1 rounded 
                        @if($followUp->status == 'completed') bg-green-100 text-green-800
                        @elseif($followUp->status == 'in_progress') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', __($followUp->status))) }}
                    </span>
                </div>
            </div>
            <p class="text-sm text-gray-700 mb-3">{{ $followUp->description }}</p>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">{{ __('Due') }}: {{ $followUp->due_date->format('M d, Y') }}</span>
                @if($followUp->status != 'completed')
                <form action="{{ route('pastoral-care.follow-ups.complete', $followUp) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                        {{ __('Mark Complete') }}
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $followUps->links() }}</div>
    @else
    <div class="bg-white shadow rounded p-8 text-center text-gray-500">{{ __('No follow-up tasks') }}</div>
    @endif
</div>
@endsection
