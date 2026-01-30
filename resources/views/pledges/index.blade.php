@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    @if(auth()->user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'financial_officer']))
    <div class="row mt-3">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($totalPledged) }}</h3>
                    <p>{{ __('Total Pledged') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($totalPaid) }}</h3>
                    <p>{{ __('Total Paid') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $completionRate }}%</h3>
                    <p>{{ __('Completion Rate') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> {{ __('My Pledges') }}
            </h3>
            <div class="card-tools">
                @can('create', App\Models\Pledge::class)
                    <a href="{{ route('pledges.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Make a Pledge') }}
                    </a>
                @endcan
            </div>
        </div>
        <div class="card-body">
            @if($pledges->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                @if(auth()->user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'financial_officer']))
                                    <th>{{ __('Member') }}</th>
                                @endif
                                <th>{{ __('Purpose') }}</th>
                                <th>{{ __('Pledged') }}</th>
                                <th>{{ __('Paid') }}</th>
                                <th>{{ __('Remaining') }}</th>
                                <th>{{ __('Progress') }}</th>
                                <th>{{ __('Period') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pledges as $pledge)
                            <tr>
                                @if(auth()->user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'financial_officer']))
                                    <td>{{ $pledge->member->full_name }}</td>
                                @endif
                                <td><strong>{{ $pledge->purpose }}</strong></td>
                                <td>{{ number_format($pledge->amount, 2) }}</td>
                                <td class="text-success"><strong>{{ number_format($pledge->amount_paid, 2) }}</strong></td>
                                <td class="text-danger">{{ number_format($pledge->remaining_balance, 2) }}</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-{{ $pledge->completion_percentage >= 100 ? 'success' : ($pledge->completion_percentage >= 50 ? 'warning' : 'danger') }}" 
                                             role="progressbar" 
                                             style="width: {{ min($pledge->completion_percentage, 100) }}%"
                                             aria-valuenow="{{ $pledge->completion_percentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ number_format($pledge->completion_percentage, 1) }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $pledge->start_date->format('M Y') }} - {{ $pledge->end_date->format('M Y') }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ $pledge->status === 'completed' ? 'success' : 'primary' }}">
                                        {{ ucfirst(__($pledge->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('pledges.show', $pledge) }}" class="btn btn-xs btn-info">
                                        <i class="fas fa-eye"></i> {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $pledges->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-hand-holding-usd fa-3x text-muted mb-3"></i>
                    <p class="text-muted">{{ __('No pledges found.') }}</p>
                    @if(!auth()->user()->member)
                        <p class="text-muted">{{ __('Please complete your profile to make pledges.') }}</p>
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-user-plus"></i> {{ __('Complete Your Profile') }}
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
