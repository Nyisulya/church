@extends('layouts.admin')

@section('title', 'Malipo Yamekamilika')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card card-outline card-success text-center shadow-lg">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success fa-5x animate__animated animate__bounceIn"></i>
                    </div>
                    
                    <h2 class="font-weight-bold text-success mb-2">Asante Sana!</h2>
                    <h4 class="text-dark mb-4">Malipo Yamekamilika Kikamilifu</h4>
                    <p class="text-muted px-4">Mchango wako umepokelewa na kuandikishwa kwenye mfumo wa kanisa. Mwenyezi Mungu akubariki sana kwa uaminifu na ukarimu wako.</p>
                    
                    <div class="bg-light p-4 rounded text-left my-4 mx-3">
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Aina ya Mchango:</div>
                            <div class="col-6 font-weight-bold text-right">{{ $payment->category }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Kiasi:</div>
                            <div class="col-6 font-weight-bold text-right text-success" style="font-size: 1.2rem;">
                                TSh {{ number_format($payment->amount) }}
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Namba ya Kumbukumbu:</div>
                            <div class="col-6 text-right font-weight-bold text-monospace">{{ $reference }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-muted">Muda:</div>
                            <div class="col-6 text-right font-weight-bold">{{ $payment->updated_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-success btn-lg px-4 mr-2">
                            <i class="fas fa-home"></i> Nyumbani
                        </a>
                        <a href="{{ route('give.form') }}" class="btn btn-outline-secondary btn-lg px-4">
                            <i class="fas fa-donate"></i> Toa Tena
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
