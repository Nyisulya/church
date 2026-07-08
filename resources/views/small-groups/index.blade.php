@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">📚 {{ __('Small Groups') }}</h1>
        </div>
    </div>

    @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor']))
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('small-groups.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('Create New Group') }}
            </a>
            <a href="{{ route('weekly-reports.admin') }}" class="btn btn-success ml-2">
                <i class="fas fa-chart-bar"></i> {{ __('View Analytics') }}
            </a>
        </div>
    </div>
    @endif

    <div class="row">
        @foreach($groups as $group)
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $group->name }}</h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $group->isFull() ? 'danger' : 'success' }}">
                            {{ $group->members->count() }}/{{ $group->max_members }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ Str::limit($group->description, 100) }}</p>
                    <p><strong>{{ __('Leader') }}:</strong> {{ $group->leader->full_name }}</p>
                    @if($group->meeting_day)
                    <p><i class="fas fa-calendar"></i> {{ __($group->meeting_day) }} @ {{ $group->meeting_time }}</p>
                    @endif
                    @if($group->location)
                    <p><i class="fas fa-map-marker-alt"></i> {{ $group->location }}</p>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('small-groups.show', $group) }}" class="btn btn-sm btn-primary">{{ __('View Details') }}</a>
                    @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor']))
                    <a href="{{ route('small-groups.edit', $group) }}" class="btn btn-sm btn-info">{{ __('Edit') }}</a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
