@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold text-gray-800">{{ __('Visit Logs') }}</h1>
    </div>

    {{-- Record Visit Form --}}
    <details class="bg-white shadow rounded p-6 mb-6">
        <summary class="text-lg font-semibold cursor-pointer">+ {{ __('Record New Visit') }}</summary>
        <form action="{{ route('pastoral-care.visits.store') }}" method="POST" class="mt-4 space-y-4">
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
                    <label class="block font-medium text-gray-700 mb-1">{{ __('Visitor') }} *</label>
                    <select name="visitor_id" class="w-full border-gray-300 rounded" required>
                        @foreach(App\Models\User::orderBy('name')->get() as $user)
                            <option value="{{ $user->id }}" {{ $user->id == auth()->id() ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">{{ __('Visit Type') }} *</label>
                    <select name="visit_type" class="w-full border-gray-300 rounded" required>
                        <option value="home">{{ __('Home Visit') }}</option>
                        <option value="hospital">{{ __('Hospital Visit') }}</option>
                        <option value="office">{{ __('Office Visit') }}</option>
                        <option value="phone_call">{{ __('Phone Call') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">{{ __('Visit Date') }} *</label>
                    <input type="date" name="visit_date" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block font-medium text-gray-700 mb-1">{{ __('Purpose') }} *</label>
                    <textarea name="purpose" rows="2" class="w-full border-gray-300 rounded" required></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
                    <textarea name="notes" rows="3" class="w-full border-gray-300 rounded"></textarea>
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="follow_up_required" value="1" class="rounded">
                        <span class="ml-2">{{ __('Follow-up Required') }}</span>
                    </label>
                </div>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">{{ __('Record Visit') }}</button>
        </form>
    </details>

    @if(session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('status') }}</div>
    @endif

    {{-- Visits List --}}
    @if($visits->count() > 0)
    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">{{ __('Date') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('Member') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('Type') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('Purpose') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('Visitor') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('Follow-up') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($visits as $visit)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $visit->visit_date->format('M d, Y') }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('members.show', $visit->member) }}" class="text-blue-600 hover:underline">
                            {{ $visit->member->full_name }}
                        </a>
                    </td>
                    <td class="px-4 py-2">
                        <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-800">
                            {{ ucfirst(str_replace('_', ' ', $visit->visit_type)) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-sm">{{ Str::limit($visit->purpose, 50) }}</td>
                    <td class="px-4 py-2 text-sm">{{ $visit->visitor->name }}</td>
                    <td class="px-4 py-2">
                        @if($visit->follow_up_required)
                            <span class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-800">{{ __('Required') }}</span>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $visits->links() }}</div>
    @else
    <div class="bg-white shadow rounded p-8 text-center text-gray-500">{{ __('No visits recorded') }}</div>
    @endif
</div>
@endsection
