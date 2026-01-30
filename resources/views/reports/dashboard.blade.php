@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">📊 Reports Dashboard</h1>

    {{-- Quick Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow rounded p-6">
            <div class="text-sm mb-2 opacity-90">Total Members</div>
            <div class="text-3xl font-bold">{{ number_format($stats['total_members']) }}</div>
            <div class="text-xs mt-1 opacity-75">{{ number_format($stats['active_members']) }} active</div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white shadow rounded p-6">
            <div class="text-sm mb-2 opacity-90">Total Income</div>
            <div class="text-3xl font-bold">{{ number_format($stats['total_income'], 2) }}</div>
            <div class="text-xs mt-1 opacity-75">Net: {{ number_format($stats['total_income'] - $stats['total_expenses'], 2) }}</div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white shadow rounded p-6">
            <div class="text-sm mb-2 opacity-90">Total Events</div>
            <div class="text-3xl font-bold">{{ number_format($stats['total_events']) }}</div>
            <div class="text-xs mt-1 opacity-75">Avg attendance: {{ $stats['avg_attendance'] }}</div>
        </div>
        <div class="bg-gradient-to-br from-pink-500 to-pink-600 text-white shadow rounded p-6">
            <div class="text-sm mb-2 opacity-90">Pending Follow-ups</div>
            <div class="text-3xl font-bold">{{ $stats['pending_followups'] }}</div>
            <div class="text-xs mt-1 opacity-75">{{ $stats['active_prayers'] }} active prayers</div>
        </div>
    </div>

    {{-- Report Categories --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('reports.members') }}" class="bg-white shadow rounded p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 p-3 rounded-full mr-4">
                    <span class="text-2xl">👥</span>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">Member Reports</h3>
                    <p class="text-sm text-gray-600">Growth, demographics, departments</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-blue-600 hover:underline text-sm">View Report →</span>
            </div>
        </a>

        <a href="{{ route('reports.financial') }}" class="bg-white shadow rounded p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <span class="text-2xl">💰</span>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">Financial Reports</h3>
                    <p class="text-sm text-gray-600">Income, expenses, pledges</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-green-600 hover:underline text-sm">View Report →</span>
            </div>
        </a>

        <a href="{{ route('reports.attendance') }}" class="bg-white shadow rounded p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 p-3 rounded-full mr-4">
                    <span class="text-2xl">📈</span>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">Attendance Reports</h3>
                    <p class="text-sm text-gray-600">Trends, event participation</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-purple-600 hover:underline text-sm">View Report →</span>
            </div>
        </a>

        <a href="{{ route('reports.pastoral-care') }}" class="bg-white shadow rounded p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-pink-100 p-3 rounded-full mr-4">
                    <span class="text-2xl">🙏</span>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">Pastoral Care Reports</h3>
                    <p class="text-sm text-gray-600">Visits, follow-ups, prayers</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-pink-600 hover:underline text-sm">View Report →</span>
            </div>
        </a>
    </div>
</div>
@endsection
