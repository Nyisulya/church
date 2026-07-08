@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">📊 {{ __('Ripoti za Wiki') }} (Weekly Reports)</h1>
            <p class="text-muted">{{ __('Kusanya na uangalie taarifa za maisha ya kiroho na huduma za wiki') }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_weeks_submitted'] }}</h3>
                    <p>{{ __('Wiki Zilizowasilishwa') }}</p>
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
                    <p>{{ __('Mitembeleo ya Uinjilisti') }}</p>
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
                    <p>{{ __('Misaada kwa Jamii') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hands-helping"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap" style="gap: 10px;">
                        <a href="{{ route('weekly-reports.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-user"></i> {{ __('Ripoti Yangu Binafsi') }}
                        </a>
                        
                        @if($isLeader)
                        <a href="{{ route('weekly-reports.group.create') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-users"></i> {{ __('Ripoti ya Jumla ya Kanda') }}
                        </a>
                        <a href="{{ route('weekly-reports.leader-dashboard') }}" class="btn btn-info btn-lg">
                            <i class="fas fa-tachometer-alt"></i> {{ __('Kanda Leader Dashboard') }}
                        </a>
                        @endif

                        @if($isAdmin)
                        <a href="{{ route('weekly-reports.admin') }}" class="btn btn-danger btn-lg">
                            <i class="fas fa-chart-pie"></i> {{ __('Admin Reports Dashboard') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submission History -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Historia ya Ripoti Zangu') }}</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Week Starting') }}</th>
                                <th>{{ __('Week Range') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
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
                                            {{ $responses->submitted_at ? $responses->submitted_at->format('M d, Y H:i') : __('Submitted') }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">No data</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('weekly-reports.edit', $report->week_starting->format('Y-m-d')) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> {{ __('Angalia / Hariri') }}
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>{{ __('Hujajaza ripoti yoyote bado. Anza kwa kujaza ripoti ya wiki hii!') }}</p>
                                    <a href="{{ route('weekly-reports.create') }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-paper-plane"></i> {{ __('Jaza Ripoti yako ya Kwanza') }}
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
