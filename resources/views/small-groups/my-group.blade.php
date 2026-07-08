@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">{{ $group->name ?? 'My Small Group' }}</h1>
        </div>
    </div>

    @if($group)
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Group Information</h3>
                </div>
                <div class="card-body">
                    <p>{{ $group->description }}</p>
                    <p><strong>Leader:</strong> {{ $group->leader->full_name }}</p>
                    @if($group->meeting_day)
                    <p><strong>Meeting:</strong> {{ $group->meeting_day }} @ {{ $group->meeting_time }}</p>
                    @endif
                    @if($group->location)
                    <p><strong>Location:</strong> {{ $group->location }}</p>
                    @endif
                </div>
            </div>

            <!-- Members List -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Members ({{ $group->members->count() }})</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table">
                        <tbody>
                            @foreach($group->members as $member)
                            <tr>
                                <td>{{ $member->full_name }}</td>
                                <td>
                                    @if($member->pivot->role === 'co-leader')
                                    <span class="badge badge-info">Co-Leader</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            
            <!-- My Payments / Debts -->
            @if(isset($myDebts) && $myDebts->count() > 0)
            <div class="card card-danger card-outline mb-3">
                <div class="card-header">
                    <h5 class="card-title text-danger">
                        <i class="fas fa-exclamation-circle"></i> Pending Payments
                    </h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($myDebts as $debt)
                        <li class="list-group-item">
                            <strong>{{ $debt->name }}</strong>
                            <span class="float-right text-danger font-weight-bold">
                                TSh {{ number_format($debt->my_balance) }}
                            </span>
                            <br>
                            <small class="text-muted">Deadline: {{ $debt->deadline ? $debt->deadline->format('M d') : 'None' }}</small>
                            <a href="{{ route('give.form') }}?amount={{ $debt->my_balance }}&category=small_group&ref={{ $debt->id }}" class="btn btn-xs btn-success float-right mt-1">
                                Pay Now
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Leader Tools (Visible only to Leader/Co-Leader) -->
            @php
                $isLeader = $group->leader_id === Auth::user()->member->id;
                $isCoLeader = $group->members->contains(function($member) {
                    return $member->id === Auth::user()->member->id && $member->pivot->role === 'co-leader';
                });
            @endphp

            @if($isLeader || $isCoLeader)
            <div class="card bg-info text-white mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">👑 Leader Tools</h5>
                    <p class="mb-3">Manage your small group reports</p>
                    <a href="{{ route('weekly-reports.leader-dashboard') }}" class="btn btn-light btn-block">
                        <i class="fas fa-tachometer-alt"></i> Open Leader Dashboard
                    </a>
                </div>
            </div>
            @endif

            <!-- Weekly Report Call to Action -->
            <div class="card bg-gradient-primary text-white mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">📝 Weekly Report</h5>
                    <p class="mb-3">Submit your weekly spiritual activities report</p>
                    <a href="{{ route('weekly-reports.create') }}" class="btn btn-light btn-block">
                        <i class="fas fa-paper-plane"></i> Submit This Week's Report
                    </a>
                    <a href="{{ route('weekly-reports.index') }}" class="btn btn-outline-light btn-block mt-2">
                        <i class="fas fa-history"></i> View My Reports
                    </a>
                </div>
            </div>

            <!-- Recent Meetings -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Meetings</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <tbody>
                            @forelse($group->meetings->take(5) as $meeting)
                            <tr>
                                <td>
                                    <strong>{{ $meeting->meeting_date->format('M d') }}</strong><br>
                                    <small>{{ $meeting->topic }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center">No meetings yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> You are not currently part of any small group. Contact your pastor to join one!
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
