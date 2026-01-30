@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow rounded p-6">
    <h1 class="text-2xl font-semibold mb-4">{{ __('Create New Event') }}</h1>
    
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('events.store') }}" method="POST">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block font-medium text-gray-700">{{ __('Event Name') }} <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded" required>
            </div>

            <div>
                <label class="block font-medium text-gray-700">{{ __('Event Type') }} <span class="text-red-500">*</span></label>
                <select name="type" class="mt-1 block w-full border-gray-300 rounded" required>
                    <option value="">{{ __('Select Type') }}</option>
                    <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>{{ __('Service') }}</option>
                    <option value="meeting" {{ old('type') == 'meeting' ? 'selected' : '' }}>{{ __('Meeting') }}</option>
                    <option value="event" {{ old('type') == 'event' ? 'selected' : '' }}>{{ __('Event') }}</option>
                </select>
            </div>

            <div>
                <label class="block font-medium text-gray-700">{{ __('Date') }} <span class="text-red-500">*</span></label>
                <input type="date" name="date" value="{{ old('date') }}" class="mt-1 block w-full border-gray-300 rounded" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-gray-700">{{ __('Start Time') }}</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}" class="mt-1 block w-full border-gray-300 rounded">
                </div>
                <div>
                    <label class="block font-medium text-gray-700">{{ __('End Time') }}</label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}" class="mt-1 block w-full border-gray-300 rounded">
                </div>
            </div>

            <div>
                <label class="block font-medium text-gray-700">{{ __('Location') }}</label>
                <input type="text" name="location" value="{{ old('location') }}" class="mt-1 block w-full border-gray-300 rounded" placeholder="e.g. Church Hall, Conference Room">
            </div>

            <div id="meeting-fields" style="display: none;">
                <div>
                    <label class="block font-medium text-gray-700">{{ __('Agenda') }}</label>
                    <textarea name="agenda" class="mt-1 block w-full border-gray-300 rounded" rows="4" placeholder="Meeting topics to discuss...">{{ old('agenda') }}</textarea>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_recurring" id="is_recurring" value="1" {{ old('is_recurring') ? 'checked' : '' }} class="mr-2">
                    <label for="is_recurring" class="font-medium text-gray-700">{{ __('Recurring Meeting') }}</label>
                </div>

                <div id="recurrence-pattern" style="display: none;">
                    <label class="block font-medium text-gray-700">{{ __('Recurrence Pattern') }}</label>
                    <select name="recurrence_pattern" class="mt-1 block w-full border-gray-300 rounded">
                        <option value="">{{ __('Select Pattern') }}</option>
                        <option value="weekly" {{ old('recurrence_pattern') == 'weekly' ? 'selected' : '' }}>{{ __('Weekly') }}</option>
                        <option value="biweekly" {{ old('recurrence_pattern') == 'biweekly' ? 'selected' : '' }}>{{ __('Bi-Weekly') }}</option>
                        <option value="monthly" {{ old('recurrence_pattern') == 'monthly' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                {{ __('Create Event') }}
            </button>
            <a href="{{ route('events.index') }}" class="ml-4 text-gray-600 hover:underline">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>

<script>
// Show/hide meeting-specific fields based on type
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.querySelector('select[name="type"]');
    const meetingFields = document.getElementById('meeting-fields');
    const recurringCheckbox = document.getElementById('is_recurring');
    const recurrencePattern = document.getElementById('recurrence-pattern');

    typeSelect.addEventListener('change', function() {
        if (this.value === 'meeting') {
            meetingFields.style.display = 'block';
        } else {
            meetingFields.style.display = 'none';
        }
    });

    recurringCheckbox.addEventListener('change', function() {
        recurrencePattern.style.display = this.checked ? 'block' : 'none';
    });

    // Trigger on page load
    if (typeSelect.value === 'meeting') {
        meetingFields.style.display = 'block';
    }
    if (recurringCheckbox.checked) {
        recurrencePattern.style.display = 'block';
    }
});
</script>
@endsection
