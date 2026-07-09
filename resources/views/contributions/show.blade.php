@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <h3 class="card-title float-none">{{ __('Contribution Receipt') }}</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle fa-4x text-success"></i>
                        <h2 class="mt-2">{{ $currencySymbol }} {{ number_format($contribution->amount, 2) }}</h2>
                        <p class="text-muted">{{ __('Recorded on') }} {{ $contribution->created_at->format('M d, Y h:i A') }}</p>
                    </div>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>{{ __('Member') }}</b> <span class="float-right">{{ $contribution->member->full_name }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>{{ __('Type') }}</b> <span class="float-right">{{ ucfirst($contribution->type) }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>{{ __('Date') }}</b> <span class="float-right">{{ $contribution->date->format('M d, Y') }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>{{ __('Payment Method') }}</b> <span class="float-right">{{ ucfirst($contribution->payment_method) }}</span>
                        </li>
                        @if($contribution->reference_number)
                            <li class="list-group-item">
                                <b>{{ __('Reference No.') }}</b> <span class="float-right">{{ $contribution->reference_number }}</span>
                            </li>
                        @endif
                        @if($contribution->notes)
                            <li class="list-group-item">
                                <b>{{ __('Notes') }}</b> <br>
                                <p class="text-muted mt-2">{{ $contribution->notes }}</p>
                            </li>
                        @endif
                    </ul>
                    
                    <div class="text-center mt-4 no-print">
                        <a href="{{ route('contributions.download', $contribution) }}" class="btn btn-success">
                            <i class="fas fa-download"></i> {{ __('Download PDF') }}
                        </a>
                        
                        <button onclick="window.print()" class="btn btn-default">
                            <i class="fas fa-print"></i> {{ __('Print Receipt') }}
                        </button>
                        
                        @can('update', $contribution)
                            <a href="{{ route('contributions.edit', $contribution) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> {{ __('Edit') }}
                            </a>
                        @endcan

                        @can('delete', $contribution)
                            <form action="{{ route('contributions.destroy', $contribution) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this contribution? This will also remove the associated financial transaction.') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> {{ __('Delete') }}
                                </button>
                            </form>
                        @endcan

                        <a href="{{ route('contributions.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
