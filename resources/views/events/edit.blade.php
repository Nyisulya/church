@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow rounded p-6">
    <h1 class="text-2xl font-semibold mb-4">{{ __('Edit Event') }}</h1>
    
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('events.update', $event) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label class="block font-medium text-gray-700">{{ __('Event Name') }} <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $event->name) }}" class="mt-1 block w-full border-gray-300 rounded" required>
            </div>

            <div>
                <label class="block font-medium text-gray-700">{{ __('Event Type') }} <span class="text-red-500">*</span></label>
                <select name="type" class="mt-1 block w-full border-gray-300 rounded" required>
                    <option value="">{{ __('Select Type') }}</option>
                    <option value="service" {{ old('type', $event->type) == 'service' ? 'selected' : '' }}>{{ __('Service') }}</option>
                    <option value="meeting" {{ old('type', $event->type) == 'meeting' ? 'selected' : '' }}>{{ __('Meeting') }}</option>
                    <option value="event" {{ old('type', $event->type) == 'event' ? 'selected' : '' }}>{{ __('Event') }}</option>
                </select>
            </div>

            <div>
                <label class="block font-medium text-gray-700">{{ __('Date') }} <span class="text-red-500">*</span></label>
                <input type="date" name="date" value="{{ old('date', $event->date->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-gray-700">{{ __('Start Time') }}</label>
                    <input type="time" name="start_time" value="{{ old('start_time', $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i') : '') }}" class="mt-1 block w-full border-gray-300 rounded">
                </div>
                <div>
                    <label class="block font-medium text-gray-700">{{ __('End Time') }}</label>
                    <input type="time" name="end_time" value="{{ old('end_time', $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('H:i') : '') }}" class="mt-1 block w-full border-gray-300 rounded">
                </div>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                {{ __('Update Event') }}
            </button>
            <a href="{{ route('events.show', $event) }}" class="ml-4 text-gray-600 hover:underline">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>
@endsection
