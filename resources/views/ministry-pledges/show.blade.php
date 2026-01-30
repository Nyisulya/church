@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">{{ $ministryPledge->title }}</h1>
            <p class="text-muted">{{ $ministryPledge->department->name }}</p>
        </div>
    </div>

    <div class="row">
        <!-- Pledge Details -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Pledge Details') }}</h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $ministryPledge->status == 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($ministryPledge->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if($ministryPledge->description)
                    <p>{{ $ministryPledge->description }}</p>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('Target Amount') }}:</strong><br>
                            <span class="h4">{{ number_format($ministryPledge->target_amount, 2) }} TZS</span>
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Raised') }}:</strong><br>
                            <span class="h4 text-success">{{ number_format($ministryPledge->total_contributed, 2) }} TZS</span>
                        </div>
                    </div>

                    <div class="progress mb-3" style="height: 30px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $ministryPledge->progress_percentage }}%">
                            {{ number_format($ministryPledge->progress_percentage, 1) }}%
                        </div>
                    </div>

                    <p><strong>{{ __('Remaining') }}:</strong> {{ number_format($ministryPledge->remaining_amount, 2) }} TZS</p>

                    @if($ministryPledge->target_date)
                    <p><strong>{{ __('Target Date') }}:</strong> {{ $ministryPledge->target_date->format('F d, Y') }}</p>
                    @endif

                    <p class="text-muted small">
                        {{ __('Created by') }}: {{ $ministryPledge->creator->name }} 
                        on {{ $ministryPledge->created_at->format('M d, Y') }}
                    </p>
                </div>
            </div>

            <!-- Contribution Form -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Make a Contribution') }}</h3>
                </div>
                <form action="{{ route('ministry-pledges.contribute', $ministryPledge) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>{{ __('Amount') }} (TZS) *</label>
                            <input type="number" name="amount" class="form-control" required min="0.01" step="0.01">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Contribution Date') }} *</label>
                                    <input type="date" name="contribution_date" class="form-control" 
                                           value="{{ date('Y-m-d') }}" required max="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Payment Method') }}</label>
                                    <select name="payment_method" class="form-control">
                                        <option value="">-- {{ __('Select Option') }} --</option>
                                        <option value="Cash">{{ __('Cash') }}</option>
                                        <option value="M-Pesa">{{ __('M-Pesa') }}</option>
                                        <option value="Bank Transfer">{{ __('Bank Transfer') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Reference Number') }}</label>
                            <input type="text" name="reference_number" class="form-control" 
                                   placeholder="{{ __('e.g. M-Pesa Code, Check No.') }}">
                        </div>

                        <div class="form-group">
                            <label>{{ __('Notes') }}</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-hand-holding-usd"></i> {{ __('Submit Contribution') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- My Contributions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('My Contributions') }}</h3>
                </div>
                <div class="card-body p-0">
                    @forelse($myContributions as $contribution)
                    <div class="p-3 border-bottom">
                        <strong class="text-success">{{ number_format($contribution->amount, 2) }} TZS</strong>
                        <br>
                        <small class="text-muted">{{ $contribution->contribution_date->format('M d, Y') }}</small>
                        @if($contribution->payment_method)
                        <br><small>{{ $contribution->payment_method }}</small>
                        @endif
                    </div>
                    @empty
                    <p class="p-3 text-muted">{{ __('No contributions yet') }}</p>
                    @endforelse
                </div>
                @if($myContributions->count() > 0)
                <div class="card-footer">
                    <strong>{{ __('Total') }}:</strong> {{ number_format($myContributions->sum('amount'), 2) }} TZS
                </div>
                @endif
            </div>

            <!-- All Contributions (Leaders Only) -->
            @if($isLeader)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">{{ __('All Contributions') }}</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('Member') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ministryPledge->contributions as $contribution)
                            <tr>
                                <td>{{ $contribution->member->full_name }}</td>
                                <td>{{ number_format($contribution->amount, 2) }}</td>
                                <td>{{ $contribution->contribution_date->format('M d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
