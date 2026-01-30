@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3"><i class="fas fa-calendar-check"></i> My Attendance History</h1>
            <p class="text-muted">View your attendance records for all events</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(!auth()->user()->member)
        <div class="alert alert-warning alert-dismissible fade show">
            <h5><i class="fas fa-exclamation-triangle"></i> Complete Your Profile to Track Attendance</h5>
            <p class="mb-2">To appear in attendance lists and have your attendance tracked, you need to complete your member profile first.</p>
            <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-user-plus"></i> Complete Your Profile Now
            </a>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Attendance Records for {{ $member->full_name }}</h3>
        </div>
        <div class="card-body">
            @if($attendances->count())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Checked In At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                            <tr>
                                <td>
                                    <strong>{{ $attendance->event->name }}</strong>
                                </td>
                                <td>{{ $attendance->event->date->format('M d, Y') }}</td>
                                <td>{{ $attendance->event->start_time ? \Carbon\Carbon::parse($attendance->event->start_time)->format('h:i A') : 'N/A' }}</td>
                                <td>
                                    @if($attendance->status === 'present')
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Present</span>
                                    @elseif($attendance->status === 'absent')
                                        <span class="badge badge-danger"><i class="fas fa-times"></i> Absent</span>
                                    @elseif($attendance->status === 'late')
                                        <span class="badge badge-warning"><i class="fas fa-clock"></i> Late</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($attendance->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->scanned_at)
                                        {{ $attendance->scanned_at->format('M d, Y h:i A') }}
                                    @else
                                        <span class="text-muted">Not recorded</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Present</span>
                                    <span class="info-box-number">{{ $attendances->where('status', 'present')->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Late</span>
                                    <span class="info-box-number">{{ $attendances->where('status', 'late')->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-times"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Absent</span>
                                    <span class="info-box-number">{{ $attendances->where('status', 'absent')->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Events</span>
                                    <span class="info-box-number">{{ $attendances->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No attendance records found.</p>
                    @if(!auth()->user()->member)
                        <p class="text-muted">Please complete your profile to start tracking attendance.</p>
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-user-plus"></i> Complete Your Profile
                        </a>
                    @else
                        <p class="text-muted">Your attendance will appear here once you attend events.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
