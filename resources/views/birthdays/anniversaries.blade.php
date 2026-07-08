@extends('layouts.admin')

@section('title', 'Anniversaries')

@section('content')
<div class="container-fluid">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">💍 {{ __('Anniversaries') }}</h1>
            <p class="text-muted small mb-0">{{ __('Celebrating love and commitment') }}</p>
        </div>
        
        <!-- Month Filter -->
        <div class="btn-group shadow-sm">
            <a href="{{ route('anniversaries.index', ['month' => $month == 1 ? 12 : $month - 1]) }}" class="btn btn-light border">
                <i class="fas fa-chevron-left"></i>
            </a>
            <button type="button" class="btn btn-light border font-weight-bold px-4">
                {{ DateTime::createFromFormat('!m', $month)->format('F') }}
            </button>
            <a href="{{ route('anniversaries.index', ['month' => $month == 12 ? 1 : $month + 1]) }}" class="btn btn-light border">
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

    <!-- Today's Anniversaries Highlight -->
    @php
        $todaysAnniversaries = $anniversaries->where('is_today', true);
    @endphp

    @if($todaysAnniversaries->isNotEmpty())
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-danger text-white shadow-lg border-0 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div style="position: absolute; right: -20px; top: -20px; font-size: 10rem; opacity: 0.1;">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4 class="font-weight-bold mb-3">💖 {{ __('Happy Anniversary!') }}</h4>
                    <div class="row">
                        @foreach($todaysAnniversaries as $item)
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center bg-white rounded p-3 text-dark shadow-sm">
                                <div class="mr-3">
                                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="font-weight-bold mb-0">{{ $item['member']->full_name }}</h5>
                                    <p class="text-muted mb-0">{{ __('Celebrating') }} {{ $item['years'] }} {{ __('years!') }}</p>
                                    
                                    @if($item['member']->user_id == auth()->id())
                                        <button onclick="openAnniversaryModal()" class="btn btn-sm btn-outline-danger btn-block mt-2">
                                            <i class="fas fa-gift mr-1"></i> {{ __('View Wishes') }}
                                        </button>
                                    @else
                                        <form action="{{ route('birthdays.sendGreeting', $item['member']) }}" method="POST" class="mt-2">
                                            @csrf
                                            <input type="hidden" name="type" value="anniversary">
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-block">
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
            <h6 class="m-0 font-weight-bold text-danger">{{ __('Anniversaries in') }} {{ DateTime::createFromFormat('!m', $month)->format('F') }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($anniversaries->where('is_today', false) as $item)
                <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 border-left-danger shadow-sm hover-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    {{ $item['day_of_week'] }}, {{ DateTime::createFromFormat('!m', $month)->format('M') }} {{ $item['date'] }}
                                </div>
                                <span class="badge badge-light border">{{ $item['years'] }} {{ __('Years') }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    <div class="bg-light text-danger rounded-circle d-flex align-items-center justify-content-center border" style="width: 50px; height: 50px;">
                                        <i class="fas fa-ring"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="font-weight-bold text-gray-800 mb-0">
                                        <a href="{{ route('members.show', $item['member']) }}" class="text-dark">
                                            {{ $item['member']->full_name }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">{{ __('Married since') }} {{ $item['member']->anniversary_date ? $item['member']->anniversary_date->format('Y') : '' }}</small>
                                </div>
                            </div>
                            @if($item['member']->user_id == auth()->id())
                                <button onclick="openAnniversaryModal()" class="btn btn-sm btn-block btn-outline-danger">
                                    <i class="fas fa-gift mr-1"></i> {{ __('View Wishes') }}
                                </button>
                            @else
                                <form action="{{ route('birthdays.sendGreeting', $item['member']) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="anniversary">
                                    <button type="submit" class="btn btn-sm btn-block btn-outline-danger">
                                        <i class="far fa-heart mr-1"></i> {{ __('Send Love') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-heart-broken fa-3x text-gray-300"></i>
                    </div>
                    <h5 class="text-gray-500">{{ __('No other anniversaries this month') }}</h5>
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
