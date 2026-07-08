@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">🙏 {{ __('Prayer Requests') }}</h1>

    {{-- Add Prayer Form --}}
    <details class="bg-white shadow rounded p-6 mb-6">
        <summary class="text-lg font-semibold cursor-pointer">+ {{ __('Add Prayer Request') }}</summary>
        <form action="{{ route('pastoral-care.prayers.store') }}" method="POST" class="mt-4 space-y-4">
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
                    <label class="block font-medium text-gray-700 mb-1">{{ __('Request Date') }} *</label>
                    <input type="date" name="request_date" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block font-medium text-gray-700 mb-1">{{ __('Request') }} *</label>
                    <textarea name="request" rows="4" class="w-full border-gray-300 rounded" required></textarea>
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_private" value="1" class="rounded">
                        <span class="ml-2">{{ __('Private (only visible to pastors)') }}</span>
                    </label>
                </div>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">{{ __('Add Request') }}</button>
        </form>
    </details>

    @if(session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('status') }}</div>
    @endif

    {{-- Prayers List --}}
    @if($prayers->count() > 0)
    <div class="space-y-4">
        @foreach($prayers as $prayer)
        <div class="bg-white shadow rounded p-6">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="font-semibold">{{ $prayer->member->full_name }}</h3>
                    <p class="text-sm text-gray-500">{{ $prayer->request_date->format('M d, Y') }}</p>
                </div>
                <div class="flex gap-2">
                    @if($prayer->is_private)
                        <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-800">{{ __('Private') }}</span>
                    @endif
                    <span class="text-xs px-2 py-1 rounded 
                        @if($prayer->status == 'answered') bg-green-100 text-green-800
                        @elseif($prayer->status == 'ongoing') bg-blue-100 text-blue-800
                        @else bg-purple-100 text-purple-800 @endif">
                        {{ ucfirst(__($prayer->status)) }}
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <p class="text-gray-700">{{ $prayer->request }}</p>
            </div>
            @if($prayer->answer)
            <div class="bg-green-50 p-3 rounded">
                <div class="text-xs font-medium text-green-800 mb-1">{{ __('Answer') }} ({{ $prayer->answered_at ? $prayer->answered_at->format('M d, Y') : '' }})</div>
                <p class="text-sm text-green-900">{{ $prayer->answer }}</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $prayers->links() }}</div>
    @else
    <div class="bg-white shadow rounded p-8 text-center text-gray-500">{{ __('No prayer requests') }}</div>
    @endif
</div>
@endsection
