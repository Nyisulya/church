@extends('layouts.admin')

@section('content')
@php
    $user = auth()->user();
    $member = $user->member;
    $isLeader = $member && ($department->chairman_id == $member->id || $department->secretary_id == $member->id);
@endphp

<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-md-8">
            <h1 class="m-0 text-dark">{{ $department->name }}</h1>
            <p class="text-muted">{{ $department->description }}</p>
        </div>
        <div class="col-md-4 text-right">
            @if($isLeader)
            <a href="{{ route('ministry-pledges.create', $department) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('Create Pledge') }}
            </a>
            @endif
        </div>
    </div>

    <!-- Ministry Info -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>{{ __('Chairman/Chairwoman') }}:</strong><br>
                            {{ $department->chairman ? $department->chairman->full_name : __('Not assigned') }}
                        </div>
                        <div class="col-md-4">
                            <strong>{{ __('Secretary') }}:</strong><br>
                            {{ $department->secretary ? $department->secretary->full_name : __('Not assigned') }}
                        </div>
                        <div class="col-md-4">
                            <strong>{{ __('Total Members') }}:</strong><br>
                            {{ $department->members->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements Section -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Announcements') }}</h3>
                </div>
                <div class="card-body">
                    <!-- Post Announcement Form (Leaders Only) -->
                    @if($isLeader)
                    <div class="mb-4 bg-light p-3 rounded">
                        <h5>{{ __('Post New Announcement') }}</h5>
                        <form action="{{ route('departments.announcements.store', $department) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <input type="text" name="title" class="form-control" placeholder="{{ __('Title') }}" required>
                            </div>
                            <div class="form-group">
                                <textarea name="body" class="form-control" rows="3" placeholder="{{ __('Message body...') }}" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">{{ __('Post Announcement') }}</button>
                        </form>
                    </div>
                    @endif

                    <!-- Announcements List -->
                    <div class="timeline">
                        @forelse($department->announcements as $announcement)
                        <div>
                            <i class="fas fa-bullhorn bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ $announcement->created_at->diffForHumans() }}</span>
                                <h3 class="timeline-header">
                                    <span class="text-primary font-weight-bold">{{ $announcement->title }}</span>
                                    <small class="text-muted">{{ __('posted by') }} {{ $announcement->author->name }}</small>
                                </h3>
                                <div class="timeline-body">
                                    {{ $announcement->body }}
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted text-center">{{ __('No announcements yet.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ministry Pledges -->
    @php
        $pledges = $department->pledges()->with('contributions')->get();
    @endphp
    
    @if($pledges->count() > 0)
    <div class="row mb-3">
        <div class="col-12">
            <h3>{{ __('Ministry Pledges') }}</h3>
        </div>
        @foreach($pledges as $pledge)
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $pledge->title }}</h4>
                    <span class="badge badge-{{ $pledge->status == 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($pledge->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="progress mb-2" style="height: 25px;">
                        <div class="progress-bar" style="width: {{ $pledge->progress_percentage }}%">
                            {{ number_format($pledge->progress_percentage, 1) }}%
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><strong>{{ __('Target') }}:</strong> {{ number_format($pledge->target_amount, 2) }} TZS</span>
                        <span><strong>{{ __('Raised') }}:</strong> {{ number_format($pledge->total_contributed, 2) }} TZS</span>
                    </div>
                    <a href="{{ route('ministry-pledges.show', $pledge) }}" class="btn btn-sm btn-primary mt-2">
                        {{ __('View') }} & {{ __('Contribute') }} →
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Member List (Leaders Only) -->
    @if($isLeader && $department->members->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Members') }}</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Phone') }}</th>
                            </tr>
                        </thead>
                       <tbody>
                            @foreach($department->members as $member)
                            <tr>
                                <td>{{ $member->full_name }}</td>
                                <td>{{ $member->email }}</td>
                                <td>{{ $member->phone }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

