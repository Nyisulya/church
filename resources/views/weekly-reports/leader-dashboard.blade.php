@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">👥 {{ $smallGroup->name }} - {{ __('Kanda Leader Dashboard') }}</h1>
            <p class="text-muted">{{ __('Muda wa Ripoti: Wiki ya') }} {{ $weekRange }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    <div class="card card-primary card-outline card-outline-tabs">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="leaderTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="reports-tab" data-toggle="pill" href="#reports" role="tab" aria-controls="reports" aria-selected="true">
                        <i class="fas fa-clipboard-list"></i> {{ __('Ripoti za Wiki') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="finance-tab" data-toggle="pill" href="#finance" role="tab" aria-controls="finance" aria-selected="false">
                        <i class="fas fa-hand-holding-usd"></i> {{ __('Michango na Sadaka za Kanda') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="communication-tab" data-toggle="pill" href="#communication" role="tab" aria-controls="communication" aria-selected="false">
                        <i class="fas fa-comments"></i> {{ __('Mawasiliano / Reminders') }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="leaderTabsContent">
                
                <!-- REPORTS TAB -->
                <div class="tab-pane fade show active" id="reports" role="tabpanel" aria-labelledby="reports-tab">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            @if($groupReportSubmitted)
                                <div class="alert alert-success py-2 mb-0">
                                    <i class="fas fa-check-circle"></i> {{ __('Ripoti ya Jumla ya Kanda imeshavasilishwa!') }}
                                </div>
                            @else
                                <div class="alert alert-warning py-2 mb-0">
                                    <i class="fas fa-info-circle"></i> {{ __('Ripoti ya Jumla ya Kanda bado haijajazwa.') }}
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('weekly-reports.group.create') }}" class="btn btn-success">
                                <i class="fas fa-users"></i> {{ __('Jaza Ripoti ya Jumla ya Kanda') }}
                            </a>
                        </div>
                    </div>

                    <!-- Group Statistics -->
                    <div class="row mb-4 mt-3">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $members->where('has_submitted', true)->count() }}/{{ $members->count() }}</h3>
                                    <p>{{ __('Ripoti za Waumini') }} (Individual)</p>
                                </div>
                                <div class="icon"><i class="fas fa-check-circle"></i></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $groupStats['total_evangelism_visits'] }}</h3>
                                    <p>{{ __('Mitembeleo ya Uinjilisti') }}</p>
                                </div>
                                <div class="icon"><i class="fas fa-user-plus"></i></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $groupStats['total_community_help'] }}</h3>
                                    <p>{{ __('Misaada kwa Jamii') }}</p>
                                </div>
                                <div class="icon"><i class="fas fa-hands-helping"></i></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $groupStats['members_read_bible'] }}</h3>
                                    <p>{{ __('Wanaosoma Biblia') }}</p>
                                </div>
                                <div class="icon"><i class="fas fa-book-open"></i></div>
                            </div>
                        </div>
                    </div>

                    <!-- Submission Status Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Mshiriki') }}</th>
                                    <th>{{ __('Hali') }} (Status)</th>
                                    <th>{{ __('Muda wa Kuwasilisha') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $memberData)
                                <tr class="{{ $memberData['has_submitted'] ? '' : 'table-warning' }}">
                                    <td>
                                        <strong>{{ $memberData['member']->full_name }}</strong><br>
                                        <small class="text-muted">{{ $memberData['member']->phone }}</small>
                                    </td>
                                    <td>
                                        @if($memberData['has_submitted'])
                                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Submitted</span>
                                        @else
                                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $memberData['submitted_at'] ? $memberData['submitted_at']->format('M d, H:i') : '-' }}</td>
                                    <td>
                                        @if(!$memberData['has_submitted'] && $memberData['member']->phone)
                                            <a href="tel:{{ $memberData['member']->phone }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-phone"></i> {{ __('Call') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- FINANCE TAB -->
                <div class="tab-pane fade" id="finance" role="tabpanel" aria-labelledby="finance-tab">
                    <div class="row mb-3">
                        <div class="col-12 text-right">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createOfferingModal">
                                <i class="fas fa-plus-circle"></i> {{ __('Create New Contribution') }}
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        @forelse($offerings as $offering)
                        <div class="col-md-6">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h3 class="card-title"><strong>{{ $offering->name }}</strong></h3>
                                    <div class="card-tools">
                                        <span class="badge badge-primary">Target: {{ number_format($offering->target_amount) }}</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p>{{ $offering->description }}</p>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $offering->progress_percentage }}%">
                                            {{ $offering->progress_percentage }}%
                                        </div>
                                    </div>
                                    <p><strong>Collected:</strong> TSh {{ number_format($offering->total_collected) }}</p>
                                    <p><strong>Per Member:</strong> {{ $offering->amount_per_member ? 'TSh '.number_format($offering->amount_per_member) : 'Voluntary' }}</p>
                                    
                                    <a href="{{ route('small-groups.finance.show', $offering) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> View Details & Record Payments
                                    </a>
                                    
                                    <form action="{{ route('small-groups.communication.remind-debtors', $offering) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Send reminders to all members who owe money?')">
                                            <i class="fas fa-bell"></i> Remind Debtors
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-piggy-bank fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No active contributions. Create one to start collecting funds.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- COMMUNICATION TAB -->
                <div class="tab-pane fade" id="communication" role="tabpanel" aria-labelledby="communication-tab">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">📢 Smart Reminders</h5>
                                </div>
                                <div class="card-body">
                                    <p>Send automated reminders to members based on their status.</p>
                                    
                                    <div class="mb-4">
                                        <h6>Weekly Report Reminders</h6>
                                        <p class="text-muted small">Sends a message to all members who haven't submitted their report for this week.</p>
                                        <form action="{{ route('small-groups.communication.remind-pending', $smallGroup) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-block">
                                                <i class="fas fa-paper-plane"></i> Remind Pending Reporters
                                            </button>
                                        </form>
                                    </div>

                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Reminders are sent via SMS/Email based on member preferences.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Create Offering Modal -->
<div class="modal fade" id="createOfferingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Group Contribution</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('small-groups.finance.store-offering', $smallGroup) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Contribution Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Emergency Fund" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Amount Per Member (Optional)</label>
                                <input type="number" name="amount_per_member" class="form-control" placeholder="0.00">
                                <small class="text-muted">Leave empty for voluntary</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Target Amount (Optional)</label>
                                <input type="number" name="target_amount" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deadline</label>
                        <input type="date" name="deadline" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Contribution</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
