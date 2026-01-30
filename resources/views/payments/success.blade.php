@extends('layouts.admin')

@section('title', 'Payment Initiated')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="fas fa-mobile-alt text-info fa-5x"></i>
                    </div>
                    <h2 class="card-title float-none mb-3">Check Your Phone!</h2>
                    <p class="card-text lead">We've sent a payment prompt to your phone number.</p>
                    
                    <div class="alert alert-warning d-inline-block text-left mt-3">
                        <ol class="mb-0">
                            <li>Unlock your phone.</li>
                            <li>Enter your M-Pesa/Mobile Money PIN to approve.</li>
                            <li>Wait for the confirmation SMS.</li>
                        </ol>
                    </div>

                    <div class="mt-4">
                        <p class="text-muted">Reference: {{ $reference }}</p>
                    </div>

                    <div class="mt-5">
                        <a href="{{ route('financial.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                        </a>
                        <a href="{{ route('give.form') }}" class="btn btn-outline-secondary ml-2">
                            <i class="fas fa-redo"></i> Try Again
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
