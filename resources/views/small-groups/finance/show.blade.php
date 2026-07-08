@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">💰 {{ $offering->name }}</h1>
            <p class="text-muted">
                <a href="{{ route('weekly-reports.leader-dashboard') }}">Back to Dashboard</a>
            </p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    <div class="row">
        <!-- Offering Details -->
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <h3 class="profile-username text-center">{{ $offering->name }}</h3>
                    <p class="text-muted text-center">{{ $offering->description }}</p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Target Amount</b> <a class="float-right">{{ number_format($offering->target_amount) }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Collected</b> <a class="float-right">{{ number_format($offering->total_collected) }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Per Member</b> <a class="float-right">{{ $offering->amount_per_member ? number_format($offering->amount_per_member) : 'Voluntary' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Deadline</b> <a class="float-right">{{ $offering->deadline ? $offering->deadline->format('M d, Y') : 'None' }}</a>
                        </li>
                    </ul>

                    <div class="progress mb-3">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $offering->progress_percentage }}%">
                            {{ $offering->progress_percentage }}%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Record Payment Form -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Record Payment</h3>
                </div>
                <form action="{{ route('small-groups.finance.store-payment', $offering) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Member</label>
                            <select name="member_id" class="form-control select2" required>
                                <option value="">Select Member</option>
                                @foreach($offering->smallGroup->members as $member)
                                <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" name="amount" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="form-group">
                            <label>Method</label>
                            <select name="payment_method" class="form-control">
                                <option value="cash">Cash</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="Optional notes">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-block">Record Payment</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Member Payments List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Member Payment Status</h3>
                    <div class="card-tools">
                        <form action="{{ route('small-groups.communication.remind-debtors', $offering) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="fas fa-bell"></i> Remind All Unpaid
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Paid</th>
                                <th>Balance (Debt)</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($membersStatus as $status)
                            <tr>
                                <td>{{ $status['member']->full_name }}</td>
                                <td>{{ number_format($status['paid']) }}</td>
                                <td class="{{ $status['balance'] > 0 ? 'text-danger font-weight-bold' : 'text-success' }}">
                                    {{ number_format($status['balance']) }}
                                </td>
                                <td>
                                    @if($status['status'] === 'Paid')
                                        <span class="badge badge-success">Paid</span>
                                    @elseif($status['status'] === 'Partial')
                                        <span class="badge badge-warning">Partial</span>
                                    @else
                                        <span class="badge badge-danger">Unpaid</span>
                                    @endif
                                </td>
                                <td>
                                    @if($status['balance'] > 0)
                                    <button class="btn btn-xs btn-primary" onclick="alert('Reminder sent to {{ $status['member']->full_name }}')">
                                        <i class="fas fa-envelope"></i> Remind
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Recent Transactions -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Recent Transactions</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Member</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Recorded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offering->payments()->latest()->take(10)->get() as $payment)
                            <tr>
                                <td>{{ $payment->paid_at->format('M d, Y') }}</td>
                                <td>{{ $payment->member->full_name }}</td>
                                <td>{{ number_format($payment->amount) }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                <td>{{ $payment->recorder->name ?? 'System' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
