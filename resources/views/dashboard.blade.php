@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">📊 {{ __('Church Dashboard') }}</h1>
    <p class="text-gray-600">{{ __('Welcome') }} {{ __('Back') }}, {{ auth()->user()->name }}! {{ __('Here\'s your church overview.') }}</p>
</div>


{{-- Quick Stats Overview --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Total Members --}}
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium mb-1">{{ __('Total Members') }}</p>
                <h3 class="text-4xl font-bold">{{ App\Models\Member::count() }}</h3>
                <p class="text-blue-100 text-xs mt-2">
                    <span class="font-semibold">{{ App\Models\Member::where('status', 'active')->count() }}</span> {{ __('active') }}
                </p>
            </div>
            <div class="bg-blue-400 bg-opacity-30 p-4 rounded-full">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Upcoming Events --}}
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white shadow-lg rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium mb-1">{{ __('Upcoming Events') }}</p>
                <h3 class="text-4xl font-bold">{{ App\Models\Event::where('date', '>=', now())->count() }}</h3>
                <p class="text-purple-100 text-xs mt-2">
                    {{ __('Next') }}: <span class="font-semibold">{{ App\Models\Event::where('date', '>=', now())->orderBy('date')->first()?->date->format('M d') ?? __('None') }}</span>
                </p>
            </div>
            <div class="bg-purple-400 bg-opacity-30 p-4 rounded-full">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- This Month Income --}}
    <div class="bg-gradient-to-br from-green-500 to-green-600 text-white shadow-lg rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium mb-1">{{ __('This Month Income') }}</p>
                <h3 class="text-4xl font-bold">{{ number_format(App\Models\Transaction::income()->whereMonth('transaction_date', now()->month)->sum('amount'), 0) }}</h3>
                <p class="text-green-100 text-xs mt-2">
                    {{ __('Expenses') }}: <span class="font-semibold">{{ number_format(App\Models\Transaction::expense()->whereMonth('transaction_date', now()->month)->sum('amount'), 0) }}</span>
                </p>
            </div>
            <div class="bg-green-400 bg-opacity-30 p-4 rounded-full">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Pending Follow-ups --}}
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white shadow-lg rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm font-medium mb-1">{{ __('Pending Tasks') }}</p>
                <h3 class="text-4xl font-bold">{{ App\Models\FollowUp::where('status', 'pending')->count() }}</h3>
                <p class="text-orange-100 text-xs mt-2">
                    {{ __('Overdue') }}: <span class="font-semibold">{{ App\Models\FollowUp::where('status', 'pending')->where('due_date', '<', now())->count() }}</span>
                </p>
            </div>
            <div class="bg-orange-400 bg-opacity-30 p-4 rounded-full">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Charts Section --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Member Growth Chart --}}
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('Member Growth Trend') }}</h2>
        <div style="height: 280px;">
            <canvas id="memberGrowthChart"></canvas>
        </div>
    </div>

    {{-- Financial Overview Chart --}}
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('Income vs Expenses') }}</h2>
        <div style="height: 280px;">
            <canvas id="financialChart"></canvas>
        </div>
    </div>
</div>

{{-- Demographic Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Gender Distribution --}}
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('Gender Distribution') }}</h2>
        <div style="height: 250px;">
            <canvas id="genderChart"></canvas>
        </div>
    </div>

    {{-- Age Groups --}}
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('Age Groups') }}</h2>
        <div style="height: 250px;">
            <canvas id="ageChart"></canvas>
        </div>
    </div>

    {{-- Marital Status --}}
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('Marital Status') }}</h2>
        <div style="height: 250px;">
            <canvas id="maritalChart"></canvas>
        </div>
    </div>
</div>

{{-- Quick Access Links --}}
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
    <a href="{{ route('members.index') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition text-center">
        <div class="text-blue-600 mb-2">
            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-gray-700">{{ __('Members') }}</p>
    </a>

    <a href="{{ route('attendance.scanner') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition text-center">
        <div class="text-green-600 mb-2">
            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-gray-700">{{ __('Scan Attendance') }}</p>
    </a>

    <a href="{{ route('events.index') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition text-center">
        <div class="text-purple-600 mb-2">
            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-gray-700">{{ __('Events') }}</p>
    </a>

    <a href="{{ route('financial.dashboard') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition text-center">
        <div class="text-green-600 mb-2">
            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-gray-700">{{ __('Financial') }}</p>
    </a>

    <a href="{{ route('pastoral-care.dashboard') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition text-center">
        <div class="text-pink-600 mb-2">
            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-gray-700">{{ __('Pastoral Care') }}</p>
    </a>

    <a href="{{ route('reports.dashboard') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition text-center">
        <div class="text-orange-600 mb-2">
            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-gray-700">{{ __('Reports') }}</p>
    </a>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Member Growth Chart
    const memberCtx = document.getElementById('memberGrowthChart').getContext('2d');
    new Chart(memberCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($memberGrowth->pluck('month')) !!},
            datasets: [{
                label: 'New Members',
                data: {!! json_encode($memberGrowth->pluck('count')) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Financial Chart
    const financialCtx = document.getElementById('financialChart').getContext('2d');
    new Chart(financialCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($financialData->pluck('month')) !!},
            datasets: [{
                label: 'Income',
                data: {!! json_encode($financialData->pluck('income')) !!},
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
            }, {
                label: 'Expenses',
                data: {!! json_encode($financialData->pluck('expense')) !!},
                backgroundColor: 'rgba(239, 68, 68, 0.8)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });



    // Gender Distribution Chart
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($genderLabels) !!},
            datasets: [{
                data: {!! json_encode($genderCounts) !!},
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(156, 163, 175, 0.8)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Age Groups Chart
    const ageCtx = document.getElementById('ageChart').getContext('2d');
    new Chart(ageCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($ageLabels) !!},
            datasets: [{
                data: {!! json_encode($ageCounts) !!},
                backgroundColor: [
                    'rgba(251, 191, 36, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(14, 165, 233, 0.8)',
                    'rgba(168, 85, 247, 0.8)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Marital Status Chart
    const maritalCtx = document.getElementById('maritalChart').getContext('2d');
    new Chart(maritalCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($maritalLabels) !!},
            datasets: [{
                data: {!! json_encode($maritalCounts) !!},
                backgroundColor: [
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(156, 163, 175, 0.8)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>

@endsection
