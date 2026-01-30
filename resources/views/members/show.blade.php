@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Header with Actions --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">{{ $member->full_name }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Member since') }} {{ $member->created_at->format('M d, Y') }}</p>
        </div>
        <div class="flex space-x-2">
            @can('update', $member)
            <a href="{{ route('members.edit', $member) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                {{ __('Edit') }}
            </a>
            @endcan
            @can('delete', $member)
            <form action="{{ route('members.destroy', $member) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this member? This action cannot be undone.') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{ __('Delete') }}
                </button>
            </form>
            @endcan
            <a href="{{ route('members.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Personal Information --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ __('Personal Information') }}
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('Full Name') }}</label>
                        <p class="text-gray-900">{{ $member->full_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('Gender') }}</label>
                        <p class="text-gray-900">{{ ucfirst($member->gender ? __($member->gender) : __('Not specified')) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('Date of Birth') }}</label>
                        <p class="text-gray-900">{{ $member->date_of_birth ? $member->date_of_birth->format('M d, Y') : __('Not specified') }}</p>
                        @if($member->date_of_birth)
                        <p class="text-xs text-gray-500">{{ __('Age') }}: {{ $member->age }} {{ __('years') }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('Marital Status') }}</label>
                        <p class="text-gray-900">{{ ucfirst($member->marital_status ? __($member->marital_status) : __('Not specified')) }}</p>
                    </div>
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-500">{{ __('Address') }}</label>
                        <p class="text-gray-900">{{ $member->address ?? __('Not provided') }}</p>
                    </div>
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ __('Contact Information') }}
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('Phone Number') }}</label>
                        <p class="text-gray-900">{{ $member->phone ?? __('Not provided') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('Email Address') }}</label>
                        <p class="text-gray-900">{{ $member->email ?? __('Not provided') }}</p>
                    </div>
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-500">{{ __('Emergency Contact') }}</label>
                        <p class="text-gray-900">{{ $member->emergency_contact_name ?? __('Not provided') }}</p>
                        @if($member->emergency_contact_phone)
                        <p class="text-sm text-gray-600">{{ $member->emergency_contact_phone }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Spiritual Information --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    {{ __('Spiritual Journey') }}
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('Salvation Date') }}</label>
                        <p class="text-gray-900">{{ $member->salvation_date ? $member->salvation_date->format('M d, Y') : __('Not specified') }}</p>
                        @if($member->salvation_date && $member->years_since_salvation)
                        <p class="text-xs text-gray-500">{{ $member->years_since_salvation }} {{ __('years in faith') }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('Baptism Date') }}</label>
                        <p class="text-gray-900">{{ $member->baptism_date ? $member->baptism_date->format('M d, Y') : __('Not specified') }}</p>
                    </div>
                    @if($member->wedding_date)
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('Wedding Anniversary') }}</label>
                        <p class="text-gray-900">{{ $member->wedding_date->format('M d, Y') }}</p>
                        @if($member->years_married)
                        <p class="text-xs text-gray-500">{{ $member->years_married }} {{ __('years married') }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            {{-- Departments --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    {{ __('Departments & Ministries') }}
                </h2>
                @if($member->departments->count())
                <div class="grid grid-cols-1 gap-3">
                    @foreach($member->departments as $dept)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <div>
                            <p class="font-medium">{{ $dept->name }}</p>
                            <p class="text-sm text-gray-600">{{ $dept->description }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs rounded bg-blue-100 text-blue-800">{{ ucfirst($dept->pivot->role ?? 'Member') }}</span>
                    </div>
                    @endforeach
                </div>
               @else
                <p class="text-gray-500">{{ __('Not assigned to any department') }}</p>
                @endif
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-6">
            {{-- Status Card --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">{{ __('Member Status') }}</h2>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-gray-600">{{ __('Current Status') }}</span>
                    @if($member->status == 'active')
                    <span class="px-3 py-1 text-sm rounded bg-green-100 text-green-800 font-medium">{{ __('Active') }}</span>
                    @else
                    <span class="px-3 py-1 text-sm rounded bg-gray-100 text-gray-800 font-medium">{{ __('Inactive') }}</span>
                    @endif
                </div>
                <div class="text-sm text-gray-600">
                    <p class="mb-1"><strong>{{ __('Member Number') }}:</strong> {{ $member->member_number ?? __('N/A') }}</p>
                    <p><strong>{{ __('Registration Type') }}:</strong> {{ ucfirst($member->registration_type ?? __('N/A')) }}</p>
                </div>
            </div>

            {{-- QR Code Card --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    {{ __('Attendance QR Code') }}
                </h2>
                <div class="bg-gray-50 p-4 rounded text-center">
                    {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($member->qr_code_content) !!}
                </div>
                <p class="text-xs text-gray-500 text-center mt-2">{{ __('Scan this code for quick attendance check-in') }}</p>
                <button onclick="window.print()" class="w-full mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    {{ __('Print QR Code') }}
                </button>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">{{ __('Quick Actions') }}</h2>
                <div class="space-y-2">
                    <a href="{{ route('members.id-card', $member) }}" class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                        <i class="fas fa-id-card mr-2"></i> {{ __('Download ID Card') }}
                    </a>
                    @if($member->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $member->phone) }}" target="_blank" class="block w-full text-center bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 text-sm">
                        <i class="fab fa-whatsapp mr-2"></i> {{ __('Chat on WhatsApp') }}
                    </a>
                    @endif
                    <a href="{{ route('pastoral-care.visits') }}?member_id={{ $member->id }}" class="block w-full text-center bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 text-sm">
                        {{ __('Log Visit') }}
                    </a>
                    <a href="{{ route('pastoral-care.follow-ups') }}?member_id={{ $member->id }}" class="block w-full text-center bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                        {{ __('Create Follow-up') }}
                    </a>
                    <a href="{{ route('pastoral-care.prayers') }}?member_id={{ $member->id }}" class="block w-full text-center bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700 text-sm">
                        {{ __('Add Prayer Request') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .bg-white.shadow.rounded-lg.p-6:has(.bg-gray-50),
    .bg-white.shadow.rounded-lg.p-6:has(.bg-gray-50) * {
        visibility: visible;
    }
    .bg-white.shadow.rounded-lg.p-6:has(.bg-gray-50) {
        position: absolute;
        left: 0;
        top: 0;
    }
}
</style>
@endsection
