@extends('layouts.admin')

@section('title', 'Birthdays')

@section('content')
<div class="container-fluid">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">🎂 {{ __('Birthdays') }}</h1>
            <p class="text-muted small mb-0">{{ __('Celebrate your members\' special days') }}</p>
        </div>
        
        <!-- Month Filter -->
        <div class="btn-group shadow-sm">
            <a href="{{ route('birthdays.index', ['month' => $month == 1 ? 12 : $month - 1]) }}" class="btn btn-light border">
                <i class="fas fa-chevron-left"></i>
            </a>
            <button type="button" class="btn btn-light border font-weight-bold px-4">
                {{ DateTime::createFromFormat('!m', $month)->format('F') }}
            </button>
            <a href="{{ route('birthdays.index', ['month' => $month == 12 ? 1 : $month + 1]) }}" class="btn btn-light border">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <!-- Today's Birthdays Highlight -->
    @php
        $todaysBirthdays = $birthdays->where('is_today', true);
    @endphp

    @if($todaysBirthdays->isNotEmpty())
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white shadow-lg border-0 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div style="position: absolute; right: -20px; top: -20px; font-size: 10rem; opacity: 0.1;">
                        <i class="fas fa-birthday-cake"></i>
                    </div>
                    <h4 class="font-weight-bold mb-3">🎉 {{ __('Happy Birthday to...') }}</h4>
                    <div class="row">
                        @foreach($todaysBirthdays as $item)
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center bg-white rounded p-3 text-dark shadow-sm">
                                <div class="mr-3">
                                    @if($item['member']->profile_photo)
                                        <img src="{{ Storage::url($item['member']->profile_photo) }}" class="rounded-circle" width="60" height="60" style="object-fit: cover;">
                                    @else
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                            {{ substr($item['member']->first_name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="font-weight-bold mb-0">{{ $item['member']->full_name }}</h5>
                                    <p class="text-muted mb-0">{{ __('Turning') }} {{ $item['age'] }} {{ __('today!') }}</p>
                                    @if($item['member']->user_id == auth()->id())
                                        <button onclick="openBirthdayModal()" class="btn btn-sm btn-outline-primary btn-block mt-2">
                                            <i class="fas fa-gift mr-1"></i> {{ __('View Wishes') }}
                                        </button>
                                    @else
                                        <form action="{{ route('birthdays.sendGreeting', $item['member']) }}" method="POST" class="mt-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary btn-block">
                                                <i class="fas fa-envelope mr-1"></i> {{ __('Send Wish') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Monthly Calendar Grid -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Upcoming Birthdays in') }} {{ DateTime::createFromFormat('!m', $month)->format('F') }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($birthdays->where('is_today', false) as $item)
                <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 border-left-info shadow-sm hover-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    {{ $item['day_of_week'] }}, {{ DateTime::createFromFormat('!m', $month)->format('M') }} {{ $item['date'] }}
                                </div>
                                <span class="badge badge-light border">{{ $item['age'] }} {{ __('Years') }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    @if($item['member']->profile_photo)
                                        <img src="{{ Storage::url($item['member']->profile_photo) }}" class="rounded-circle" width="50" height="50" style="object-fit: cover;">
                                    @else
                                        <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center border" style="width: 50px; height: 50px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="font-weight-bold text-gray-800 mb-0">
                                        <a href="{{ route('members.show', $item['member']) }}" class="text-dark">
                                            {{ $item['member']->full_name }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">{{ $item['member']->phone ?? __('No phone') }}</small>
                                </div>
                            </div>
                            
                            @if($item['member']->user_id == auth()->id())
                                <button onclick="openBirthdayModal()" class="btn btn-sm btn-outline-primary btn-block">
                                   <i class="fas fa-gift mr-1"></i> {{ __('View Wishes') }}
                                </button>
                            @else
                                <form action="{{ route('birthdays.sendGreeting', $item['member']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-block btn-outline-info">
                                        <i class="far fa-paper-plane mr-1"></i> {{ __('Send Greeting') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-calendar-times fa-3x text-gray-300"></i>
                    </div>
                    <h5 class="text-gray-500">{{ __('No other birthdays this month') }}</h5>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.2s;
}
.hover-card:hover {
    transform: translateY(-5px);
}
</style>
@endsection
