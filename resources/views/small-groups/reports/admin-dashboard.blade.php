@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">📊 Small Groups Analytics Dashboard</h1>
            <p class="text-muted">Church-wide weekly report statistics - {{ $weekRange }}</p>
        </div>
    </div>

    <!-- Church-Wide Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $churchStats['participation_rate'] }}%</h3>
                    <p>Participation Rate</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $churchStats['total_evangelism_visits'] }}</h3>
                    <p>Total Evangelism Visits</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $churchStats['total_community_help'] }}</h3>
                    <p>Community Service Acts</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hands-helping"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $churchStats['total_submissions'] }}/{{ $churchStats['total_members'] }}</h3>
                    <p>Reports Submitted</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Group Comparison Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Group-by-Group Participation</h3>
                    <div class="card-tools">
                        <a href="{{ route('small-groups.questions.index') }}" class="btn btn-sm btn-info">
                            <i class="fas fa-cog"></i> Manage Questions
                        </a>
                        <a href="{{ route('small-groups.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Small Groups
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Group Name</th>
                                <th>Leader</th>
                                <th>Total Members</th>
                                <th>Submitted</th>
                                <th>Participation Rate</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groups as $groupData)
                            <tr>
                                <td><strong>{{ $groupData['group']->name }}</strong></td>
                                <td>{{ $groupData['group']->leader->full_name }}</td>
                                <td>{{ $groupData['total_members'] }}</td>
                                <td>{{ $groupData['submitted_count'] }}</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar 
                                            @if($groupData['participation_rate'] >= 80) bg-success
                                            @elseif($groupData['participation_rate'] >= 50) bg-warning
                                            @else bg-danger
                                            @endif" 
                                            role="progressbar" 
                                            style="width: {{ $groupData['participation_rate'] }}%">
                                            {{ $groupData['participation_rate'] }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($groupData['participation_rate'] >= 80)
                                        <span class="badge badge-success">Excellent</span>
                                    @elseif($groupData['participation_rate'] >= 50)
                                        <span class="badge badge-warning">Good</span>
                                    @else
                                        <span class="badge badge-danger">Needs Follow-up</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No active small groups found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Insights -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">💡 Weekly Insights</h5>
                </div>
                <div class="card-body">
                    <h6>This Week's Highlights:</h6>
                    <ul>
                        <li><strong>{{ $churchStats['total_evangelism_visits'] }}</strong> evangelism visits made across all groups</li>
                        <li><strong>{{ $churchStats['total_community_help'] }}</strong> acts of community service performed</li>
                        <li><strong>{{ $churchStats['participation_rate'] }}%</strong> of members submitted their weekly reports</li>
                        <li><strong>{{ $groups->where('participation_rate', '>=', 80)->count() }}</strong> groups achieved 80%+ participation</li>
                    </ul>

                    @if($churchStats['participation_rate'] < 50)
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Action Needed:</strong> 
                        Overall participation is below 50%. Consider encouraging leaders to follow up with their members.
                    </div>
                    @elseif($churchStats['participation_rate'] >= 80)
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-trophy"></i> <strong>Excellent!</strong> 
                        Church-wide participation is outstanding this week. Keep up the great work!
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-header">
                    <h5 class="card-title mb-0">📅 Report Period</h5>
                </div>
                <div class="card-body">
                    <p><strong>Week:</strong> {{ $weekRange }}</p>
                    <p><strong>Total Groups:</strong> {{ $groups->count() }}</p>
                    <p><strong>Total Members:</strong> {{ $churchStats['total_members'] }}</p>
                    <p><strong>Total Submissions:</strong> {{ $churchStats['total_submissions'] }}</p>
                    <hr>
                    <p class="text-muted mb-0"><small>Reports are calculated from Saturday to Friday (Adventist week)</small></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
