@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-10">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <!-- Pledge Details Card -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-hand-holding-usd"></i> Pledge Details
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('pledges.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Pledges
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Left Column - Basic Info -->
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2"><i class="fas fa-info-circle"></i> Pledge Information</h5>
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td><strong>Member:</strong></td>
                                        <td>{{ $pledge->member->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Purpose:</strong></td>
                                        <td><span class="badge badge-info">{{ $pledge->purpose }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge badge-{{ $pledge->status === 'completed' ? 'success' : 'primary' }}">
                                                {{ ucfirst($pledge->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Start Date:</strong></td>
                                        <td>{{ $pledge->start_date->format('F d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>End Date:</strong></td>
                                        <td>{{ $pledge->end_date->format('F d, Y') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Right Column - Financial Info -->
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2"><i class="fas fa-dollar-sign"></i> Financial Summary</h5>
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td><strong>Total Pledged:</strong></td>
                                        <td class="text-primary"><strong>{{ number_format($pledge->amount, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Amount Paid:</strong></td>
                                        <td class="text-success"><strong>{{ number_format($pledge->amount_paid, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Remaining Balance:</strong></td>
                                        <td class="text-danger"><strong>{{ number_format($pledge->remaining_balance, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Completion:</strong></td>
                                        <td>
                                            <div class="progress" style="height: 25px;">
                                                <div class="progress-bar bg-{{ $pledge->completion_percentage >= 100 ? 'success' : ($pledge->completion_percentage >= 50 ? 'warning' : 'danger') }}" 
                                                     role="progressbar" 
                                                     style="width: {{ min($pledge->completion_percentage, 100) }}%"
                                                     aria-valuenow="{{ $pledge->completion_percentage }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    <strong>{{ number_format($pledge->completion_percentage, 1) }}%</strong>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Make Payment Card -->
            @if($pledge->status !== 'completed' && $pledge->remaining_balance > 0)
                @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer']))
                    <div class="card card-success card-outline mt-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-money-bill-wave"></i> {{ __('Record Offline Payment (Admins Only)') }}
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('give.form') }}?pledge_id={{ $pledge->id }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-credit-card"></i> {{ __('Lipa Online (M-Pesa / Benki)') }}
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('pledges.payment', $pledge) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('Payment Amount') }} <span class="text-danger">*</span></label>
                                            <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                                   step="0.01" min="0.01" max="{{ $pledge->remaining_balance }}" 
                                                   placeholder="{{ __('Enter amount') }}" required value="{{ old('amount') }}">
                                            <small class="text-muted">{{ __('Maximum') }}: {{ number_format($pledge->remaining_balance, 2) }}</small>
                                            @error('amount')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('Payment Method') }} <span class="text-danger">*</span></label>
                                            <select name="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                                                <option value="">{{ __('Select method') }}</option>
                                                <option value="cash">{{ __('Cash') }}</option>
                                                <option value="mpesa">{{ __('M-Pesa') }}</option>
                                                <option value="bank">{{ __('Bank Transfer') }}</option>
                                                <option value="check">{{ __('Check') }}</option>
                                            </select>
                                            @error('payment_method')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('Reference Number') }}</label>
                                            <input type="text" name="reference_number" class="form-control" placeholder="{{ __('Transaction ID') }}" value="{{ old('reference_number') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('Payment Date') }} <span class="text-danger">*</span></label>
                                            <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" 
                                                   value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                            @error('payment_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{ __('Notes') }}</label>
                                            <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('Optional notes') }}">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> {{ __('Record Payment') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- For Regular Members: Guide to Online Payment Gateway -->
                    <div class="card card-primary card-outline mt-3 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-light">
                            <h3 class="card-title font-weight-bold text-dark mb-0">
                                <i class="fas fa-credit-card text-primary mr-1"></i> {{ __('Lipa Ahadi Hapa (Pay Pledge Online)') }}
                            </h3>
                        </div>
                        <div class="card-body text-center py-4">
                            <p class="text-muted mb-4" style="font-size: 15px;">
                                {{ __('Tafadhali tumia mfumo salama wa mtandaoni (M-Pesa, Tigo Pesa, Airtel Money, au Kadi ya Benki) kufanya malipo ya ahadi yako. Malipo yako yatarekodiwa moja kwa moja na kwa usalama.') }}
                            </p>
                            <a href="{{ route('give.form') }}?pledge_id={{ $pledge->id }}" class="btn btn-lg btn-success font-weight-bold shadow-sm px-4 py-2" style="border-radius: 8px; background-color: #28a745; border: none;">
                                <i class="fas fa-mobile-alt mr-2"></i> {{ __('Pay Pledge Online') }}
                            </a>
                            <p class="text-sm text-muted mt-4 mb-0">
                                <i class="fas fa-info-circle text-info"></i> {{ __('Kama ulilipa kwa pesa taslimu (Cash) au kuweka benki moja kwa moja, tafadhali mpatie risiti au taarifa Katibu wa Fedha wa Kanisa ili arekodi malipo yako kwenye mfumo.') }}
                            </p>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Payment History -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Payment History
                    </h3>
                </div>
                <div class="card-body">
                    @if($pledge->payments->count())
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pledge->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td><strong>{{ number_format($payment->amount, 2) }}</strong></td>
                                        <td>{{ ucfirst($payment->transaction->payment_method ?? 'N/A') }}</td>
                                        <td>{{ $payment->transaction->reference_number ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td><strong>Total Paid:</strong></td>
                                        <td colspan="3"><strong class="text-success">{{ number_format($pledge->amount_paid, 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No payments recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
