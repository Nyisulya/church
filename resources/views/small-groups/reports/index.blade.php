@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">📊 My Weekly Reports</h1>
            <p class="text-muted">View your submission history and statistics</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_weeks_submitted'] }}</h3>
                    <p>Weeks Submitted</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['total_evangelism_visits'] }}</h3>
                    <p>Evangelism Visits</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['total_community_help'] }}</h3>
                    <p>Community Service</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hands-helping"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['bible_reading_weeks'] }}</h3>
                    <p>Bible Reading Weeks</p>
                </div>
                <div class="icon">
                    <i class="fas fa-book-open"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Weekly Reports History</h3>
                    <div class="card-tools">
                        <a href="{{ route('small-groups.reports.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Submit This Week's Report
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Week Starting</th>
                                <th>Week Range</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($weeklyReports as $report)
                            @php
                                $weekRange = App\Models\SmallGroupResponse::formatWeekRange($report->week_starting);
                                $responses = App\Models\SmallGroupResponse::forMember(Auth::user()->member->id)
                                    ->forWeek($report->week_starting)
                                    ->first();
                            @endphp
                            <tr>
                                <td>{{ $report->week_starting->format('M d, Y') }}</td>
                                <td>{{ $weekRange }}</td>
                                <td>
                                    @if($responses)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> 
                                            {{ $responses->submitted_at->format('M d, Y H:i') }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">No data</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('small-groups.reports.edit', $report->week_starting->format('Y-m-d')) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> View/Edit
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No reports submitted yet. Start by submitting this week's report!</p>
                                    <a href="{{ route('small-groups.reports.create') }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-paper-plane"></i> Submit Your First Report
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
