@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">{{ __('Ministry Pledges') }}</h1>
            <p class="text-muted">{{ __('Pledges from your ministries') }}</p>
        </div>
    </div>

    <div class="row">
        @forelse($pledges as $pledge)
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $pledge->title }}</h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $pledge->status == 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($pledge->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <p><strong>{{ __('Ministry') }}:</strong> {{ $pledge->department->name }}</p>
                    <p class="text-muted">{{ Str::limit($pledge->description, 100) }}</p>
                    
                    <div class="progress mb-2" style="height: 25px;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $pledge->progress_percentage }}%"
                             aria-valuenow="{{ $pledge->progress_percentage }}" 
                             aria-valuemin="0" aria-valuemax="100">
                            {{ number_format($pledge->progress_percentage, 1) }}%
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span><strong>{{ __('Target') }}:</strong> {{ number_format($pledge->target_amount, 2) }} TZS</span>
                        <span><strong>{{ __('Raised') }}:</strong> {{ number_format($pledge->total_contributed, 2) }} TZS</span>
                    </div>
                    
                    @if($pledge->target_date)
                    <p class="text-muted small">
                        <i class="fas fa-calendar"></i> {{ __('Target Date') }}: {{ $pledge->target_date->format('M d, Y') }}
                    </p>
                    @endif
                    
                    <a href="{{ route('ministry-pledges.show', $pledge) }}" class="btn btn-primary btn-sm">
                        {{ __('View Details') }} & {{ __('Contribute') }} →
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> {{ __('No ministry pledges yet.') }}
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
